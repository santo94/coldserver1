<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\Lotes;
use App\Models\Contenedores;
use App\Models\Movimientos;
use App\Models\ProductosUbicacionesExistencia;
use App\Models\OrdenProductoPresentacion;
use App\Models\EmpresasSSCC_EmpresasSSCC;
use Carbon\Carbon;

class OrdenController extends Controller
{
    /**
     * Mostrar órdenes activas para handheld
     * Estatus en Ordenes
     *   0 es cerrado
     *   2 es parcial (entrada parcial - no concluida)
     *   1 es activa
     */
    public function ordenesActivas()
    {
        try {
            // Consultar órdenes con estatus = 1 (activo) o 2 (entrada parcial)
            $ordenes = Orden::with(['ordenTipo', 'detalle'])
                ->whereIn('Estatus', [1, 2]) // Incluir activas (1) y parciales (2)
                ->where('OrdenesTipos', 1) 
                ->orderBy('fecha', 'desc')
                ->paginate(20); // Paginación para mejor rendimiento en handheld
            
            return view('handheld.ordenes', compact('ordenes'));
            
        } catch (\Exception $e) {
            return view('handheld.ordenes', [
                'ordenes' => collect(),
                'error' => 'Error al cargar las órdenes: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar detalle de una orden específica
     */
    public function detalleOrden($id)
    {
        try {
            $orden = Orden::with([
                'ordenTipo',
                'detalle',
                'cliente', // Cargar la información del cliente/empresa
                'productosPresententaciones.ProdPre', // Cargar la relación anidada con ProductosPresentaciones
                'productosPresententaciones.unidadMedida', // Cargar la unidad de medida (Nombre y Abreviatura)
                'productosPresententaciones.movimientos' // Cargar movimientos para verificar si ya fue procesado
                
            ])->whereIn('Estatus', [1, 2])->findOrFail($id); // Permitir ver detalles de activas (1) y parciales (2)
            
            return view('handheld.orden-detalle', compact('orden'));
            
        } catch (\Exception $e) {
            return redirect()->route('ordenes.activas')
                ->with('error', 'Orden no encontrada o inactiva');
        }
    }
    
    /**
     * API para obtener órdenes activas en formato JSON
     */
    public function apiOrdenesActivas()
    {
        try {
            $ordenes = Orden::with(['ordenTipo', 'detalle'])
                ->whereIn('Estatus', [1, 2]) // Incluir activas (1) y parciales (2)
                ->orderBy('fecha', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json([
                'status' => 'success',
                'total' => $ordenes->count(),
                'ordenes' => $ordenes
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar la recolección de un producto
     */
    public function guardarRecoleccion(Request $request)
    {
        try {
            // Validar datos de entrada
            $request->validate([
                'producto_id' => 'required|integer',
                'cantidad' => 'required|numeric|min:0',
                'lote' => 'required|string|max:255',
                'descripcion' => 'required|string|max:255'
            ]);

            // Obtener el producto de la orden
            $ordenProducto = OrdenProductoPresentacion::with(['ProdPre.unidadMedida', 'orden'])->findOrFail($request->producto_id);
            
            // Obtener la orden para acceder al OidProveedor del cliente
            $orden = $ordenProducto->orden;
            
            // 1. BUSCAR O CREAR LOTE
            $lote = Lotes::firstOrCreate(
                ['Lote' => $request->lote],
                ['FechaRecepcion' => Carbon::now()]
            );

            // 2. CREAR CONTENEDOR
            // Obtener último consecutivo
            $ultimoConsecutivo = Contenedores::max('Consecutivo') ?? 0;
            $nuevoConsecutivo = $ultimoConsecutivo + 1;

            // Generar SSCC
            // Obtener el último SSCC global y siempre incrementar la secuencia
            $ultimoSSCCGlobal = Contenedores::whereNotNull('SSCC')
                ->where('SSCC', 'like', '4014%')
                ->orderBy('SSCC', 'desc')
                ->first();
            
            $fechaSSCC = Carbon::now()->format('ymd');
            $siguienteNumero = 1;
            
            if ($ultimoSSCCGlobal) {
                // Siempre incrementar el número secuencial (últimos 8 dígitos)
                $numeroActual = (int)substr($ultimoSSCCGlobal->SSCC, -8);
                $siguienteNumero = $numeroActual + 1;
                
                // Si alcanza el máximo de 8 dígitos (99999999), reiniciar en 1
                if ($siguienteNumero > 99999999) {
                    $siguienteNumero = 1;
                }
            }
            
            $sscc = '4014' . $fechaSSCC . str_pad($siguienteNumero, 8, '0', STR_PAD_LEFT);

            // Generar Batch
            $fechaBatch = Carbon::now()->format('ymd');
            $ultimoBatch = Contenedores::where('Batch', 'like', $fechaBatch . '%')
                ->orderBy('Batch', 'desc')
                ->first();
            
            $siguienteBatch = 1;
            if ($ultimoBatch) {
                $numeroActualBatch = (int)substr($ultimoBatch->Batch, -4);
                $siguienteBatch = ($numeroActualBatch >= 9999) ? 1 : $numeroActualBatch + 1;
            }
            $batch = $fechaBatch . str_pad($siguienteBatch, 4, '0', STR_PAD_LEFT);

            // Generar fechas
            $fechaProduccion = Carbon::now()->setTime(12, 0, 0); // Hoy a las 12:00:00
            $fechaRecepcion = Carbon::now(); // Fecha y hora exacta actual

            $contenedor = Contenedores::create([
                'Consecutivo' => $nuevoConsecutivo,
                'Tipo' => 0,
                'Estatus' => 1,
                'Oid_Padre' => null,
                'LotesContenedores' => $lote->OID,
                'FechaProduccion' => $fechaProduccion,
                'FechaRecepcion' => $fechaRecepcion,
                'SSCC' => $sscc,
                'Batch' => $batch,
                'Descripcion' => $request->descripcion
            ]);

            // Generar Codigo como OID + Consecutivo después de crear el registro
            $codigo = $contenedor->OID . $contenedor->Consecutivo;
            $contenedor->update(['Codigo' => $codigo]);

            // 3. CREAR MOVIMIENTO
            $movimiento = Movimientos::create([
                'FechaCreacion' => Carbon::now(),
                'Cantidad' => $request->cantidad,
                'MovimientosTipos' => 1,
                'ordenesProductosPresentaciones' => $ordenProducto->OID,
                'UbicacionesOrigenes' => null,
                'UbicacionesDestinos' => 3,
                'UsuarioCrea' => '90bdc88b-be85-4041-a9fc-2ef8dde38872',
                'Lotes' => $lote->OID,
                'Contenedores' => $contenedor->OID,
                'ProductosPresentaciones' => $ordenProducto->productosPresentaciones ?? null,
                'CantidadEnPiezas' => (float)$request->cantidad,
                'OIDUM' => $ordenProducto->UnidadesMedidas ?? null
            ]);

            // 4. CREAR PRODUCTOS UBICACIONES EXISTENCIA
            $existencia = ProductosUbicacionesExistencia::create([
                'CantidadExistente' => $request->cantidad,
                'FechaIngreso' => Carbon::now(),
                'ProductosPresentaciones' => $ordenProducto->productosPresentaciones ?? null,
                'Ubicaciones' => 3,
                'Lotes' => $lote->OID,
                'ContenedorOid' => $contenedor->OID,
                'Estatus' => 1
            ]);

            // 5. CREAR REGISTRO EN EMPRESASSSCC_EMPRESASSSCC
            $empresaSSCC = EmpresasSSCC_EmpresasSSCC::create([
                'SSCC' => $sscc,
                'oidEmpresa' => $orden->OidProveedor  // OID del proveedor/cliente desde la orden
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Producto guardado correctamente',
                'data' => [
                    'lote_id' => $lote->OID,
                    'contenedor_id' => $contenedor->OID,
                    'movimiento_id' => $movimiento->OID,
                    'existencia_id' => $existencia->OID,
                    'empresa_sscc_id' => $empresaSSCC->SSCC,  // Usar SSCC como clave primaria
                    'sscc' => $sscc,
                    'batch' => $batch,
                    'oid_proveedor' => $orden->OidProveedor  // Para verificar que se obtuvo correctamente
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar una orden (cambiar estatus a 0)
     */
    public function cerrarOrden(Request $request)
    {
        try {
            $request->validate([
                'orden_id' => 'required|integer'
            ]);

            $orden = Orden::findOrFail($request->orden_id);
            
            // Cambiar estatus a 0 (cerrada)
            $orden->update(['Estatus' => 0]);

            return response()->json([
                'status' => 'success',
                'message' => 'Orden cerrada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cerrar la orden: ' . $e->getMessage()
            ], 500);
        }
    }
}

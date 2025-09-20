<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
     * Mostrar órdenes activas pa            // Datos para la etiqueta
            $datosEtiqueta = [
                'sku' => $producto->ProdPre->Codigo ?? 'SKU-000',
                'producto' => $producto->ProdPre->Nombre ?? 'Producto sin nombre',
                'contador' => $contador,
                'total' => $totalEtiquetas,
                'fecha' => \Carbon\Carbon::parse($movimiento->FechaCreacion)->format('d/m/Y'),
                'cantidad' => $movimiento->Cantidad,
                'peso' => $movimiento->Cantidad, // Asumir que cantidad = peso por ahora
                'um' => $producto->unidadMedida->Nombre ?? 'UNIDAD',
                'um_abreviatura' => $producto->unidadMedida->Abreviatura ?? 'UND',
                'sscc' => $contenedor->SSCC,
                'cliente' => $orden->cliente->Nombre ?? $orden->cliente->RazonSocial ?? 'Cliente no disponible',
                'qr_data' => $qrData,     * Estatus en Ordenes
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
                'Estatus' => 0
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

    /**
     * Mostrar órdenes para imprimir etiquetas con conteo de productos
     */
    public function ordenesParaImprimir()
    {
        try {
            // Consultar órdenes para imprimir etiquetas (sin filtro de estatus)
            // Solo las primeras 100 ordenadas por fecha de recepción más reciente
            $ordenes = Orden::with([
                'ordenTipo', 
                'detalle', 
                'cliente',
                'productosPresententaciones' => function($query) {
                    $query->with(['movimientos', 'ProdPre']);
                }
            ])
                ->where('OrdenesTipos', 1) 
                ->orderBy('fecha', 'desc') // Más reciente primero
                ->limit(100) // Límite de 100 órdenes
                ->get();
            
            // Calcular estadísticas de cada orden
            $ordenesConEstadisticas = $ordenes->map(function($orden) {
                $totalProductosSolicitados = $orden->productosPresententaciones->count();
                $totalCantidadSolicitada = $orden->productosPresententaciones->sum('Cantidad');
                
                // Contar productos que ya tienen movimientos (fueron capturados)
                $productosCapturados = $orden->productosPresententaciones->filter(function($producto) {
                    return $producto->movimientos && $producto->movimientos->count() > 0;
                })->count();
                
                // Calcular cantidad total recolectada
                $cantidadRecolectada = $orden->productosPresententaciones->reduce(function($total, $producto) {
                    if ($producto->movimientos && $producto->movimientos->count() > 0) {
                        return $total + $producto->movimientos->sum('Cantidad');
                    }
                    return $total;
                }, 0);
                
                // Determinar si tiene etiquetas generadas
                $tieneEtiquetas = $orden->productosPresententaciones->some(function($producto) {
                    return $producto->movimientos && $producto->movimientos->count() > 0;
                });
                
                // Calcular porcentaje de completitud
                $porcentajeProductos = $totalProductosSolicitados > 0 ? 
                    round(($productosCapturados / $totalProductosSolicitados) * 100) : 0;
                
                $porcentajeCantidad = $totalCantidadSolicitada > 0 ? 
                    round(($cantidadRecolectada / $totalCantidadSolicitada) * 100) : 0;
                
                // Añadir propiedades calculadas al objeto orden
                $orden->productos_capturados = $productosCapturados;
                $orden->productos_solicitados = $totalProductosSolicitados;
                $orden->cantidad_recolectada = $cantidadRecolectada;
                $orden->cantidad_solicitada = $totalCantidadSolicitada;
                $orden->tiene_etiquetas = $tieneEtiquetas;
                $orden->porcentaje_productos = $porcentajeProductos;
                $orden->porcentaje_cantidad = $porcentajeCantidad;
                $orden->texto_progreso = "{$productosCapturados} de {$totalProductosSolicitados} productos";
                $orden->texto_cantidad = number_format($cantidadRecolectada, 2) . " de " . number_format($totalCantidadSolicitada, 2);
                
                return $orden;
            });
            
            return view('handheld.ordenes-imprimir', compact('ordenesConEstadisticas'));
            
        } catch (\Exception $e) {
            return view('handheld.ordenes-imprimir', [
                'ordenesConEstadisticas' => collect(),
                'error' => 'Error al cargar las órdenes: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Generar etiquetas para pallets de una orden
     */
    public function generarEtiquetasPallet($id)
    {
        $orden = Orden::with([
            'cliente',
            'detalle',
            'productosPresententaciones.ProdPre',
            'productosPresententaciones.movimientos.lote',
            'productosPresententaciones.movimientos.contenedor',
            'productosPresententaciones.unidadMedida'
        ])->findOrFail($id);

        // Preparar datos para etiquetas
        $datosEtiquetas = $this->prepararDatosEtiquetas($orden);

        // Configurar TCPDF para etiquetas 4x6" (152.4 x 101.6 mm) en orientación horizontal (Landscape)
        $pdf = new \App\Pdf\CustomTCPDF('L', 'mm', array(152.4, 101.6), true, 'UTF-8', false);
        $pdf->SetCreator('Sistema de Etiquetas SSCC');
        $pdf->SetAuthor('Coldtainer');
        $pdf->SetTitle('Etiquetas SSCC - ' . $orden->Codigo);

        // Sin headers ni footers para etiquetas
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Márgenes mínimos para etiquetas
        $pdf->SetMargins(3, 3, 3);
        $pdf->SetAutoPageBreak(false); // Sin salto automático para etiquetas

        foreach ($datosEtiquetas['etiquetas'] as $etiqueta) {
            $pdf->AddPage();
            
            // Generar HTML para esta etiqueta específica
            $htmlEtiqueta = view('templates.etiqueta_individual', compact('etiqueta'))->render();
            
            $pdf->writeHTML($htmlEtiqueta, true, false, true, false, '');
        }

        // Devolver el PDF de etiquetas
        return $pdf->Output('etiquetas_sscc_' . $orden->Codigo . '.pdf', 'I');
    }

    /**
     * Preparar datos para las etiquetas de pallets
     */
    private function prepararDatosEtiquetas($orden)
    {
        $etiquetas = [];
        $contador = 1;
        
        // Obtener todos los contenedores generados para esta orden
        $contenedores = collect();
        
        foreach ($orden->productosPresententaciones as $producto) {
            // Solo productos que ya fueron procesados (tienen movimientos)
            if ($producto->movimientos && $producto->movimientos->count() > 0) {
                foreach ($producto->movimientos as $movimiento) {
                    if ($movimiento->contenedor) {
                        $contenedores->push([
                            'producto' => $producto,
                            'movimiento' => $movimiento,
                            'contenedor' => $movimiento->contenedor,
                            'lote' => $movimiento->lote
                        ]);
                    }
                }
            }
        }
        
        $totalEtiquetas = $contenedores->count();
        
        foreach ($contenedores as $item) {
            $producto = $item['producto'];
            $movimiento = $item['movimiento'];
            $contenedor = $item['contenedor'];
            $lote = $item['lote'];
            
            // Datos para QR
            $qrData = $this->generarDatosQR([
                'sku' => $producto->ProdPre->Codigo ?? 'N/A',
                'lote' => $lote->Lote ?? 'N/A',
                //'fecha' => \Carbon\Carbon::parse($movimiento->FechaCreacion)->format('d/m/Y'),
                'cantidad' => $movimiento->Cantidad,
                'codigo' => $contenedor->Codigo ?? 'N/A',
                'batch' => $contenedor->Batch ?? 'N/A',
                'sscc' => $contenedor->SSCC
            ]);
            
            $etiquetas[] = $this->crearDatosEtiqueta([
                'sku' => $producto->ProdPre->Codigo ?? 'N/A',
                'producto' => $producto->ProdPre->Nombre ?? 'Producto sin nombre',
                'contador' => $contador,
                'total' => $totalEtiquetas,
                'fecha' => \Carbon\Carbon::parse($movimiento->FechaCreacion)->format('d/m/Y'),
                'cantidad' => $movimiento->Cantidad,
                'peso' => $movimiento->Cantidad, // Asumir que cantidad = peso por ahora
                'um' => $producto->unidadMedida->Nombre ?? 'UNIDAD',
                'um_abreviatura' => $producto->unidadMedida->Abreviatura ?? 'UND',
                'sscc' => $contenedor->SSCC,
                'cliente' => $orden->cliente->Nombre ?? $orden->cliente->RazonSocial ?? 'Cliente no disponible',
                'qr_data' => $qrData,
                'tipo' => 'SSCC',
                'batch' => $contenedor->Batch,
                'lote' => $lote->Lote ?? 'N/A'
            ]);
            
            $contador++;
        }
        
        return [
            'orden' => $orden,
            'etiquetas' => $etiquetas,
            'total_etiquetas' => count($etiquetas),
        ];
    }

        /**
     * Crear datos estructurados para una etiqueta
     */
    private function crearDatosEtiqueta($datos)
    {
        return [
            'sku' => $datos['sku'],
            'producto' => $datos['producto'],
            'contador_texto' => $datos['contador'] . ' de ' . $datos['total'],
            'fecha' => $datos['fecha'],
            'cantidad_texto' => $datos['cantidad'] . ' CAJAS' ,
            'peso' => $datos['peso'] ?? 0,
            'um_nombre' => $datos['um'] ?? 'CAJAS',
            'um_abreviatura' => $datos['um_abreviatura'] ?? 'CAJAS',
            'sscc' => $datos['sscc'],
            'cliente' => Str::limit($datos['cliente'], 85),
            'qr_data' => $datos['qr_data'],
            'qr_image' => $this->generarCodigoQR($datos['qr_data']),
            'tipo' => $datos['tipo'],
        ];
    }

    /**
     * Generar string de datos para el código QR
     */
    private function generarDatosQR($datos)
    {
        // Formato: SKU|fecha|cantidad|mixto|batch|lote|sscc
        $campos = [
            $datos['sku'],
            $datos['lote'],
            $datos['cantidad'],
            $datos['codigo'],
            $datos['batch'],
            $datos['sscc'],
        ];

        return implode('|', $campos);
    }

    /**
     * Generar imagen del código QR usando endroid/qr-code v6
     */
    private function generarCodigoQR($data)
    {
        // En v6.x, QrCode es readonly y se configura en el constructor
        $qrCode = new \Endroid\QrCode\QrCode(
            data: $data,
            size: 240,
            margin: 1
        );
        
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);

        // Convertir a base64 para incluir en HTML
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    /**
     * Generar código de barras usando picqer/php-barcode-generator
     */
    private function generarCodigoBarras($sscc)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        
        // Usar CODE128 que es ideal para códigos alfanuméricos como SSCC
        $barcodeData = $generator->getBarcode($sscc, $generator::TYPE_CODE_128, 2, 40);
        
        // Convertir a base64 para incluir en HTML
        return 'data:image/png;base64,' . base64_encode($barcodeData);
    }

    /**
     * Método temporal para debuggear el problema con OidProveedor
     */
    public function testOrdenProveedor($producto_id = 51213)
    {
        try {
            echo "=== DEBUG TEST ORDEN PROVEEDOR ===\n";
            echo "Producto ID: " . $producto_id . "\n";
            
            // Obtener el producto de la orden con todas las relaciones
            $ordenProducto = OrdenProductoPresentacion::with(['ProdPre.unidadMedida', 'orden'])->find($producto_id);
            
            if (!$ordenProducto) {
                echo "❌ ERROR: No se encontró el OrdenProductoPresentacion con ID: " . $producto_id . "\n";
                return;
            }
            
            echo "✅ OrdenProductoPresentacion encontrado:\n";
            echo "- OID: " . $ordenProducto->OID . "\n";
            echo "- Cantidad: " . $ordenProducto->Cantidad . "\n";
            echo "- Ordenes (FK): " . $ordenProducto->Ordenes . "\n";
            
            // Verificar la orden relacionada
            $orden = $ordenProducto->orden;
            
            if (!$orden) {
                echo "❌ ERROR: No se encontró la Orden relacionada\n";
                echo "Verificando FK 'Ordenes': " . $ordenProducto->Ordenes . "\n";
                
                // Intentar buscar la orden directamente
                $ordenDirecta = \App\Models\Orden::find($ordenProducto->Ordenes);
                if ($ordenDirecta) {
                    echo "✅ Orden encontrada directamente con ID: " . $ordenDirecta->OID . "\n";
                    echo "- OidProveedor: " . ($ordenDirecta->OidProveedor ?? 'NULL') . "\n";
                    echo "- OidCliente: " . ($ordenDirecta->OidCliente ?? 'NULL') . "\n";
                } else {
                    echo "❌ Orden no encontrada ni directamente\n";
                }
                return;
            }
            
            echo "✅ Orden relacionada encontrada:\n";
            echo "- OID: " . $orden->OID . "\n";
            echo "- OidProveedor: " . ($orden->OidProveedor ?? 'NULL') . "\n";
            echo "- OidCliente: " . ($orden->OidCliente ?? 'NULL') . "\n";
            echo "- Estatus: " . $orden->Estatus . "\n";
            
            // Mostrar todos los atributos de la orden
            echo "\n--- TODOS LOS CAMPOS DE LA ORDEN ---\n";
            foreach ($orden->getAttributes() as $campo => $valor) {
                echo "- " . $campo . ": " . ($valor ?? 'NULL') . "\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
        }
    }
}

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Empresa;
use App\Models\EmpresasTipos;
use App\Models\Orden;
use App\Models\OrdenProductoPresentacion;
use App\Models\OrdenDetalle;
use App\Models\UnidadesMedidas;
use App\Models\ProductosPresentaciones;
use App\Models\EmpresaPresentacion;
use App\Models\Lotes;
use App\Models\Contenedores;
use App\Models\Movimientos;
use App\Models\Servicios;
use App\Models\OrdenesServicios;
use App\Models\OrdenesTipos;
use App\Models\MovimientosTipos;
use App\Models\UbicacionesTipos;
use App\Models\Ubicaciones;
use App\Models\ABC;
use App\Http\Controllers\EmpresasController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/visualizar', [EmpresasController::class, 'index'])->name('admin.empresas');
Route::post('/agregar',[EmpresasController::class,'index'])->name('entrada.agregar');
Route::post('/agregars',[EmpresasController::class,'salidas'])->name('entrada.agregarsalida');

Route::post('/buscarentrada',[EmpresasController::class,'index'])->name('buscar');

Route::get('/salidas',[EmpresasController::class, 'salidas'])->name('salidas.lista');
Route::post('/buscarsalida',[EmpresasController::class,'salidas'])->name('buscar.salidas');
Route::get('/almacenamiento',[EmpresasController::class,'almacenamiento'])->name('almacenamiento.lista');
Route::post('/agregara',[EmpresasController::class,'cargar'])->name('almacenamiento.lista');

Route::get('/crear',[EmpresasController::class,'crear']);

Route::get('/movimientos',[EmpresasController::class,'movform']);

Route::post('//buscarrep',[EmpresasController::class,'buscarrep']);

// Ruta para probar la conexión a SQL Server consultando la tabla ordenes
Route::get('/ordenes', function () {
    try {
        // Consultar las primeras 10 órdenes
        $ordenes = DB::table('Ordenes')->take(10)->get();
        
        // Obtener el total de órdenes
        $totalOrdenes = DB::table('Ordenes')->count();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Conexión exitosa a SQL Server',
            'total_ordenes' => $totalOrdenes,
            'primeras_10_ordenes' => $ordenes,
            'servidor' => 'SQL Server 2022',
            'base_datos' => env('DB_DATABASE')
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error al consultar la base de datos: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar los modelos y relaciones
Route::get('/empresas-tipos', function () {
    try {
        $tiposEmpresas = EmpresasTipos::take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a EmpresasTipos',
            'total' => EmpresasTipos::count(),
            'datos' => $tiposEmpresas
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/empresas', function () {
    try {
        // Obtener empresas con su tipo de empresa relacionado
        $empresas = Empresa::with('empresaTipo')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Empresas con relaciones',
            'total' => Empresa::count(),
            'datos' => $empresas
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/empresas-tipo/{id}/empresas', function ($id) {
    try {
        // Obtener un tipo de empresa con todas sus empresas
        $tipoEmpresa = EmpresasTipos::with('empresas')->find($id);
        
        if (!$tipoEmpresa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipo de empresa no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Tipo de empresa con sus empresas',
            'tipo_empresa' => $tipoEmpresa,
            'total_empresas' => $tipoEmpresa->empresas->count()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar los modelos Orden y OrdenProductoPresentacion
Route::get('/ordenes-modelo', function () {
    try {
        // Obtener órdenes con sus productos presentaciones, detalles y tipo de orden
        $ordenes = Orden::with(['productosPresententaciones', 'detalle', 'ordenTipo'])->take(5)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Ordenes con relaciones (productos, detalles y tipo)',
            'total' => Orden::count(),
            'datos' => $ordenes
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/ordenes-productos-presentaciones', function () {
    try {
        // Obtener productos presentaciones con su orden relacionada
        $productosPresent = OrdenProductoPresentacion::with('orden')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a OrdenesProductosPresentaciones con relaciones',
            'total' => OrdenProductoPresentacion::count(),
            'datos' => $productosPresent
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/orden/{id}/productos', function ($id) {
    try {
        // Obtener una orden específica con todos sus productos presentaciones, detalles y tipo
        $orden = Orden::with(['productosPresententaciones', 'detalle', 'ordenTipo'])->find($id);
        
        if (!$orden) {
            return response()->json([
                'status' => 'error',
                'message' => 'Orden no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orden con sus productos presentaciones, detalles y tipo',
            'orden' => $orden,
            'total_productos' => $orden->productosPresententaciones->count(),
            'tiene_detalles' => $orden->detalle ? true : false,
            'tipo_orden' => $orden->ordenTipo ? $orden->ordenTipo : null
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo OrdenDetalle
Route::get('/ordenes-detalles', function () {
    try {
        // Obtener detalles de órdenes con su orden relacionada
        $detalles = OrdenDetalle::with('orden')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a OrdenesDetalles con relaciones',
            'total' => OrdenDetalle::count(),
            'datos' => $detalles
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/orden/{id}/detalle', function ($id) {
    try {
        // Obtener una orden específica solo con su detalle
        $orden = Orden::with('detalle')->find($id);
        
        if (!$orden) {
            return response()->json([
                'status' => 'error',
                'message' => 'Orden no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orden con su detalle',
            'orden_id' => $orden->OID,
            'detalle' => $orden->detalle,
            'tiene_detalle' => $orden->detalle ? true : false
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo UnidadesMedidas
Route::get('/unidades-medidas', function () {
    try {
        // Obtener unidades de medida con sus productos presentaciones
        $unidades = UnidadesMedidas::with('productosPresentaciones')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a UnidadesMedidas con relaciones',
            'total' => UnidadesMedidas::count(),
            'datos' => $unidades
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo ProductosPresentaciones
Route::get('/productos-presentaciones', function () {
    try {
        // Obtener productos presentaciones con su unidad de medida y código ABC
        $presentaciones = ProductosPresentaciones::with(['unidadMedida', 'codigoABC'])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a ProductosPresentaciones con relaciones',
            'total' => ProductosPresentaciones::count(),
            'datos' => $presentaciones
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener una unidad de medida específica con todos sus productos presentaciones
Route::get('/unidad-medida/{id}/productos', function ($id) {
    try {
        $unidad = UnidadesMedidas::with(['productosPresentaciones.codigoABC', 'productosPresentaciones.empresas'])->find($id);
        
        if (!$unidad) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unidad de medida no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Unidad de medida con sus productos presentaciones (incluyendo código ABC)',
            'unidad_id' => $unidad->OID,
            'productos_count' => $unidad->productosPresentaciones->count(),
            'datos' => $unidad
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo EmpresaPresentacion (tabla pivote)
Route::get('/empresas-presentaciones', function () {
    try {
        // Obtener relaciones empresa-presentación con sus modelos relacionados
        $relaciones = EmpresaPresentacion::with(['empresa', 'productoPresentacion'])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a EmpresaPPresentacion con relaciones',
            'total' => EmpresaPresentacion::count(),
            'datos' => $relaciones
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para probar relación many-to-many desde Empresas
Route::get('/empresa/{id}/productos-presentaciones', function ($id) {
    try {
        $empresa = Empresa::with(['productosPresentaciones.unidadMedida', 'productosPresentaciones.codigoABC'])->find($id);
        
        if (!$empresa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Empresa no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Empresa con sus productos presentaciones (incluyendo código ABC)',
            'empresa_id' => $empresa->OID,
            'productos_count' => $empresa->productosPresentaciones->count(),
            'datos' => $empresa
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para probar relación many-to-many desde ProductosPresentaciones
Route::get('/producto-presentacion/{id}/empresas', function ($id) {
    try {
        $producto = ProductosPresentaciones::with(['empresas', 'unidadMedida'])->find($id);
        
        if (!$producto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto presentación no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Producto presentación con sus empresas',
            'producto_id' => $producto->OID,
            'empresas_count' => $producto->empresas->count(),
            'datos' => $producto
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo Lotes
Route::get('/lotes', function () {
    try {
        // Obtener lotes con sus contenedores
        $lotes = Lotes::with('contenedores')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Lotes con relaciones',
            'total' => Lotes::count(),
            'datos' => $lotes
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo Contenedores
Route::get('/contenedores', function () {
    try {
        // Obtener contenedores con su lote y movimientos
        $contenedores = Contenedores::with(['lote', 'movimientos.movimientoTipo'])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Contenedores con relaciones (lote y movimientos)',
            'total' => Contenedores::count(),
            'datos' => $contenedores
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un lote específico con todos sus contenedores
Route::get('/lote/{id}/contenedores', function ($id) {
    try {
        $lote = Lotes::with('contenedores')->find($id);
        
        if (!$lote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lote no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lote con sus contenedores',
            'lote_id' => $lote->OID,
            'contenedores_count' => $lote->contenedores->count(),
            'datos' => $lote
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un contenedor específico con su lote
Route::get('/contenedor/{id}/lote', function ($id) {
    try {
        $contenedor = Contenedores::with('lote')->find($id);
        
        if (!$contenedor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contenedor no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Contenedor con su lote',
            'contenedor_id' => $contenedor->OID,
            'lote' => $contenedor->lote,
            'tiene_lote' => $contenedor->lote ? true : false
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo Movimientos
Route::get('/movimientoss', function () {
    try {
        // Obtener movimientos con todas sus relaciones (lote, orden producto presentación, tipo, ubicaciones y contenedor)
        $movimientos = Movimientos::with([
            'movimientoTipo',
            'lote', 
            'contenedor.lote',
            'ordenProductoPresentacion.orden',
            'ubicacionOrigen.ubicacionTipo',
            'ubicacionDestino.ubicacionTipo'
        ])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Movimientos con todas las relaciones (incluyendo contenedor)',
            'total' => Movimientos::count(),
            'datos' => $movimientos
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un lote específico con sus contenedores y movimientos
Route::get('/lote/{id}/completo', function ($id) {
    try {
        $lote = Lotes::with([
            'contenedores.movimientos.movimientoTipo',
            'movimientos.movimientoTipo',
            'movimientos.contenedor',
            'movimientos.ordenProductoPresentacion.orden'
        ])->find($id);
        
        if (!$lote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lote no encontrado'
            ], 404);
        }
        
        // Calcular movimientos totales (del lote + de sus contenedores)
        $movimientosLote = $lote->movimientos->count();
        $movimientosContenedores = $lote->contenedores->sum(function ($contenedor) {
            return $contenedor->movimientos->count();
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lote completo con contenedores y movimientos (incluyendo movimientos de contenedores)',
            'lote_id' => $lote->OID,
            'contenedores_count' => $lote->contenedores->count(),
            'movimientos_lote_count' => $movimientosLote,
            'movimientos_contenedores_count' => $movimientosContenedores,
            'total_movimientos' => $movimientosLote + $movimientosContenedores,
            'datos' => $lote
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener una orden producto presentación con sus movimientos
Route::get('/orden-producto/{id}/movimientos', function ($id) {
    try {
        $ordenProducto = OrdenProductoPresentacion::with(['orden', 'movimientos.lote'])->find($id);
        
        if (!$ordenProducto) {
            return response()->json([
                'status' => 'error',
                'message' => 'Orden producto presentación no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orden producto presentación con sus movimientos',
            'orden_producto_id' => $ordenProducto->OID,
            'movimientos_count' => $ordenProducto->movimientos->count(),
            'datos' => $ordenProducto
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un movimiento específico con todas sus relaciones
Route::get('/movimiento/{id}/completo', function ($id) {
    try {
        $movimiento = Movimientos::with([
            'movimientoTipo',
            'lote.contenedores',
            'contenedor.lote',
            'ordenProductoPresentacion.orden',
            'ubicacionOrigen.ubicacionTipo',
            'ubicacionDestino.ubicacionTipo'
        ])->find($id);
        
        if (!$movimiento) {
            return response()->json([
                'status' => 'error',
                'message' => 'Movimiento no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Movimiento completo con todas sus relaciones (incluyendo contenedor)',
            'movimiento_id' => $movimiento->OID,
            'tiene_tipo' => $movimiento->movimientoTipo ? true : false,
            'tiene_lote' => $movimiento->lote ? true : false,
            'tiene_contenedor' => $movimiento->contenedor ? true : false,
            'tiene_orden_producto' => $movimiento->ordenProductoPresentacion ? true : false,
            'tiene_ubicacion_origen' => $movimiento->ubicacionOrigen ? true : false,
            'tiene_ubicacion_destino' => $movimiento->ubicacionDestino ? true : false,
            'datos' => $movimiento
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo Servicios
Route::get('/servicios', function () {
    try {
        // Obtener servicios con sus órdenes relacionadas
        $servicios = Servicios::with('ordenes')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Servicios con relaciones',
            'total' => Servicios::count(),
            'datos' => $servicios
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo OrdenesServicios (tabla pivote)
Route::get('/ordenes-servicios', function () {
    try {
        // Obtener relaciones orden-servicio con sus modelos relacionados
        $relaciones = OrdenesServicios::with(['orden', 'servicio'])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a OrdenesServicios con relaciones',
            'total' => OrdenesServicios::count(),
            'datos' => $relaciones
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para probar relación many-to-many desde Ordenes con servicios
Route::get('/orden/{id}/servicios', function ($id) {
    try {
        $orden = Orden::with(['servicios', 'productosPresententaciones', 'detalle', 'ordenTipo'])->find($id);
        
        if (!$orden) {
            return response()->json([
                'status' => 'error',
                'message' => 'Orden no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orden completa con servicios, productos, detalles y tipo',
            'orden_id' => $orden->OID,
            'servicios_count' => $orden->servicios->count(),
            'productos_count' => $orden->productosPresententaciones->count(),
            'tiene_detalle' => $orden->detalle ? true : false,
            'tipo_orden' => $orden->ordenTipo ? $orden->ordenTipo : null,
            'datos' => $orden
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para probar relación many-to-many desde Servicios con órdenes
Route::get('/servicio/{id}/ordenes', function ($id) {
    try {
        $servicio = Servicios::with('ordenes')->find($id);
        
        if (!$servicio) {
            return response()->json([
                'status' => 'error',
                'message' => 'Servicio no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Servicio con sus órdenes',
            'servicio_id' => $servicio->OID,
            'ordenes_count' => $servicio->ordenes->count(),
            'datos' => $servicio
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener una orden con todas sus relaciones (productos, servicios, detalles)
Route::get('/orden/{id}/completa', function ($id) {
    try {
        $orden = Orden::with([
            'servicios',
            'productosPresententaciones.movimientos.lote',
            'detalle'
        ])->find($id);
        
        if (!$orden) {
            return response()->json([
                'status' => 'error',
                'message' => 'Orden no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orden completa con todas sus relaciones',
            'orden_id' => $orden->OID,
            'servicios_count' => $orden->servicios->count(),
            'productos_count' => $orden->productosPresententaciones->count(),
            'tiene_detalle' => $orden->detalle ? true : false,
            'datos' => $orden
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo OrdenesTipos
Route::get('/ordenes-tipos', function () {
    try {
        // Obtener tipos de órdenes con sus órdenes relacionadas
        $tiposOrdenes = OrdenesTipos::with('ordenes')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a OrdenesTipos con relaciones',
            'total' => OrdenesTipos::count(),
            'datos' => $tiposOrdenes
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un tipo de orden específico con todas sus órdenes
Route::get('/orden-tipo/{id}/ordenes', function ($id) {
    try {
        $tipoOrden = OrdenesTipos::with(['ordenes.servicios', 'ordenes.productosPresententaciones', 'ordenes.detalle'])->find($id);
        
        if (!$tipoOrden) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipo de orden no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Tipo de orden con todas sus órdenes y relaciones',
            'tipo_orden_id' => $tipoOrden->OID,
            'ordenes_count' => $tipoOrden->ordenes->count(),
            'datos' => $tipoOrden
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo MovimientosTipos
Route::get('/movimientos-tipos', function () {
    try {
        // Obtener tipos de movimientos con sus movimientos relacionados
        $tiposMovimientos = MovimientosTipos::with('movimientos')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a MovimientosTipos con relaciones',
            'total' => MovimientosTipos::count(),
            'datos' => $tiposMovimientos
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un tipo de movimiento específico con todos sus movimientos
Route::get('/movimiento-tipo/{id}/movimientos', function ($id) {
    try {
        $tipoMovimiento = MovimientosTipos::with([
            'movimientos.lote.contenedores',
            'movimientos.ordenProductoPresentacion.orden'
        ])->find($id);
        
        if (!$tipoMovimiento) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipo de movimiento no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Tipo de movimiento con todos sus movimientos y relaciones',
            'tipo_movimiento_id' => $tipoMovimiento->OID,
            'movimientos_count' => $tipoMovimiento->movimientos->count(),
            'datos' => $tipoMovimiento
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo UbicacionesTipos
Route::get('/ubicaciones-tipos', function () {
    try {
        // Obtener tipos de ubicaciones con sus ubicaciones relacionadas
        $tiposUbicaciones = UbicacionesTipos::with('ubicaciones')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a UbicacionesTipos con relaciones',
            'total' => UbicacionesTipos::count(),
            'datos' => $tiposUbicaciones
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo Ubicaciones
Route::get('/ubicaciones', function () {
    try {
        // Obtener ubicaciones con su tipo de ubicación y relaciones jerárquicas
        $ubicaciones = Ubicaciones::with(['ubicacionTipo', 'ubicacionPadre', 'ubicacionesHijas'])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Ubicaciones con relaciones (tipo y jerarquía)',
            'total' => Ubicaciones::count(),
            'datos' => $ubicaciones
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un tipo de ubicación específico con todas sus ubicaciones
Route::get('/ubicacion-tipo/{id}/ubicaciones', function ($id) {
    try {
        $tipoUbicacion = UbicacionesTipos::with('ubicaciones')->find($id);
        
        if (!$tipoUbicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipo de ubicación no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Tipo de ubicación con todas sus ubicaciones',
            'tipo_ubicacion_id' => $tipoUbicacion->OID,
            'ubicaciones_count' => $tipoUbicacion->ubicaciones->count(),
            'datos' => $tipoUbicacion
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener una ubicación específica con su tipo
Route::get('/ubicacion/{id}/tipo', function ($id) {
    try {
        $ubicacion = Ubicaciones::with('ubicacionTipo')->find($id);
        
        if (!$ubicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubicación no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ubicación con su tipo',
            'ubicacion_id' => $ubicacion->OID,
            'tipo_ubicacion' => $ubicacion->ubicacionTipo,
            'tiene_tipo' => $ubicacion->ubicacionTipo ? true : false
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar el modelo ABC
Route::get('/abc', function () {
    try {
        // Obtener códigos ABC con sus productos presentaciones relacionados
        $codigosABC = ABC::with('productosPresentaciones')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a ABC con relaciones',
            'total' => ABC::count(),
            'datos' => $codigosABC
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un código ABC específico con todos sus productos presentaciones
Route::get('/abc/{id}/productos', function ($id) {
    try {
        $codigoABC = ABC::with(['productosPresentaciones.unidadMedida', 'productosPresentaciones.empresas'])->find($id);
        
        if (!$codigoABC) {
            return response()->json([
                'status' => 'error',
                'message' => 'Código ABC no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Código ABC con todos sus productos presentaciones y relaciones',
            'abc_id' => $codigoABC->OID,
            'productos_count' => $codigoABC->productosPresentaciones->count(),
            'datos' => $codigoABC
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener ubicaciones raíz (sin ubicación padre)
Route::get('/ubicaciones/raiz', function () {
    try {
        // Obtener ubicaciones que no tienen ubicación padre (nivel raíz)
        $ubicacionesRaiz = Ubicaciones::with(['ubicacionTipo', 'ubicacionesHijas.ubicacionTipo'])
            ->whereNull('UbicacionP')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ubicaciones raíz con sus hijas directas',
            'total_raiz' => $ubicacionesRaiz->count(),
            'datos' => $ubicacionesRaiz
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener una ubicación con toda su jerarquía descendiente
Route::get('/ubicacion/{id}/jerarquia', function ($id) {
    try {
        $ubicacion = Ubicaciones::with([
            'ubicacionTipo',
            'ubicacionPadre.ubicacionTipo',
            'ubicacionesDescendientes.ubicacionTipo'
        ])->find($id);
        
        if (!$ubicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubicación no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ubicación con toda su jerarquía',
            'ubicacion_id' => $ubicacion->OID,
            'tiene_padre' => $ubicacion->ubicacionPadre ? true : false,
            'hijas_directas' => $ubicacion->ubicacionesHijas->count(),
            'datos' => $ubicacion
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener una ubicación con sus hijas directas solamente
Route::get('/ubicacion/{id}/hijas', function ($id) {
    try {
        $ubicacion = Ubicaciones::with([
            'ubicacionTipo',
            'ubicacionesHijas.ubicacionTipo'
        ])->find($id);
        
        if (!$ubicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubicación no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ubicación con sus ubicaciones hijas directas',
            'ubicacion_id' => $ubicacion->OID,
            'hijas_count' => $ubicacion->ubicacionesHijas->count(),
            'datos' => $ubicacion
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener el árbol completo de ubicaciones (limitado para evitar sobrecarga)
Route::get('/ubicaciones/arbol', function () {
    try {
        // Obtener solo las ubicaciones raíz con sus descendientes (máximo 3 niveles)
        $arbolUbicaciones = Ubicaciones::with([
            'ubicacionTipo',
            'ubicacionesHijas.ubicacionTipo',
            'ubicacionesHijas.ubicacionesHijas.ubicacionTipo'
        ])
        ->whereNull('UbicacionP')
        ->take(5) // Limitamos a 5 raíces para evitar sobrecarga
        ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Árbol de ubicaciones (máximo 3 niveles, 5 raíces)',
            'total_raices' => $arbolUbicaciones->count(),
            'datos' => $arbolUbicaciones
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Rutas para probar las relaciones entre Ubicaciones y Movimientos
Route::get('/ubicacion/{id}/movimientos-origen', function ($id) {
    try {
        $ubicacion = Ubicaciones::with([
            'ubicacionTipo',
            'movimientosOrigen.movimientoTipo',
            'movimientosOrigen.ubicacionDestino.ubicacionTipo'
        ])->find($id);
        
        if (!$ubicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubicación no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ubicación con movimientos donde es origen',
            'ubicacion_id' => $ubicacion->OID,
            'movimientos_origen_count' => $ubicacion->movimientosOrigen->count(),
            'datos' => $ubicacion
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/ubicacion/{id}/movimientos-destino', function ($id) {
    try {
        $ubicacion = Ubicaciones::with([
            'ubicacionTipo',
            'movimientosDestino.movimientoTipo',
            'movimientosDestino.ubicacionOrigen.ubicacionTipo'
        ])->find($id);
        
        if (!$ubicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubicación no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Ubicación con movimientos donde es destino',
            'ubicacion_id' => $ubicacion->OID,
            'movimientos_destino_count' => $ubicacion->movimientosDestino->count(),
            'datos' => $ubicacion
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/ubicacion/{id}/todos-movimientos', function ($id) {
    try {
        $ubicacion = Ubicaciones::with('ubicacionTipo')->find($id);
        
        if (!$ubicacion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ubicación no encontrada'
            ], 404);
        }
        
        // Obtener todos los movimientos relacionados con esta ubicación
        $todosMovimientos = $ubicacion->todosLosMovimientos()
            ->with([
                'movimientoTipo',
                'ubicacionOrigen.ubicacionTipo',
                'ubicacionDestino.ubicacionTipo'
            ])
            ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Todos los movimientos relacionados con la ubicación',
            'ubicacion_id' => $ubicacion->OID,
            'total_movimientos' => $todosMovimientos->count(),
            'ubicacion' => $ubicacion,
            'movimientos' => $todosMovimientos
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener movimientos entre dos ubicaciones específicas
Route::get('/movimientos/origen/{origen_id}/destino/{destino_id}', function ($origen_id, $destino_id) {
    try {
        $movimientos = Movimientos::with([
            'movimientoTipo',
            'ubicacionOrigen.ubicacionTipo',
            'ubicacionDestino.ubicacionTipo',
            'lote'
        ])
        ->where('UbicacionesOrigenes', $origen_id)
        ->where('UbicacionesDestinos', $destino_id)
        ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Movimientos entre ubicaciones específicas',
            'origen_id' => $origen_id,
            'destino_id' => $destino_id,
            'total_movimientos' => $movimientos->count(),
            'datos' => $movimientos
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un contenedor específico con todos sus movimientos
Route::get('/contenedor/{id}/movimientos', function ($id) {
    try {
        $contenedor = Contenedores::with([
            'lote',
            'movimientos.movimientoTipo',
            'movimientos.ubicacionOrigen.ubicacionTipo',
            'movimientos.ubicacionDestino.ubicacionTipo'
        ])->find($id);
        
        if (!$contenedor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contenedor no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Contenedor con todos sus movimientos',
            'contenedor_id' => $contenedor->OID,
            'movimientos_count' => $contenedor->movimientos->count(),
            'tiene_lote' => $contenedor->lote ? true : false,
            'datos' => $contenedor
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta para obtener un contenedor completo (con lote y movimientos)
Route::get('/contenedor/{id}/completo', function ($id) {
    try {
        $contenedor = Contenedores::with([
            'lote.movimientos.movimientoTipo',
            'movimientos.movimientoTipo',
            'movimientos.ubicacionOrigen.ubicacionTipo',
            'movimientos.ubicacionDestino.ubicacionTipo',
            'movimientos.ordenProductoPresentacion.orden'
        ])->find($id);
        
        if (!$contenedor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contenedor no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Contenedor completo con lote y todos los movimientos',
            'contenedor_id' => $contenedor->OID,
            'movimientos_count' => $contenedor->movimientos->count(),
            'lote_movimientos_count' => $contenedor->lote ? $contenedor->lote->movimientos->count() : 0,
            'datos' => $contenedor
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/empresas', [App\Http\Controllers\EmpresasController::class, 'index'])->name('home');

// Rutas para la interfaz Handheld
Route::prefix('handheld')->group(function () {
    Route::get('/ordenes-activas', [App\Http\Controllers\OrdenController::class, 'ordenesActivas'])->name('ordenes.activas');
    Route::get('/orden/{id}/detalle', [App\Http\Controllers\OrdenController::class, 'detalleOrden'])->name('orden.detalle');
    Route::get('/api/ordenes-activas', [App\Http\Controllers\OrdenController::class, 'apiOrdenesActivas'])->name('api.ordenes.activas');
    Route::post('/guardar-recoleccion', [App\Http\Controllers\OrdenController::class, 'guardarRecoleccion'])->name('guardar.recoleccion');
    Route::post('/cerrar-orden', [App\Http\Controllers\OrdenController::class, 'cerrarOrden'])->name('cerrar.orden');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

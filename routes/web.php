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

Route::get('/', function () {
    return view('welcome');
});

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
        // Obtener productos presentaciones con su unidad de medida
        $presentaciones = ProductosPresentaciones::with('unidadMedida')->take(10)->get();
        
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
        $unidad = UnidadesMedidas::with('productosPresentaciones')->find($id);
        
        if (!$unidad) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unidad de medida no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Unidad de medida con sus productos presentaciones',
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
        $empresa = Empresa::with('productosPresentaciones.unidadMedida')->find($id);
        
        if (!$empresa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Empresa no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Empresa con sus productos presentaciones',
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
        // Obtener contenedores con su lote
        $contenedores = Contenedores::with('lote')->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Contenedores con relaciones',
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
Route::get('/movimientos', function () {
    try {
        // Obtener movimientos con sus relaciones (lote y orden producto presentación)
        $movimientos = Movimientos::with(['lote', 'ordenProductoPresentacion.orden'])->take(10)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Movimientos con relaciones',
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
        $lote = Lotes::with(['contenedores', 'movimientos.ordenProductoPresentacion.orden'])->find($id);
        
        if (!$lote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lote no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lote completo con contenedores y movimientos',
            'lote_id' => $lote->OID,
            'contenedores_count' => $lote->contenedores->count(),
            'movimientos_count' => $lote->movimientos->count(),
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
            'lote.contenedores',
            'ordenProductoPresentacion.orden'
        ])->find($id);
        
        if (!$movimiento) {
            return response()->json([
                'status' => 'error',
                'message' => 'Movimiento no encontrado'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Movimiento completo con todas sus relaciones',
            'movimiento_id' => $movimiento->OID,
            'tiene_lote' => $movimiento->lote ? true : false,
            'tiene_orden_producto' => $movimiento->ordenProductoPresentacion ? true : false,
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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

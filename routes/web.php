<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Empresa;
use App\Models\EmpresasTipos;
use App\Models\Orden;
use App\Models\OrdenProductoPresentacion;

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
        // Obtener órdenes con sus productos presentaciones relacionados
        $ordenes = Orden::with('productosPresententaciones')->take(5)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Consulta exitosa a Ordenes con relaciones',
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
        // Obtener una orden específica con todos sus productos presentaciones
        $orden = Orden::with('productosPresententaciones')->find($id);
        
        if (!$orden) {
            return response()->json([
                'status' => 'error',
                'message' => 'Orden no encontrada'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orden con sus productos presentaciones',
            'orden' => $orden,
            'total_productos' => $orden->productosPresententaciones->count()
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

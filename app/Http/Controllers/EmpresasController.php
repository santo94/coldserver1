<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

use Carbon\Carbon;

class EmpresasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)

    {
        if($_POST){
            $fecha1=Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fecha2=Carbon::parse($request->fin)->format('Y-m-d H:i:s');
           
            $ordenesEntrada=Orden::where("OrdenesTipos",1)->whereBetween('fecha', [$fecha1 ,$fecha2])->get();
        $ordenempresas=$ordenesEntrada;
      
        $clientes = $ordenesEntrada->pluck('cliente.Nombre')->unique()->sort();
        $filtro=1;
        return view('tabla1',compact('ordenesEntrada','clientes','filtro','fecha1','fecha2'));
        }else{
        // $contenedores = Contenedores::with(['lote', 'movimientos.movimientoTipo'])->take(10)->get();
        
        return view('tabla1');
    }
        
       
    }

    public function salidas(Request $request)
    {
        if($_POST){
            $fecha1=Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fecha2=Carbon::parse($request->fin)->format('Y-m-d H:i:s');
           
            $ordenesEntrada=Orden::where("OrdenesTipos",2)->whereBetween('fecha', [$fecha1 ,$fecha2])->get();
        $ordenempresas=$ordenesEntrada;
      
        $clientes = $ordenesEntrada->pluck('proveedor.Nombre')->unique()->sort();
        $filtro=1;
        return view('tabla2',compact('ordenesEntrada','clientes','filtro','fecha1','fecha2'));
        }else{
        // $contenedores = Contenedores::with(['lote', 'movimientos.movimientoTipo'])->take(10)->get();
        
        return view('tabla2');
    }
    }

    public function almacenamiento(){
        $filtro=1;
        $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
    $q->where('CantidadExistente', '>', 0);
})->take(10)->get();
        $clientes = $ordenesEntrada->pluck('proveedor.Nombre')->unique()->sort();

       return view('tabla3',compact('ordenesEntrada','clientes','filtro'));
    }
}

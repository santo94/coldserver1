<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\EmpresasTipos;
use App\Models\Orden;
use App\Models\OrdenProductoPresentacion;
use App\Models\User;
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
use App\Models\Datoscontenedor;

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

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)

    {

        if($_POST){
            $ordinput="";
            if(isset($_POST["contenedor_oid"]) && !empty($_POST["contenedor_oid"])){

                date_default_timezone_set("America/Mexico_City");
                $contenedor=Contenedores::where("OID",$request->contenedor_oid)->first();

                if($contenedor->datos){
                    $dat=Datoscontenedor::where("ContenedorOid",$request->contenedor_oid)->first();
                    $dat->altura=$request->altura;
                    $dat->tipo=$request->tipo_almacenado;
                    $dat->fecha=date("Y-m-d H:i:s");
                    $dat->save();

                    $ordinput=$request->orden;
                }
                else{
                    $datosC=new Datoscontenedor;
                    $datosC->ContenedorOid=$request->contenedor_oid;
                    $datosC->altura=$request->altura;
                    $datosC->tipo=$request->tipo_almacenado;
                    $datosC->fecha=date("Y-m-d H:i:s");

                    $datosC->save();
                    $ordinput=$request->orden;
                }

            }
            $fecha1=Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fecha2=Carbon::parse($request->fin)->format('Y-m-d H:i:s');
           
            $ordenesEntrada=Orden::where("OrdenesTipos",1)->whereBetween('fecha', [$fecha1 ,$fecha2])->get();
        $ordenempresas=$ordenesEntrada;
      
        $clientes = $ordenesEntrada->pluck('cliente.Nombre')->unique()->sort();
        $filtro=1;

        return view('tabla1',compact('ordenesEntrada','clientes','filtro','fecha1','fecha2','ordinput'));
        }else{
        // $contenedores = Contenedores::with(['lote', 'movimientos.movimientoTipo'])->take(10)->get();
        
        return view('tabla1');
    }
        
       
    }

    public function salidas(Request $request)
    {

        if($_POST){
            

            $ordinput="";
            if(isset($_POST["contenedor_oid"]) && !empty($_POST["contenedor_oid"])){


                date_default_timezone_set("America/Mexico_City");
                $contenedor=Contenedores::where("OID",$request->contenedor_oid)->first();

                if($contenedor->datos){
                    $dat=Datoscontenedor::where("ContenedorOid",$request->contenedor_oid)->first();
                    $dat->altura=$request->altura;
                    $dat->tipo=$request->tipo_almacenado;
                    $dat->fecha=date("Y-m-d H:i:s");
                    $dat->save();

                    $ordinput=$request->orden;
                }
                else{
                    $datosC=new Datoscontenedor;
                    $datosC->ContenedorOid=$request->contenedor_oid;
                    $datosC->altura=$request->altura;
                    $datosC->tipo=$request->tipo_almacenado;
                    $datosC->fecha=date("Y-m-d H:i:s");

                    $datosC->save();
                    $ordinput=$request->orden;
                }

            }

            $fecha1=Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fecha2=Carbon::parse($request->fin)->format('Y-m-d H:i:s');
           
            $ordenesEntrada=Orden::where([["OrdenesTipos",2],["OidCliente", $request->cliente]])->whereBetween('fecha', [$fecha1 ,$fecha2])->get();

        $ordenempresas=$ordenesEntrada;
        
        $clientes = $ordenesEntrada->pluck('proveedor.Nombre')->unique()->sort();
        $filtro=1;
      $mov=$ordenesEntrada[0]->productosPresententaciones[0]->contenedor->movimientos;
        $sumen=0;
        $sumsal=0;
      foreach ($mov as $movi) {
        if($movi->FechaCreacion <= $fecha2){
          if($movi->MovimientosTipos ==1){
            $sumen += $movi->Cantidad;
          }else{
            if($movi->MovimientosTipos==8){
                $sumsal += $movi->Cantidad;
            }
          }
      }
  }
  $existencia=$sumen-$sumsal;

  //dd("entradas: " .$sumen . " salidas: " . $sumsal ."Existencia" . $existencia. "Contenedor". $mov[0]->Contenedores);

        return view('tabla2',compact('ordenesEntrada','clientes','filtro','fecha1','fecha2','ordinput'));
        }else{
         $clientesT=Empresa::where("EmpresasTipos",1)->get();
        
        return view('tabla2',compact('clientesT'));
    }
    }

    public function almacenamientoi(Request $request)
    {

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           // dd($_POST);
            if($_POST["cliente"] !=""){
                if($_POST["orden"] !=""){
                    $paginatel="no";
                    $empresaId = $request->cliente;
                    $codigoOrden = $request->orden; // input para buscar por orden
                    $fechaInici=$request->inicio;
                    $fechaFin=$request->fin;

                    $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
                            $q->where('CantidadExistente', '>', 0);
                        })
                        ->whereHas('productosPresententaciones.movimientoEntrada.contenedor.sscc_ep', function($q) use ($empresaId) {
                            if ($empresaId) {
                                $q->where('OidEmpresa', $empresaId);
                            }
                        })
                        // Filtro por código de orden (si se proporciona)
                        ->when($codigoOrden, function($query) use ($codigoOrden) {
                            $query->where('Codigo', 'like', "%{$codigoOrden}%");
                        })
                        ->get();
                }else{
                    $empresaId = $request->cliente; // ID de la empresa que quieres filtrar

                    $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
                        $q->where('CantidadExistente', '>', 0);
                    })
                    ->whereHas('productosPresententaciones.movimientoEntrada.contenedor.sscc_ep', function($q) use ($empresaId) {
                        $q->where('OidEmpresa', $empresaId);
                    })
                    ->get();

                    $paginatel="no";

                }

            }else{

                if($_POST["orden"] != ""){
                    $paginatel="no";
                    $codigoOrden = $request->orden; // input para buscar por orden

                    $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
                            $q->where('CantidadExistente', '>', 0);
                        })
                        // Filtro por código de orden (si se proporciona)
                        ->when($codigoOrden, function($query) use ($codigoOrden) {
                            $query->where('Codigo', 'like', "%{$codigoOrden}%");
                        })
                        ->get();
                }else{
                    
                            
                            
                    return redirect()->back()->with('warning', 'No se pudo actualizar la tabla, no se encontro el dato solicitado');
                }
            }
        }

        
        else{
            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico', function($q) {
    $q->where('CantidadExistente', '>', 0);
})->paginate(10);
            $paginatel="si";
        
        $fecha1=date('Y-m-d');

        }
        $filtro=1;
        $cliente=Empresa::where("EmpresasTipos",2)->get();
}
        

public function almacenamiento1(Request $request){

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST["cliente"] != "") {
        if ($_POST["orden"] != "") {
            $paginatel = "no";
            $empresaId   = $request->cliente;
            $codigoOrden = $request->orden; // input para buscar por orden
            $fechaInici = Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fechaFin   = Carbon::parse($request->fin)->format('Y-m-d H:i:s'); 


          $ordenesEntrada = Contenedores::with([
        'sscc_ep.empresasc',
        'movimientoEntrada.ordenProductoPresentacion.ProdPre.unidadMedida',
        'movimientoEntrada.ordenProductoPresentacion.orden',
        'movimientos' => function($q) use ($fechaFin) {
            $q->where('FechaCreacion', '<=', $fechaFin);
        }
    ])
    ->whereHas('sscc_ep.empresasc', fn($q) => $q->where('OidEmpresa', $empresaId))
    ->withSum(['movimientos as total_entradas' => fn($q) => $q->where('MovimientosTipos', 1)->where('FechaCreacion', '<=', $fechaFin)], 'Cantidad')
    ->withSum(['movimientos as total_salidas' => fn($q) => $q->where('MovimientosTipos', 8)->where('FechaCreacion', '<=', $fechaFin)], 'Cantidad')
    ->get()
    ->map(fn($contenedor) => tap($contenedor, fn($c) => $c->existencia = ($c->total_entradas ?? 0) - ($c->total_salidas ?? 0)))
    ->filter(fn($contenedor) => $contenedor->existencia > 0);

        } else {
            $empresaId = $request->cliente; 
            $fechaInici = Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fechaFin   = Carbon::parse($request->fin)->format('Y-m-d H:i:s');

               $ordenesEntrada = Contenedores::with([
        'sscc_ep.empresasc',
        'movimientoEntrada.ordenProductoPresentacion.ProdPre.unidadMedida',
        'movimientoEntrada.ordenProductoPresentacion.orden',
        'movimientos' => function($q) use ($fechaFin) {
            $q->where('FechaCreacion', '<=', $fechaFin);
        }
    ])
    ->whereHas('sscc_ep.empresasc', fn($q) => $q->where('OidEmpresa', $empresaId))
    ->withSum(['movimientos as total_entradas' => fn($q) => $q->where('MovimientosTipos', 1)->where('FechaCreacion', '<=', $fechaFin)], 'Cantidad')
    ->withSum(['movimientos as total_salidas' => fn($q) => $q->where('MovimientosTipos', 8)->where('FechaCreacion', '<=', $fechaFin)], 'Cantidad')
    ->get()
    ->map(fn($contenedor) => tap($contenedor, fn($c) => $c->existencia = ($c->total_entradas ?? 0) - ($c->total_salidas ?? 0)))
    ->filter(fn($contenedor) => $contenedor->existencia > 0);
  

            $paginatel = "no";
        }

    } else {
        if ($_POST["orden"] != "") {
            $paginatel = "no";
            $codigoOrden = $request->orden;
            $fechaInici = Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fechaFin   = Carbon::parse($request->fin)->format('Y-m-d H:i:s'); 

            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico', function($q) use ($fechaInici, $fechaFin) {
                    $q->where('CantidadExistente', '>', 0)
                      ->whereBetween('FechaInsercionDatosHistorico', [$fechaInici, $fechaFin]);
                })
                ->when($codigoOrden, function($q) use ($codigoOrden) {
                    $q->where('Codigo', 'like', "%{$codigoOrden}%");
                })
                ->with([
                    'cliente',
                    'productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico',
                    'productosPresententaciones.ProdPre'
                ])
                ->get();

        } else {
            return redirect()->back()->with('warning', 'No se pudo actualizar la tabla, no se encontro el dato solicitado');
        }
    }

   // dd($ordenesEntrada[0]->productosPresententaciones[0]->movimientos[0]->contenedor->ultimaExistenciaHistorico);
   
    $filtro=1;

    $cliente=Empresa::where("EmpresasTipos",2)->get();
    return view('tabla3',compact('ordenesEntrada','filtro','cliente','paginatel' ,'fechaInici','fechaFin'));
}

 
        
        else{
            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
    $q->where('CantidadExistente', '>', 0);
})->paginate(10);
            $paginatel="si";
       
       
        }
        $fecha1=date('Y-m-d');


        

        //dd($ordenesEntrada);
        $filtro=1;
        $cliente=Empresa::where("EmpresasTipos",2)->get();
        
        
       return view('tabla3',compact('ordenesEntrada','filtro','cliente','paginatel','fecha1' ));


}

    
public function almacenamiento(Request $request){
    if ($_POST) {
    if ($_POST["cliente"] != "") {
        if ($_POST["orden"] != "") {
            $paginatel = "no";
            $empresaId   = $request->cliente;
            $codigoOrden = $request->orden; // input para buscar por orden
            $fechaInici = Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fechaFin   = Carbon::parse($request->fin)->format('Y-m-d H:i:s'); 

            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico', function($q) use ($fechaInici, $fechaFin) {
                    $q->where('CantidadExistente', '>', 0)
                      ->whereBetween('FechaInsercionDatosHistorico', [$fechaInici, $fechaFin]);
                })
                ->whereHas('productosPresententaciones.movimientoEntrada.contenedor.sscc_ep', function($q) use ($empresaId) {
                    if ($empresaId) {
                        $q->where('OidEmpresa', $empresaId);
                    }
                })
                ->when($codigoOrden, function($query) use ($codigoOrden) {
                    $query->where('Codigo', 'like', "%{$codigoOrden}%");
                })
                ->with([
                    'productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico'
                ])
                ->get();

        } else {
            $empresaId = $request->cliente; 
            $fechaInici = Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fechaFin   = Carbon::parse($request->fin)->format('Y-m-d H:i:s');

            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico', function($q) use ($fechaInici, $fechaFin) {
                    $q->where('CantidadExistente', '>', 0)
                      ->whereBetween('FechaInsercionDatosHistorico', [$fechaInici, $fechaFin]);
                })
                ->whereHas('productosPresententaciones.movimientoEntrada.contenedor.sscc_ep', function($q) use ($empresaId) {
                    $q->where('OidEmpresa', $empresaId);
                })
                ->with([
                    'productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico'
                ])
                ->get();

            $paginatel = "no";
        }

    } else {
        if ($_POST["orden"] != "") {
            $paginatel = "no";
            $codigoOrden = $request->orden;
            $fechaInici = Carbon::parse($request->inicio)->format('Y-m-d H:i:s');
            $fechaFin   = Carbon::parse($request->fin)->format('Y-m-d H:i:s'); 

            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico', function($q) use ($fechaInici, $fechaFin) {
                    $q->where('CantidadExistente', '>', 0)
                      ->whereBetween('FechaInsercionDatosHistorico', [$fechaInici, $fechaFin]);
                })
                ->when($codigoOrden, function($query) use ($codigoOrden) {
                    $query->where('Codigo', 'like', "%{$codigoOrden}%");
                })
                ->with([
                    'productosPresententaciones.movimientoEntrada.contenedor.ultimaExistenciaHistorico'
                ])
                ->get();

        } else {
            return redirect()->back()->with('warning', 'No se pudo actualizar la tabla, no se encontro el dato solicitado');
        }
    }

   // dd($ordenesEntrada[0]->productosPresententaciones[0]->movimientos[0]->contenedor->ultimaExistenciaHistorico);
    $filtro=1;
    $cliente=Empresa::where("EmpresasTipos",2)->get();
    return view('tabla3',compact('ordenesEntrada','filtro','cliente','paginatel' ,'fechaInici','fechaFin'));
}

 
        
        else{
            $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
    $q->where('CantidadExistente', '>', 0);
})->paginate(10);
            $paginatel="si";


            if($_POST){
                $fecha1=$fechaFin ;

            }else{
             $fecha1=date('Y-m-d');   
            }
        
        


        }

        //dd($ordenesEntrada);
        $filtro=1;
        $cliente=Empresa::where("EmpresasTipos",2)->get();
        

       return view('tabla3',compact('ordenesEntrada','filtro','cliente','paginatel','fecha1' ));
    }

    public function cargar(Request $request)
    {
        
        if(isset($_POST["contenedor_oid"]) && !empty($_POST["contenedor_oid"])){
            


                date_default_timezone_set("America/Mexico_City");
                $contenedor=Contenedores::where("OID",$request->contenedor_oid)->first();

                if($contenedor->datos){
                    $dat=Datoscontenedor::where("ContenedorOid",$request->contenedor_oid)->first();
                    $dat->altura=$request->altura;
                    $dat->tipo=$request->tipo_almacenado;
                    $dat->fecha=date("Y-m-d H:i:s");
                    $dat->save();

                    $ordinput=$request->orden;
                }
                else{
                    $datosC=new Datoscontenedor;
                    $datosC->ContenedorOid=$request->contenedor_oid;
                    $datosC->altura=$request->altura;
                    $datosC->tipo=$request->tipo_almacenado;
                    $datosC->fecha=date("Y-m-d H:i:s");

                    $datosC->save();
                    $ordinput=$request->orden;
                }

            }

            return redirect('/almacenamiento?page=' . $request->pagina);

    }


    public function empresa(){
        $ordenesEntrada = Orden::with('productosPresententaciones.empresa')
    ->when($request->empresa_id, function($query) use ($request) {
        $query->whereHas('productosPresententaciones.empresa', function($q) use ($request) {
            $q->where('id', $request->empresa_id);
        });
    })
    ->whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
        $q->where('CantidadExistente', '>', 0);
    })
    ->paginate(10);
    }

    public function buscarrep(Request $request)
    {

        $contenedor=Contenedores::where("SSCC",$request->sscc)->first();
        
        return view('movimientosb',compact('contenedor'));

    }

    public function movform()
    {
        $movimientos=Movimientos::Orderby("Contenedores","DESC")->paginate(100);
        $sscc = $movimientos->pluck('contenedor.SSCC')->unique()->sort();

       
        return view('movimientos',compact('movimientos','sscc'));
    }

    public function crear(){

        $user= new User;
        $user->name="Registro";
        $user->email="registro@gmail.com";
        $user->password=Hash::make('Registrov1');
        $user->save();
    }

    public function agregar(Request $request){
      
        date_default_timezone_set("America/Mexico_City");
        $contenedor=Contenedores::where("OID",$request->contenedor_oid)->first();

        if($contenedor->datos){
            dd("existe");
        }
        else{
            $datosC=new Datoscontenedor;
            $datosC->ContenedorOid=$request->contenedor_oid;
            $datosC->altura=$request->altura;
            $datosC->tipo=$request->tipo_almacenado;
            $datosC->fecha=date("Y-m-d H:i:s");

            $datosC->save();
        }

    }
}

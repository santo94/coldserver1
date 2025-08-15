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
           
            $ordenesEntrada=Orden::where("OrdenesTipos",2)->whereBetween('fecha', [$fecha1 ,$fecha2])->get();
        $ordenempresas=$ordenesEntrada;
      
        $clientes = $ordenesEntrada->pluck('proveedor.Nombre')->unique()->sort();
        $filtro=1;
        return view('tabla2',compact('ordenesEntrada','clientes','filtro','fecha1','fecha2','ordinput'));
        }else{
        // $contenedores = Contenedores::with(['lote', 'movimientos.movimientoTipo'])->take(10)->get();
        
        return view('tabla2');
    }
    }

    public function almacenamiento(Request $request){

        $filtro=1;
        
        $ordenesEntrada = Orden::whereHas('productosPresententaciones.movimientoEntrada.contenedor.ProdUbicExis', function($q) {
    $q->where('CantidadExistente', '>', 0);
})->paginate(10);
        $clientes = $ordenesEntrada->pluck('proveedor.Nombre')->unique()->sort();
        $fecha1=date('Y-m-d');

       return view('tabla3',compact('ordenesEntrada','clientes','filtro'));
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
        $user->name="Coldtainer adm";
        $user->email="coldtainer@gmail.com";
        $user->password=Hash::make('Coltainerv1');
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

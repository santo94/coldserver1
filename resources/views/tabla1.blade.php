@extends('adminlte::page')

@section('title', 'Proveedores')


@section('content_header')
    <h1>Entradas</h1>
@stop

@section('content')



@if(isset($filtro))
<div class="container">

    <h3>Resultados del {{$fecha1}} al {{$fecha2}}</h3> <a class="btn btn-info" href="/visualizar"> <i class="fa fa-reply" aria-hidden="true"></i> Regresar...</a>

<div class="form-group row">
    <label for="filtroOrden" class="col-md-2 col-form-label">Filtrar por Orden</label>
    <div class="col-md-6">
    <select class="form-control" id="filtroOrden" >
        <option value="" >Todos</option>
        @foreach($ordenesEntrada as $orden)
            <option  value="{{ $orden->Codigo }}" @if($ordinput == $orden->Codigo) selected @endif>{{ $orden->Codigo }}</option>
        @endforeach
    </select>
</div>
</div>

<div class="form-group row">
<label for="filtroCliente" class="col-md-2 col-form-label">Filtrar por Cliente: </label>
    <div class="col-md-6">
    <select class="form-control" id="filtroCliente">
        <option value="">Todos</option>
        @foreach($clientes as $cliente)
            <option value="{{$cliente}}">{{$cliente}}</option>
        @endforeach
    </select>
</div>
</div>

 <div class="table-responsive"> 
<table id="miTabla" class="table table-bordered table-striped ">
<thead class="gray">
            <tr>
                <th>Código Producto</th>
               <!-- <th>Codigo Prod pre</th>-->
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Estatus</th>
                <th>SSCC</th>
                <th>Descripcion</th>
                <th>Fecha</th>
                <th>Codigo Orden</th>
                <th>Cliente</th>
                <th>Codigo Servicio</th>
                <th>Servicios</th>
                <th>Altura</th>
                <th>Tipo de almacenado</th>
                <th></th>
               
            </tr>
        </thead>
        <tbody>
         
          @foreach($ordenesEntrada as $ordenes)
          @foreach ($ordenes->productosPresententaciones as $prodpre)
            <tr >
                <td>{{$prodpre->ProdPre->Codigo}}</td>
              <!--  <td>{{$prodpre->OID}}</td> -->
                <td>{{$prodpre->ProdPre->Nombre}}</td>
                <td>{{$prodpre->MovimientoEntrada->Cantidad}}</td>
                <td>{{$prodpre->MovimientoEntrada->contenedor->OID}}</td>
                <td>{{$prodpre->MovimientoEntrada->contenedor->SSCC}}</td>
                <td>{{$prodpre->unidmed->Nombre}}</td>
                @php $fecha=new DateTime($ordenes->Fecha); @endphp
                <td data-order="{{$fecha->format('Y-m-d H:i:s')}}"
    data-search="{{$fecha->format('Y-m-d H:i:s')}}">{{$fecha->format("Y-m-d g:i:s A")}}</td>
                <td>{{$ordenes->Codigo}}</td>
                <td>{{$ordenes->cliente->Nombre}}</td>
                <td>
                   @if($ordenes->ordenDeServicio)
                    {{$ordenes->ordenDeServicio->Codigo }}

                   @endif  
                    
                </td>
               
               
                
                <td>
                    
                    @if($ordenes->ordenDeServicio)

                    @foreach ($ordenes->ordenDeServicio->ordenesServicios as $servicios)

                    {{$servicios->Cantidad ." ". $servicios->servicio->Nombre}},<br>
                    @endforeach

                   @endif 
                </td>
                <td>
                    @if($prodpre->MovimientoEntrada->contenedor->datos)
                    {{$prodpre->MovimientoEntrada->contenedor->datos->altura}}
                    @endif

                </td>
                <td>
                    
                    @if($prodpre->MovimientoEntrada->contenedor->datos)
                    {{$prodpre->MovimientoEntrada->contenedor->datos->tipo}}
                    @endif
                </td>
                
                <td>
                <button 
                    class="btn btn-sm btn-primary btn-altura-almacenado" 
                    data-contenedor="{{$prodpre->MovimientoEntrada->Contenedor->OID}}" 
                    data-toggle="modal" 
                    data-target="#modalAlturaAlmacenado">
                    Agregar
                </button>
            </td>
            </tr>

         
            @endforeach
            @endforeach
            
            <!-- Agrega más filas como necesites -->
        </tbody>
</table>
</div>  

</div>

@else

<div>
    
    <form action="/buscarentrada" method="POST">
        @csrf

        <div class="form-group row">
            <label for="filtroOrden" class="col-md-2 col-form-label">Fecha inicio</label>
            <div class="col-md-6">
                <input name="inicio" type="datetime-local" class="form-control">
            </div>
        </div>

        <div class="form-group row">
            <label for="filtroOrden" class="col-md-2 col-form-label">Fecha fin</label>
            <div class="col-md-6">
                <input name="fin" type="datetime-local"class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Buscar...</button>
        
    </form>
</div>

@endif


<!-- Modal Altura y Tipo de Almacenado -->
<div class="modal fade" id="modalAlturaAlmacenado" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="/agregar" method="POST">
        @csrf
        <input type="hidden" name="contenedor_oid" id="contenedor_oid">
        <input type="text" name="orden" id="ordenb">
        <input   name="inicio" value="@if(isset($fecha1)){{$fecha1}} @endif">
        <input   name="fin" value="@if(isset($fecha2)){{$fecha2}} @endif">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Agregar Altura y Tipo de Almacenado</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="altura">Altura</label>
            <select name="altura" class="form-control" required>
                <option value="">Seleccione...</option>
                <option value="1.35">1.35 M</option>
                 <option value="1.70">1.70 M</option>
                  <option value="2.0">2.0 M</option>
            </select>
          </div>
          <div class="form-group">
            <label for="tipo_almacenado">Tipo de Almacenado</label>
            <select name="tipo_almacenado" id="tipo_almacenado" class="form-control" required>
              <option value="">Seleccione...</option>
              <option value="REFRIGERADO">REFRIGERADO</option>
              <option value="CONGELADO">CONGELADO</option>
              <option value="TEMPERATURA">TEMPERATURA</option>
              <!-- Puedes agregar más -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
  
@stop

    @section('css')
       <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    @stop

@section('js')


    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>


    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Librerías necesarias para Excel/PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


    <script>

        $(document).on('click', '.btn-altura-almacenado', function() {
            let contenedorOid = $(this).data('contenedor');
            $('#contenedor_oid').val(contenedorOid);

            var orden=$('#filtroOrden').val();
            $('#ordenb').val(orden);
        });
$(document).ready(function() {
    var table = $('#miTabla').DataTable({
        columnDefs: [
        {
            targets: [11], // índice de la columna que quieres formatear
            render: function(data, type, row) {
                return parseFloat(data).toFixed(2); // siempre 2 decimales
            }
        }
    ],
        responsive: true,
        autoWidth: true,
         stateSave: true, 
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copyHtml5', text: 'Copiar', exportOptions: { orthogonal: 'export' } },
            {
  extend: 'excelHtml5',
  text: 'Excel',
  title: 'Entradas',
  exportOptions: {
    orthogonal: 'export',
    format: {
      body: function (data, row, column, node) {
        // Ajusta el índice de columna (0-based). Si SSCC es la 5ª -> column === 4
        if (column === 4) {
          // Limpia HTML y agrega un zero-width space delante
          var plain = $('<div>').html(data).text();
          return '\u200B' + plain;
        }
        return $('<div>').html(data).text();
      }
    }
  }
},
            { extend: 'csvHtml5', text: 'CSV', title: 'Entradas', exportOptions: { orthogonal: 'export' } },
            { extend: 'pdfHtml5', text: 'PDF', title: 'Entradas', orientation: 'landscape', pageSize: 'A4', exportOptions: { orthogonal: 'export' } },
            { extend: 'print', text: 'Imprimir', exportOptions: { orthogonal: 'export' } }
        ]
    });

    // Filtro personalizado por Código de Orden y Cliente
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var codigo = $('#filtroOrden').val().toLowerCase();
        var cliente = $('#filtroCliente').val().toLowerCase();

        var columnaOrden = data[7].toLowerCase();
        var columnaCliente = $('<div>').html(data[8]).text().toLowerCase();

        if ((codigo === "" || columnaOrden === codigo) &&
            (cliente === "" || columnaCliente === cliente)) {
            return true;
        }
        return false;
    });

    // Aplicar filtros al cambiar cualquiera de los selects
    $('#filtroOrden, #filtroCliente').on('change', function() {
        table.draw();
    });
});
</script>



    @stop
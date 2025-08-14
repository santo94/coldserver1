@extends('adminlte::page')

@section('title', 'Proveedores')


@section('content_header')
    <h1>Salidas</h1>
@stop

@section('content')



@if(isset($filtro))
<div class="container">

   

<div class="form-group row">
    <label for="filtroOrden" class="col-md-2 col-form-label">Filtrar por Orden</label>
    <div class="col-md-6">
    <select class="form-control" id="filtroOrden">
        <option value="">Todos</option>
        @foreach($ordenesEntrada as $orden)
            <option value="{{ $orden->Codigo }}">{{ $orden->Codigo }}</option>
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
                <th>Fecha recepción</th>
                <th>Fecha Salida</th>
                <th>Codigo Orden</th>
                
                
                <th>Altura</th>
                <th>Tipo de almacenado</th>
               
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
                @php $fecha1=new DateTime($prodpre->MovimientoEntrada->contenedor->FechaRecepcion); @endphp
                <td data-order="{{$fecha1->format('Y-m-d H:i:s')}}"
    data-search="{{$fecha1->format('Y-m-d H:i:s')}}">{{$fecha1->format("Y-m-d g:i:s A")}}</td>
                @php $fecha=new DateTime($ordenes->Fecha); @endphp
                <td data-order="{{$fecha->format('Y-m-d H:i:s')}}"
    data-search="{{$fecha->format('Y-m-d H:i:s')}}">{{$fecha->format("Y-m-d g:i:s A")}}</td>
                <td>{{$ordenes->Codigo}}</td>
                
                

                <td></td>
                <td></td>
                
                
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
    
    <form action="/buscarsalida" method="POST">
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
$(document).ready(function() {
    var table = $('#miTabla').DataTable({
        responsive: true,
        autoWidth: true,
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

        var columnaOrden = data[8].toLowerCase();
        var columnaCliente = $('<div>').html(data[9]).text().toLowerCase();

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
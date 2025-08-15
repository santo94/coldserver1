@extends('adminlte::page')

@section('title', 'Proveedores')


@section('content_header')
    <h1>Almacenamiento</h1>
@stop

@section('content')




<div class="container">

    <div class="card shadow-sm">
  <div class="card-header">
    <h3 class="card-title mb-0">
      <i class="fas fa-box-open fa-fw mr-2"></i>Buscar
    </h3>
  </div>

  <div class="card-body">
    <!-- Tu contenido (tabla, formularios, etc.) -->
    <form action="/buscarrep" method="POST" >
        @csrf
        <label>SSCC</label>
        <input class="form-control" type="text" name="sscc" placeholder="Ej: 401425080900020412">
        <button class="btn btn-success" type="submit">Buscar...</button>
    </form>
  </div>


</div>

   

<div class="form-group row">
    <label for="filtroOrden" class="col-md-2 col-form-label">Filtrar por SSCC</label>
    <div class="col-md-6">
    <select class="form-control" id="filtroOrden">
        <option value="">Todos</option>
        @for($x=0;$x < $sscc->count(); $x++)
            @if(isset($sscc[$x]))
            <option value="{{ $sscc[$x] }}">{{ $sscc[$x] }}</option>
            @endif
        @endfor
    </select>
</div>
</div>

 <div class="table-responsive">
 
<table id="miTabla" class="table table-bordered table-striped ">
<thead class="gray">
            <tr>
                <th>Cliente</th>
               <!-- <th>Codigo Prod pre</th>-->
                <th>Producto</th>
                <th>SSCC</th>
                <th>Fecha</th>
                
                <th>Tipo Movimiento</th>
                <th>Cantidad</th>
                <th>Existencia</th>
                <th>Ubicación Origen</th>
                
                
                
                <th>Ubicación destino</th>
                <th>Orden entrada</th>
                
                <th>Orden Salida</th>
                <th>Usuario</th>
                
               
            </tr>
        </thead>
        <tbody>
         
          @foreach($movimientos as $movimiento)
            <tr >
                <td>{{$movimiento->contenedor->sscc_ep->empresasc->Nombre}}</td>
              
                <td>
                    @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->ProdPre)
                    {{$movimiento->ordenProductoPresentacion->ProdPre->Nombre}}
                    @endif
                </td>
                <td>{{$movimiento->contenedor->SSCC}}</td>
                 <td>{{$movimiento->FechaCreacion}}</td>
                 <td>{{$movimiento->movimientoTipo->Nombre}}</td>
                 
                <td>{{$movimiento->Cantidad}}</td>
                <td>{{$movimiento->Cantidad}}</td>
    
                <td> 
                    @if($movimiento->ubicacionOrigen)
                    {{$movimiento->ubicacionOrigen->Nombre}}
                    @endif

                </td>
                <td> 
                    @if($movimiento->ubicacionDestino)
                    {{$movimiento->ubicacionDestino->Nombre}}
                    @endif
                </td>
                
                <td>
                    @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->orden)
                    @if($movimiento->ordenProductoPresentacion->orden->OrdenesTipos == 1)
                    {{$movimiento->ordenProductoPresentacion->orden->Codigo}}
                    @endif
                    @endif
                </td>
                
                <td>
                    @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->orden)
                    @if($movimiento->ordenProductoPresentacion->orden->OrdenesTipos == 2)
                    {{$movimiento->ordenProductoPresentacion->orden->Codigo}}
                    @endif
                    @endif
                </td>
                <td>
                    @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->orden->usuar)
                    
                    {{$movimiento->ordenProductoPresentacion->orden->usuar->NombreCompleto}}
                   
                    @endif
                   
                </td>
                
            </tr>
            @endforeach
            
            
            <!-- Agrega más filas como necesites -->
        </tbody>
</table>
{{-- Links de paginación --}}

</div>  

</div>






<!-- Modal Altura y Tipo de Almacenado -->
<div class="modal fade" id="modalAlturaAlmacenado" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="/agregara" method="POST">
        @csrf
        <input type="text" name="pagina" 
       value="{{ request()->get('page', '') }}">
        <input hidden  name="contenedor_oid" id="contenedor_oid">
        <input hidden type="text" name="orden" id="ordenb">
        <input hidden type="text" name="inicio" 
       value="@if(isset($fecha1)){{ $fecha1->format('Y-m-d H:m:s') }} @endif">
        <input hidden type="text"   name="fin" value="@if(isset($fecha2)){{$fecha2}} @endif">
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
$(document).ready(function() {
    var table = $('#miTabla').DataTable({
        columnDefs: [
            {
                targets: [5], // Columna "Cantidad"
                render: function(data, type, row) {
                    var num = parseFloat(data);
                    return isNaN(num) ? data : num.toFixed(2);
                }
            }
        ],
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
                            // SSCC está en columna 2 (índice 0-based)
                            if (column === 2) {
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

    // Filtro personalizado SOLO por SSCC
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var codigo = $('#filtroOrden').val().toLowerCase();
        var columnaSSCC = $('<div>').html(data[2]).text().toLowerCase(); // Columna SSCC

        if (codigo === "" || columnaSSCC === codigo) {
            return true;
        }
        return false;
    });

    // Aplicar filtro al cambiar el select
    $('#filtroOrden').on('change', function() {
        table.draw();
    });
});
</script>



    @stop
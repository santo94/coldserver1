@extends('adminlte::page')

@section('title', 'Almacenamiento')

@section('content_header')
    <h1>Almacenamiento</h1>
@stop

@section('content')

 @if (session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning', // Cambiado a warning
            title: 'Atención',
            text: @json(session('warning')), // Escapa automáticamente el texto
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    });
</script>
@endif

@if(isset($filtro))
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Inventario en Almacén</h3>
        </div>

        <div class="card shadow-sm">
  <div class="card-header">
    <h3 class="card-title mb-0">
      <i class="fas fa-search"></i>   Buscar
    </h3>
  </div>

  <div class="card-body">
    <!-- Tu contenido (tabla, formularios, etc.) -->
    <form action="/almacenamiento" method="POST" >
        @csrf
        <label>Cliente</label>
        <select  class="form-control" id="clienteSelect" name="cliente" class="selectpicker">
            <option value="">Seleccionar una opción</option>
           @foreach($cliente as $clien)
    <option value="{{ $clien->OID }}">{{ $clien->Nombre }}</option>
@endforeach
        </select>
        <div >
            <label> Orden</label>
        <input type="text" name="orden" class="form-control" placeholder="COLDORD-2020">
            
        </div>
        <div class="row">
           <div class="form-group col-6 col-md-4">
                <label for="inicio">Fecha inicio</label>
                <input id="inicio" name="inicio" type="datetime-local" class="form-control" required>
              </div>
              <div class="form-group col-6 col-md-4">
                <label for="fin">Fecha fin</label>
                <input id="fin" name="fin" type="datetime-local" class="form-control" required>
              </div>

        </div>
        
        <button class="btn btn-success" type="submit">Buscar...</button>
    </form>
  </div>


</div>
<br>
<br>
        <div class="card-body">

            @if (isset($fechaFin)){{$fechaFin}} @endif
            <div class="table-responsive">
                <table id="miTabla" class="table table-bordered table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Cód. Producto</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Orden</th>
                            <th>F. Ingreso</th>
                            <th>SSCC</th>
                            <th>Existencia al</th>
                            <th>Días</th>
                            <th>Cliente</th>
                           
                            <th>Altura</th>
                            <th>Tipo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- El contenido de la tabla sigue siendo el mismo --}}
                        @foreach($ordenesEntrada as $contenedor)
                               @if($contenedor->movimientoEntrada)
                                <tr>
                                    <td>{{$contenedor->movimientoEntrada->ordenProductoPresentacion->ProdPre->Codigo ?? 0}}</td>
                                   <td>{{$contenedor->movimientoEntrada->ordenProductoPresentacion->ProdPre->Nombre ?? 0}}</td>
                                   <td>{{$contenedor->total_entradas}}</td>
                                   <td>{{$contenedor->movimientoEntrada->ordenProductoPresentacion->ProdPre->unidadMedida->Nombre}}</td>
                                   <td>{{$contenedor->movimientoEntrada->ordenProductoPresentacion->orden->Codigo}}</td>
                                   <td>{{ \Carbon\Carbon::parse($contenedor->movimientoEntrada->ordenProductoPresentacion->orden->Fecha)->format('Y-m-d') }}</td>
                                   
                                   <td>{{$contenedor->SSCC}}</td>
                                   <td>{{ \Carbon\Carbon::parse($fechaFin)->format('Y-m-d')}}</td>
                                   @php
                                    $fechabd = \Carbon\Carbon::parse($contenedor->movimientoEntrada->ordenProductoPresentacion->orden->Fecha);
    $fecha1  = \Carbon\Carbon::parse($fechaFin);
    $fecha2  = \Carbon\Carbon::parse($fechaInici);

    if($fechabd < $fecha2){
        $diferencia = $fecha2->diffInDays($fecha1);
    } else {
        $diferencia = $fechabd->diffInDays($fecha1); 
    }
                                    @endphp
                                   <td>{{ceil($diferencia+1)}}</td>
                                   <td>{{$contenedor->sscc_ep->empresasc->Nombre}}</td>
                                  
                                   <td>{{$contenedor->datos->altura ?? 0}}</td>
                                   <td>{{$contenedor->datos->tipo ?? ""}}</td>
                                   <td>
                                         <button class="btn btn-sm btn-primary btn-altura-almacenado" 
                                            data-contenedor="{{$contenedor->OID}}" 
                                            data-orden="{{$contenedor->movimientoEntrada->ordenProductoPresentacion->orden->Codigo}}"
                                            data-toggle="modal" 
                                            data-target="#modalAlturaAlmacenado">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                   </td>
                                </tr>
                                @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Cód. Producto</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Orden</th>
                            <th>F. Ingreso</th>
                            <th>SSCC</th>
                            <th>Existencia</th>
                            <th>Cliente</th>
                            <th>Días</th>
                            <th>Altura</th>
                            <th>Tipo</th>
                            <th></th> {{-- Columna de acciones, sin filtro --}}
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-3">
                @if($paginatel =="si")
                 {{ $ordenesEntrada->links() }}
                 @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAlturaAlmacenado" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="/agregar" method="POST">
        @csrf
        <input type="hidden" name="contenedor_oid" id="contenedor_oid">
        <input type="hidden" name="orden" id="ordenb">
        <input type="hidden" name="inicio" value="@if(isset($fecha1)){{$fecha1}}@endif">
        <input type="hidden" name="fin" value="@if(isset($fecha2)){{$fecha2}}@endif">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Agregar Altura y Tipo de Almacenado</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-12 col-md-6">
              <label for="altura">Altura</label>
              <select name="altura" id="altura" class="form-control" required>
                  <option value="">Seleccione...</option>
                  <option value="1.35">1.35 m</option>
                  <option value="1.70">1.70 m</option>
                  <option value="2.0">2.00 m</option>
              </select>
            </div>
            <div class="form-group col-12 col-md-6">
              <label for="tipo_almacenado">Tipo de Almacenado</label>
              <select name="tipo_almacenado" id="tipo_almacenado" class="form-control" required>
                <option value="">Seleccione...</option>
                <option value="REFRIGERADO">REFRIGERADO</option>
                <option value="CONGELADO">CONGELADO</option>
                <option value="TEMPERATURA">TEMPERATURA</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@else
    {{-- ... (código del else sin cambios) ... --}}
@endif

{{-- ... (código del modal sin cambios) ... --}}

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <style>
/* Cambiar color y borde del select */
.bootstrap-select .btn {
    background-color: #f8f9fa; /* gris claro */
    border: 2px solid #ced4da; /* borde gris */
    color: #333;
    font-weight: 500;
    border-radius: 8px;
}

/* Color al pasar el mouse */
.bootstrap-select .btn:hover {
    background-color: #f8f9fa;
    color: #fff;
}

/* Input de búsqueda dentro del select */
.bootstrap-select .bs-searchbox input {
    border: 1px solid #f8f9fa;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.9rem;
}

/* Opciones del dropdown */
.bootstrap-select .dropdown-menu li a {
    font-size: 0.9rem;
    padding: 8px 12px;
}

/* Resaltar opción seleccionada */
.bootstrap-select .dropdown-menu li.selected a {
    background-color: #28a745;
    color: #fff !important;
    font-weight: bold;
}
</style>
   
<!-- Select2 CSS -->

@stop

@section('js')

<!-- Select2 JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $('#clienteSelect').selectpicker({
    liveSearch: true,
    noneSelectedText: "Buscar cliente..."
});
</script>

    <script>
        $(document).ready(function() {
            // Lógica para el modal (sin cambios)
            $(document).on('click', '.btn-altura-almacenado', function() {
                let contenedorOid = $(this).data('contenedor');
                $('#contenedor_oid').val(contenedorOid);
            });

            var table = $('#miTabla').DataTable({
                paging: false,
                info: false,
                columnDefs: [
                    { targets: [2, 10], render: (data) => parseFloat(data).toFixed(2) },
                    { targets: -1, orderable: false, searchable: false }
                ],
                responsive: true,
                autoWidth: true,
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                dom: 'Brtip',
                buttons: [ 'copy', 'excel', 'pdf', 'print' ],
                
                // --- MODIFICACIÓN PRINCIPAL PARA FILTROS HÍBRIDOS ---
                initComplete: function() {
                    const textSearchColumns = ['F. Ingreso', 'SSCC', 'Cliente']; // Columnas con buscador de texto

                    this.api().columns().every(function() {
                        var column = this;
                        var footer = $(column.footer());
                        var title = $(column.header()).text();

                        // Ignorar la última columna vacía (Acciones)
                        if (footer.text() === '') {
                            return;
                        }

                        // Si el título de la columna está en nuestro array, crear un input de texto
                        if (textSearchColumns.includes(title)) {
                            var input = $('<input type="text" class="form-control form-control-sm" placeholder="Buscar..." />')
                                .appendTo(footer.empty())
                                .on('keyup change clear', function() {
                                    if (column.search() !== this.value) {
                                        column.search(this.value).draw();
                                    }
                                });
                        } 
                        // De lo contrario, crear un select
                        else {
                            var select = $('<select class="form-control form-control-sm"><option value="">Todos</option></select>')
                                .appendTo(footer.empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                });

                            column.data().unique().sort().each(function(d, j) {
                                let text = typeof d === 'string' ? d.replace(/<.*?>/g, '') : d;
                                select.append('<option value="' + text + '">' + text + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>
    
@stop
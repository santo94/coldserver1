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
        
        <button class="btn btn-success" type="submit">Buscar...</button>
    </form>
  </div>


</div>
<br>
<br>
        <div class="card-body">
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
                            <th>Existencia</th>
                            <th>Cliente</th>
                            <th>Días</th>
                            <th>Altura</th>
                            <th>Tipo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- El contenido de la tabla sigue siendo el mismo --}}
                        @foreach($ordenesEntrada as $ordenes)
                            @foreach ($ordenes->productosPresententaciones as $prodpre)
                                @if($prodpre->MovimientoEntrada->contenedor->ProdUbicExis->CantidadExistente > 0)
                                <tr>
                                    <td>{{$prodpre->ProdPre->Codigo}}</td>
                                    <td>{{$prodpre->ProdPre->Nombre}}</td>
                                    <td>{{$prodpre->MovimientoEntrada->contenedor->ProdUbicExis->CantidadExistente}}</td>
                                    <td>{{$prodpre->unidmed->Nombre}}</td>
                                    <td>{{$ordenes->Codigo}}</td>
                                    @php $fecha1 = new DateTime($prodpre->MovimientoEntrada->contenedor->FechaRecepcion); @endphp
                                    <td data-order="{{$fecha1->format('Y-m-d H:i:s')}}">{{$fecha1->format("d/m/Y")}}</td>
                                    <td>{{$prodpre->MovimientoEntrada->contenedor->SSCC}}</td>
                                    @php $fecha = date('Y-m-d'); @endphp
                                    <td data-order="{{$fecha}}">{{date("d/m/Y")}}</td>
                                    <td>{{ $prodpre->MovimientoEntrada->contenedor->sscc_ep->empresa->Nombre ?? '' }}</td>
                                    <td>
                                        @php
                                        $fechaEvaluar = new DateTime($fecha);
                                        if ($fechaEvaluar->format("Y-m") === $fecha1->format("Y-m")) {
                                            $diasTranscurridos = $fecha1->diff($fechaEvaluar)->days + 2;
                                        } else {
                                            $fecin = new DateTime($fechaEvaluar->format("Y-m-01"));
                                            $diasTranscurridos = $fecin->diff($fechaEvaluar)->days + 1;
                                        }
                                        @endphp
                                        {{$diasTranscurridos}}
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
                                        <button class="btn btn-sm btn-primary btn-altura-almacenado" data-contenedor="{{$prodpre->MovimientoEntrada->Contenedor->OID}}" data-toggle="modal" data-target="#modalAlturaAlmacenado">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
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
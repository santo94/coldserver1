@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="m-0">Entradas</h1>
        @if(isset($filtro))
            <a class="btn btn-sm btn-outline-secondary" href="/visualizar">
                <i class="fa fa-reply" aria-hidden="true"></i> Regresar
            </a>
        @endif
    </div>
@stop

@section('content')

@if(isset($filtro))
<div class="container-fluid px-0">
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h5 class="mb-0">Resultados:
                    <span class="badge badge-info ml-2">{{ $fecha1 }} → {{ $fecha2 }}</span>
                </h5>
                <div class="ml-auto d-flex gap-2">
                    <button id="limpiarFiltros" class="btn btn-light btn-sm"><i class="fas fa-eraser"></i> Limpiar filtros</button>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="filtroOrden" class="text-muted small mb-1">Filtrar por Orden</label>
                    <select class="form-control" id="filtroOrden">
                        <option value="">Todos</option>
                        @foreach($ordenesEntrada as $orden)
                            <option value="{{ $orden->Codigo }}" @if($ordinput == $orden->Codigo) selected @endif>{{ $orden->Codigo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="filtroCliente" class="text-muted small mb-1">Filtrar por Cliente</label>
                    <select class="form-control" id="filtroCliente">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente}}">{{$cliente}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-md-3">
            <div class="table-responsive">
                <table id="miTabla" class="table table-sm table-striped table-hover w-100">
                    <thead class="thead-light sticky-head">
                        <tr>
                            <th class="all">Código</th>
                            <th class="min-tablet">Nombre</th>
                            <th class="min-phone-l">Cantidad</th>
                            <th class="min-phone-l">Estatus</th>
                            <th class="min-desktop">SSCC</th>
                            <th class="min-desktop">Unidad</th>
                            <th class="all">Fecha</th>
                            <th class="min-tablet">Orden</th>
                            <th class="min-desktop">Cliente</th>
                            <th class="min-desktop">Código Servicio</th>
                            <th class="min-desktop" style="max-width:260px">Servicios</th>
                            <th class="min-phone-l">Altura</th>
                            <th class="min-phone-l">Tipo almacenado</th>
                            <th class="none">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenesEntrada as $ordenes)
                            @foreach ($ordenes->productosPresententaciones as $prodpre)
                                @php $fecha=new DateTime($ordenes->Fecha); @endphp
                                <tr>
                                    <td class="text-monospace">{{$prodpre->ProdPre->Codigo}}</td>
                                    <td>{{$prodpre->ProdPre->Nombre}}</td>
                                    <td data-order="{{ number_format((float)$prodpre->MovimientoEntrada->Cantidad, 2, '.', '') }}">
                                        {{ number_format((float)$prodpre->MovimientoEntrada->Cantidad, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Activa</span>
                                    </td>
                                    <td class="text-nowrap">
                                        {{$prodpre->MovimientoEntrada->contenedor->SSCC}}
                                    </td>
                                    <td>{{$prodpre->unidmed->Nombre}}</td>
                                    <td data-order="{{$fecha->format('Y-m-d H:i:s')}}" data-search="{{$fecha->format('Y-m-d H:i:s')}}">
                                        <span title="{{$fecha->format('Y-m-d H:i:s')}}">{{$fecha->format('Y-m-d g:i:s A')}}</span>
                                    </td>
                                    <td class="font-weight-bold">{{$ordenes->Codigo}}</td>
                                    <td>{{$ordenes->cliente->Nombre}}</td>
                                    <td>
                                        @if($ordenes->ordenDeServicio)
                                            {{$ordenes->ordenDeServicio->Codigo }}
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width:260px">
                                        @if($ordenes->ordenDeServicio)
                                            @foreach ($ordenes->ordenDeServicio->ordenesServicios as $servicios)
                                                <span class="badge badge-pill badge-light mb-1">{{$servicios->Cantidad}} {{$servicios->servicio->Nombre}}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($prodpre->MovimientoEntrada->contenedor->datos)->altura)
                                            {{ number_format((float)$prodpre->MovimientoEntrada->contenedor->datos->altura, 2) }} m
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($prodpre->MovimientoEntrada->contenedor->datos)->tipo)
                                            <span class="badge badge-primary">{{$prodpre->MovimientoEntrada->contenedor->datos->tipo}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-altura-almacenado" 
                                            data-contenedor="{{$prodpre->MovimientoEntrada->Contenedor->OID}}" 
                                            data-orden="{{$ordenes->Codigo}}"
                                            data-toggle="modal" 
                                            data-target="#modalAlturaAlmacenado">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else

<div class="container-fluid px-0">
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="/buscarentrada" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-12 col-md-6">
                        <label for="inicio">Fecha inicio</label>
                        <input id="inicio" name="inicio" type="datetime-local" class="form-control" required>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="fin">Fecha fin</label>
                        <input id="fin" name="fin" type="datetime-local" class="form-control" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Buscar</button>
                    <button type="reset" class="btn btn-light">Limpiar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Altura y Tipo de Almacenado -->
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

@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<style>
    /* Cabecera fija para tablas largas */
    .sticky-head th { position: sticky; top: 0; z-index: 2; }
    /* Mejora de legibilidad en celdas estrechas */
    td, th { vertical-align: middle !important; }
    .dataTables_wrapper .dt-buttons .btn { margin-right: .25rem; }
    .text-monospace { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    .gap-2 { gap:.5rem; }
    @media (max-width: 575.98px){
        .card-body { padding: .75rem; }
    }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).on('click', '.btn-altura-almacenado', function() {
    const contenedorOid = $(this).data('contenedor');
    const orden = $(this).data('orden') || $('#filtroOrden').val();
    $('#contenedor_oid').val(contenedorOid);
    $('#ordenb').val(orden);
});

$(document).ready(function() {
    var table = $('#miTabla').DataTable({
        order: [[6, 'asc']],
        responsive: {
            details: {
                type: 'inline',
                target: 'tr'
            }
        },
        autoWidth: false,
        stateSave: true,
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        columnDefs: [
            {
                targets: [2],
                render: function(data, type, row) {
                    var val = parseFloat(String(data).replace(/[^0-9.-]/g, ''));
                    if (isNaN(val)) return data;
                    return type === 'display' ? val.toLocaleString(undefined,{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) : val;
                },
                className: 'text-right'
            },
            { targets: [3], orderable: false },
            { targets: -1, orderable: false, searchable: false }
        ],
        dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"fB>rt<"d-flex align-items-center justify-content-between mt-2"lip>',
        buttons: [
            { extend: 'copyHtml5', className: 'btn btn-light btn-sm', text: '<i class="far fa-copy"></i> Copiar', exportOptions: { orthogonal: 'export' } },
            {
                extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: '<i class="far fa-file-excel"></i> Excel', title: 'Entradas',
                exportOptions: {
                    orthogonal: 'export', columns: ':not(:last-child)',
                    format: {
                        body: function (data, row, column, node) {
                            if(column === 4){ // SSCC
                                var plain = $('<div>').html(data).text();
                                return '\u200B' + plain;
                            }
                            return $('<div>').html(data).text();
                        }
                    }
                }
            },
            { extend: 'csvHtml5', className: 'btn btn-info btn-sm', text: '<i class="far fa-file-alt"></i> CSV', title: 'Entradas', exportOptions: { orthogonal: 'export', columns: ':not(:last-child)' } },
            {
                extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: '<i class="far fa-file-pdf"></i> PDF', title: 'Entradas', orientation: 'landscape', pageSize: 'LETTER',
                exportOptions: { orthogonal: 'export', columns: ':not(:last-child)' },
                customize: function(doc){
                    doc.defaultStyle.fontSize = 8;
                    doc.pageMargins = [10,10,10,10];
                    doc.styles.tableHeader.fontSize = 8;
                    // Ajuste de anchos aproximados
                    var widths = ['7%','15%','6%','6%','12%','8%','10%','8%','10%','8%','10%','5%','8%','7%'];
                    if(doc.content[1] && doc.content[1].table){
                        doc.content[1].table.widths = widths.slice(0, doc.content[1].table.body[0].length);
                    }
                }
            },
            { extend: 'print', className: 'btn btn-secondary btn-sm', text: '<i class="fas fa-print"></i> Imprimir', exportOptions: { orthogonal: 'export', columns: ':not(:last-child)' } }
        ]
    });

    // Filtro personalizado por Código de Orden y Cliente
    $.fn.dataTable.ext.search.push(function(settings, data) {
        if (settings.nTable.id !== 'miTabla') return true;
        var codigo = $('#filtroOrden').val().toLowerCase();
        var cliente = $('#filtroCliente').val().toLowerCase();
        var columnaOrden = (data[7] || '').toLowerCase();
        var columnaCliente = $('<div>').html(data[8] || '').text().toLowerCase();
        var matchOrden = (codigo === '' || columnaOrden === codigo);
        var matchCliente = (cliente === '' || columnaCliente === cliente);
        return matchOrden && matchCliente;
    });

    $('#filtroOrden, #filtroCliente').on('change', function() { table.draw(); });

    $('#limpiarFiltros').on('click', function(){
        $('#filtroOrden').val('');
        $('#filtroCliente').val('');
        table.search('').columns().search('');
        table.draw();
    });

    // Reiniciar formulario al cerrar modal
    $('#modalAlturaAlmacenado').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
    });
});
</script>
@stop

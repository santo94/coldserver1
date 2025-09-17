@extends('adminlte::page')

@section('title', 'Movimientos de SSCC')

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

        <a href="/movimientos" class="btn btn-info mb-2">Regresar</a>

        <div class="table-responsive">
            <table id="miTabla" class="table table-bordered table-striped">
                <thead class="gray">
                    <tr>
                        <th>Cliente</th>
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
                    @foreach($contenedor->movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->contenedor->sscc_ep->empresasc->Nombre }}</td>
                            <td>
                                @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->ProdPre)
                                    {{ $movimiento->ordenProductoPresentacion->ProdPre->Nombre }}
                                @endif
                            </td>
                            <td>{{ $movimiento->contenedor->SSCC }}</td>
                            <td data-order="{{ \Carbon\Carbon::parse($movimiento->FechaCreacion)->timestamp }}">
                                {{ \Carbon\Carbon::parse($movimiento->FechaCreacion)->format('Y-m-d H:i:s') }}
                            </td>
                            <td>{{ $movimiento->movimientoTipo->Nombre }}</td>
                            <td>{{ $movimiento->Cantidad }}</td>
                            <td>{{ $movimiento->Cantidad }}</td>
                            <td>
                                @if($movimiento->ubicacionOrigen)
                                    {{ $movimiento->ubicacionOrigen->Nombre }}
                                @endif
                            </td>
                            <td>
                                @if($movimiento->ubicacionDestino)
                                    {{ $movimiento->ubicacionDestino->Nombre }}
                                @endif
                            </td>
                            <td>
                                @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->orden && $movimiento->ordenProductoPresentacion->orden->OrdenesTipos == 1)
                                    {{ $movimiento->ordenProductoPresentacion->orden->Codigo }}
                                @endif
                            </td>
                            <td>
                                @if($movimiento->ordenProductoPresentacion && $movimiento->ordenProductoPresentacion->orden && $movimiento->ordenProductoPresentacion->orden->OrdenesTipos == 2)
                                    {{ $movimiento->ordenProductoPresentacion->orden->Codigo }}
                                @endif
                            </td>
                            <td>{{ $movimiento->usuariom->NombreCompleto }}</td>
                        </tr>
                    @endforeach

                    @if($contenedor->Ajustes)
                        @foreach($contenedor->Ajustes as $inventarioa)
                            <tr>
                                <td>{{ $inventarioa->contenedorajust->sscc_ep->empresasc->Nombre }}</td>
                                <td>{{ $inventarioa->Prod_pre->Nombre }}</td>
                                <td>{{ $inventarioa->contenedorajust->SSCC }}</td>
                                <td data-order="{{ \Carbon\Carbon::parse($inventarioa->Fecha)->timestamp }}">
                                    {{ \Carbon\Carbon::parse($inventarioa->Fecha)->format('Y-m-d H:i:s') }}
                                </td>
                                <td>Ajuste - {{ $inventarioa->Notas }}</td>
                                <td>{{ $inventarioa->CantidadSistema }}</td>
                                <td>{{ $inventarioa->CantidadAjuste }}</td>
                                <td>{{ $inventarioa->ubic->Nombre }}</td>
                                <td>{{ $inventarioa->ubic->Nombre }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ $inventarioa->usuarioajuste->NombreCompleto}}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#miTabla').DataTable({
        order: [[3, 'asc']], // Orden ascendente por Fecha
        columnDefs: [
            {
                targets: [5,6], // Cantidad y Existencia con 2 decimales
                render: function(data){ 
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
            { extend: 'copyHtml5', text: 'Copiar', exportOptions: { format: {
                body: function (data, row, column, node) {
                    if (column === 2) { // Columna SSCC
                        var plain = $('<div>').html(data).text();
                        return '\u200B' + plain;
                    }
                    return $('<div>').html(data).text();
                }
            } } },
            { extend: 'excelHtml5', text: 'Excel', title: 'Movimientos', exportOptions: { format: {
                body: function (data, row, column, node) {
                    if (column === 2) { // Columna SSCC
                        var plain = $('<div>').html(data).text();
                        return '\u200B' + plain;
                    }
                    return $('<div>').html(data).text();
                }
            } } },
            { extend: 'csvHtml5', text: 'CSV', title: 'Movimientos', exportOptions: { format: {
                body: function (data, row, column, node) {
                    if (column === 2) { // Columna SSCC
                        var plain = $('<div>').html(data).text();
                        return '\u200B' + plain;
                    }
                    return $('<div>').html(data).text();
                }
            } } },
            { extend: 'pdfHtml5', text: 'PDF', orientation: 'landscape', pageSize: 'LETTER', exportOptions: { format: {
                body: function (data, row, column, node) {
                    if (column === 2) { // Columna SSCC
                        var plain = $('<div>').html(data).text();
                        return '\u200B' + plain;
                    }
                    return $('<div>').html(data).text();
                }
            } },
            customize: function(doc){
                    doc.defaultStyle.fontSize = 8;
                    doc.pageMargins = [10,10,10,10];
                    doc.styles.tableHeader.fontSize = 8;
                    var colCount = doc.content[1].table.body[0].length;
                    doc.content[1].table.widths = [
        '11%',  // Código Producto
        '12%', // Nombre
        '10%',  // Cantidad
        '7%',  // Estatus
        '12%', // SSCC
        '6%',  // Descripcion
        '7%', // Fecha
        '8%',  // Codigo Orden
        '10%',  // Cliente
        '8%',  // Codigo Servicio
        '9%', // Servicios
        '10%' // Altura
           // Tipo almacenado
    ];
                     for (var i = 0; i < colCount; i++) {
        // '*' hace que PDFMake ajuste proporcionalmente la columna
        doc.content[1].table.widths.push('*');
    }
                }

             },
            { extend: 'print', text: 'Imprimir', exportOptions: { format: {
                body: function (data, row, column, node) {
                    if (column === 2) { // Columna SSCC
                        var plain = $('<div>').html(data).text();
                        return '\u200B' + plain;
                    }
                    return $('<div>').html(data).text();
                }
            } } }
        ]
    });
});
</script>
@stop
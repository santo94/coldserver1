@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
<div class="d-flex flex-wrap justify-content-between align-items-center">
  <div>
    <h1 class="h3 mb-0">Salidas</h1>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ url('/home') }}">Inicio</a></li>
      <li class="breadcrumb-item active">Salidas</li>
    </ol>
  </div>
  <div class="d-flex flex-wrap">
    @if(isset($filtro))
      <a class="btn btn-outline-secondary mr-2 mb-2" href="/salidas"><i class="fa fa-reply mr-1"></i> Regresar</a>
    @endif
    <button class="btn btn-primary mb-2" data-toggle="collapse" data-target="#filtrosWrap">
      <i class="fas fa-filter mr-1"></i> Filtros
    </button>
  </div>
</div>
@stop

@section('content')
<div class="container-fluid px-0">

  @if(isset($filtro))
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      <strong>Resultados:</strong>
      <span class="badge badge-dark ml-2">{{ $fecha1 }} → {{ $fecha2 }}</span>
    </div>
    <a class="btn btn-sm btn-outline-light" href="/salidas"><i class="fa fa-reply mr-1"></i> Regresar</a>
  </div>
  @endif

  {{-- Filtros (collapse BS4) --}}
  <div id="filtrosWrap" class="collapse mb-3">
    <div class="card">
      <div class="card-body">
        @if(isset($filtro))
          <div class="form-row">
            <div class="form-group col-12 col-md-4">
              <label for="filtroOrden">Filtrar por Orden</label>
              <select class="form-control" id="filtroOrden">
                <option value="">Todos</option>
                @foreach($ordenesEntrada as $orden)
                  <option value="{{ $orden->Codigo }}" @if($ordinput == $orden->Codigo) selected @endif>
                    {{ $orden->Codigo }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-12 col-md-4">
              <label for="filtroCliente">Filtrar por Cliente</label>
              <select class="form-control" id="filtroCliente">
                <option value="">Todos</option>
                @foreach($clientes as $cliente)
                  <option value="{{ $cliente }}">{{ $cliente }}</option>
                @endforeach
              </select>
            </div>
          </div>
        @else
          <form action="/buscarsalida" method="POST">
            @csrf
            <div class="form-row">
              
                <div class="form-group col-12 col-md-4">
                <label for="inicio">Cliente</label>
                <select class="selectpicker form-control" name="cliente" id="clienteSelect" required>
                  <option value="">-- Seleccionar una opción</option>
                    @foreach($clientesT as $cliente)
                      <option value="{{$cliente->OID}}">{{$cliente->Nombre}}</option>
                    @endforeach
                </select>
              </div>
              <div class="form-group col-12 col-md-4">
                <label for="inicio">Fecha inicio</label>
                <input id="inicio" name="inicio" type="datetime-local" class="form-control" required>
              </div>
              <div class="form-group col-12 col-md-4">
                <label for="fin">Fecha fin</label>
                <input id="fin" name="fin" type="datetime-local" class="form-control" required>
              </div>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-search mr-1"></i> Buscar</button>
          </form>
        @endif
      </div>
    </div>
  </div>

  @if(isset($filtro))
  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <span class="font-weight-bold">Listado de salidas</span>
      <div id="dt-acciones" class="d-flex flex-wrap"></div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table id="miTabla" class="table table-hover table-striped mb-0 w-100">
          <thead class="thead-light">
            <tr>
              <th>Código Producto</th>
              <th>Nombre</th>
              <th class="text-right">Cantidad</th>
              <th>SSCC</th>
              <th>Descripción</th>
              <th>Fecha recepción</th>
              <th>Fecha salida</th>
              <th>Código Orden</th>
              <th>Cliente</th>
              <th>Código Servicio</th>
              <th>Servicios</th>
              <th>Total días Almacenaje</th>
              <th>Altura</th>
              <th>Tipo de almacenado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($ordenesEntrada as $ordenes)
           
            @foreach ($ordenes->productosPresententaciones as $prodpre)
            
            <tr>
              <td>{{ $prodpre->ProdPre->Codigo }}</td>
              <td>{{ $prodpre->ProdPre->Nombre }}</td>

              @if($prodpre->MovimientoEntrada)
                <td class="text-right">{{ number_format($prodpre->MovimientoEntrada->Cantidad,2) }}</td>
                <td>{{ $prodpre->MovimientoEntrada->contenedor->SSCC }}</td>
                <td>{{ $prodpre->unidmed->Nombre }}</td>
                @php $fecha1 = new DateTime($prodpre->MovimientoEntrada->contenedor->FechaRecepcion); @endphp
                <td data-order="{{ $fecha1->format('Y-m-d H:i:s') }}"
                    data-search="{{ $fecha1->format('Y-m-d H:i:s') }}">
                    {{ $fecha1->format('Y-m-d g:i:s A') }}
                </td>
              @else
                <td class="text-right">{{ number_format($prodpre->Cantidad,2) }}</td>
               
                <td>{{ $prodpre->SSCC }}</td>
                <td>{{ $prodpre->unidmed->Nombre }}</td>
                <td>{{ $prodpre->contenedor->FechaRecepcion }}</td>
              @endif

              @php $fecha = new DateTime($ordenes->Fecha); @endphp
              <td data-order="{{ $fecha->format('Y-m-d H:i:s') }}"
                  data-search="{{ $fecha->format('Y-m-d H:i:s') }}">
                  {{ $fecha->format('Y-m-d g:i:s A') }}
              </td>
              <td>{{ $ordenes->Codigo }}</td>
              <td>{{ $ordenes->proveedor->Nombre }}</td>
              <td>
                @if($ordenes->ordenDeServicio)
                  {{ $ordenes->ordenDeServicio->Codigo }}
                @endif
              </td>
              <td style="min-width:180px">
                @if($ordenes->ordenDeServicio)
                  @foreach ($ordenes->ordenDeServicio->ordenesServicios as $servicios)
                    {{ $servicios->Cantidad." ".$servicios->servicio->Nombre }}@if(!$loop->last),@endif<br>
                  @endforeach
                @endif
              </td>
              <td class="text-right">
                @php 
    $sumen = 0;
    $sumsal = 0;
    $entrada=0;
    $almacenado=0;
    $diasTranscurridos = null;
   $fechr=$fecha1->format('Y-m-d');
   $fechs=$fecha->format('Y-m-d');

    if ($prodpre->MovimientoEntrada) {
        $mov = $prodpre->MovimientoEntrada->contenedor->movimientos;
    } else {
        $mov = $prodpre->contenedor->movimientos ?? collect();
    }

   foreach ($mov as $movi) {

    if($movi->ordenProductoPresentacion){
      $fechaOr=$movi->ordenProductoPresentacion->orden->Fecha;
    }else{
      $fechaOr=$movi->contenedor->ordprodpre->orden->Fecha;
    }
    if ($ordenes->Fecha >= $fechaOr) {
        switch ($movi->MovimientosTipos) {
            case 2:
            case 3:
                $almacenado++;
                break;

            case 1:
                $sumen += $movi->Cantidad;
                break;

            case 8:
                $sumsal += $movi->Cantidad;
                break;
        }
    }
}

    $existencia = $sumen - $sumsal;

    if ($existencia == 0 && $almacenado >=1) {

      if($fechr== $fechs){
        $diasTranscurridos=1;
      }else{
        $fechaEvaluar = $fecha instanceof \DateTime ? $fecha : new \DateTime($fecha);

        if ($fecha->format('Y-m') === $fecha1->format('Y-m')) {
            $fecin = $fecha1;
            $diferencia = $fecin->diff($fechaEvaluar);
            $diasTranscurridos = $diferencia->days + 2;
        } else {
            $fecin = new \DateTime($fechaEvaluar->format('Y-m-01'));
            $diferencia = $fecin->diff($fechaEvaluar);
            $diasTranscurridos = $diferencia->days + 1;
        }
      }
    }
@endphp

   
    {{ $diasTranscurridos }} 

              </td>
              <td>{{ optional($prodpre->contenedor->datos)->altura }}</td>
              <td>{{ optional($prodpre->contenedor->datos)->tipo }}</td>
              <td class="text-center">
                @php
                  $bs = $prodpre->MovimientoEntrada
                    ? $prodpre->MovimientoEntrada->Contenedor->OID
                    : $prodpre->contenedor->OID;
                @endphp
                <button
                  class="btn btn-sm btn-primary btn-altura-almacenado"
                  data-contenedor="{{ $bs }}"
                  data-toggle="modal" data-target="#modalAlturaAlmacenado">
                  Agregar
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
  @endif

</div>

{{-- Modal BS4 --}}
<div class="modal fade" id="modalAlturaAlmacenado" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="/agregars" method="POST">
        @csrf
        <input type="hidden" name="contenedor_oid" id="contenedor_oid">
        <input type="hidden" name="orden" id="ordenb">
       

        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Agregar Altura y Tipo de Almacenado</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="altura">Altura</label>
            <select name="altura" id="altura" class="form-control" required>
              <option value="">Seleccione…</option>
              <option value="1.35">1.35 m</option>
              <option value="1.70">1.70 m</option>
              <option value="2.0">2.0 m</option>
            </select>
          </div>
          <div class="form-group">
            <label for="tipo_almacenado">Tipo de Almacenado</label>
            <select name="tipo_almacenado" id="tipo_almacenado" class="form-control" required>
              <option value="">Seleccione…</option>
              <option value="REFRIGERADO">REFRIGERADO</option>
              <option value="CONGELADO">CONGELADO</option>
              <option value="TEMPERATURA">TEMPERATURA</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>

  .bootstrap-select .dropdown-toggle {
    background-color: #fff !important; /* blanco */
    color: #000 !important;           /* texto negro */
}
  
  .table thead th{white-space:nowrap}
  .dataTables_wrapper .dt-buttons .btn{margin:.125rem}
  @media (max-width:576px){
    .card-header{position:sticky;top:56px;z-index:5}
  }


</style>
{{-- DataTables Bootstrap 4 --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

@stop

@section('js')
{{-- jQuery y Bootstrap 4 (AdminLTE v3 ya los incluye; si no, descomenta) --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> --}}
{{-- <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}

{{-- DataTables + plugins (BS4) --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

{{-- Excel/PDF --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>

  // Click en Agregar → preparar modal (BS4)
  $(document).on('click', '.btn-altura-almacenado', function(){
    var contenedorOid = $(this).data('contenedor');
    $('#contenedor_oid').val(contenedorOid);
    $('#ordenb').val($('#filtroOrden').val() || '');
    // mostrar modal explícitamente por si no accionó el data-target
    $('#modalAlturaAlmacenado').modal('show');
  });

  $('#clienteSelect').selectpicker({
    liveSearch: true,
    noneSelectedText: "Buscar cliente..."
});

  $(function(){
    var table = $('#miTabla').DataTable({
      responsive: { details: { type: 'inline' } },
      autoWidth: false,
      language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json" },
      dom:'<"d-flex flex-wrap align-items-center justify-content-between mb-2"fB>rt<"d-flex align-items-center justify-content-between mt-2"lip>',
      buttons: [
        { extend:'copyHtml5', text:'Copiar', className:'btn btn-outline-secondary btn-sm' },
        {
          extend:'excelHtml5', text:'Excel', title:'Salidas',
          className:'btn btn-outline-success btn-sm',
          exportOptions:{
            columns:':not(:last-child)',
            orthogonal:'export',
            format:{ body:function(data, row, col){
              if(col===3){ // SSCC: evitar que Excel quite ceros
                var plain = $('<div>').html(data).text();
                return '\u200B'+plain;
              }
              return $('<div>').html(data).text();
            }}
          }
        },
        { extend:'csvHtml5', text:'CSV', title:'Salidas', className:'btn btn-outline-primary btn-sm',
          exportOptions:{ columns:':not(:last-child)', orthogonal:'export' } },
        {
          extend:'pdfHtml5', text:'PDF', title:'Salidas', className:'btn btn-outline-danger btn-sm',
          orientation:'landscape', pageSize:'LETTER',
          exportOptions:{ columns:':not(:last-child)', orthogonal:'export' },
          customize:function(doc){
            doc.defaultStyle.fontSize = 8;
            doc.pageMargins = [10,10,10,10];
            doc.styles.tableHeader.fontSize = 8;
            doc.content[1].table.widths = ['8%','14%','6%','12%','8%','9%','9%','8%','8%','8%','10%','7%','6%','9%','7%'];
          }
        },
        { extend:'print', text:'Imprimir', className:'btn btn-outline-dark btn-sm',
          exportOptions:{ columns:':not(:last-child)', orthogonal:'export' } },
      ],
      columnDefs: [
  {
    targets:[11],
    className:'text-right',
    render: function (data, type, row) {
      let valor = parseFloat(data);
      if (isNaN(valor)) return data;
      return valor.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
  
      });
    }
  }
],
      initComplete:function(){
        var cont = document.querySelector('#dt-acciones');
        var btns = document.querySelector('.dt-buttons');
        if(cont && btns) cont.appendChild(btns);
      }
    });

    // Filtros por Orden y Cliente
    $.fn.dataTable.ext.search.push(function(settings, data){
      var codigo = ($('#filtroOrden').val()||'').toString().toLowerCase();
      var clienteSel = ($('#filtroCliente').val()||'').toString().toLowerCase();
      var colOrden = (data[7]||'').toString().toLowerCase();
      var colCliente = $('<div>').html(data[8]||'').text().toLowerCase();
      var okOrden = !codigo || colOrden === codigo;
      var okCliente = !clienteSel || colCliente === clienteSel;
      return okOrden && okCliente;
    });
    $('#filtroOrden, #filtroCliente').on('change', function(){ table.draw(); });
  });
</script>
@stop

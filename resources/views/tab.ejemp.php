 <td>{{$prodpre->ProdPre->Nombre}}</td>
                                    <td>{{$prodpre->MovimientoEntrada->contenedor->ultimaExistenciaHistorico->CantidadExistente ?? 0}}</td>
                                    <td>{{$prodpre->unidmed->Nombre}}</td>
                                    <td>{{$ordenes->Codigo}}</td>
                                    @php $fecha1 = new DateTime($prodpre->MovimientoEntrada->contenedor->FechaRecepcion ?? 0); @endphp
                                    <td data-order="{{$fecha1->format('Y-m-d H:i:s')}}">{{$fecha1->format("d/m/Y")}}</td>
                                    <td>{{$prodpre->MovimientoEntrada->contenedor->SSCC}}</td>
                                    @php 
                                    
                                        $fecha = date('Y-m-d');
                                        if(isset($fechaFin)){
                                        $fechafind=\Carbon\Carbon::parse($fechaFin);
                                    }
                                    
                                     @endphp
                                    <td >{{$prodpre->MovimientoEntrada->contenedor->ultimaExistenciaHistorico->FechaInsercionDatosHistorico ?? 0}}</td>
                                    <td></td>
                                    <td>{{ $prodpre->MovimientoEntrada->contenedor->sscc_ep->empresasc->Nombre }}</td>
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
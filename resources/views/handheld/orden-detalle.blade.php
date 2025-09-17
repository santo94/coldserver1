<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle Orden {{ $orden->codigo ?? $orden->OID }} - Handheld</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            font-size: 14px;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .back-btn {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .container {
            padding: 15px;
        }
        
        .info-card {
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-header {
            background: #3498db;
            color: white;
            padding: 10px 15px;
            margin: -15px -15px 15px -15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
            font-size: 14px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #34495e;
            flex: 1;
        }
        
        .info-value {
            flex: 1;
            text-align: right;
            color: #2c3e50;
        }
        
        .status-badge {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        
        .item-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .item {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 4px;
            border-left: 3px solid #3498db;
        }
        
        .item-clickable {
            position: relative;
            transition: all 0.2s ease;
        }
        
        .item-clickable:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(52, 152, 219, 0.1);
        }
        
        .item-name {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }        .item-details {
            font-size: 13px;
            color: #34495e;
            line-height: 1.4;
        }
        
        .item-details strong {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .collapsible-header {
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .collapsible-header:active {
            background-color: #2980b9;
        }
        
        .collapse-icon {
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        
        .collapse-icon.expanded {
            transform: rotate(180deg);
        }
        
        .collapsible-content {
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .collapsible-content.collapsed {
            max-height: 0;
        }
        
        .collapsible-content.expanded {
            max-height: 500px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            flex: 1;
            background: #3498db;
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-warning {
            background: #f39c12;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="header">
        <button class="back-btn" onclick="history.back()">←</button>
        <h1>📋 Orden {{ $orden->codigo ?? $orden->OID }}</h1>
    </div>

    <div class="container">
        <!-- Información General -->
        <div class="info-card">
            <div class="info-header collapsible-header" onclick="toggleCollapse('info-general')">
                <span>📊 Información General</span>
                <span class="collapse-icon" id="icon-info-general">▼</span>
            </div>
            <div class="collapsible-content collapsed" id="content-info-general">
            <div class="info-row">
                <div class="info-label">Número:</div>
                <div class="info-value">{{ $orden->OID }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Código:</div>
                <div class="info-value">{{ $orden->Codigo ?? $orden->OID }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tipo:</div>
                <div class="info-value">{{ $orden->ordenTipo->descripcion ?? 'Tipo ' . $orden->OrdenesTipos }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($orden->Fecha)->format('d/m/Y H:i') }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value">
                    <span class="status-badge">✅ Activa</span>
                </div>
            </div>
            
            @if($orden->proveedor)
            <div class="info-row">
                <div class="info-label">Proveedor:</div>
                <div class="info-value">{{ $orden->proveedor }}</div>
            </div>
            @endif
            
            @if($orden->cliente)
            <div class="info-row">
                <div class="info-label">Cliente:</div>
                <div class="info-value">{{ $orden->cliente->Nombre ?? $orden->cliente->RazonSocial }}</div>
            </div>
            @endif
            </div>
        </div>

        <!-- Observaciones -->
        @if($orden->detalle && $orden->detalle->observaciones)
        <div class="info-card">
            <div class="info-header">📝 Observaciones</div>
            <p>{{ $orden->detalle->observaciones }}</p>
        </div>
        @endif

        <!-- Productos -->
        @if($orden->productosPresententaciones && $orden->productosPresententaciones->count() > 0)
        @php
            $totalProductos = $orden->productosPresententaciones->count();
            $productosProcessados = $orden->productosPresententaciones->filter(function($producto) {
                return $producto->movimientos && $producto->movimientos->count() > 0;
            })->count();
            $productosPendientes = $totalProductos - $productosProcessados;
        @endphp
        <div class="info-card">
            <div class="info-header">
                📦 Productos ({{ $totalProductos }}) - 
                <span style="color: #27ae60;">✅ {{ $productosProcessados }}</span> / 
                <span style="color: #e74c3c;">⏳ {{ $productosPendientes }}</span> pendientes
            </div>
            <div class="item-list">
                @foreach($orden->productosPresententaciones as $producto)
                @php
                    // Verificar si el producto ya fue procesado (tiene movimientos)
                    $estaProcesado = $producto->movimientos && $producto->movimientos->count() > 0;
                    $cantidadProcesada = $estaProcesado ? $producto->movimientos->sum('Cantidad') : 0;
                @endphp
                <div class="item item-clickable" id="producto-{{ $producto->OID }}"
                     @if(!$estaProcesado)
                         onclick="abrirModal({{ $producto->OID }}, '{{ addslashes($producto->ProdPre->Nombre ?? 'Producto '.$producto->OID) }}', {{ $producto->Cantidad ? number_format($producto->Cantidad, 2, '.', '') : '0' }}, '{{ $producto->unidadMedida ? ($producto->unidadMedida->Abreviatura ?? $producto->unidadMedida->Nombre) : 'Sin unidad' }}')"
                         style="cursor: pointer; transition: all 0.2s ease; border-left: 4px solid #3498db;"
                     @else
                         style="cursor: default; transition: all 0.2s ease; border-left: 4px solid #27ae60; background: #e8f5e8;"
                     @endif
                     >
                    <div class="item-name">
                        @if($estaProcesado)
                            <span style="background: #27ae60; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-right: 8px;">✅ PROCESADO</span>
                        @else
                            <span style="background: #3498db; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-right: 8px;">{{ $loop->iteration }}</span>
                        @endif
                        @if($producto->ProdPre && $producto->ProdPre->Nombre)
                            {{ $producto->ProdPre->Nombre }}
                        @else
                            Producto {{ $producto->OID }}
                        @endif
                        @if(!$estaProcesado)
                            <span style="float: right; color: #27ae60; font-size: 18px;">👆</span>
                        @else
                            <span style="float: right; color: #27ae60; font-size: 18px;">✅</span>
                        @endif
                    </div>
                    <div class="item-details">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 8px;">
                            <div>
                                <strong>SKU:</strong> {{ $producto->OID }}
                            </div>
                            <div>
                                @if($producto->unidadMedida)
                                    <strong>Unidad:</strong> {{ $producto->unidadMedida->Abreviatura ?? $producto->unidadMedida->Nombre }}
                                @endif
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #f8f9fa; padding: 8px; border-radius: 4px;">
                            <div>
                                <strong>Cant. Solicitada:</strong><br>
                                <span style="color: #2c3e50; font-size: 15px; font-weight: 600;">
                                    {{ $producto->Cantidad ? number_format($producto->Cantidad, 2, '.', '') : '0.00' }}
                                </span>
                            </div>
                            <div>
                                <strong>Cant. Rec.:</strong><br>
                                @if($estaProcesado)
                                    <span id="cant-rec-{{ $producto->OID }}" style="color: #27ae60; font-size: 15px; font-weight: bold;">
                                        {{ number_format($cantidadProcesada, 2, '.', '') }}
                                    </span>
                                @else
                                    <span id="cant-rec-{{ $producto->OID }}" style="color: #e74c3c; font-size: 15px; font-weight: bold;">
                                        0.00
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Botones de Acción -->
        <div class="btn-group">
            <button class="btn btn-success" onclick="cerrarOrden({{ $orden->OID }})">
                🔒 Cerrar orden
            </button>
        </div>
    </div>

    <!-- Modal para Registro de Recepción -->
    <div id="modalRecepcion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 400px;">
            <div style="text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ecf0f1;">
                <h3 style="margin: 0; color: #2c3e50;">📝 Registrar Recepción</h3>
                <p id="modalProductoNombre" style="margin: 5px 0; color: #7f8c8d; font-size: 14px;"></p>
            </div>
            
            <form id="formRecepcion">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #34495e;">Cantidad Recolectada:</label>
                    <input type="number" id="cantidadFisica" step="0.01" lang="en-US"
                           style="width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 16px;" 
                           placeholder="0.00" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #34495e;">Unidad de Medida:</label>
                    <input type="text" id="unidadMedida" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ecf0f1; border-radius: 4px; font-size: 16px; background-color: #f8f9fa; color: #6c757d;" 
                           placeholder="No especificada">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #34495e;">Descripción/Observaciones:</label>
                    <textarea id="descripcion" 
                              style="width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 14px; resize: vertical;" 
                              rows="3" placeholder="Descripción opcional del producto recibido..."></textarea>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #34495e;">Lote (Fecha):</label>
                    <input type="text" id="lote" 
                           style="width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 16px;" 
                           placeholder="ddMMyy" maxlength="6">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="cerrarModal()" 
                            style="flex: 1; background: #95a5a6; color: white; border: none; padding: 12px; border-radius: 4px; font-size: 14px;">
                        ❌ Cancelar
                    </button>
                    <button type="submit" 
                            style="flex: 1; background: #27ae60; color: white; border: none; padding: 12px; border-radius: 4px; font-size: 14px;">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let productoActual = null;
        
        // Función para manejar secciones plegables
        function toggleCollapse(sectionId) {
            const content = document.getElementById('content-' + sectionId);
            const icon = document.getElementById('icon-' + sectionId);
            
            if (content.classList.contains('collapsed')) {
                content.classList.remove('collapsed');
                content.classList.add('expanded');
                icon.textContent = '▲';
                icon.classList.add('expanded');
            } else {
                content.classList.remove('expanded');
                content.classList.add('collapsed');
                icon.textContent = '▼';
                icon.classList.remove('expanded');
            }
        }
        
        // Generar lote automático (formato yyMMdd)
        function generarLoteHoy() {
            const hoy = new Date();
            const dia = String(hoy.getDate()).padStart(2, '0');
            const mes = String(hoy.getMonth() + 1).padStart(2, '0');
            const año = String(hoy.getFullYear()).slice(-2);
            return año + mes + dia;
        }
        
        // Abrir modal
        function abrirModal(productoId, nombreProducto, cantidadSolicitada, unidadMedida) {
            productoActual = {
                id: productoId,
                nombre: nombreProducto,
                cantidadSolicitada: cantidadSolicitada,
                unidadMedida: unidadMedida
            };
            
            document.getElementById('modalProductoNombre').textContent = nombreProducto;
            // Asegurar formato con punto decimal
            const cantidadFormateada = parseFloat(cantidadSolicitada || 0).toFixed(2);
            document.getElementById('cantidadFisica').value = cantidadFormateada;
            document.getElementById('unidadMedida').value = unidadMedida || 'No especificada';
            document.getElementById('descripcion').value = '';
            document.getElementById('lote').value = generarLoteHoy();
            document.getElementById('modalRecepcion').style.display = 'block';
            
            // Focus en el campo cantidad y seleccionar todo el texto para fácil edición
            setTimeout(() => {
                const campoQuantidad = document.getElementById('cantidadFisica');
                campoQuantidad.focus();
                campoQuantidad.select(); // Selecciona todo el texto para reemplazo rápido
                
                // Forzar formato con punto decimal
                campoQuantidad.addEventListener('input', function() {
                    let valor = this.value.replace(',', '.'); // Reemplazar coma por punto
                    this.value = valor;
                });
            }, 100);
        }
        
        // Cerrar modal
        function cerrarModal() {
            document.getElementById('modalRecepcion').style.display = 'none';
            productoActual = null;
        }
        
        // Manejar envío del formulario
        document.getElementById('formRecepcion').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cantidadFisica = parseFloat(document.getElementById('cantidadFisica').value);
            const descripcion = document.getElementById('descripcion').value;
            const lote = document.getElementById('lote').value;
            
            if (!cantidadFisica || cantidadFisica < 0) {
                alert('Por favor ingrese una cantidad válida');
                return;
            }
            
            if (!lote || lote.trim() === '') {
                alert('Por favor ingrese un lote');
                return;
            }

            if (!descripcion || descripcion.trim() === '') {
                alert('Por favor ingrese una descripción');
                return;
            }
            
            // Deshabilitar botón para evitar múltiples envíos
            const btnGuardar = document.querySelector('button[type="submit"]');
            const textoOriginal = btnGuardar.textContent;
            btnGuardar.disabled = true;
            btnGuardar.textContent = '⏳ Guardando...';
            
            // Enviar datos al servidor via AJAX
            fetch('/handheld/guardar-recoleccion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    producto_id: productoActual.id,
                    cantidad: cantidadFisica,
                    descripcion: descripcion,
                    lote: lote
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Actualizar la UI - Cantidad Recolectada
                    actualizarCantidadRecolectada(productoActual.id, cantidadFisica);
                    
                    // Pintar la línea de verde para indicar que está completada
                    pintarLineaCompletada(productoActual.id);
                    
                    // Mostrar mensaje de éxito
                    alert('✅ Producto guardado correctamente\n' +
                          'SSCC: ' + data.data.sscc + '\n' +
                          'Batch: ' + data.data.batch);
                    
                    cerrarModal();
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al guardar: ' + error.message);
            })
            .finally(() => {
                // Rehabilitar botón
                btnGuardar.disabled = false;
                btnGuardar.textContent = textoOriginal;
            });
        });
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('modalRecepcion').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
        
        // Función para actualizar la cantidad recolectada en la UI
        function actualizarCantidadRecolectada(productoId, cantidadRecolectada) {
            const spanCantidad = document.getElementById(`cant-rec-${productoId}`);
            if (spanCantidad) {
                spanCantidad.textContent = parseFloat(cantidadRecolectada).toFixed(2);
                spanCantidad.style.color = '#27ae60'; // Cambiar a verde
            }
        }
        
        // Función para pintar la línea de producto como completada
        function pintarLineaCompletada(productoId) {
            const lineaProducto = document.getElementById(`producto-${productoId}`);
            if (lineaProducto) {
                // Cambiar el fondo y borde para indicar completado
                lineaProducto.style.backgroundColor = '#d5f4e6';
                lineaProducto.style.borderLeft = '4px solid #27ae60';
                
                // Agregar un checkmark visual
                const itemName = lineaProducto.querySelector('.item-name');
                if (itemName && !itemName.querySelector('.check-completed')) {
                    const checkMark = document.createElement('span');
                    checkMark.className = 'check-completed';
                    checkMark.style.cssText = 'float: right; color: #27ae60; font-size: 18px; margin-left: 10px;';
                    checkMark.textContent = '✅';
                    
                    // Reemplazar el ícono de dedo por el checkmark
                    const fingerIcon = itemName.querySelector('span[style*="float: right"]');
                    if (fingerIcon) {
                        fingerIcon.remove();
                    }
                    itemName.appendChild(checkMark);
                }
            }
        }
        
        // Función para actualizar datos en tiempo real
        function actualizarDatos() {
            // Aquí puedes agregar lógica para actualizar via AJAX
            console.log('Actualizando datos...');
        }
        
        // Función para cerrar orden
        function cerrarOrden(ordenId) {
            if (confirm('¿Estás seguro de que deseas cerrar esta orden?\n\nEsta acción la quitará de la lista de órdenes pendientes.')) {
                // Mostrar indicador de carga
                const btnCerrar = event.target;
                const textoOriginal = btnCerrar.textContent;
                btnCerrar.disabled = true;
                btnCerrar.textContent = '⏳ Cerrando...';
                
                fetch('/handheld/cerrar-orden', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        orden_id: ordenId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('✅ Orden cerrada correctamente');
                        // Redirigir a la lista de órdenes
                        window.location.href = '/handheld/ordenes-activas';
                    } else {
                        throw new Error(data.message || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Error al cerrar la orden: ' + error.message);
                    // Restaurar botón
                    btnCerrar.disabled = false;
                    btnCerrar.textContent = textoOriginal;
                });
            }
        }
        
        // Auto-refresh cada 2 minutos
        setTimeout(function() {
            window.location.reload();
        }, 120000);
    </script>
</body>
</html>

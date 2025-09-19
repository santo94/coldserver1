<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Etiquetas - Handheld</title>
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
            background: #27ae60;
            color: white;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .stats {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .container {
            padding: 0 15px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .orden-card {
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #27ae60;
        }
        
        .orden-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .orden-numero {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .orden-tipo {
            background: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        
        .orden-info {
            font-size: 12px;
            color: #7f8c8d;
            line-height: 1.4;
        }
        
        .orden-fecha {
            color: #27ae60;
            font-weight: 500;
        }
        
        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            text-align: center;
            min-height: 48px;
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .btn-print {
            background: #e74c3c;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .btn-print:hover {
            background: #c0392b;
        }
        
        .error {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
        
        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .nav-buttons .btn {
            flex: 1;
            margin-top: 0;
        }
        
        .progreso-bar {
            background: #ecf0f1;
            border-radius: 10px;
            height: 8px;
            margin: 8px 0;
            overflow: hidden;
        }
        
        .progreso-fill {
            height: 100%;
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .progreso-fill.warning {
            background: linear-gradient(90deg, #f39c12, #e67e22);
        }
        
        .progreso-fill.danger {
            background: linear-gradient(90deg, #e74c3c, #c0392b);
        }
        
        .estadisticas {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .tiene-etiquetas {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            margin-left: 5px;
        }
        
        .sin-etiquetas {
            display: inline-block;
            background: #95a5a6;
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Imprimir Etiquetas</h1>
        <div class="stats">
            {{ isset($ordenesConEstadisticas) ? count($ordenesConEstadisticas) : (isset($ordenes) ? count($ordenes) : 0) }} órdenes disponibles
        </div>
    </div>
    
    <!-- Navegación -->
    <div class="container" style="padding-top: 15px;">
        <div class="nav-buttons">
            <a href="{{ route('ordenes.activas') }}" class="btn" style="background: #3498db;">
                Órdenes Activas
            </a>
            <a href="{{ route('ordenes.imprimir') }}" class="btn" style="background: #27ae60;">
                Imprimir Etiquetas
            </a>
        </div>
    </div>

    <div class="container">
        @if(isset($error))
            <div class="error">
                {{ $error }}
            </div>
        @elseif((isset($ordenesConEstadisticas) && $ordenesConEstadisticas->isEmpty()) || (isset($ordenes) && $ordenes->isEmpty()))
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>No hay órdenes disponibles</h3>
                <p>No se encontraron órdenes para imprimir etiquetas.</p>
            </div>
        @else
            @foreach($ordenesConEstadisticas ?? $ordenes ?? [] as $orden)
                <div class="orden-card">
                    <div class="orden-header">
                        <div class="orden-numero">{{ $orden->Codigo ?? 'N/A' }}</div>
                        <div style="display: flex; align-items: center;">
                            @if($orden->Estatus == 0)
                                <div class="orden-tipo" style="background: #e74c3c;">CERRADA</div>
                            @elseif($orden->Estatus == 1)
                                <div class="orden-tipo" style="background: #3498db;">ACTIVA</div>
                            @elseif($orden->Estatus == 2)
                                <div class="orden-tipo" style="background: #f39c12;">PARCIAL</div>
                            @else
                                <div class="orden-tipo" style="background: #95a5a6;">ESTADO {{ $orden->Estatus }}</div>
                            @endif
                            
                            @if(isset($orden->tiene_etiquetas))
                                @if($orden->tiene_etiquetas)
                                    <span class="tiene-etiquetas">📋 Con etiquetas</span>
                                @else
                                    <span class="sin-etiquetas">❌ Sin etiquetas</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($orden->productos_capturados))
                        <!-- Barra de progreso de productos -->
                        <div style="margin: 10px 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #2c3e50; margin-bottom: 3px;">
                                📦 Productos: {{ $orden->texto_progreso }} ({{ $orden->porcentaje_productos }}%)
                            </div>
                            <div class="progreso-bar">
                                <div class="progreso-fill 
                                    @if($orden->porcentaje_productos >= 80) 
                                    @elseif($orden->porcentaje_productos >= 50) warning
                                    @else danger 
                                    @endif" 
                                    style="width: {{ $orden->porcentaje_productos }}%">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Barra de progreso de cantidad -->
                        <div style="margin: 10px 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #2c3e50; margin-bottom: 3px;">
                                Cantidad: {{ $orden->texto_cantidad }} ({{ $orden->porcentaje_cantidad }}%)
                            </div>
                            <div class="progreso-bar">
                                <div class="progreso-fill 
                                    @if($orden->porcentaje_cantidad >= 80) 
                                    @elseif($orden->porcentaje_cantidad >= 50) warning
                                    @else danger 
                                    @endif" 
                                    style="width: {{ $orden->porcentaje_cantidad }}%">
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="orden-info">
                        <div><strong>Cliente:</strong> {{ $orden->cliente->Nombre ?? $orden->cliente->RazonSocial ?? 'No disponible' }}</div>
                        <div><strong>Fecha:</strong> <span class="orden-fecha">{{ $orden->Fecha ? \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y H:i') : 'N/A' }}</span></div>
                        @if($orden->detalle && $orden->detalle->Observaciones)
                            <div><strong>Notas:</strong> {{ Str::limit($orden->detalle->Observaciones, 60) }}</div>
                        @endif
                    </div>
                    <!-- Conditional Action Buttons -->
                    @if(($orden->productos_capturados ?? 0) > 0)
                        <a href="{{ route('generar.etiquetas', $orden->OID) }}" 
                           class="btn btn-print" 
                           target="_blank">
                            🖨️ Generar Etiquetas ({{ $orden->productos_capturados }})
                        </a>
                    @else
                        <button class="btn btn-disabled" disabled>
                            ⚠️ Sin productos capturados
                        </button>
                    @endif
                </div>
            @endforeach
        @endif
        
        <div style="height: 80px;"></div> <!-- Espacio para el botón flotante -->
    </div>

    <button class="refresh-btn" onclick="location.reload();">
        🔄
    </button>

    <style>
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Progress Tracking Styles */
        .progress-container {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .progress-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .captured-products {
            color: #2c3e50;
            font-size: 18px;
            font-weight: bold;
        }
        
        .divider {
            margin: 0 8px;
            color: #7f8c8d;
        }
        
        .total-products {
            color: #34495e;
            font-size: 16px;
        }
        
        .products-label {
            margin-left: 8px;
            color: #7f8c8d;
        }
        
        .percentage-badge {
            margin-left: 10px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .percentage-high {
            background-color: #27ae60;
        }
        
        .percentage-medium {
            background-color: #f39c12;
        }
        
        .percentage-low {
            background-color: #e74c3c;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }
        
        .progress-high {
            background-color: #27ae60;
        }
        
        .progress-medium {
            background-color: #f39c12;
        }
        
        .progress-low {
            background-color: #e74c3c;
        }
        
        .btn-disabled {
            background-color: #95a5a6 !important;
            color: #ecf0f1 !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        
        .btn-disabled:hover {
            background-color: #95a5a6 !important;
            transform: none !important;
        }
    </style>
</body>
</html>

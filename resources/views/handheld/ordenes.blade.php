<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes Activas - Handheld</title>
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
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .stats {
            background: #34495e;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
        
        .container {
            padding: 10px;
        }
        
        .orden-card {
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
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
            background: #3498db;
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
        
        .error {
            background: #e74c3c;
            color: white;
            padding: 15px;
            margin: 10px;
            border-radius: 4px;
            text-align: center;
        }
        
        .no-orders {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
        
        .pagination {
            text-align: center;
            padding: 20px;
        }
        
        .pagination a {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            margin: 0 5px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .pagination .active {
            background: #2c3e50;
        }
        
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
    </style>
</head>
<body>
    <div class="header">

        <h1>🔄 Órdenes Activas</h1> 
        <div class="stats">
            Total: {{ $ordenes->total() ?? 0 }} órdenes activas
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">
            Cerrar sesión
        </button>
    </form>

    <!-- Navegación -->
    <div class="container" style="padding-top: 15px;">
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <a href="{{ route('ordenes.activas') }}" class="btn" style="background: #3498db; flex: 1;">
                Órdenes Activas
            </a>
            <a href="{{ route('ordenes.imprimir') }}" class="btn" style="background: #27ae60; flex: 1;">
                Imprimir Etiquetas
            </a>
        </div>
    </div>

    <div class="container">
        @if(isset($error))
            <div class="error">
                ⚠️ {{ $error }}
            </div>
        @endif

        @if($ordenes->count() > 0)
            @foreach($ordenes as $orden)
                <div class="orden-card">
                    <div class="orden-header">
                        <div class="orden-numero">ID: # {{ $orden->OID }}</div>
                        <div class="orden-numero">Código: # {{ $orden->Codigo }}</div>
                        
                    </div>
                    
                    <div class="orden-info">
                        <div class="orden-fecha">📅 {{ \Carbon\Carbon::parse($orden->Fecha)->format('d/m/Y H:i') }}</div>
                        @if($orden->proveedor)
                            <div>🏢 Proveedor: {{ $orden->proveedor }}</div>
                        @endif
                        @if($orden->detalle && $orden->detalle->observaciones)
                            <div>📝 {{ Str::limit($orden->detalle->observaciones, 50) }}</div>
                        @endif
                    </div>
                    
                    <a href="{{ route('orden.detalle', $orden->OID) }}" class="btn btn-detalle">
                        👁️ Ver Detalle
                    </a>
                </div>
            @endforeach

            <div class="pagination">
                {{ $ordenes->links('pagination::simple-bootstrap-4') }}
            </div>
        @else
            <div class="no-orders">
                <h3>📭 No hay órdenes activas</h3>
                <p>No se encontraron órdenes con estatus activo</p>
            </div>
        @endif
    </div>

    <button class="refresh-btn" onclick="window.location.reload()">
        🔄
    </button>

    <script>
        // Auto-refresh cada 5 minutos
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        
        // Función para resetear solo los botones de detalle de órdenes
        function resetearBotonesDetalle() {
            document.querySelectorAll('.btn-detalle').forEach(function(btn) {
                btn.innerHTML = '👁️ Ver Detalle';
            });
        }
        
        // Resetear botones cuando la página se muestre (incluso al regresar)
        window.addEventListener('pageshow', function(event) {
            resetearBotonesDetalle();
        });
        
        // Mostrar loading solo en los botones de detalle
        document.querySelectorAll('.btn-detalle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.innerHTML = '⏳ Cargando...';
            });
        });
        
        // Resetear botones al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            resetearBotonesDetalle();
        });
    </script>
</body>
</html>

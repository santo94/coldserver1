<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Etiqueta Pallet</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 14pt;
            margin: 0;
            padding: 2mm;
            line-height: 1.1;
        }
        .sku-producto-bloque {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 1mm;
            line-height: 1.1;
        }
        .sku-texto {
            font-size: 12pt;
            font-weight: normal;
            color: #666;
        }
        .fecha-central {
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            color: #fff;
            margin: 1mm 0;
            background-color: #000;
            line-height: 1.1;
            padding: 1mm 0;
        }
        .pallet-cajas {
            font-size: 14pt;
            text-align: center;
            margin-bottom: 0.5mm;
            line-height: 1.1;
        }
        .kilos-um {
            font-size: 14pt;
            text-align: center;
            margin-top: 0.5mm;
            margin-bottom: 1mm;
            line-height: 1.1;
        }
        .sscc-cliente {
            font-size: 13pt;
            
        }
        .cliente-valor {
            text-transform: uppercase;
        }
        .contador {
            font-size: 10pt;
            color: #666;
            margin-top: 1mm;
        }
    </style>
</head>
<body>
    <!-- Layout horizontal: 55% contenido - 45% QR -->
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="46%" style="border: none; vertical-align: top; padding: 1mm;">
                
                <!-- SKU y nombre del producto en un solo bloque -->
                <div class="sku-producto-bloque">
                    <span class="sku-texto">SKU: {{ $etiqueta['sku'] }}</span><br>{{ Str::limit($etiqueta['producto'], 30) }}
                </div>

                <div>
                    <span style="font-size: 12px">{{ Str::limit($etiqueta['descripcion'], 70) }}</span>
                </div>
                <!-- Pallet y Cajas en el centro (arriba de la fecha) -->
                <div class="pallet-cajas">
                    <strong>{{ $etiqueta['contador_texto'] }}</strong>
                </div>
                
                <!-- Fecha en grande en el centro -->
                <div class="fecha-central">{{ $etiqueta['fecha'] }}</div>
                
                <!-- Kilos y UM debajo de la fecha -->
                <div class="kilos-um">
                    <strong>{{ number_format($etiqueta['peso'] ?? 0, 2) }}</strong> | <span style="font-size: 12pt;">UM: {{ $etiqueta['um_abreviatura'] ?? 'CAJAS' }}</span>
                </div>
                
                <!-- SSCC abajo -->
                <div class="sscc-cliente">
                    <strong>SSCC:</strong> {{ Str::limit($etiqueta['sscc'], 18) }}
                </div>
            </td>
            <td width="54%" style="border: none; height: 100%;">
                <div style="display: flex; justify-content: center; align-items: center; height: 100%; width: 100%;">
                    <img src="{{ $etiqueta['qr_image'] }}" alt="QR Code" style="width: 90mm; height: 90mm; display: block;">
                </div>
            </td>
        </tr>    
    </table>
    <div class="sscc-cliente" style="margin-top: 0px;">
        <strong>CLIENTE:</strong> <span class="cliente-valor">{{ $etiqueta['cliente'] }}</span>
    </div>
</body>
</html>

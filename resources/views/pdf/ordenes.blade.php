<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Órdenes de Trabajo</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 20px;
        }
        .orden {
            page-break-after: always;
            margin-bottom: 30px;
        }
        .orden:last-child {
            page-break-after: avoid;
        }
        .barcode { 
            margin: 10px 0; 
            text-align: center;
        }
        .section { 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        th, td { 
            border: 1px solid black; 
            padding: 5px; 
            text-align: left; 
            font-size: 11px;
        }
        th {
            background-color: #f0f0f0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: auto auto;
            gap: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    @foreach($ordenes as $ordenData)
    <div class="orden">
        <div class="header">
            <h2>Recepción de Mercadería</h2>
        </div>

        <div class="section">
            <div class="info-grid">
                <div class="info-item">
                    <strong>Número OT:</strong> {{ $ordenData['orden']['codigo'] }}
                </div>
                <div class="info-item">
                    <strong>Cliente:</strong> {{ $ordenData['orden']['cliente']['nombre'] }}
                </div>
                <div class="info-item">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($ordenData['orden']['fecha_entrega'])->format('d/m/Y') }}
                </div>
                <div class="info-item">
                    <strong>Almacén destino:</strong> {{ $ordenData['orden']['almacen_destino'] }}
                </div>
            </div>

            <div class="barcode">
                <img src="data:image/png;base64,{{ $ordenData['barcodeOt'] }}" height="40">
                <br>
                <span>{{ $ordenData['orden']['codigo'] }}</span>
            </div>
        </div>

        <div class="section">
            <h3>Detalle de productos</h3>
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Código de Barra</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordenData['productos'] as $producto)
                    <tr>
                        <td>{{ $producto['sku'] }}</td>
                        <td>{{ $producto['nombre'] }}</td>
                        <td>{{ $producto['cantidad'] }}</td>
                        <td>
                            <div class="barcode">
                                <img src="data:image/png;base64,{{ $producto['barcode'] }}" height="30">
                                <br>
                                <small>{{ $producto['sku'] }}</small>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</body>
</html>

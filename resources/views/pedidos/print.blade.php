<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Pedido #{{ $pedido->numero_venta ?? $pedido->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 380px; 
            margin: 0 auto; 
            padding: 20px;
            font-size: 12px;
            color: #000;
            background: white;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        .header h2 { 
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            margin: 3px 0;
        }
        .titulo-boleta {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            text-align: center;
        }
        .info-row {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            font-weight: bold;
        }
        table { 
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        table th {
            text-align: left;
            padding: 5px 0;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }
        table td {
            padding: 5px 0;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .separador {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .totales {
            margin-top: 10px;
        }
        .totales .row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .total-final { 
            font-size: 14px; 
            font-weight: bold; 
            margin-top: 5px;
            padding-top: 5px;
            border-top: 2px solid #000;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        .footer p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        @media print {
            body {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1500);">
    <div class="header">
        <h2>FOODPOINT RESTAURANT</h2>
        <p>RUC: 20123456789</p>
        <p>Av. Principal 123, Centro</p>
        <p>Tel: (01) 234-5678</p>
    </div>
    
    <div class="titulo-boleta">
        BOLETA DE VENTA ELECTRÓNICA<br>
        {{ $pedido->numero_venta ?? 'B001-' . str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
    </div>
    
    <div class="separador"></div>
    
    <div class="info-row">
        <span class="info-label">Fecha:</span>
        <span>{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Cliente:</span>
        <span>{{ $pedido->cliente_nombre ?? 'Cliente General' }}</span>
    </div>
    @if($pedido->mesa_numero)
    <div class="info-row">
        <span class="info-label">Mesa:</span>
        <span>{{ $pedido->mesa_numero }}</span>
    </div>
    @endif
    
    <div class="separador"></div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Cant</th>
                <th style="width: 55%">Descripción</th>
                <th style="width: 15%" class="text-right">P.Unit</th>
                <th style="width: 15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->items as $item)
            <tr>
                <td>{{ $item->cantidad }}</td>
                <td>{{ $item->producto_nombre ?? 'Producto' }}</td>
                <td class="text-right">{{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="separador"></div>
    
    <div class="totales">
        <div class="row">
            <span>SUBTOTAL:</span>
            <span>S/ {{ number_format($pedido->subtotal, 2) }}</span>
        </div>
        @if($pedido->descuento > 0)
        <div class="row">
            <span>DESCUENTO:</span>
            <span>-S/ {{ number_format($pedido->descuento, 2) }}</span>
        </div>
        @endif
        @if($pedido->impuesto > 0)
        <div class="row">
            <span>IGV (18%):</span>
            <span>S/ {{ number_format($pedido->impuesto, 2) }}</span>
        </div>
        @endif
        <div class="row total-final">
            <span>TOTAL:</span>
            <span>S/ {{ number_format($pedido->total, 2) }}</span>
        </div>
    </div>
    
    <div class="footer">
        <p>¡Gracias por su preferencia!</p>
        <p>Conserve su comprobante</p>
        <p>No se aceptan devoluciones</p>
    </div>
</body>
</html>
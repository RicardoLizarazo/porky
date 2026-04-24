<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: monospace;
        font-size: 12px;
        color: #000;
    }

    .center { text-align: center; }
    .right { text-align: right; }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 4px 0;
    }

    .line {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    .total {
        font-weight: bold;
        font-size: 14px;
    }
</style>
</head>
<body>

{{-- 🏪 ENCABEZADO --}}
<div class="center">
    <strong>PIQUETEADERO PORKY</strong><br>
    Crr. 103F 139 - 20<br>
    Tel: 681 23 53<br>
    Cel: 311 831 2457<br>
    NIT: 79869213-1
</div>

<div class="line"></div>

{{-- 🧾 INFO PEDIDO --}}
<p>
    Pedido: #{{ $order->id }}<br>
    Fecha: {{ optional($order->ordered_at)->format('d/m/Y H:i') }}<br>
</p>

<p>
    Cliente: {{ $order->customer?->name }}<br>
    Dirección: {{ $order->customer?->full_address ?? 'N/A' }}<br>
    Teléfono: {{ $order->customer?->telephone ?? 'N/A' }}
</p>

<div class="line"></div>

{{-- 🛒 PRODUCTOS --}}
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th class="right">Cant</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->details as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">
                    ${{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="line"></div>

{{-- 💰 DESGLOSE --}}
<table>
    <tr>
        <td>Subtotal productos</td>
        <td class="right">
            ${{ number_format($order->subtotal, 0, ',', '.') }}
        </td>
    </tr>

    @if(($order->packaging_total ?? 0) > 0)
    <tr>
        <td>Empaque</td>
        <td class="right">
            ${{ number_format($order->packaging_total, 0, ',', '.') }}
        </td>
    </tr>
    @endif

    @if(($order->delivery_cost ?? 0) > 0)
    <tr>
        <td>Domicilio</td>
        <td class="right">
            ${{ number_format($order->delivery_cost, 0, ',', '.') }}
        </td>
    </tr>
    @endif
</table>

<div class="line"></div>

{{-- 🔥 TOTAL --}}
<table>
    <tr class="total">
        <td>TOTAL</td>
        <td class="right">
            ${{ number_format($order->total, 0, ',', '.') }}
        </td>
    </tr>
</table>

<div class="line"></div>

{{-- 📝 INDICACIONES --}}
@if($order->indication)
<p>
    <strong>Indicaciones:</strong><br>
    {{ $order->indication }}
</p>
@endif

{{-- 💬 COMENTARIOS --}}
@if($order->comment)
<p>
    <strong>Comentarios:</strong><br>
    {{ $order->comment }}
</p>
@endif

<div class="line"></div>

{{-- 🙏 MENSAJE --}}
<div class="center">
    Gracias por su compra
</div>

<br>

{{-- 🔳 QR --}}
<div class="center">
    {!! QrCode::size(90)->generate(route('orders.invoice', $order->id)) !!}
</div>

</body>
</html>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
    width: 58mm;
    font-family: monospace;
    font-size: 10px;
}
.center { text-align: center; }
.row {
    display: flex;
    justify-content: space-between;
}
hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 4px 0;
}
</style>
</head>
<body onload="window.print()">
{{-- 🖼️ LOGO --}}
<div class="center">
    <img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}" style="width: 100px;">
</div>
<br>
{{-- 🏪 ENCABEZADO --}}
<div class="center">
    <strong>Piqueteadero Porky de la 105</strong><br>
    Crr. 103F 139 - 20<br>
    Tel: 681 23 53<br>
    Cel: 311 831 2457<br>
    NIT: 79869213-1<br>
</div>
<hr>
<div class="center">
    <strong>Pedido #{{ $order->id }}</strong>
</div>
<hr>
{{-- 🍽️ MESA: pedido consumido en el local, nunca se pide nombre de cliente --}}
<div>
    Mesa: {{ $order->diningTable?->name }}<br>
    @if($order->floor)
        Piso: {{ $order->floor->name }}<br>
    @endif
    @if($order->user?->name ?? $order->diningTable?->waiter?->name)
        Atendido por: {{ $order->user?->name ?? $order->diningTable?->waiter?->name }}<br>
    @endif
</div>
<hr>
{{-- 🛒 PRODUCTOS --}}
@foreach($order->details as $item)
    <div>{{ $item->product_name }}</div>
    <div class="row">
        <span>{{ $item->quantity }} x {{ number_format($item->price) }}</span>
        <span>${{ number_format($item->subtotal) }}</span>
    </div>
@endforeach
<hr>
{{-- 💰 RESUMEN --}}
<div class="row">
    <span>Subtotal</span>
    <span>${{ number_format($order->subtotal) }}</span>
</div>
@if(($order->packaging_total ?? 0) > 0)
<div class="row">
    <span>Empaque</span>
    <span>${{ number_format($order->packaging_total) }}</span>
</div>
@endif
<hr>
{{-- 🔥 TOTAL --}}
<div class="row">
    <strong>TOTAL</strong>
    <strong>${{ number_format($order->total) }}</strong>
</div>
<hr>
{{-- 📝 INDICACIONES --}}
@if($order->indication)
Indicaciones:<br>
{{ $order->indication }}<br>
@endif
{{-- 💬 COMENTARIOS --}}
@if($order->comment)
Comentarios:<br>
{{ $order->comment }}<br>
@endif
<hr>
{{-- 🔳 QR --}}
<div class="center">
    {!! QrCode::size(80)->generate($order->id) !!}
</div>
<br>
<div class="center">
    Gracias por su visita
</div>
</body>
</html>
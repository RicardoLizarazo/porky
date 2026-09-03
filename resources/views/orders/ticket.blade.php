<!doctype html>
<html>
<head>
<meta charset="utf-8">

<style>
/* =========================
   CONFIGURACIÓN IMPRESIÓN
========================= */
@page {
    size: 80mm auto;
    margin: 1.5mm;
}

/* =========================
   GENERAL
========================= */
body {
    width: 70mm;
    margin: 0 auto;
    padding: 0;
    font-family: monospace;
    font-size: 11px;
    line-height: 1.15;
    color: #000;
}

/* =========================
   UTILIDADES
========================= */
.center {
    text-align: center;
}

.row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 5px;
}

.row span:last-child {
    text-align: right;
    white-space: nowrap;
}

hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 4px 0;
}

strong {
    font-weight: bold;
}

/* =========================
   LOGO
========================= */
.logo {
    width: 115px;
    max-width: 100%;
}

/* =========================
   QR
========================= */
.qr svg {
    width: 90px;
    height: 90px;
}

/* =========================
   TEXTO
========================= */
.small {
    font-size: 10px;
}
</style>

</head>

<body onload="window.print()">

{{-- =========================
     LOGO
========================= --}}
<div class="center">
    <img 
        src="{{ asset('vendor/adminlte/dist/img/logo.png') }}" 
        class="logo"
    >
</div>

{{-- =========================
     ENCABEZADO
========================= --}}
<div class="center small">
    <strong>PIQUETEADERO PORKY DE LA 105</strong><br>
    Cra. 103F #139 - 20<br>
    Tel: 6812353 - 3118312457<br>
    NIT: 79869213-1
</div>

<hr>

{{-- =========================
     PEDIDO
========================= --}}
<div class="center">
    <strong>PEDIDO #{{ $order->id }}</strong>
</div>

<hr>

<div class="center">
    {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }} - {{ \Carbon\Carbon::now()->format('h:i A') }}
</div>

<hr>

{{-- =========================
     CLIENTE
========================= --}}
@php
    $address = $order->customer?->defaultAddress;
@endphp

<div class="small">
    <strong>Cliente:</strong>
    {{ $order->customer?->name ?? 'Consumidor Final' }}<br>

    <strong>Dir:</strong>
    {{ $address?->address ?? '---' }}<br>

    @if($address?->reference)
        <strong>Ref:</strong>
        {{ $address->reference }}<br>
    @endif

    <strong>Tel:</strong>
    {{ $order->customer?->telephone ?? '---' }}
</div>

<hr>

{{-- =========================
     PRODUCTOS
========================= --}}
@foreach($order->details as $item)

    <div>
        {{ $item->product_name }}
    </div>

    <div class="row">
        <span>
            {{ $item->quantity }} x {{ number_format($item->price) }}
        </span>

        <span>
            ${{ number_format($item->subtotal) }}
        </span>
    </div>

@endforeach

<hr>

{{-- =========================
     RESUMEN
========================= --}}
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

@if(($order->delivery_cost ?? 0) > 0)
<div class="row">
    <span>Domicilio</span>
    <span>${{ number_format($order->delivery_cost) }}</span>
</div>
@endif

<hr>

{{-- =========================
     TOTAL
========================= --}}
<div class="row" style="font-size:13px;">
    <strong>TOTAL</strong>

    <strong>
        ${{ number_format($order->total) }}
    </strong>
</div>

<hr>

{{-- =========================
     INDICACIONES
========================= --}}
@if($order->indication)

<div class="small">
    <strong>Indicaciones:</strong><br>
    {{ $order->indication }}
</div>

<hr>

@endif

{{-- =========================
     COMENTARIOS
========================= --}}
@if($order->comment)

<div class="small">
    <strong>Comentarios:</strong><br>
    {{ $order->comment }}
</div>

<hr>

@endif

{{-- =========================
     QR
========================= --}}
<div class="center qr">
    {!! QrCode::size(90)->generate($order->id) !!}
</div>

<div class="center small">
    ¡GRACIAS POR SU COMPRA!<br>
    Vuelva pronto
</div>

<br>

</body>
</html>
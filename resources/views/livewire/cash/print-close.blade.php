<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
@page {
    size: 58mm auto;
    margin: 1.5mm;
}

body {
    width: 54mm;
    margin: 0 auto;
    padding: 0;
    font-family: monospace;
    font-size: 13px;
    line-height: 1.15;
    color: #000;
}

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
    margin: 5px 0;
}

.bold {
    font-weight: bold;
}

.small {
    font-size: 10px;
}
</style>
</head>
<body onload="window.print()">
<div class="center">
<img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}"
style="width:100px; max-width:100%;">
<br>
<strong>
Piqueteadero Porky de la 105
</strong>
<br>
Crr. 103F 139 - 20
<br>
Tel: 681 23 53
<br>
NIT: 79869213-1
</div>
<hr>
<div class="center">
<strong>
CIERRE DE CAJA
</strong>
</div>
<hr>
<div class="center">
{{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }} - {{ \Carbon\Carbon::now()->format('h:i A') }}
</div>
<hr>
Caja:
{{ $cashSession->cashRegister->name }}
<br>
Piso:
{{ $cashSession->cashRegister->floor->name }}
<br>
Cajero:
{{ $cashSession->user->name }}
<hr>
<strong>RESUMEN</strong>
<div class="row">
<span>Base inicial</span>
<span>
${{number_format($cashSession->opening_amount)}}
</span>
</div>
<div class="row">
<span>Total ventas</span>
<span>
${{number_format($cashSession->sales_total)}}
</span>
</div>
<hr>
<strong>MEDIOS DE PAGO</strong>
<div class="row">
<span>Efectivo</span>
<span>${{number_format($cashSession->cash_total)}}</span>
</div>
<div class="row">
<span>Nequi</span>
<span>${{number_format($cashSession->nequi_total)}}</span>
</div>
<div class="row">
<span>Daviplata</span>
<span>${{number_format($cashSession->daviplata_total)}}</span>
</div>
<div class="row">
<span>QR</span>
<span>${{number_format($cashSession->qr_total)}}</span>
</div>
<hr>
<strong>ARQUEO DE EFECTIVO</strong>
<div class="small">(Base inicial + ventas en efectivo)</div>
<div class="row">
<span>Efectivo esperado</span>
<span>
${{number_format($cashSession->expected_cash)}}
</span>
</div>
<div class="row">
<span>Efectivo contado</span>
<span>
${{number_format($cashSession->closing_amount)}}
</span>
</div>
@php
$difference =
$cashSession->closing_amount -
$cashSession->expected_cash;
@endphp
<div class="row">
<span>Diferencia</span>
<span>
@if($difference > 0)
SOBRANTE
${{number_format($difference)}}
@elseif($difference < 0)
FALTANTE
${{number_format(abs($difference))}}
@else
$0
@endif
</span>
</div>
<hr>
Pedidos:
{{ $cashSession->orders_count }}
@if($cashSession->closing_notes)
<hr>
Observaciones:
<br>
{{ $cashSession->closing_notes }}
@endif
<hr>
<div class="center">
Cerrado por:
<br>
{{ $cashSession->closedBy?->name }}
<br><br>
Gracias
</div>
</body>
</html>
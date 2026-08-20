<!doctype html>
<html>
<head>

<meta charset="utf-8">

<style>

body {
    width:58mm;
    font-family:monospace;
    font-size:13px;
}

.center {
    text-align:center;
}

.row {
    display:flex;
    justify-content:space-between;
}

hr {
    border:none;
    border-top:1px dashed #000;
    margin:5px 0;
}

.bold {
    font-weight:bold;
}

</style>

</head>


<body onload="window.print()">


<div class="center">

<img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}"
style="width:100px">

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


<strong>ARQUEO</strong>


<div class="row">

<span>Esperado</span>

<span>
${{number_format($cashSession->expected_cash)}}
</span>

</div>


<div class="row">

<span>Contado</span>

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
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Acta de Cierre de Caja
    </title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>

        body{

            font-size:14px;

            color:#000;

            background:#fff;

        }

        .title{

            font-size:26px;

            font-weight:bold;

        }

        .subtitle{

            font-size:16px;

            color:#666;

        }

        .section-title{

            font-size:16px;

            font-weight:bold;

            border-bottom:2px solid #000;

            margin-top:25px;

            margin-bottom:10px;

            padding-bottom:5px;

        }

        .table td,
        .table th{

            padding:.45rem;

            vertical-align:middle;

        }

        .signature{

            margin-top:80px;

        }

        .signature div{

            border-top:1px solid #000;

            width:250px;

            text-align:center;

            padding-top:8px;

            margin:auto;

        }

        @media print{

            .no-print{

                display:none;

            }

        }

    </style>

</head>

<body>

<div class="container mt-4">

    <div class="row">

        <div class="col-8">

            <div class="title">

                PORKY

            </div>

            <div class="subtitle">

                ACTA DE CIERRE DE CAJA

            </div>

        </div>

        <div class="col-4 text-right">

            <strong>

                {{ now()->format('d/m/Y H:i') }}

            </strong>

        </div>

    </div>

    <hr>

    {{-- Información General --}}

    <div class="section-title">

        Información General

    </div>

    <table class="table table-bordered">

        <tr>

            <th width="25%">Caja</th>

            <td>

                {{ $cashSession->cashRegister->name }}

            </td>

            <th width="20%">Piso</th>

            <td>

                {{ $cashSession->cashRegister->floor->name }}

            </td>

        </tr>

        <tr>

            <th>Sede</th>

            <td>

                {{ $cashSession->cashRegister->location->name }}

            </td>

            <th>Cajero</th>

            <td>

                {{ $cashSession->user->name }}

            </td>

        </tr>

        <tr>

            <th>Apertura</th>

            <td>

                {{ $cashSession->opened_at?->format('d/m/Y h:i A') }}

            </td>

            <th>Cierre</th>

            <td>

                {{ $cashSession->closed_at?->format('d/m/Y h:i A') }}

            </td>

        </tr>

    </table>

    {{-- Resumen Financiero --}}

    <div class="section-title">

        Resumen Financiero

    </div>

    <table class="table table-bordered">

        <tr>

            <th>Base Inicial</th>

            <td class="text-right">

                $ {{ number_format($cashSession->opening_amount,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>Total Ventas</th>

            <td class="text-right">

                $ {{ number_format($cashSession->sales_total,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>Efectivo</th>

            <td class="text-right">

                $ {{ number_format($cashSession->cash_total,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>Nequi</th>

            <td class="text-right">

                $ {{ number_format($cashSession->nequi_total,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>Daviplata</th>

            <td class="text-right">

                $ {{ number_format($cashSession->daviplata_total,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>QR</th>

            <td class="text-right">

                $ {{ number_format($cashSession->qr_total,0,',','.') }}

            </td>

        </tr>

    </table>

    {{-- Estadísticas --}}

    <div class="section-title">

        Estadísticas del Turno

    </div>

    <table class="table table-bordered">

        <tr>

            <th width="50%">

                Pedidos Cobrados

            </th>

            <td>

                {{ $cashSession->orders_count }}

            </td>

        </tr>

        <tr>

            <th>

                Facturas Electrónicas Solicitadas

            </th>

            <td>

                {{ $cashSession->invoices_requested }}

            </td>

        </tr>

    </table>

    {{-- Arqueo --}}

    <div class="section-title">

        Arqueo Final

    </div>

    <table class="table table-bordered">

        <tr>

            <th width="50%">

                Efectivo Esperado

            </th>

            <td class="text-right">

                $ {{ number_format($cashSession->expected_cash,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>

                Efectivo Contado

            </th>

            <td class="text-right">

                $ {{ number_format($cashSession->closing_amount,0,',','.') }}

            </td>

        </tr>

        <tr>

            <th>

                Diferencia

            </th>

            <td class="text-right">

                @php

                    $difference = $cashSession->closing_amount - $cashSession->expected_cash;

                @endphp

                @if($difference > 0)

                    <span class="text-success">

                        +$ {{ number_format($difference,0,',','.') }}

                    </span>

                @elseif($difference < 0)

                    <span class="text-danger">

                        -$ {{ number_format(abs($difference),0,',','.') }}

                    </span>

                @else

                    $0

                @endif

            </td>

        </tr>

    </table>

    {{-- Observaciones --}}

    <div class="section-title">

        Observaciones

    </div>

    <div class="border p-3" style="min-height:90px;">

        {!! nl2br(e($cashSession->closing_notes)) !!}

    </div>

    {{-- Firmas --}}

    <div class="row signature">

        <div class="col-6">

            <div>

                Firma Cajero

            </div>

        </div>

        <div class="col-6">

            <div>

                Firma Administrador

            </div>

        </div>

    </div>

    <div class="text-center mt-5 no-print">

        <button

            onclick="window.print()"

            class="btn btn-primary"

        >

            <i class="fas fa-print"></i>

            Imprimir

        </button>

    </div>

</div>

</body>

</html>
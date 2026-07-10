<div class="container-fluid">

    @if(!$session)

        <livewire:cash.open-cash />

    @else

        {{-- INFORMACIÓN DE LA CAJA --}}

        <div class="row">

            <div class="col-md-12">

                <div class="card card-success">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="fas fa-cash-register mr-2"></i>

                            Caja Operativa

                        </h3>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">

                                <strong>Caja</strong>

                                <br>

                                {{ $session->cashRegister->name }}

                            </div>

                            <div class="col-md-3">

                                <strong>Sede</strong>

                                <br>

                                {{ $session->cashRegister->location?->name }}

                            </div>

                            <div class="col-md-3">

                                <strong>Piso</strong>

                                <br>

                                {{ $session->cashRegister->floor?->name }}

                            </div>

                            <div class="col-md-3">

                                <strong>Cajero</strong>

                                <br>

                                {{ $session->user?->name }}

                            </div>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-4">

                                <strong>Apertura</strong>

                                <br>

                                {{ $session->opened_at?->format('d/m/Y h:i A') }}

                            </div>

                            <div class="col-md-4">

                                <strong>Base Inicial</strong>

                                <br>

                                ${{ number_format($session->opening_amount,0,',','.') }}

                            </div>

                            <div class="col-md-4">

                                <strong>Estado</strong>

                                <br>

                                <span class="badge badge-success">

                                    ABIERTA

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- RESUMEN DEL TURNO --}}

        <div class="row">

            <div class="col-md-3">

                <div class="info-box">

                    <span class="info-box-icon bg-info">

                        <i class="fas fa-receipt"></i>

                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">

                            Ventas Turno

                        </span>

                        <span class="info-box-number">

                            ${{ number_format($session->sales_total,0,',','.') }}

                        </span>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="info-box">

                    <span class="info-box-icon bg-success">

                        <i class="fas fa-money-bill-wave"></i>

                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">

                            Efectivo

                        </span>

                        <span class="info-box-number">

                            ${{ number_format($session->cash_total,0,',','.') }}

                        </span>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="info-box">

                    <span class="info-box-icon bg-primary">

                        <i class="fas fa-mobile-alt"></i>

                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">

                            Pagos Digitales

                        </span>

                        <span class="info-box-number">

                            ${{ number_format(
                                $session->nequi_total +
                                $session->daviplata_total +
                                $session->qr_total,
                            0,',','.') }}

                        </span>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="info-box">

                    <span class="info-box-icon bg-warning">

                        <i class="fas fa-wallet"></i>

                    </span>

                    <div class="info-box-content">

                        <span class="info-box-text">

                            Efectivo Esperado

                        </span>

                        <span class="info-box-number">

                            ${{ number_format($session->expected_cash,0,',','.') }}

                        </span>

                    </div>

                </div>

            </div>

        </div>

        {{-- DETALLE POR MÉTODO DE PAGO --}}

        <div class="row">

            <div class="col-md-8">

                <div class="card">

                    <div class="card-header">

                        <h3 class="card-title">

                            Métodos de Pago

                        </h3>

                    </div>

                    <div class="card-body p-0">

                        <table class="table table-striped">

                            <thead>

                                <tr>

                                    <th>Método</th>

                                    <th class="text-right">Valor</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td>Efectivo</td>

                                    <td class="text-right">

                                        ${{ number_format($session->cash_total,0,',','.') }}

                                    </td>

                                </tr>

                                <tr>

                                    <td>Nequi</td>

                                    <td class="text-right">

                                        ${{ number_format($session->nequi_total,0,',','.') }}

                                    </td>

                                </tr>

                                <tr>

                                    <td>Daviplata</td>

                                    <td class="text-right">

                                        ${{ number_format($session->daviplata_total,0,',','.') }}

                                    </td>

                                </tr>

                                <tr>

                                    <td>QR</td>

                                    <td class="text-right">

                                        ${{ number_format($session->qr_total,0,',','.') }}

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card">

                    <div class="card-header">

                        <h3 class="card-title">

                            Indicadores

                        </h3>

                    </div>

                    <div class="card-body">

                        <p>

                            <strong>Pedidos cobrados:</strong>

                            {{ $session->orders_count }}

                        </p>

                        <p>

                            <strong>Facturas solicitadas:</strong>

                            {{ $session->invoices_requested }}

                        </p>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>
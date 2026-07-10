<div class="modal fade"
     id="paymentModal"
     tabindex="-1"
     wire:ignore.self>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-success">

                <h4 class="modal-title">

                    <i class="fas fa-cash-register mr-2"></i>

                    Cobro de Pedido

                </h4>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                @if($order)

                <div class="row">

                    <div class="col-md-4">

                        <div class="small-box bg-info">

                            <div class="inner">

                                <h4>Mesa {{ $order->diningTable?->name }}</h4>

                                <p>

                                    Pedido #{{ $order->id }}

                                </p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-utensils"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="small-box bg-secondary">

                            <div class="inner">

                                <h4>

                                    {{ $order->user?->name }}

                                </h4>

                                <p>Mesero</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-user"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="small-box bg-success">

                            <div class="inner">

                                <h3>

                                    $ {{ number_format($order->total,0,',','.') }}

                                </h3>

                                <p>Total Pedido</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-dollar-sign"></i>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-header">

                        <strong>

                            Consumo del Cliente

                        </strong>

                    </div>

                    <div class="card-body p-0">

                        <table class="table table-sm table-striped">

                            <thead>

                            <tr>

                                <th>Cant.</th>

                                <th>Producto</th>

                                <th class="text-right">

                                    Valor Unit.

                                </th>

                                <th class="text-right">

                                    Total

                                </th>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach($order->details as $detail)

                                @php

                                    $unitPrice = $detail->manual_price ?: $detail->price;

                                    $lineTotal = $unitPrice * $detail->quantity;

                                    preg_match(
                                        '/EMPAQUE=(\d+)/',
                                        $detail->comment ?? '',
                                        $matches
                                    );

                                    $packagingQty = (int) ($matches[1] ?? 0);

                                    $packagingCost = $detail->product?->packaging_cost ?? 0;

                                    $packagingTotal = $packagingQty * $packagingCost;

                                @endphp

                                <tr>

                                    <td>{{ $detail->quantity }}</td>

                                    <td>

                                        {{ $detail->product_name }}

                                        @if($detail->manual_price)

                                            <br>

                                            <small class="text-warning">

                                                Precio manual

                                            </small>

                                        @endif

                                        @if($packagingQty > 0)

                                            <br>

                                            <small class="text-muted">

                                                Empaque x {{ $packagingQty }}

                                                (${{ number_format($packagingTotal,0,',','.') }})

                                            </small>

                                        @endif

                                    </td>

                                    <td class="text-right">

                                        $

                                        {{ number_format($unitPrice,0,',','.') }}

                                    </td>

                                    <td class="text-right">

                                        $

                                        {{ number_format($lineTotal + $packagingTotal,0,',','.') }}

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

                @endif

                <div class="card mt-3">

                    <div class="card-header">

                        <strong>

                            Métodos de Pago

                        </strong>

                    </div>

                    <div class="card-body">

                        @foreach($payments as $index=>$payment)

                        <div class="row mb-2">

                            <div class="col-md-5">

                                <select class="form-control"

                                    wire:model.live="payments.{{ $index }}.payment_method">

                                    <option>Efectivo</option>

                                    <option>Nequi</option>

                                    <option>Daviplata</option>

                                    <option>QR</option>

                                </select>

                            </div>

                            <div class="col-md-5">

                                <input

                                    type="number"

                                    class="form-control"

                                    min="0"

                                    step="100"

                                    wire:model.live="payments.{{ $index }}.amount">

                            </div>

                            <div class="col-md-2">

                                @if(count($payments)>1)

                                <button

                                    class="btn btn-danger btn-block"

                                    wire:click="removePayment({{ $index }})">

                                    <i class="fas fa-trash"></i>

                                </button>

                                @endif

                            </div>

                        </div>

                        @endforeach

                        <button

                            class="btn btn-primary btn-sm"

                            wire:click="addPayment">

                            <i class="fas fa-plus"></i>

                            Agregar método

                        </button>

                    </div>

                </div>

                <div class="row mt-3">

                    <div class="col-md-3">

                        <div class="info-box bg-info">

                            <span class="info-box-icon">

                                <i class="fas fa-receipt"></i>

                            </span>

                            <div class="info-box-content">

                                <span>Total Pedido</span>

                                <span class="info-box-number">

                                    $

                                    {{ number_format($order?->total ?? 0,0,',','.') }}

                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="info-box bg-success">

                            <span class="info-box-icon">

                                <i class="fas fa-money-bill-wave"></i>

                            </span>

                            <div class="info-box-content">

                                <span>Recibido</span>

                                <span class="info-box-number">

                                    $

                                    {{ number_format($this->totalReceived,0,',','.') }}

                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="info-box bg-warning">

                            <span class="info-box-icon">

                                <i class="fas fa-hand-holding-usd"></i>

                            </span>

                            <div class="info-box-content">

                                <span>Cambio</span>

                                <span class="info-box-number">

                                    $

                                    {{ number_format($this->change,0,',','.') }}

                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        @if($this->remaining>0)

                        <div class="info-box bg-danger">

                        @else

                        <div class="info-box bg-success">

                        @endif

                            <span class="info-box-icon">

                                <i class="fas fa-balance-scale"></i>

                            </span>

                            <div class="info-box-content">

                                <span>Saldo Pendiente</span>

                                <span class="info-box-number">

                                    $

                                    {{ number_format($this->remaining,0,',','.') }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="custom-control custom-switch mt-3">

                    <input

                        type="checkbox"

                        id="invoice_requested"

                        class="custom-control-input"

                        wire:model.live="invoice_requested">

                    <label

                        class="custom-control-label"

                        for="invoice_requested">

                        Solicita Factura Electrónica

                    </label>

                </div>

                @if($invoice_requested)

                <hr>

                <div class="card card-outline card-primary">

                    <div class="card-header">

                        <strong>Datos para Factura Electrónica</strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">

                                <label>Tipo Documento</label>

                                <select
                                    class="form-control"
                                    wire:model.live="invoice.document_type"
                                >
                                    <option value="CC">CC</option>
                                    <option value="CE">CE</option>
                                    <option value="NIT">NIT</option>
                                    <option value="TI">TI</option>
                                    <option value="PP">Pasaporte</option>
                                </select>

                            </div>

                            <div class="col-md-4">

                                <label>Número</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="invoice.document"
                                >

                            </div>

                            <div class="col-md-5">

                                <label>Nombre / Razón Social</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="invoice.name"
                                >

                            </div>

                        </div>

                        <div class="row mt-2">

                            <div class="col-md-4">

                                <label>Celular</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="invoice.phone"
                                >

                            </div>

                            <div class="col-md-4">

                                <label>Correo</label>

                                <input
                                    type="email"
                                    class="form-control"
                                    wire:model.live="invoice.email"
                                >

                            </div>

                            <div class="col-md-4">

                                <label>Dirección</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    wire:model.live="invoice.address"
                                >

                            </div>

                        </div>

                    </div>

                </div>

                @endif

                @error('payments')

                    <div class="alert alert-danger mt-3">

                        {{ $message }}

                    </div>

                @enderror

            </div>

            <div class="modal-footer">

                <button

                    class="btn btn-secondary"

                    data-dismiss="modal">

                    Cancelar

                </button>

                <button

                    class="btn btn-success"

                    wire:click="pay"

                    @if($this->remaining>0) disabled @endif>

                    <i class="fas fa-check-circle"></i>

                    Cobrar

                </button>

            </div>

        </div>

    </div>

</div>
<div class="container-fluid">

@if(!$session)

    @if($openSessions->isEmpty())

        <div class="alert alert-danger">
            No existe una caja abierta.
        </div>

    @else

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card card-danger">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lock mr-2"></i>
                            ¿Cuál caja deseas cerrar?
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($openSessions as $s)
                                <button
                                    type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    wire:click="selectSession({{ $s->id }})"
                                >
                                    <span>
                                        <i class="fas fa-cash-register mr-2"></i>
                                        <strong>{{ $s->cashRegister->name }}</strong>
                                        <small class="text-muted d-block">
                                            {{ $s->cashRegister->floor?->name }}
                                            &middot;
                                            Abierta {{ $s->opened_at->diffForHumans() }}
                                        </small>
                                    </span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

    @endif

@else

<div class="row">
    <div class="col-md-12">
        <div class="card card-danger">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-lock mr-2"></i>

                    Cierre de Caja

                </h3>

                @if($openSessions->count() > 1)
                    <div class="card-tools">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="changeSession"
                        >
                            <i class="fas fa-exchange-alt mr-1"></i>
                            Cambiar de caja
                        </button>
                    </div>
                @endif

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <strong>Caja</strong><br>

                        {{ $session->cashRegister->name }}

                    </div>

                    <div class="col-md-3">

                        <strong>Sede</strong><br>

                        {{ $session->cashRegister->location?->name }}

                    </div>

                    <div class="col-md-3">

                        <strong>Piso</strong><br>

                        {{ $session->cashRegister->floor?->name }}

                    </div>

                    <div class="col-md-3">

                        <strong>Cajero</strong><br>

                        {{ $session->user?->name }}

                    </div>

                </div>

                <hr>

                <div class="row">

                    <div class="col-md-3">

                        <strong>Apertura</strong><br>

                        {{ $session->opened_at->format('d/m/Y h:i A') }}

                    </div>

                    <div class="col-md-3">

                        <strong>Duración</strong><br>

                        {{ $session->opened_at->diffForHumans(now(), true) }}

                    </div>

                    <div class="col-md-3">

                        <strong>Pedidos</strong><br>

                        {{ $session->orders_count }}

                    </div>

                    <div class="col-md-3">

                        <strong>Facturas</strong><br>

                        {{ $session->invoices_requested }}

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="row">

    <div class="col-md-3">

        <div class="info-box bg-info">

            <span class="info-box-icon">

                <i class="fas fa-dollar-sign"></i>

            </span>

            <div class="info-box-content">

                <span class="info-box-text">

                    Ventas

                </span>

                <span class="info-box-number">

                    ${{ number_format($session->sales_total,0,',','.') }}

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

                <span class="info-box-text">

                    Efectivo

                </span>

                <span class="info-box-number">

                    ${{ number_format($session->cash_sales,0,',','.') }}

                </span>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="info-box bg-primary">

            <span class="info-box-icon">

                <i class="fas fa-mobile-alt"></i>

            </span>

            <div class="info-box-content">

                <span class="info-box-text">

                    Nequi + Daviplata

                </span>

                <span class="info-box-number">

                    ${{ number_format($session->nequi_sales + $session->daviplata_sales,0,',','.') }}

                </span>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="info-box bg-warning">

            <span class="info-box-icon">

                <i class="fas fa-qrcode"></i>

            </span>

            <div class="info-box-content">

                <span class="info-box-text">

                    QR

                </span>

                <span class="info-box-number">

                    ${{ number_format($session->qr_sales,0,',','.') }}

                </span>

            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Conciliación de Efectivo

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <label>

                    Efectivo esperado

                </label>

                <input

                    class="form-control"

                    readonly

                    value="${{ number_format($session->expected_cash,0,',','.') }}"
                >

            </div>

            <div class="col-md-4">
                <label>
                    Efectivo contado
                </label>

                <input
                    type="text"
                    inputmode="numeric"
                    class="form-control"
                    x-data="{
                        raw: @entangle('counted_cash').live,
                        display: ''
                    }"
                    x-init="display = raw ? new Intl.NumberFormat('es-CO').format(raw) : ''"
                    x-model="display"
                    @input="
                        let clean = $event.target.value.replace(/\D/g, '');
                        raw = clean ? parseInt(clean, 10) : 0;
                        display = clean ? new Intl.NumberFormat('es-CO').format(clean) : '';
                    "
                >
            </div>

            <div class="col-md-4">

                <label>

                    Diferencia

                </label>

                <input

                    class="form-control font-weight-bold"

                    readonly

                    value="${{ number_format($this->difference,0,',','.') }}"
                >

            </div>

            @if($this->difference != 0)

            <div class="alert alert-warning mt-3">

                <strong>Diferencia detectada:</strong>

                @if($this->difference > 0)

                    Sobrante de

                    <strong>

                        ${{ number_format($this->difference,0,',','.') }}

                    </strong>

                @else

                    Faltante de

                    <strong>

                        ${{ number_format(abs($this->difference),0,',','.') }}

                    </strong>

                @endif

            </div>

            @endif

        </div>

        <div class="form-group mt-3">

            <label>

                Observaciones del cierre

            </label>

            <textarea

                class="form-control"

                rows="4"

                wire:model="closing_notes"

            ></textarea>

        </div>

    </div>

    <div class="card-footer text-right">
        <button

            class="btn btn-danger"

            wire:click="closeCash"

            wire:loading.attr="disabled"

        >

            <i class="fas fa-lock mr-1"></i>

            Cerrar Caja

        </button>

        <a
            href="{{ route('cash.close.print', $session) }}"
            target="_blank"
            class="btn btn-primary"
        >

            <i class="fas fa-print"></i>

            Imprimir Acta

        </a>

    </div>

</div>

@endif

</div>
@push('script')
    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-print', (event) => {
            window.open(event.url ?? event[0].url, '_blank');
        });
    });
    </script>
@endpush
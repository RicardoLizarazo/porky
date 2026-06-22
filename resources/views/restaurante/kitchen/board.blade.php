<div wire:poll.2s>

    <div class="row mb-3">

        <div class="col-12">

            <div class="info-box bg-danger">

                <span class="info-box-icon">
                    <i class="fas fa-fire"></i>
                </span>

                <div class="info-box-content">

                    <span class="info-box-text">
                        ESTACIÓN
                    </span>

                    <span class="info-box-number">
                        {{ strtoupper($station) }}
                    </span>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        @forelse($details as $item)

            @php

                $ticket = $item->kitchenOrder;

                $minutes = $ticket->sent_at
                    ? $ticket->sent_at->diffInMinutes(now())
                    : 0;

            @endphp

            <div class="col-lg-4 col-md-6">

                <div class="card card-danger">

                    <div class="card-header">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h4 class="mb-0">

                                    Mesa
                                    {{ $ticket->diningTable?->name }}

                                </h4>

                                <small>

                                    Piso:
                                    {{ $ticket->floor?->name }}

                                </small>

                            </div>

                            <div>

                                @if($minutes <= 10)

                                    <span class="badge badge-success p-2">

                                        {{ $minutes }} min

                                    </span>

                                @elseif($minutes <= 20)

                                    <span class="badge badge-warning p-2">

                                        {{ $minutes }} min

                                    </span>

                                @else

                                    <span class="badge badge-danger p-2">

                                        {{ $minutes }} min

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="mb-2">

                            <span class="badge badge-info">

                                Mesero:
                                {{ $ticket->order?->user?->name ?? 'N/A' }}

                            </span>

                        </div>

                        <div class="mb-2">

                            <strong>

                                Hora:
                                {{ $ticket->sent_at?->format('h:i A') }}

                            </strong>

                        </div>

                        <hr>

                        <h2 class="font-weight-bold">

                            {{ $item->quantity }}
                            x
                            {{ strtoupper($item->product_name) }}

                        </h2>

                        <div class="mt-2">

                            <span class="badge badge-secondary">

                                Valor unidad

                                ${{ number_format($item->price,0,',','.') }}

                            </span>

                        </div>

                        <div class="mt-2">

                            <span class="badge badge-dark">

                                Total item

                                ${{ number_format($item->subtotal,0,',','.') }}

                            </span>

                        </div>

                        <div class="mt-2">

                            <span class="badge badge-primary">

                                Pedido total

                                ${{ number_format($ticket->order?->total ?? 0,0,',','.') }}

                            </span>

                        </div>

                        @if($item->comment)

                            <div class="alert alert-warning mt-3 mb-0">

                                <strong>

                                    Observación:

                                </strong>

                                <br>

                                {{ $item->comment }}

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-success">

                    <h3 class="mb-0">

                        No hay pedidos pendientes en

                        {{ $station }}

                    </h3>

                </div>

            </div>

        @endforelse

    </div>

</div>
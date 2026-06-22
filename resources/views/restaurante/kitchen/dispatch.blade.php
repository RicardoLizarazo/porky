<div wire:poll.2s>

    <div class="row">

        @forelse($tickets as $ticket)

            @php

                $minutes = $ticket->sent_at
                    ? $ticket->sent_at->diffInMinutes(now())
                    : 0;

            @endphp

            <div class="col-lg-4 col-md-6">

                <div class="card card-danger">

                    <div class="card-header">

                        <div class="d-flex justify-content-between align-items-center">

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

                                <strong>

                                    ${{ number_format($ticket->order?->total ?? 0,0,',','.') }}

                                </strong>

                            </div>

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="mb-2">

                            <span class="badge badge-info">

                                Mesero:
                                {{ $ticket->order?->user?->name ?? 'N/A' }}

                            </span>

                            @if($minutes <= 10)

                                <span class="badge badge-success">
                                    {{ $minutes }} min
                                </span>

                            @elseif($minutes <= 20)

                                <span class="badge badge-warning">
                                    {{ $minutes }} min
                                </span>

                            @else

                                <span class="badge badge-danger">
                                    {{ $minutes }} min
                                </span>

                            @endif

                        </div>

                        <div class="mb-3">

                            <small class="text-muted">

                                Pedido:
                                {{ $ticket->sent_at?->format('h:i A') }}

                            </small>

                        </div>

                        @foreach($ticket->details as $item)

                            <div class="border-bottom pb-2 mb-2">

                                <h5 class="mb-1">

                                    {{ $item->quantity }}
                                    x
                                    {{ $item->product_name }}

                                </h5>

                                <strong>

                                    ${{ number_format($item->subtotal ?? 0,0,',','.') }}

                                </strong>

                                <small class="text-muted">

                                    (${{ number_format(($item->price ?? 0),0,',','.') }} c/u)

                                </small>

                                @if($item->comment)

                                    <div>

                                        <small class="text-danger">

                                            {{ $item->comment }}

                                        </small>

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                    <div class="card-footer">

                        <button
                            wire:click="ready({{ $ticket->id }})"
                            class="btn btn-success btn-lg btn-block">

                            <i class="fas fa-check-circle mr-2"></i>

                            MARCAR COMO LISTO

                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-success">

                    <h4 class="mb-0">

                        No hay pedidos pendientes

                    </h4>

                </div>

            </div>

        @endforelse

    </div>

</div>
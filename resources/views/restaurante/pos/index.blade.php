<div class="container-fluid">

    {{-- HEADER --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="mb-1">
                        {{ $order->table?->name }}
                    </h4>
                    <small class="text-muted">
                        Mesero:
                        {{ auth()->user()->name }}
                    </small>
                </div>

                <div class="text-right">
                    <h4 class="text-danger">
                        ${{ number_format($this->total,0,',','.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH --}}
    <div class="mb-3">
        <input
            type="text"
            wire:model.live="search"
            class="form-control form-control-lg"
            placeholder="Buscar producto..."
        >
    </div>

    {{-- CATEGORIES --}}
    <div class="mb-3 d-flex overflow-auto pb-2">
        <button
            wire:click="$set('category_id', null)"
            class="btn btn-sm mr-2 {{ !$category_id ? 'btn-danger' : 'btn-light' }}"
        >
            Todos
        </button>

        @foreach($categories as $category)
            <button
                wire:click="filterCategory({{ $category->id }})"
                class="btn btn-sm mr-2 {{ $category_id == $category->id ? 'btn-danger' : 'btn-light' }}"
            >
                {{ $category->name }}
            </button>
        @endforeach

    </div>

    {{-- CONTENT --}}
    <div class="row">
        {{-- PRODUCTS --}}
        <div class="col-lg-8">
            <div class="row">
                @foreach($products as $product)
                    <div class="col-12 col-md-6 mb-3">
                        <div class="product-card-pro shadow-sm">
                            <div class="d-flex">
                                {{-- IMAGE --}}
                                <div class="product-img-wrapper">
                                    <img
                                        src="{{ $product->image
                                            ? asset('storage/'.$product->image)
                                            : asset('images/no-image.png') }}"
                                        class="product-img"
                                    >
                                </div>

                                {{-- INFO --}}
                                <div class="flex-grow-1 p-2">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="font-weight-bold">
                                            {{ $product->name }}
                                        </h6>
                                        <span class="text-danger font-weight-bold">
                                            ${{ number_format($product->price,0,',','.') }}
                                        </span>
                                    </div>

                                    <small class="text-muted d-block">
                                        {{ $product->category?->name }}
                                    </small>

                                    <div class="mt-3">
                                        <button
                                            wire:click="addProduct({{ $product->id }})"
                                            class="btn btn-dark btn-sm rounded-pill"
                                        >
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- CART --}}
        <div class="col-lg-4">
            <div class="card sticky-top">

                <div class="card-header">
                    Pedido
                </div>

                <div class="card-body">

                    @forelse($cart as $item)

                        <div class="border-bottom pb-2 mb-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>
                                        {{ $item['product']['name'] }}
                                    </strong>

                                    <div class="small text-muted">
                                        x{{ $item['quantity'] }}
                                    </div>

                                    @if($item['product']['allow_manual_price'])

                                        <div class="mt-2">

                                            <small class="text-muted">
                                                Valor por porción
                                            </small>

                                            <input
                                                type="number"
                                                min="0"
                                                step="500"
                                                value="{{ $item['manual_price'] ?: $item['price'] }}"
                                                wire:change="updateManualPrice(
                                                    {{ $item['id'] }},
                                                    $event.target.value
                                                )"
                                                class="form-control form-control-sm"
                                                style="width:120px;"
                                            >

                                        </div>

                                    @endif

                                    @if($item['manual_price'])
                                        <div class="small text-primary">

                                            💲 Ajustado:
                                            ${{ number_format($item['manual_price'],0,',','.') }}

                                        </div>
                                    @endif

                                    @php
                                        preg_match(
                                            '/EMPAQUE=(\d+)/',
                                            $item['comment'] ?? '',
                                            $matches
                                        );

                                        $packagingQty = (int) ($matches[1] ?? 0);
                                    @endphp

                                    @if($packagingQty > 0)

                                        <div class="small text-warning mt-1">
                                            🥡 {{ $packagingQty }}
                                            x
                                            ${{ number_format(
                                                $item['product']['packaging_cost'] ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </div>

                                    @endif

                                </div>

                                <div class="text-right">
                                    <div class="mb-1">
                                        ${{ number_format($item['subtotal'],0,',','.') }}
                                    </div>

                                    {{-- CONTROLES PRINCIPALES --}}
                                    <div class="btn-group">
                                        <button
                                            wire:click="removeProduct({{ $item['id'] }})"
                                            class="btn btn-cef btn-cef-delete"
                                        >
                                            -
                                        </button>

                                        <button
                                            class="btn btn-light"
                                            disabled
                                        >
                                            {{ $item['quantity'] }}
                                        </button>

                                        <button
                                            wire:click="addProduct({{ $item['product_id'] }})"
                                            class="btn btn-cef btn-cef-edit"
                                        >
                                            +
                                        </button>

                                        <button
                                            wire:click="deleteDetail({{ $item['id'] }})"
                                            class="btn btn-sm btn-secondary"
                                        >
                                            🗑
                                        </button>
                                    </div>

                                    {{-- EMPAQUE --}}
                                    @if(($item['product']['packaging_cost'] ?? 0) > 0)
                                        @php
                                            preg_match(
                                                '/EMPAQUE=(\d+)/',
                                                $item['comment'] ?? '',
                                                $matches
                                            );

                                            $packagingQty = (int) ($matches[1] ?? 0);
                                        @endphp

                                        @if($packagingQty > 0)
                                            <div class="small text-warning mt-1">
                                                🥡 {{ $packagingQty }}
                                                x
                                                ${{ number_format(
                                                    $item['product']['packaging_cost'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </div>
                                        @endif

                                        <div class="mt-2">
                                            <small class="text-muted d-block">
                                                Para llevar
                                            </small>
                                            <div class="btn-group btn-group-sm">
                                                <button
                                                    wire:click="decreasePackaging({{ $item['id'] }})"
                                                    class="btn btn-outline-warning"
                                                >
                                                    -
                                                </button>

                                                <button
                                                    class="btn btn-warning"
                                                    disabled
                                                >
                                                    🥡 {{ $packagingQty }}
                                                </button>

                                                <button
                                                    wire:click="increasePackaging({{ $item['id'] }})"
                                                    class="btn btn-outline-warning"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                    @empty
                        <div class="text-muted">
                            No hay productos
                        </div>
                    @endforelse
                </div>

                <div class="card-footer">

                    @if($order->packaging_total > 0)

                        <div class="d-flex justify-content-between small mb-2">

                            <span>🥡 Empaque</span>

                            <span>
                                ${{ number_format(
                                    $order->packaging_total,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </span>

                        </div>

                    @endif

                    <div class="d-flex justify-content-between">

                        <strong>Total</strong>

                        <strong>
                            ${{ number_format($this->total,0,',','.') }}
                        </strong>

                    </div>

                    <button wire:click="sendToKitchen" class="btn btn-danger btn-block mt-2">
                        Enviar a Cocina
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('success', () => {
            Swal.fire({
                icon: 'success',
                title: 'Pedido enviado a cocina.',
                showConfirmButton: false,
                timer: 1500
            })
        });
    });
</script>
@endpush
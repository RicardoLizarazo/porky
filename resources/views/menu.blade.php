<div class="container-fluid">

    {{-- 🔍 BUSCADOR --}}
    <div class="mb-3">
        <input type="text"
               wire:model.debounce.500ms="search"
               class="form-control form-control-lg"
               placeholder="¿Qué quieres comer hoy?">
    </div>

    {{-- 🧭 CATEGORÍAS TIPO SCROLL --}}
    <div class="mb-3 d-flex overflow-auto pb-2">

        <button wire:click="filterCategory(null)"
                class="btn btn-sm mr-2 {{ !$category_id ? 'btn-danger' : 'btn-light' }}">
            Todas
        </button>

        @foreach($categories as $category)
            <button wire:click="filterCategory({{ $category->id }})"
                    class="btn btn-sm mr-2 {{ $category_id == $category->id ? 'btn-danger' : 'btn-light' }}">
                {{ $category->name }}
            </button>
        @endforeach

    </div>

    {{-- 🍔 PRODUCTOS ESTILO RAPPI --}}
    @php use Illuminate\Support\Str; @endphp

    <div class="row">
        @forelse($products as $product)
            <div class="col-12 col-md-6 col-xl-4 mb-3">
                <div class="card product-card-pro shadow-sm border-0">
                    <div class="d-flex">
                        {{-- IMAGEN --}}
                        <div class="product-img-wrapper">
                            <img src="{{ $product->image 
                                ? asset('storage/'.$product->image) 
                                : asset('images/no-image.png') }}"
                                class="product-img flyable">

                            {{-- BOTÓN VER --}}
                            <button 
                                wire:click="$dispatch('view-product', { id: {{ $product->id }} })"
                                class="btn btn-dark btn-sm btn-view">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        {{-- INFO --}}
                        <div class="flex-grow-1 p-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1 font-weight-bold">
                                    {{ $product->name }}
                                </h6>
                                {{-- PRECIO --}}
                                <span class="text-danger font-weight-bold">
                                    ${{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>

                            <small class="text-muted d-block">
                                {{ $product->category->name }}
                            </small>

                            <small class="text-muted">
                                {{ Str::limit($product->description, 70) }}
                            </small>

                            {{-- ACCIONES --}}
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                @if($isOpen)
                                    @if($this->getQty($product->id) > 0)
                                        <div class="qty-control">
                                            <button wire:click="$dispatch('cart-remove', { id: {{ $product->id }} })">
                                                −
                                            </button>

                                            <span>
                                                {{ $this->getQty($product->id) }}
                                            </span>

                                            <button wire:click="$dispatch('cart-add', { id: {{ $product->id }} }); $dispatch('animate-cart', { id: {{ $product->id }} });">
                                                +
                                            </button>
                                        </div>
                                    @else
                                        <button wire:click="$dispatch('cart-add', { id: {{ $product->id }} })"
                                                class="btn btn-dark btn-sm rounded-pill">
                                            Agregar
                                        </button>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">
                                        Cerrado
                                    </span>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">
                    No hay productos disponibles
                </div>
            </div>
        @endforelse

    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-3">
        {{ $products->links() }}
    </div>

</div>
@push('script')
    <script>
    document.addEventListener('livewire:init', () => {
        /*
        |--------------------------------------------------------------------------
        | MODAL PRODUCTO
        |--------------------------------------------------------------------------
        */
        Livewire.on('product-data', (data) => {
            Swal.fire({
                title: data.name,
                html: `
                    <p>${data.description}</p>
                    <strong style="font-size:18px;">
                        $${Number(data.price).toLocaleString()}
                    </strong>
                `,
                imageUrl: data.image,
                confirmButtonText: 'Agregar al carrito',
                confirmButtonColor: '#dc3545'
            });

        });

        /*
        |--------------------------------------------------------------------------
        | ANIMACIÓN AL CARRITO
        |--------------------------------------------------------------------------
        */
        Livewire.on('animate-cart', ({ id }) => {

            const img = document.querySelector(`.flyable[data-id='${id}']`);
            const cart = document.getElementById('cartFab')

            if (!img || !cart) return;

            const imgRect = img.getBoundingClientRect();
            const cartRect = cart.getBoundingClientRect();

            const clone = img.cloneNode(true);

            clone.style.position = 'fixed';
            clone.style.zIndex = 2000;
            clone.style.left = imgRect.left + 'px';
            clone.style.top = imgRect.top + 'px';
            clone.style.width = imgRect.width + 'px';
            clone.style.height = imgRect.height + 'px';
            clone.style.transition = 'all 0.6s cubic-bezier(.4,0,.2,1)';

            document.body.appendChild(clone);

            setTimeout(() => {
                clone.style.left = cartRect.left + 'px';
                clone.style.top = cartRect.top + 'px';
                clone.style.width = '20px';
                clone.style.height = '20px';
                clone.style.opacity = '0.3';
            }, 10);

            setTimeout(() => clone.remove(), 700);
        });
    });
    </script>
@endpush
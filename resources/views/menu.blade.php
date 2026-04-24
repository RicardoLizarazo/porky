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
                                wire:click="$dispatch('view-product', {
                                    name: '{{ $product->name }}',
                                    description: '{{ $product->description }}',
                                    image: '{{ $product->image ? asset('storage/'.$product->image) : asset('images/no-image.png') }}',
                                    price: {{ $product->price }}
                                })"
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
            // 🔥 DEFINE PRIMERO
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            /*
            |--------------------------------------------------------------------------
            | TOAST PRODUCTO
            |--------------------------------------------------------------------------
            */
            window.addEventListener('product-added-toast', event => {
                const { name } = event.detail;

                Toast.fire({
                    icon: "success",
                    title: `${name} agregado`
                });
            });
            
            /*
            |--------------------------------------------------------------------------
            | MODAL PRODUCTO
            |--------------------------------------------------------------------------
            */
            Livewire.on('view-product', (data) => {
                Swal.fire({
                    title: data.name,
                    html: `
                        <div class="product-modal">
                            <img src="${data.image}" class="product-modal-img"/>
                            <p class="mt-2">${data.description}</p>
                            <strong>$${Number(data.price).toLocaleString()}</strong>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'Ok',
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

            Livewire.on('confirm-order', () => {
                Swal.fire({
                    title: '¿Confirmar pedido?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: 'Sí, confirmar',
                }).then((result) => {

                    if (result.isConfirmed) {
                        Livewire.dispatch('execute-confirm-order');
                    }
                });
            });

            Livewire.on('order-success', (orderId) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido realizado',
                    text: 'Tu pedido fue enviado correctamente',
                    confirmButtonColor: '#dc3545'
                });
            });

            Livewire.on('order-error', (msg) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            });

            Livewire.on('print-ticket', (data) => {
                window.open(data.url, '_blank');
            });

            Livewire.on('open-inline-create-modal', () => {
                $('#modal-inline-create').modal('show');
            });

            Livewire.on('close-inline-create-modal', () => {
                $('#modal-inline-create').modal('hide');
            });

            Livewire.on('open-inline-edit-modal', () => {
                $('#modal-inline-edit').modal('show');
            });

            Livewire.on('close-inline-edit-modal', () => {
                $('#modal-inline-edit').modal('hide');
            });
        });
    </script>
@endpush
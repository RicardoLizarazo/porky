<div class="btn-group action-group" role="group">

    {{-- 👁️ VER --}}
    <button 
        @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('view-order', { id: {{ $row->id }} })"
        class="btn action-btn action-view"
        title="Ver detalle"
        data-toggle="tooltip"
    >
        <i class="fas fa-eye"></i>
    </button>

    {{-- ✏️ EDITAR --}}
    @if($row->status_id == 1)
        <button 
            @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('edit-order', { id: {{ $row->id }} })"
            class="btn action-btn action-edit"
            title="Editar pedido"
            data-toggle="tooltip"
        >
            <i class="fas fa-pen"></i>
        </button>
    @endif

    {{-- 🔄 CAMBIAR ESTADO --}}
    <div class="btn-group">
        <button 
            class="btn action-btn action-warning dropdown-toggle"
            data-toggle="dropdown"
            title="Cambiar estado"
        >
            <i class="fas fa-sync-alt"></i>
        </button>

        <div class="dropdown-menu shadow-lg">
            @foreach(\App\Models\StatusOrder::all() as $status)
                <a 
                    href="#" 
                    class="dropdown-item d-flex justify-content-between"
                    wire:click.prevent="$dispatch('change-status', { 
                        id: {{ $row->id }}, 
                        status: {{ $status->id }} 
                    })"
                >
                    <span>{{ $status->name }}</span>

                    @if($row->status_id == $status->id)
                        <i class="fas fa-check text-success"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- 🚚 DOMICILIARIO --}}
    @if($row->type_id == 1)
        @php
            static $deliveryUsers;

            if (!$deliveryUsers) {
                $deliveryUsers = \App\Models\User::whereHas('roles', function($query) {
                    $query->where('name', 'Domiciliario');
                })->pluck('name', 'id');
            }
        @endphp

        <div class="btn-group">
            <button 
                class="btn action-btn {{ $row->delivery_user_id ? 'action-success' : 'action-info' }} dropdown-toggle"
                data-toggle="dropdown"
                title="Asignar domiciliario"
            >
                <i class="fas fa-motorcycle"></i>
            </button>

            <div class="dropdown-menu shadow-lg">

                {{-- Quitar asignación --}}
                <a 
                    href="#"
                    class="dropdown-item text-danger"
                    wire:click.prevent="$dispatch('change-delivery', { 
                        id: {{ $row->id }}, 
                        delivery: null 
                    })"
                >
                    ❌ Quitar asignación
                </a>

                <div class="dropdown-divider"></div>

                {{-- Lista --}}
                @foreach($deliveryUsers as $id => $name)
                    <a 
                        href="#"
                        class="dropdown-item d-flex justify-content-between"
                        wire:click.prevent="$dispatch('change-delivery', { 
                            id: {{ $row->id }}, 
                            delivery: {{ $id }} 
                        })"
                    >
                        <span>{{ $name }}</span>

                        @if($row->delivery_user_id == $id)
                            <i class="fas fa-check text-success"></i>
                        @endif
                    </a>
                @endforeach

            </div>
        </div>
    @endif

    {{-- 💳 MÉTODO DE PAGO --}}
    <div class="btn-group">
        <button 
            class="btn action-btn action-payment dropdown-toggle"
            data-toggle="dropdown"
            title="Cambiar método de pago"
        >
            <i class="fas fa-credit-card"></i>
        </button>

        <div class="dropdown-menu shadow-lg">
            @foreach(['Efectivo', 'Transferencia', 'Nequi', 'Daviplata', 'Tarjeta'] as $method)
                <a 
                    href="#"
                    class="dropdown-item d-flex justify-content-between"
                    wire:click.prevent="$dispatch('change-payment', { 
                        id: {{ $row->id }}, 
                        payment: '{{ $method }}' 
                    })"
                >
                    <span>{{ $method }}</span>

                    @if($row->payment_method === $method)
                        <i class="fas fa-check text-success"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- 🧾 TICKET --}}
    <a 
        href="{{ route('orders.ticket', $row->id) }}" 
        target="_blank" 
        class="btn action-btn action-dark"
        title="Imprimir ticket"
    >
        <i class="fas fa-receipt"></i>
    </a>

    {{-- ❌ ELIMINAR --}}
    @if($row->status_id == 1)
        <button 
            wire:click="$dispatch('delete', {{ $row->id }})"
            class="btn action-btn action-danger"
            title="Eliminar pedido"
        >
            <i class="fas fa-trash"></i>
        </button>
    @endif

</div>
<div 
    x-data="{ open: @entangle('showDropdown') }"
    class="position-relative"
    style="z-index: 1055;"
>

    <input
        type="text"
        wire:model.live="query"
        class="form-control"
        placeholder="Buscar cliente..."
        @focus="open = true; $wire.resetSearch()"
        @click.away="open = false"
    >

    @if(strlen($query) >= 2)
        <div
            x-show="open"
            class="position-absolute bg-white border w-100 shadow mt-1 rounded"
            style="max-height: 260px; overflow-y: auto;"
        >
            <ul class="list-group list-group-flush">

                @foreach($results as $customer)
                    <li
                        class="list-group-item"
                        wire:key="customer-{{ $customer['id'] }}"
                    >
                        <div class="d-flex justify-content-between align-items-start">

                            {{-- INFO --}}
                            <div 
                                style="cursor:pointer"
                                wire:click="selectCustomer({{ $customer['id'] }})"
                                class="flex-grow-1"
                            >
                                <strong>{{ $customer['name'] }}</strong><br>

                                <small class="text-muted d-block">
                                    📞 {{ $customer['telephone'] ?? 'Sin teléfono' }}
                                </small>

                                <small class="text-muted d-block">
                                    📍 {{ $customer['address'] ?? 'Sin dirección registrada' }}
                                </small>
                            </div>

                            {{-- ACCIONES --}}
                            <div class="ml-2">
                                <button 
                                    class="btn btn-xs {{ empty($customer['address']) ? 'btn-outline-danger' : 'btn-outline-secondary' }}"
                                    title="Editar cliente"
                                    wire:click.stop="editCustomer({{ $customer['id'] }})"
                                >
                                    ✏️
                                </button>
                            </div>

                        </div>
                    </li>
                @endforeach

                {{-- 🔴 SIN RESULTADOS --}}
                @if($noResults)
                    <li class="list-group-item text-center">

                        <div class="text-muted mb-2">
                            No se encontraron resultados
                        </div>

                        <button 
                            class="btn btn-sm btn-primary"
                            wire:click="createCustomer('{{ $query }}')"
                        >
                            ➕ Crear cliente "{{ $query }}"
                        </button>
                    </li>
                @endif
            </ul>
        </div>
    @endif
</div>

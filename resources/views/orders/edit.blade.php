<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-receipt mr-2"></i>
                    Editar Pedido #{{ $order_id }}
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="update">
                <div class="modal-body">

                    <div class="row">

                        {{-- CLIENTE --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Cliente</label>
                                <select class="form-control @error('customer_id') is-invalid @enderror"
                                        wire:model.defer="customer_id">
                                    <option value="">Seleccione</option>
                                    @foreach($customers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- TIPO --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Tipo de pedido</label>
                                <select class="form-control"
                                        wire:model.defer="type_id">
                                    <option value="">Seleccione</option>
                                    @foreach($types as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Estado</label>
                                <select class="form-control"
                                        wire:model.defer="status_id">
                                    @foreach($statuses as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- MÉTODO DE PAGO --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Método de pago</label>
                                <select class="form-control"
                                        wire:model.defer="payment_method">
                                    <option value="cash">Efectivo</option>
                                    <option value="card">Tarjeta</option>
                                    <option value="transfer">Transferencia</option>
                                </select>
                            </div>
                        </div>

                        {{-- DOMICILIARIO --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Domiciliario</label>
                                <select class="form-control"
                                        wire:model.defer="delivery_user_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($deliveryUsers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- INDICACIONES --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Indicaciones</label>
                                <input type="text"
                                       class="form-control"
                                       wire:model.defer="indication">
                            </div>
                        </div>

                        {{-- COMENTARIO --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Comentario</label>
                                <textarea class="form-control"
                                          rows="2"
                                          wire:model.defer="comment"></textarea>
                            </div>
                        </div>

                    </div>

                    <hr>

                    {{-- 🛒 PRODUCTOS --}}
                    <div class="row">
                        <div class="col-md-8">

                            <label class="font-weight-bold">Agregar productos</label>

                            {{-- 📦 PRODUCTOS AGRUPADOS --}}
                            <div style="max-height: 400px; overflow-y: auto;">

                                @foreach($products as $categoryName => $group)

                                    @php
                                        $first = $group->first();
                                        $categoryId = $first->category_id ?? null;

                                        // filtro por categoría
                                        if ($category_id && $category_id != $categoryId) continue;
                                    @endphp

                                    {{-- 🏷️ NOMBRE CATEGORÍA --}}
                                    <h6 class="text-danger font-weight-bold mt-3">
                                        {{ $categoryName }}
                                    </h6>

                                    <div class="row">

                                        @foreach($group as $product)
                                            <div class="col-md-6 mb-2">
                                                <button type="button"
                                                        wire:click="addProduct({{ $product->id }})"
                                                        class="btn btn-outline-primary btn-block text-left">

                                                    <div class="d-flex justify-content-between">
                                                        <span>{{ $product->name }}</span>
                                                        <strong>${{ number_format($product->price,0,',','.') }}</strong>
                                                    </div>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 🧾 RESUMEN --}}
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <strong>Resumen</strong>
                                </div>
                                <div class="card-body">

                                    @forelse($items as $item)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <small>{{ $item['name'] }}</small><br>
                                                <input type="number"
                                                    min="1"
                                                    class="form-control form-control-sm"
                                                    style="width:70px"
                                                    wire:change="updateQty({{ $item['product_id'] }}, $event.target.value)"
                                                    value="{{ $item['qty'] }}">
                                            </div>

                                            <div class="text-right">
                                                <small>
                                                    Producto: $ {{ number_format($item['price'], 0, ',', '.') }}
                                                </small>

                                                @if(($item['packaging_cost'] ?? 0) > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        Empaque: $ {{ number_format($item['packaging_cost'], 0, ',', '.') }}
                                                    </small>
                                                @endif
                                                <button type="button"
                                                        wire:click="removeItem({{ $item['product_id'] }})"
                                                        class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted small">Sin productos</p>
                                    @endforelse

                                    <hr>

                                    {{-- 🧮 Productos --}}
                                    <div class="d-flex justify-content-between">
                                        <span>Productos</span>
                                        <span>
                                            $ {{ number_format($subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- 📦 Empaque --}}
                                    @if($packaging_total > 0)
                                        <div class="d-flex justify-content-between text-muted">
                                            <span>Empaque</span>
                                            <span>$ {{ number_format($packaging_total, 0, ',', '.') }}</span>
                                        </div>
                                    @endif

                                    {{-- 🚚 Domicilio editable --}}
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Domicilio</span>
                                        <input type="number"
                                                min="0"
                                                step="500"
                                            class="form-control form-control-sm text-right"
                                            style="width:120px"
                                            wire:model.live="delivery_cost">
                                    </div>

                                    <hr>

                                    {{-- 💰 TOTAL --}}
                                    <div class="d-flex justify-content-between">
                                        <strong>Total</strong>
                                        <span class="font-weight-bold h5">
                                            $ {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-cef btn-cef-create">
                        Actualizar Pedido
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
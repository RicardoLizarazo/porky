{{-- Productos que surte el proveedor --}}
<hr>
<h5>Productos que surte</h5>

@if($availableItems->isEmpty())
    <p class="text-muted small">
        Aún no hay productos de inventario creados. Créalos primero en "Productos de Inventario".
    </p>
@else
    <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th style="width: 40px;"></th>
                    <th>Producto</th>
                    <th>SKU del proveedor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableItems as $item)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   wire:model.live="selectedItems.{{ $item->id }}">
                        </td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   placeholder="Opcional"
                                   wire:model.defer="skus.{{ $item->id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

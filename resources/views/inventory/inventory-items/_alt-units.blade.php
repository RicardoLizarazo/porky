{{-- Unidades alternas de compra/manejo --}}
<hr>
<h5>Unidades alternas</h5>
<p class="text-muted small">
    Además de la unidad base, indica en qué otras unidades se puede comprar o manejar este producto
    y cuántas unidades base equivalen a 1 de ellas. Ej: 1 Bulto = 100 Libras.
</p>

@foreach($altUnits as $index => $row)
    <div class="row mb-2 align-items-center">
        <div class="col-md-5">
            <select class="form-control form-control-sm" wire:model.defer="altUnits.{{ $index }}.unit_id">
                <option value="">Seleccione unidad...</option>
                @foreach($units as $unit)
                    @if($unit->id != $base_unit_id)
                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <input type="number" step="0.0001" min="0"
                       class="form-control"
                       placeholder="Equivale a X unidades base"
                       wire:model.defer="altUnits.{{ $index }}.factor_to_base">
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeAltUnit({{ $index }})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
@endforeach

<button type="button" class="btn btn-outline-primary btn-sm" wire:click="addAltUnit">
    + Agregar unidad alterna
</button>

{{-- Líneas a devolver --}}
<hr>
<h5>Productos a devolver</h5>

@if(!$purchase_id)
    <p class="text-muted small">Seleccione primero la compra a la que pertenece la devolución.</p>
@elseif(empty($lines))
    <p class="text-muted small">Esta compra no tiene líneas disponibles.</p>
@else
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Pendiente por devolver</th>
                    <th style="width:140px;">Cantidad a devolver</th>
                    <th style="width:120px;">Costo unitario</th>
                    <th style="width:120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lines as $detailId => $line)
                    <tr>
                        <td>{{ $line['name'] }}</td>
                        <td>{{ number_format($line['remaining'], 2) }} {{ $line['unit'] }}</td>
                        <td>
                            @if($readOnly)
                                {{ number_format($line['quantity'], 2) }} {{ $line['unit'] }}
                            @else
                                <input type="number" step="0.0001" min="0" max="{{ $line['remaining'] }}"
                                       class="form-control form-control-sm"
                                       wire:model.live.debounce.400ms="lines.{{ $detailId }}.quantity">
                            @endif
                        </td>
                        <td>${{ number_format($line['unit_cost'], 2) }}</td>
                        <td>${{ number_format(($line['quantity'] ?: 0) * $line['unit_cost'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-right mt-2">
        <strong>Total: ${{ number_format($formTotal, 2) }}</strong>
    </div>
@endif

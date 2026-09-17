<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class PurchaseReturns extends Component
{
    #[Locked]
    public $purchase_return_id;

    public $purchase_id;

    #[Validate('required|date')]
    public $return_date;

    #[Validate('nullable')]
    public $reason;

    #[Validate('nullable|max:100')]
    public $credit_note_number;

    /** [purchase_detail_id => ['inventory_item_id','name','unit','remaining','quantity','unit_cost']] */
    public $lines = [];

    public $confirmedPurchases = [];

    public $readOnly = false;

    public function mount()
    {
        if (!Auth::user()?->can('purchase_returns.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->return_date = now()->format('Y-m-d');
        $this->confirmedPurchases = Purchase::confirmed()->with('supplier')->orderByDesc('id')->get();
    }

    public function render()
    {
        return view('inventory.purchase-returns.index', [
            'formTotal' => collect($this->lines)->sum(fn($l) =>
                (float) ($l['quantity'] ?? 0) * (float) ($l['unit_cost'] ?? 0)
            ),
        ]);
    }

    public function resetInput()
    {
        $this->reset(['purchase_return_id', 'purchase_id', 'reason', 'credit_note_number', 'lines', 'readOnly']);
        $this->return_date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-create-modal');
    }

    public function updatedPurchaseId($value)
    {
        $this->lines = [];

        if (!$value) {
            return;
        }

        $purchase = Purchase::with('details.inventoryItem.baseUnit')->findOrFail($value);

        foreach ($purchase->details as $detail) {
            $remaining = $detail->remainingReturnableQuantity();

            $this->lines[$detail->id] = [
                'inventory_item_id' => $detail->inventory_item_id,
                'name' => $detail->inventoryItem->name,
                'unit' => $detail->inventoryItem->baseUnit->abbreviation ?? '',
                'remaining' => $remaining,
                'quantity' => 0,
                'unit_cost' => $detail->base_quantity > 0 ? $detail->subtotal / $detail->base_quantity : 0,
            ];
        }
    }

    public function store()
    {
        $this->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'return_date' => 'required|date',
            'credit_note_number' => 'nullable|max:100',
        ]);

        $activeLines = collect($this->lines)->filter(fn($l) => (float) ($l['quantity'] ?? 0) > 0);

        if ($activeLines->isEmpty()) {
            $this->dispatch('error', 'Debe indicar al menos una cantidad a devolver.');
            return;
        }

        foreach ($activeLines as $detailId => $line) {
            if ((float) $line['quantity'] > (float) $line['remaining']) {
                $this->dispatch('error', "La cantidad a devolver de \"{$line['name']}\" supera lo comprado pendiente de devolución.");
                return;
            }
        }

        $purchase = Purchase::findOrFail($this->purchase_id);

        DB::transaction(function () use ($purchase, $activeLines) {
            $total = $activeLines->sum(fn($l) => (float) $l['quantity'] * (float) $l['unit_cost']);

            $return = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'return_date' => $this->return_date,
                'reason' => $this->reason,
                'credit_note_number' => $this->credit_note_number,
                'total' => $total,
                'created_by' => Auth::id(),
            ]);

            foreach ($activeLines as $detailId => $line) {
                $quantity = (float) $line['quantity'];
                $unitCost = (float) $line['unit_cost'];

                $return->details()->create([
                    'purchase_detail_id' => $detailId,
                    'inventory_item_id' => $line['inventory_item_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $quantity * $unitCost,
                ]);

                $item = InventoryItem::whereKey($line['inventory_item_id'])->lockForUpdate()->first();
                $newStock = $item->stock - $quantity;

                $item->update(['stock' => $newStock]);

                InventoryMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => InventoryMovement::TYPE_PURCHASE_RETURN_OUT,
                    'quantity' => -$quantity,
                    'unit_cost' => $unitCost,
                    'balance_after' => $newStock,
                    'reference_id' => $return->id,
                    'reference_type' => PurchaseReturn::class,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('view')]
    public function show($id)
    {
        $this->resetInput();

        $return = PurchaseReturn::with('details.inventoryItem.baseUnit', 'purchase')->findOrFail($id);

        $this->purchase_return_id = $id;
        $this->purchase_id = $return->purchase_id;
        $this->return_date = $return->return_date->format('Y-m-d');
        $this->reason = $return->reason;
        $this->credit_note_number = $return->credit_note_number;
        $this->readOnly = true;

        foreach ($return->details as $detail) {
            $this->lines[$detail->purchase_detail_id] = [
                'inventory_item_id' => $detail->inventory_item_id,
                'name' => $detail->inventoryItem->name,
                'unit' => $detail->inventoryItem->baseUnit->abbreviation ?? '',
                'remaining' => 0,
                'quantity' => $detail->quantity,
                'unit_cost' => $detail->unit_cost,
            ];
        }

        $this->dispatch('open-view-modal');
    }
}

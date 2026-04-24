<?php

namespace App\Livewire\Components;
use App\Models\Customer;

use Livewire\Component;

class CustomerInlineEdit extends Component
{
    public $customer_id;

    public $name;
    public $telephone;
    public $address;
    public $reference;

    public $original = [];

    protected $listeners = ['editCustomerInline'];

    public function editCustomerInline($id)
    {
        $customer = Customer::with('defaultAddress')->findOrFail($id);

        $this->customer_id = $id;

        $this->name = $customer->name;
        $this->telephone = $customer->telephone;
        $this->address = $customer->defaultAddress->address ?? null;
        $this->reference = $customer->defaultAddress->reference ?? null;

        $this->original = [
            'name' => $this->name,
            'telephone' => $this->telephone,
            'address' => $this->address,
            'reference' => $this->reference,
        ];

        $this->dispatch('open-inline-edit-modal');
    }

    public function updateInline()
    {
        $this->validate([
            'name' => 'required|min:3|max:100',
            'telephone' => 'required|max:50',
            'address' => 'required|string|max:250',
        ]);

        $customer = Customer::with('defaultAddress')->findOrFail($this->customer_id);

        $customer->update([
            'name' => $this->name,
            'telephone' => $this->telephone,
        ]);

        if ($customer->defaultAddress) {
            $customer->defaultAddress->update([
                'address' => $this->address,
                'reference' => $this->reference,
            ]);
        }

        $this->dispatch('customerUpdated', id: $customer->id);
        $this->dispatch('close-inline-edit-modal');
    }

    public function render()
    {
        return view('livewire.components.customer-inline-edit');
    }
}

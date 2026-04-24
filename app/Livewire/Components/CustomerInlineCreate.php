<?php

namespace App\Livewire\Components;

use App\Models\Customer;
use Livewire\Component;

class CustomerInlineCreate extends Component
{
    public $name;
    public $telephone;
    public $address;
    public $reference;

    protected $listeners = ['createCustomer'];

    public function createCustomer($query = null)
    {
        $this->resetForm();

        // 🔥 prellenado inteligente
        if ($query) {
            if (is_numeric($query)) {
                $this->telephone = $query;
            } else {
                $this->name = $query;
            }
        }

        $this->dispatch('open-inline-create-modal');
    }

    public function storeInline()
    {
        $this->validate([
            'name' => 'required|min:3|max:100',
            'telephone' => 'required|max:50',
            'address' => 'required|string|max:250',
        ]);

        // 🔥 evitar duplicados por teléfono
        $existing = Customer::where('telephone', $this->telephone)->first();

        if ($existing) {
            $this->dispatch('customerCreated', id: $existing->id);
            $this->dispatch('close-inline-create-modal');
            return;
        }

        // 🔥 crear cliente
        $customer = Customer::create([
            'name' => $this->name,
            'telephone' => $this->telephone,
            'email' => null,
            'status' => 1,
            'password' => bcrypt('12345678'),
        ]);

        // 🔥 crear dirección
        $customer->addresses()->create([
            'address' => $this->address,
            'reference' => $this->reference,
            'latitude' => 0,
            'longitude' => 0,
            'is_default' => 1
        ]);

        $this->dispatch('customerCreated', id: $customer->id);
        $this->dispatch('close-inline-create-modal');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'telephone',
            'address',
            'reference',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.components.customer-inline-create');
    }
}
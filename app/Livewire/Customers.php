<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Illuminate\Validation\Rules\Password;

class Customers extends Component
{
    #[Locked]
    public $customer_id;

    public $name;
    public $email;
    public $telephone;
    public $status = 1;

    public $password;
    public $password_confirmation;

    public $addresses = [];
    public $defaultIndex = 0;

    public $inline_address;
    public $inline_reference;

    public $original = [];

    public function render()
    {
        return view('customers.index');
    }

    public function resetInput()
    {
        $this->reset([
            'name',
            'email',
            'telephone',
            'password',
            'password_confirmation',
            'status',
            'addresses',
            'defaultIndex',
        ]);

        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        
        // inicializar dirección
        $this->addresses = [
            [
                'address' => '',
                'latitude' => '',
                'longitude' => '',
                'reference' => '',
            ]
        ];

        $this->dispatch('open-create-modal');
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers',
            'telephone' => 'required|max:50',
            'password' => 'required|min:8|confirmed',
            'addresses' => 'required|array|min:1',

            'addresses.*.address' => 'required|string|max:250',
            'addresses.*.latitude' => 'required|numeric',
            'addresses.*.longitude' => 'required|numeric',
        ]);

        try {

            // 1. Crear cliente
            $customer = Customer::create([
                'name' => $this->name,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'status' => $this->status,
                'password' => Hash::make($this->password),
            ]);

            // 2. Crear dirección principal
            foreach ($this->addresses as $index => $addr) {

                $customer->addresses()->create([
                    'address' => $addr['address'],
                    'latitude' => $addr['latitude'],
                    'longitude' => $addr['longitude'],
                    'reference' => $addr['reference'],
                    'is_default' => $index == $this->defaultIndex
                ]);
            }

            $this->dispatch('store');
            $this->dispatch('refreshDatatable');

        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function addAddress()
    {
        $this->addresses[] = [
            'address' => '',
            'latitude' => '',
            'longitude' => '',
            'reference' => '',
        ];
    }

    public function removeAddress($index)
    {
        unset($this->addresses[$index]);
        $this->addresses = array_values($this->addresses);

        if ($this->defaultIndex == $index) {
            $this->defaultIndex = 0;
        }
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetInput();

        $customer = Customer::with('addresses')->findOrFail($id);

        $this->customer_id = $id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->telephone = $customer->telephone;
        $this->status = $customer->status;

        // 🔥 cargar TODAS las direcciones
        $this->addresses = $customer->addresses->map(function ($addr) {
            return [
                'id' => $addr->id,
                'address' => $addr->address,
                'latitude' => $addr->latitude,
                'longitude' => $addr->longitude,
                'reference' => $addr->reference,
                'is_default' => $addr->is_default,
            ];
        })->toArray();

        // 🔥 detectar default
        $this->defaultIndex = collect($this->addresses)
            ->search(fn($a) => $a['is_default']) ?? 0;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:255|unique:customers,email,' . $this->customer_id,
            'telephone' => 'required|max:50',
            'addresses' => 'required|array|min:1',
            'addresses.*.address' => 'required',
        ]);

        $customer = Customer::with('addresses')->findOrFail($this->customer_id);

        // 1. actualizar cliente
        $customer->update([
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'status' => $this->status,
        ]);

        // 🔥 IDs existentes en BD
        $existingIds = $customer->addresses->pluck('id')->toArray();

        $sentIds = collect($this->addresses)
            ->pluck('id')
            ->filter()
            ->toArray();

        // 🔥 eliminar direcciones borradas en UI
        $toDelete = array_diff($existingIds, $sentIds);
        CustomerAddress::destroy($toDelete);

        // 🔥 actualizar / crear
        foreach ($this->addresses as $index => $addr) {

            $data = [
                'address' => $addr['address'],
                'latitude' => $addr['latitude'] ?? null,
                'longitude' => $addr['longitude'] ?? null,
                'reference' => $addr['reference'] ?? null,
                'is_default' => $index == $this->defaultIndex,
            ];

            if (isset($addr['id'])) {
                CustomerAddress::where('id', $addr['id'])->update($data);
            } else {
                $customer->addresses()->create($data);
            }
        }

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    #[On('editCustomerInline')]
    public function editInline($id)
    {
        $customer = Customer::with('defaultAddress')->findOrFail($id);

        $this->customer_id = $id;

        $this->name = $customer->name;
        $this->telephone = $customer->telephone;

        $this->inline_address = $customer->defaultAddress->address ?? null;
        $this->inline_reference = $customer->defaultAddress->reference ?? null;

        $this->original = [
            'name' => $this->name,
            'telephone' => $this->telephone,
            'address' => $this->inline_address,
            'reference' => $this->inline_reference,
        ];

        $this->dispatch('open-inline-edit-modal');
    }

    public function updateInline()
    {
        $this->validate([
            'name' => 'required|min:3|max:100',
            'telephone' => 'required|max:50',
            'inline_address' => 'required|string|max:250',
        ]);

        $customer = Customer::with('defaultAddress')->findOrFail($this->customer_id);

        $customer->update([
            'name' => $this->name,
            'telephone' => $this->telephone,
        ]);

        if ($customer->defaultAddress) {
            $customer->defaultAddress->update([
                'address' => $this->inline_address,
                'reference' => $this->inline_reference,
            ]);
        }

        $this->dispatch('customerUpdated', id: $customer->id);
        $this->dispatch('close-inline-edit-modal');
    }

    public function delete($id)
    {
        try {
            Customer::findOrFail($id)->delete();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
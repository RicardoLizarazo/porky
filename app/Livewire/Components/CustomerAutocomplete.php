<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Customer;

class CustomerAutocomplete extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $showDropdown = false;
    public ?int $selectedId = null;

    public string $event = 'customerSelected';
    public bool $noResults = false;

    protected $listeners = [
        'customerCreated',
        'customerUpdated'
    ];

    public function mount(?int $selectedId = null, string $event = 'customerSelected')
    {
        $this->selectedId = $selectedId;
        $this->event = $event;

        if ($selectedId) {
            $customer = Customer::find($selectedId);
            if ($customer) {
                $this->query = $customer->name;
            }
        }
    }

    public function updatedQuery($value)
    {
        $query = trim($value);

        if (mb_strlen($query) < 2) {
            $this->resetDropdown();
            return;
        }

        $words = preg_split('/\s+/', $query);

        $results = Customer::query()
            ->with(['defaultAddress:id,customer_id,address'])
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere(function ($sub) use ($word) {
                        $sub->where('name', 'like', "%{$word}%")
                            ->orWhere('telephone', 'like', "%{$word}%")
                            ->orWhere('email', 'like', "%{$word}%")
                            ->orWhereHas('defaultAddress', function ($q2) use ($word) {
                                $q2->where('address', 'like', "%{$word}%");
                            });
                    });
                }
            })
            ->orderBy('name')
            ->limit(7)
            ->get();

        $this->results = $results->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'telephone' => $c->telephone,
            'address' => $c->defaultAddress->address ?? null,
        ])->toArray();

        $this->noResults = count($this->results) === 0;
        $this->showDropdown = true;
    }

    public function selectCustomer($id)
    {
        $customer = Customer::find($id);
        if (!$customer) return;

        $this->selectedId = $customer->id;

        // 👇 mantener contexto visible
        $this->query = $customer->name;

        $this->results = [];
        $this->showDropdown = false;

        $this->dispatch($this->event, id: $customer->id);
    }

    public function createCustomer($query = null)
    {
        $this->dispatch('createCustomer', query: trim($query ?? $this->query));
    }

    public function editCustomer($id)
    {
        $this->dispatch('editCustomerInline', id: $id);
    }

    public function customerCreated($id)
    {
        $this->selectCustomer($id);
    }

    public function customerUpdated($id)
    {
        $this->selectCustomer($id);
    }

    protected function resetDropdown()
    {
        $this->results = [];
        $this->noResults = false;
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.components.customer-autocomplete');
    }

    public function resetSearch()
    {
        $this->results = [];
        $this->showDropdown = false;
    }
}
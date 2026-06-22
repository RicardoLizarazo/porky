<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductRule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

class ProductRules extends Component
{
    #[Locked]
    public $rule_id;

    #[Validate('required|min:3')]
    public $name;

    #[Validate('nullable')]
    public $description;

    #[Validate('required|numeric|min:0')]
    public $extra_price = 0;

    #[Validate('boolean')]
    public $is_active = true;

    public function render()
    {
        return view('restaurante.product-rules.index');
    }

    public function resetInput()
    {
        $this->reset([
            'name',
            'description',
            'extra_price',
            'is_active'
        ]);
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();

        $this->dispatch('open-create-modal');
    }

    public function store()
    {
        $this->validate();

        ProductRule::create([

            'name' => $this->name,

            'description' => $this->description,

            'extra_price' => $this->extra_price,

            'is_active' => $this->is_active,
        ]);

        $this->dispatch('store');

        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $rule = ProductRule::findOrFail($id);

        $this->rule_id = $rule->id;

        $this->name = $rule->name;

        $this->description = $rule->description;

        $this->extra_price = $rule->extra_price;

        $this->is_active = $rule->is_active;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        ProductRule::findOrFail($this->rule_id)
            ->update([

                'name' => $this->name,

                'description' => $this->description,

                'extra_price' => $this->extra_price,

                'is_active' => $this->is_active,
            ]);

        $this->dispatch('update');

        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        ProductRule::findOrFail($id)
            ->delete();

        $this->dispatch('refreshDatatable');
    }
}
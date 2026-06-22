<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductRule;

class ProductRuleManager extends Component
{
    public Product $product;

    public $rules = [];

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->rules = $product
            ->rules()
            ->pluck('product_rules.id')
            ->toArray();
    }

    public function save()
    {
        $this->product
            ->rules()
            ->sync($this->rules);

        $this->dispatch(
            'success',
            'Reglas actualizadas correctamente.'
        );
    }

    public function render()
    {
        return view(
            'restaurante.products.rules',
            [
                'availableRules' => ProductRule::where(
                    'is_active',
                    true
                )->orderBy('name')->get()
            ]
        );
    }
}
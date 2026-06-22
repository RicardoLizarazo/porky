<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductRule;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class Products extends Component
{
    use WithFileUploads;

    #[Locked]
    public $product_id;

    #[Validate('required|min:3')]
    public $name;

    #[Validate('nullable')]
    public $code;

    #[Validate('required|exists:categories,id')]
    public $category_id;

    #[Validate('required|numeric|min:0')]
    public $price;

    #[Validate('nullable')]
    public $description;

    #[Validate('nullable|numeric|min:0')]
    public $packaging_cost = 0;

    #[Validate('boolean')]
    public $is_visible = true;

    #[Validate('boolean')]
    public $is_active = true;

    #[Validate('nullable|image|max:2048')] // 2MB
    public $image;

    public $image_preview;

    public $availableRules = [];
    public $selectedRules = [];
    public $productId;
    public $productName;

    public function render()
    {
        return view('restaurante.products.index', [
            'categories' => Category::pluck('name','id')
        ]);
    }

    public function resetInput()
    {
        $this->reset([
            'name',
            'code',
            'category_id',
            'price',
            'description',
            'packaging_cost',
            'is_visible',
            'is_active',
            'image',
            'image_preview'
        ]);

        $this->packaging_cost = 0;
        $this->is_visible = true;
        $this->is_active = true;

        $this->resetValidation();
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

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        Product::create([
            'name' => $this->name,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'description' => $this->description,
            'packaging_cost' => $this->packaging_cost ?? 0,
            'is_visible' => $this->is_visible,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ]);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $this->product_id = $id;
        $this->name = $product->name;
        $this->code = $product->code;
        $this->category_id = $product->category_id;
        $this->price = $product->price;
        $this->description = $product->description;
        $this->packaging_cost = $product->packaging_cost;
        $this->is_visible = $product->is_visible;
        $this->is_active = $product->is_active;
        
        $this->image_preview = $product->image_url;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        $product = Product::findOrFail($this->product_id);

        $imagePath = $product->image;

        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        $product->update([
            'name' => $this->name,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'description' => $this->description,
            'packaging_cost' => $this->packaging_cost ?? 0,
            'is_visible' => $this->is_visible,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ]);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    #[On('product-rules')]
    public function manageRules($id)
    {
        $product = Product::with('rules')
            ->findOrFail($id);

        $this->productId = $product->id;
        $this->productName = $product->name;

        $this->selectedRules = $product->rules
            ->pluck('id')
            ->toArray();

        $this->availableRules = ProductRule::where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->dispatch('open-rules-modal');
    }

    public function saveRules()
    {
        Product::find($this->productId)
            ->rules()
            ->sync($this->selectedRules);

        $this->dispatch('success');

        $this->dispatch('rules-saved');
    }

    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('refreshDatatable');
    }
}
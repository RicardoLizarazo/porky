<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\KitchenStation;
use App\Models\ProductRule;
use App\Models\ProductOption;
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

    #[Validate('boolean')]
    public $allow_manual_price = false;

    // Ahora es un arreglo: un producto puede pertenecer a varias
    // estaciones (ej. una picada que va a Parrilla y a Picadas).
    #[Validate('array')]
    public $kitchen_station_ids = [];

    #[Validate('nullable|image|max:2048')] // 2MB
    public $image;

    public $image_preview;

    public $availableRules = [];
    public $selectedRules = [];
    public $productId;
    public $productName;
    
    public $optionProductId;
    public $optionProductName;
    public $productOptions = [];   // [['id'=>1,'name'=>'Con Dulce']

    public function render()
    {
        return view('restaurante.products.index',[
            'categories' => Category::pluck('name','id'),
            'kitchenStations' => KitchenStation::active()
                ->pluck('name','id'),
        ]);
    }

    public function resetInput()
    {
        $this->reset([
            'name',
            'code',
            'category_id',
            'kitchen_station_ids',
            'price',
            'description',
            'packaging_cost',
            'is_visible',
            'is_active',
            'allow_manual_price',
            'image',
            'image_preview'
        ]);

        $this->kitchen_station_ids = [];
        $this->packaging_cost = 0;
        $this->is_visible = true;
        $this->is_active = true;
        $this->allow_manual_price = false;

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

        $product = Product::create([
            'name' => $this->name,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'description' => $this->description,
            'packaging_cost' => $this->packaging_cost ?? 0,
            'allow_manual_price' => $this->allow_manual_price,
            'is_visible' => $this->is_visible,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ]);

        // Guarda las estaciones marcadas en la tabla pivote
        $product->kitchenStations()->sync($this->kitchen_station_ids);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $product = Product::with('kitchenStations')
            ->findOrFail($id);

        $this->product_id = $id;
        $this->name = $product->name;
        $this->code = $product->code;
        $this->category_id = $product->category_id;
        $this->kitchen_station_ids = $product->kitchenStations
            ->pluck('id')
            ->toArray();
        $this->price = $product->price;
        $this->description = $product->description;
        $this->packaging_cost = $product->packaging_cost;
        $this->allow_manual_price = $product->allow_manual_price;
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
            'allow_manual_price' => $this->allow_manual_price,
            'is_visible' => $this->is_visible,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ]);

        // Reemplaza el set de estaciones por el que quedó marcado
        // (sync borra las que se desmarcaron y agrega las nuevas)
        $product->kitchenStations()->sync($this->kitchen_station_ids);

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
    

    #[On('product-options')]
    public function manageOptions($id)
    {
        $product = Product::findOrFail($id);
    
        $this->optionProductId   = $product->id;
        $this->optionProductName = $product->name;
    
        $this->productOptions = ProductOption::where('product_id', $product->id)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->map(fn ($o) => ['id' => $o->id, 'name' => $o->name])
            ->toArray();
    
        $this->dispatch('open-options-modal');
    }
    
    public function addOptionRow()
    {
        $this->productOptions[] = ['id' => null, 'name' => ''];
    }
    
    public function removeOptionRow($index)
    {
        unset($this->productOptions[$index]);
    
        $this->productOptions = array_values($this->productOptions);
    }
    
    public function saveOptions()
    {
        $keepIds = [];
    
        foreach ($this->productOptions as $i => $row) {
    
            // El pipe es el separador del campo comment en PosOrder:
            // si entra en el nombre, rompe el parseo del empaque.
            $name = trim(str_replace(['|', ','], ' ', $row['name'] ?? ''));
    
            if ($name === '') {
                continue;
            }
    
            if (! empty($row['id'])) {
    
                ProductOption::where('id', $row['id'])
                    ->where('product_id', $this->optionProductId)
                    ->update(['name' => $name, 'sort_order' => $i]);
    
                $keepIds[] = $row['id'];
    
            } else {
    
                $keepIds[] = ProductOption::create([
                    'product_id' => $this->optionProductId,
                    'name'       => $name,
                    'sort_order' => $i,
                    'active'     => true,
                ])->id;
            }
        }
    
        ProductOption::where('product_id', $this->optionProductId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    
        $this->dispatch('success');
        $this->dispatch('options-saved');
    }
}
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Categories extends Component
{
    #[Locked]
    public $category_id;

    #[Validate('required|min:3')]
    public $name;

    #[Validate('nullable')]
    public $description;

    #[Validate('required|boolean')]
    public $is_active = true;

    public function render()
    {
        return view('restaurante.categories.index');
    }

    public function resetInput()
    {
        $this->reset(['name','description','is_active']);
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

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        $this->category_id = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->is_active = $category->is_active;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        Category::findOrFail($this->category_id)->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('refreshDatatable');
    }
}
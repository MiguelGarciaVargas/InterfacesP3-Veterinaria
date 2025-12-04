<?php

namespace App\Livewire\Admin;

use App\Models\AnimalType;
use Livewire\Component;
use Livewire\WithPagination;

class AnimalTypes extends Component
{
    use WithPagination;

    public $animalTypeId = null;
    public $name = '';
    public $image_url = '';
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:animal_types,name',
        'image_url' => 'nullable|url',
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.unique' => 'Ya existe un tipo de animal con ese nombre.',
        'image_url.url' => 'La URL de la imagen no es válida.',
    ];

    public function render()
    {
        $animalTypes = AnimalType::orderBy('name')->paginate(10);

        return view('livewire.admin.animal-types', [
            'animalTypes' => $animalTypes,
        ])->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->reset(['animalTypeId', 'name', 'image_url', 'isEdit']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
    }

    public function edit($id)
    {
        $animalType = AnimalType::findOrFail($id);

        $this->animalTypeId = $animalType->id;
        $this->name = $animalType->name;
        $this->image_url = $animalType->image_url ?? '';
        $this->isEdit = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        // Ajustar regla unique cuando se edita
        $rules = $this->rules;
        if ($this->isEdit && $this->animalTypeId) {
            $rules['name'] = 'required|string|max:255|unique:animal_types,name,' . $this->animalTypeId;
        }

        $this->validate($rules);

        if ($this->isEdit && $this->animalTypeId) {
            $animalType = AnimalType::findOrFail($this->animalTypeId);
            $animalType->update([
                'name' => $this->name,
                'image_url' => $this->image_url ?: null,
            ]);
        } else {
            AnimalType::create([
                'name' => $this->name,
                'image_url' => $this->image_url ?: null,
            ]);
        }

        session()->flash('success', $this->isEdit ? 'Tipo de animal actualizado.' : 'Tipo de animal creado.');

        $this->resetForm();
    }

    public function delete($id)
    {
        $animalType = AnimalType::findOrFail($id);

        if ($animalType->pets()->exists()) {
            session()->flash('error', 'No puedes borrar un tipo que tiene mascotas asociadas.');
            return;
        }

        $animalType->delete();

        session()->flash('success', 'Tipo de animal eliminado.');
    }


}

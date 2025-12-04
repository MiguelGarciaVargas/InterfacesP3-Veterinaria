<?php

namespace App\Livewire\User;

use App\Models\AnimalType;
use App\Models\Pet;
use Livewire\Component;
use Livewire\WithPagination;

class Pets extends Component
{
    use WithPagination;

    public $petId = null;
    public $animal_type_id = '';
    public $name = '';
    public $gender = '';
    public $birth_date = '';
    public $notes = '';
    public $isEdit = false;

    protected $rules = [
        'animal_type_id' => 'required|exists:animal_types,id',
        'name'           => 'required|string|max:255',
        'gender'         => 'nullable|in:male,female',
        'birth_date'     => 'nullable|date',
        'notes'          => 'nullable|string',
    ];

    protected $messages = [
        'animal_type_id.required' => 'Selecciona un tipo de animal.',
        'animal_type_id.exists'   => 'El tipo de animal seleccionado no es válido.',
        'name.required'           => 'El nombre es obligatorio.',
    ];

    public function render()
    {
        $userId = auth()->id();

        $pets = Pet::with('type')
            ->where('user_id', $userId)
            ->orderBy('name')
            ->paginate(10);

        $animalTypes = AnimalType::orderBy('name')->get();

        return view('livewire.user.pets', [
            'pets' => $pets,
            'animalTypes' => $animalTypes,
        ])->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->reset([
            'petId',
            'animal_type_id',
            'name',
            'gender',
            'birth_date',
            'notes',
            'isEdit',
        ]);

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
        $userId = auth()->id();

        $pet = Pet::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $this->petId = $pet->id;
        $this->animal_type_id = $pet->animal_type_id;
        $this->name = $pet->name;
        $this->gender = $pet->gender ?? '';
        $this->birth_date = $pet->birth_date ? $pet->birth_date->format('Y-m-d') : '';
        $this->notes = $pet->notes ?? '';

        $this->isEdit = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $userId = auth()->id();

        if ($this->isEdit && $this->petId) {
            $pet = Pet::where('id', $this->petId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $pet->update([
                'animal_type_id' => $this->animal_type_id,
                'name'           => $this->name,
                'gender'         => $this->gender ?: null,
                'birth_date'     => $this->birth_date ?: null,
                'notes'          => $this->notes ?: null,
            ]);

            session()->flash('success', 'Mascota actualizada.');
        } else {
            Pet::create([
                'user_id'        => $userId,
                'animal_type_id' => $this->animal_type_id,
                'name'           => $this->name,
                'gender'         => $this->gender ?: null,
                'birth_date'     => $this->birth_date ?: null,
                'notes'          => $this->notes ?: null,
            ]);

            session()->flash('success', 'Mascota creada.');
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        $userId = auth()->id();

        $pet = Pet::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($pet->appointments()->exists()) {
            session()->flash('error', 'No puedes borrar una mascota con citas asociadas.');
            return;
        }

        $pet->delete();

        session()->flash('success', 'Mascota eliminada.');
    }
}

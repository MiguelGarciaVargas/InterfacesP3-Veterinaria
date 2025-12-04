<?php

namespace App\Livewire\Admin;

use App\Models\AppointmentSlot;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class AppointmentSlots extends Component
{
    use WithPagination, WithFileUploads;

    public $slotId = null;
    public $starts_at = '';
    public $capacity = 1;
    public $is_active = true;
    public $isEdit = false;

    public $scheduleFile;

    protected $rules = [
        'starts_at' => 'required|date',
        'capacity'  => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'starts_at.required' => 'La fecha y hora son obligatorias.',
        'starts_at.date'     => 'Formato de fecha/hora inválido.',
        'capacity.required'  => 'La capacidad es obligatoria.',
        'capacity.integer'   => 'La capacidad debe ser un número entero.',
        'capacity.min'       => 'La capacidad mínima es 1.',
    ];

    // 🔧 Helper para luego cambiar fácil a auth()->id()
    protected function getCurrentAdminId(): int
    {
        return auth()->id();
    }

    public function render()
    {
        $slots = AppointmentSlot::orderBy('starts_at', 'asc')->paginate(10);

        return view('livewire.admin.appointment-slots', [
            'slots' => $slots,
        ])->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->reset([
            'slotId',
            'starts_at',
            'capacity',
            'is_active',
            'isEdit',
        ]);

        $this->is_active = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;


        $this->starts_at = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
    }

    public function edit($id)
    {
        $slot = AppointmentSlot::findOrFail($id);

        $this->slotId = $slot->id;
        $this->starts_at = $slot->starts_at?->format('Y-m-d\TH:i');
        $this->capacity = $slot->capacity;
        $this->is_active = $slot->is_active;
        $this->isEdit = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $adminId = $this->getCurrentAdminId();

        $startsAt = Carbon::parse($this->starts_at);

        if ($this->isEdit && $this->slotId) {
            $slot = AppointmentSlot::findOrFail($this->slotId);

            if ($this->capacity < $slot->booked) {
                $this->addError('capacity', 'La capacidad no puede ser menor que el número de reservas actuales ('.$slot->booked.').');
                return;
            }

            $slot->update([
                'starts_at' => $startsAt,
                'capacity'  => $this->capacity,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Slot actualizado.');
        } else {
            AppointmentSlot::create([
                'starts_at' => $startsAt,
                'capacity'  => $this->capacity,
                'booked'    => 0,
                'is_active' => $this->is_active,
                'created_by'=> $adminId,
            ]);

            session()->flash('success', 'Slot creado.');
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        $slot = AppointmentSlot::findOrFail($id);

        if ($slot->appointments()->exists()) {
            session()->flash('error', 'No puedes borrar un slot que ya tiene citas.');
            return;
        }

        $slot->delete();

        session()->flash('success', 'Slot eliminado.');
    }

        public function importSchedule()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->validate([
            'scheduleFile' => 'required|file|mimes:csv,txt',
        ], [
            'scheduleFile.required' => 'Debes seleccionar un archivo.',
            'scheduleFile.mimes'    => 'El archivo debe ser CSV.',
        ]);

        $adminId = Auth::id();
        $created = 0;
        $skipped = 0;

        $path = $this->scheduleFile->getRealPath();

        if (! $path) {
            session()->flash('error', 'No se pudo leer el archivo.');
            return;
        }

        if (($handle = fopen($path, 'r')) === false) {
            session()->flash('error', 'No se pudo abrir el archivo.');
            return;
        }

        $isFirstRow = true;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Saltar encabezado
            if ($isFirstRow) {
                $isFirstRow = false;
                // Si quieres que detecte si tiene encabezado, puedes hacer lógica extra aquí
                continue;
            }

            if (count($row) < 2) {
                $skipped++;
                continue;
            }

            [$dateTime, $capacity, $active] = [
                trim($row[0] ?? ''),
                trim($row[1] ?? ''),
                trim($row[2] ?? '1'),
            ];

            if ($dateTime === '' || $capacity === '') {
                $skipped++;
                continue;
            }

            try {
                $startsAt = Carbon::parse($dateTime);
            } catch (\Throwable $e) {
                $skipped++;
                continue;
            }

            $capacityInt = (int) $capacity;
            if ($capacityInt < 1) {
                $skipped++;
                continue;
            }

            $isActive = ($active === '1' || strtolower($active) === 'true');

            // Evitar duplicados por starts_at
            $exists = \App\Models\AppointmentSlot::where('starts_at', $startsAt)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            \App\Models\AppointmentSlot::create([
                'starts_at'  => $startsAt,
                'capacity'   => $capacityInt,
                'booked'     => 0,
                'is_active'  => $isActive,
                'created_by' => $adminId,
            ]);

            $created++;
        }

        fclose($handle);

        $this->scheduleFile = null;

        session()->flash('success', "Importación completada. Creados: {$created}, omitidos: {$skipped}.");
    }

}

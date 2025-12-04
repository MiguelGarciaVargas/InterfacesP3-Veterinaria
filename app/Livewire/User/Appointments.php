<?php

namespace App\Livewire\User;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Pet;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Appointments extends Component
{
    use WithPagination;

    public $pet_id = '';
    public $appointment_slot_id = '';
    public $reason = '';

    protected $rules = [
        'pet_id'             => 'required|exists:pets,id',
        'appointment_slot_id'=> 'required|exists:appointment_slots,id',
        'reason'             => 'nullable|string|max:500',
    ];

    protected $messages = [
        'pet_id.required'              => 'Selecciona una mascota.',
        'pet_id.exists'                => 'La mascota seleccionada no es válida.',
        'appointment_slot_id.required' => 'Selecciona un horario disponible.',
        'appointment_slot_id.exists'   => 'El horario seleccionado no es válido.',
    ];

    protected function getCurrentUserId(): int
    {
        // cuando haya auth, cámbialo por: return auth()->id();
        return auth()->id();
    }

    public function render()
    {
        $userId = $this->getCurrentUserId();

        $pets = Pet::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $availableSlots = AppointmentSlot::where('is_active', true)
            ->where('starts_at', '>=', now())
            ->whereColumn('booked', '<', 'capacity')
            ->orderBy('starts_at')
            ->get();

        $appointments = Appointment::with(['pet', 'slot'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.user.appointments', [
            'pets'           => $pets,
            'availableSlots' => $availableSlots,
            'appointments'   => $appointments,
        ])->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->reset(['pet_id', 'appointment_slot_id', 'reason']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $userId = $this->getCurrentUserId();

        // validar que la mascota pertenece al usuario
        $pet = Pet::where('id', $this->pet_id)
            ->where('user_id', $userId)
            ->first();

        if (! $pet) {
            $this->addError('pet_id', 'Esa mascota no te pertenece.');
            return;
        }

        DB::transaction(function () use ($userId, $pet) {
            $slot = AppointmentSlot::lockForUpdate()
                ->findOrFail($this->appointment_slot_id);

            if (! $slot->isAvailable()) {
                $this->addError('appointment_slot_id', 'Este horario ya no está disponible.');
                return;
            }

            // evitar duplicados mascota+slot
            $exists = Appointment::where('pet_id', $pet->id)
                ->where('appointment_slot_id', $slot->id)
                ->exists();

            if ($exists) {
                $this->addError('appointment_slot_id', 'Ya tienes una cita para esa mascota en este horario.');
                return;
            }

            Appointment::create([
                'user_id'            => $userId,
                'pet_id'             => $pet->id,
                'appointment_slot_id'=> $slot->id,
                'status'             => 'pending',
                'reason'             => $this->reason ?: null,
            ]);

            $slot->increment('booked');
        });

        session()->flash('success', 'Cita creada.');

        $this->resetForm();
    }

    public function cancel($appointmentId)
    {
        $userId = $this->getCurrentUserId();

        $appointment = Appointment::where('id', $appointmentId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($appointment->status === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($appointment) {
            $slot = AppointmentSlot::lockForUpdate()
                ->find($appointment->appointment_slot_id);

            if ($slot && $slot->booked > 0) {
                $slot->decrement('booked');
            }

            $appointment->update([
                'status' => 'cancelled',
            ]);
        });

        session()->flash('success', 'Cita cancelada.');
    }
}

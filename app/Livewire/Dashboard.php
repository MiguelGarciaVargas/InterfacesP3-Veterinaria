<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\AnimalType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $userId = $user->id;
        $isAdmin = $user->role === 'admin';

        // --- Datos para dashboard de USUARIO ---
        $pets = Pet::with('type')
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $upcomingAppointments = Appointment::with(['pet.type', 'slot'])
            ->where('user_id', $userId)
            ->whereHas('slot', function ($q) {
                $q->where('starts_at', '>=', now());
            })
            ->get()
            ->sortBy(fn ($appointment) => $appointment->slot?->starts_at);

        // --- Datos para dashboard de ADMIN ---
        $appointmentsChartLabels = [];
        $appointmentsChartData = [];
        $animalChartLabels = [];
        $animalChartData = [];

        if ($isAdmin) {
            // Citas últimos 30 días (contadas por día)
            $start = now()->subDays(29)->startOfDay();
            $end = now()->endOfDay();

            // inicializar array de fechas
            $dates = [];
            $cursor = $start->copy();
            while ($cursor <= $end) {
                $label = $cursor->format('d/m');
                $dates[$label] = 0;
                $cursor->addDay();
            }

            $appointments = Appointment::with('slot')
                ->whereHas('slot', function ($q) use ($start, $end) {
                    $q->whereBetween('starts_at', [$start, $end]);
                })
                ->get();

            foreach ($appointments as $appointment) {
                if (! $appointment->slot) {
                    continue;
                }

                $label = $appointment->slot->starts_at->format('d/m');
                if (array_key_exists($label, $dates)) {
                    $dates[$label]++;
                }
            }

            $appointmentsChartLabels = array_keys($dates);
            $appointmentsChartData = array_values($dates);

            // Pie chart: cantidad de mascotas por tipo de animal
            $animals = AnimalType::withCount('pets')->get();
            $animalChartLabels = $animals->pluck('name')->toArray();
            $animalChartData = $animals->pluck('pets_count')->toArray();
        }

        return view('livewire.dashboard', [
            'isAdmin'                 => $isAdmin,
            'pets'                    => $pets,
            'upcomingAppointments'    => $upcomingAppointments,
            'appointmentsChartLabels' => $appointmentsChartLabels,
            'appointmentsChartData'   => $appointmentsChartData,
            'animalChartLabels'       => $animalChartLabels,
            'animalChartData'         => $animalChartData,
        ])->layout('layouts.app');
    }
}

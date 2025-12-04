<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-2">Dashboard</h1>
    <p class="mb-6 text-gray-700">
        Hola, {{ auth()->user()->name }} 👋
    </p>

    @if ($isAdmin)
        {{-- DASHBOARD DE ADMIN --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Citas últimos 30 días --}}
            <div class="border rounded-lg bg-white p-4 shadow">
                <h2 class="text-xl font-semibold mb-2">Citas en los últimos 30 días</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Total de citas por día.
                </p>
                <div class="h-64">
                    <canvas id="appointmentsChart"></canvas>
                </div>
            </div>

            {{-- Distribución de mascotas por tipo --}}
            <div class="border rounded-lg bg-white p-4 shadow">
                <h2 class="text-xl font-semibold mb-2">Mascotas por tipo de animal</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Cantidad de mascotas registradas por tipo.
                </p>
                <div class="h-64">
                    <canvas id="animalTypesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Opcional: links rápidos --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.animal-types.index') }}"
               class="border rounded-lg bg-white p-4 shadow hover:shadow-md transition">
                <h3 class="font-semibold mb-1">Tipos de animales</h3>
                <p class="text-sm text-gray-600">
                    Gestionar los tipos disponibles en el sistema.
                </p>
            </a>

            <a href="{{ route('admin.appointment-slots.index') }}"
               class="border rounded-lg bg-white p-4 shadow hover:shadow-md transition">
                <h3 class="font-semibold mb-1">Slots de citas</h3>
                <p class="text-sm text-gray-600">
                    Crear y administrar horarios disponibles.
                </p>
            </a>

            <a href="{{ route('appointments.index') }}"
               class="border rounded-lg bg-white p-4 shadow hover:shadow-md transition">
                <h3 class="font-semibold mb-1">Ver citas de usuario</h3>
                <p class="text-sm text-gray-600">
                    Ver las citas desde la vista de usuario.
                </p>
            </a>
        </div>

        {{-- Chart.js CDN y scripts --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Line chart: citas por día
                const appLabels = @json($appointmentsChartLabels);
                const appData   = @json($appointmentsChartData);

                const ctx1 = document.getElementById('appointmentsChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: appLabels,
                        datasets: [{
                            label: 'Citas',
                            data: appData,
                            fill: false,
                            tension: 0.2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0,
                            }
                        }
                    }
                });

                // Pie chart: mascotas por tipo
                const animalLabels = @json($animalChartLabels);
                const animalData   = @json($animalChartData);

                const ctx2 = document.getElementById('animalTypesChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: animalLabels,
                        datasets: [{
                            data: animalData,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            });
        </script>
    @else
        {{-- DASHBOARD DE USUARIO --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Tabla de mascotas --}}
            <div class="border rounded-lg bg-white p-4 shadow">
                <h2 class="text-xl font-semibold mb-4">Mis mascotas</h2>

                @if ($pets->isEmpty())
                    <p class="text-sm text-gray-500 mb-2">
                        No tienes mascotas registradas aún.
                    </p>
                    <a href="{{ route('pets.index') }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                        Ir a gestionar mascotas
                    </a>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-1">Nombre</th>
                                <th class="text-left py-2 px-1">Tipo</th>
                                <th class="text-left py-2 px-1">Género</th>
                                <th class="text-left py-2 px-1">Nacimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pets as $pet)
                                <tr class="border-b">
                                    <td class="py-2 px-1 font-medium">
                                        {{ $pet->name }}
                                    </td>
                                    <td class="py-2 px-1">
                                        {{ $pet->type?->name ?? '-' }}
                                    </td>
                                    <td class="py-2 px-1">
                                        @if ($pet->gender === 'male')
                                            Macho
                                        @elseif ($pet->gender === 'female')
                                            Hembra
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-1">
                                        {{ $pet->birth_date ? $pet->birth_date->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('pets.index') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                            Ver / gestionar todas las mascotas
                        </a>
                    </div>
                @endif
            </div>

            {{-- Tabla de citas futuras --}}
            <div class="border rounded-lg bg-white p-4 shadow">
                <h2 class="text-xl font-semibold mb-4">Próximas citas</h2>

                @if ($upcomingAppointments->isEmpty())
                    <p class="text-sm text-gray-500 mb-2">
                        No tienes citas futuras.
                    </p>
                    <a href="{{ route('appointments.index') }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                        Agendar una cita
                    </a>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-1">Fecha y hora</th>
                                <th class="text-left py-2 px-1">Mascota</th>
                                <th class="text-left py-2 px-1">Tipo</th>
                                <th class="text-left py-2 px-1">Estado</th>
                                <th class="text-left py-2 px-1">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upcomingAppointments as $appointment)
                                <tr class="border-b">
                                    <td class="py-2 px-1">
                                        {{ $appointment->slot?->starts_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="py-2 px-1">
                                        {{ $appointment->pet?->name ?? '-' }}
                                    </td>
                                    <td class="py-2 px-1">
                                        {{ $appointment->pet?->type?->name ?? '-' }}
                                    </td>
                                    <td class="py-2 px-1">
                                        @php
                                            $status = $appointment->status;
                                            $labelClass = match ($status) {
                                                'pending'   => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default     => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="text-xs px-2 py-1 rounded {{ $labelClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-1">
                                        <span class="text-xs text-gray-700">
                                            {{ $appointment->reason ? (strlen($appointment->reason) > 40 ? substr($appointment->reason, 0, 40).'...' : $appointment->reason) : '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('appointments.index') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                            Ver / gestionar todas las citas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

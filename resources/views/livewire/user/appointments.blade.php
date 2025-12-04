<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Mis citas</h1>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulario nueva cita --}}
    <div class="mb-8 border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-4">Nueva cita</h2>

        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Mascota
                    </label>
                    <select wire:model.defer="pet_id"
                            class="w-full border rounded px-3 py-2">
                        <option value="">-- Selecciona --</option>
                        @foreach ($pets as $pet)
                            <option value="{{ $pet->id }}">
                                {{ $pet->name }} ({{ $pet->type?->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('pet_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Horario disponible
                    </label>
                    <select wire:model.defer="appointment_slot_id"
                            class="w-full border rounded px-3 py-2">
                        <option value="">-- Selecciona --</option>
                        @foreach ($availableSlots as $slot)
                            <option value="{{ $slot->id }}">
                                {{ $slot->starts_at?->format('d/m/Y H:i') }}
                                ({{ $slot->capacity - $slot->booked }} disponibles)
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_slot_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Motivo (opcional)
                </label>
                <textarea wire:model.defer="reason"
                          rows="3"
                          class="w-full border rounded px-3 py-2"></textarea>
                @error('reason')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                Crear cita
            </button>
        </form>
    </div>

    {{-- Listado de citas --}}
    <div class="border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-4">Historial de citas</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-1">Fecha</th>
                    <th class="text-left py-2 px-1">Mascota</th>
                    <th class="text-left py-2 px-1">Tipo</th>
                    <th class="text-left py-2 px-1">Estado</th>
                    <th class="text-left py-2 px-1">Motivo</th>
                    <th class="text-right py-2 px-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
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
                                {{ \Illuminate\Support\Str::limit($appointment->reason, 40) }}
                            </span>
                        </td>
                        <td class="py-2 px-1 text-right">
                            @if ($appointment->status !== 'cancelled')
                                <button wire:click="cancel({{ $appointment->id }})"
                                        onclick="return confirm('¿Cancelar esta cita?')"
                                        class="text-red-600 hover:underline text-sm">
                                    Cancelar
                                </button>
                            @else
                                <span class="text-xs text-gray-400">Sin acciones</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">
                            No tienes citas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </div>
</div>

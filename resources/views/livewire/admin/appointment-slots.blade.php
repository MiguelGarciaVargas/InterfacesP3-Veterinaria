<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Slots de citas</h1>

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

        {{-- Importar horarios desde CSV --}}
    <div class="mb-8 border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-2">Importar horarios desde Excel (CSV)</h2>
        <p class="text-sm text-gray-600 mb-3">
            Sube un archivo CSV exportado desde Excel con las columnas:
            <code>date_time, capacity, is_active</code>.
        </p>
        <p class="text-xs text-gray-500 mb-3">
            Ejemplo:
            <br>
            <code>2025-12-01 10:00,1,1</code><br>
            <code>2025-12-01 11:00,2,1</code>
        </p>

        <form wire:submit.prevent="importSchedule" class="space-y-3">
            <div>
                <input type="file"
                       wire:model="scheduleFile"
                       accept=".csv"
                       class="block w-full text-sm text-gray-700 border rounded px-3 py-2">
                @error('scheduleFile')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                    Importar horarios
                </button>

                @if ($scheduleFile)
                    <span class="text-xs text-gray-500">
                        Archivo seleccionado: {{ $scheduleFile->getClientOriginalName() }}
                    </span>
                @endif
            </div>
        </form>
    </div>


    {{-- Formulario --}}
    <div class="mb-8 border rounded-lg bg-white p-4 shadow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">
                {{ $isEdit ? 'Editar slot' : 'Nuevo slot' }}
            </h2>
            @unless($isEdit)
                <button type="button"
                        wire:click="create"
                        class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-sm">
                    Nuevo slot rápido
                </button>
            @endunless
        </div>

        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Fecha y hora
                    </label>
                    <input type="datetime-local"
                           wire:model.defer="starts_at"
                           class="w-full border rounded px-3 py-2">
                    @error('starts_at')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Capacidad
                    </label>
                    <input type="number"
                           min="1"
                           wire:model.defer="capacity"
                           class="w-full border rounded px-3 py-2">
                    @error('capacity')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-2">
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox"
                           wire:model.defer="is_active"
                           class="mr-2">
                    Activo
                </label>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    {{ $isEdit ? 'Guardar cambios' : 'Crear slot' }}
                </button>

                @if ($isEdit)
                    <button type="button"
                            wire:click="create"
                            class="px-3 py-2 rounded border bg-gray-100 hover:bg-gray-200 text-sm">
                        Cancelar edición
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-4">Listado de slots</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-1">Fecha y hora</th>
                    <th class="text-left py-2 px-1">Capacidad</th>
                    <th class="text-left py-2 px-1">Reservadas</th>
                    <th class="text-left py-2 px-1">Activo</th>
                    <th class="text-right py-2 px-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($slots as $slot)
                    <tr class="border-b">
                        <td class="py-2 px-1">
                            {{ $slot->starts_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-2 px-1">{{ $slot->capacity }}</td>
                        <td class="py-2 px-1">{{ $slot->booked }}</td>
                        <td class="py-2 px-1">
                            @if ($slot->is_active)
                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">
                                    Sí
                                </span>
                            @else
                                <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">
                                    No
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-1 text-right">
                            <button wire:click="edit({{ $slot->id }})"
                                    class="text-blue-600 hover:underline text-sm mr-2">
                                Editar
                            </button>
                            <button wire:click="delete({{ $slot->id }})"
                                    onclick="return confirm('¿Seguro que quieres eliminar este slot?')"
                                    class="text-red-600 hover:underline text-sm">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">
                            No hay slots registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $slots->links() }}
        </div>
    </div>
</div>

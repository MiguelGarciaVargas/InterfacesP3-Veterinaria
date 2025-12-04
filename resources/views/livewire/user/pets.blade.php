<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Mis mascotas</h1>

    {{-- Mensajes --}}
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

    {{-- Formulario --}}
    <div class="mb-8 border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-4">
            {{ $isEdit ? 'Editar mascota' : 'Nueva mascota' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tipo de animal</label>
                <select wire:model.defer="animal_type_id"
                        class="w-full border rounded px-3 py-2">
                    <option value="">-- Selecciona --</option>
                    @foreach ($animalTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('animal_type_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text"
                       wire:model.defer="name"
                       class="w-full border rounded px-3 py-2">
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Género</label>
                    <select wire:model.defer="gender"
                            class="w-full border rounded px-3 py-2">
                        <option value="">-- Sin especificar --</option>
                        <option value="male">Macho</option>
                        <option value="female">Hembra</option>
                    </select>
                    @error('gender')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de nacimiento</label>
                    <input type="date"
                           wire:model.defer="birth_date"
                           class="w-full border rounded px-3 py-2">
                    @error('birth_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Notas</label>
                <textarea wire:model.defer="notes"
                          rows="3"
                          class="w-full border rounded px-3 py-2"></textarea>
                @error('notes')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    {{ $isEdit ? 'Guardar cambios' : 'Crear mascota' }}
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

    {{-- Listado --}}
    <div class="border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-4">Listado de mascotas</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-1">Nombre</th>
                    <th class="text-left py-2 px-1">Tipo</th>
                    <th class="text-left py-2 px-1">Género</th>
                    <th class="text-left py-2 px-1">Nacimiento</th>
                    <th class="text-left py-2 px-1">Notas</th>
                    <th class="text-right py-2 px-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pets as $pet)
                    <tr class="border-b">
                        <td class="py-2 px-1 font-medium">{{ $pet->name }}</td>
                        <td class="py-2 px-1">{{ $pet->type?->name }}</td>
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
                        <td class="py-2 px-1">
                            <span class="text-xs text-gray-700">
                                {{ Str::limit($pet->notes, 40) }}
                            </span>
                        </td>
                        <td class="py-2 px-1 text-right">
                            <button wire:click="edit({{ $pet->id }})"
                                    class="text-blue-600 hover:underline text-sm mr-2">
                                Editar
                            </button>

                            <button wire:click="delete({{ $pet->id }})"
                                    onclick="return confirm('¿Seguro que quieres eliminar esta mascota?')"
                                    class="text-red-600 hover:underline text-sm">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">
                            No tienes mascotas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $pets->links() }}
        </div>
    </div>
</div>

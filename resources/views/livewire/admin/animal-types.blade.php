<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Tipos de animales</h1>

    {{-- Mensajes de sesión --}}
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
            {{ $isEdit ? 'Editar tipo de animal' : 'Nuevo tipo de animal' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text"
                       wire:model.defer="name"
                       class="w-full border rounded px-3 py-2">
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">URL de imagen</label>
                <div class="flex gap-2">
                    <input type="text"
                           wire:model.defer="image_url"
                           class="flex-1 border rounded px-3 py-2">

                </div>
                @error('image_url')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror

                @if ($image_url)
                    <div class="mt-3">
                        <p class="text-sm text-gray-600 mb-1">Preview:</p>
                        <img src="{{ $image_url }}" alt="Preview"
                             class="w-64 h-40 object-cover rounded border">
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    {{ $isEdit ? 'Guardar cambios' : 'Crear' }}
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

    {{-- Tabla listado --}}
    <div class="border rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-semibold mb-4">Lista</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-1">ID</th>
                    <th class="text-left py-2 px-1">Nombre</th>
                    <th class="text-left py-2 px-1">Imagen</th>
                    <th class="text-right py-2 px-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($animalTypes as $type)
                    <tr class="border-b">
                        <td class="py-2 px-1">{{ $type->id }}</td>
                        <td class="py-2 px-1">{{ $type->name }}</td>
                        <td class="py-2 px-1">
                            @if ($type->image_url)
                                <img src="{{ $type->image_url }}"
                                     alt="{{ $type->name }}"
                                     class="w-16 h-12 object-cover rounded border">
                            @else
                                <span class="text-gray-400 text-xs">Sin imagen</span>
                            @endif
                        </td>
                        <td class="py-2 px-1 text-right">
                            <button wire:click="edit({{ $type->id }})"
                                    class="text-blue-600 hover:underline text-sm mr-2">
                                Editar
                            </button>
                            <button wire:click="delete({{ $type->id }})"
                                    onclick="return confirm('¿Seguro que quieres eliminar este tipo?')"
                                    class="text-red-600 hover:underline text-sm">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">
                            No hay tipos de animales registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $animalTypes->links() }}
        </div>
    </div>
</div>

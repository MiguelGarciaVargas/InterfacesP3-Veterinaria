<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Livewire components
use App\Livewire\Dashboard;
use App\Livewire\Admin\AnimalTypes;
use App\Livewire\Admin\AppointmentSlots;
use App\Livewire\User\Pets as UserPets;
use App\Livewire\User\Appointments as UserAppointments;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified']);

// Rutas de usuario 
Route::middleware('auth')->group(function () {

    // CRUD de mascotas del usuario
    Route::get('/pets', UserPets::class)
        ->name('pets.index');

    // Citas del usuario (crear / cancelar)
    Route::get('/appointments', UserAppointments::class)
        ->name('appointments.index');

    // Rutas de Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de admin 
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // CRUD tipos de animales
        Route::get('/animal-types', AnimalTypes::class)
            ->name('animal-types.index');

        // CRUD slots de citas
        Route::get('/appointment-slots', AppointmentSlots::class)
            ->name('appointment-slots.index');
    });

require __DIR__.'/auth.php';

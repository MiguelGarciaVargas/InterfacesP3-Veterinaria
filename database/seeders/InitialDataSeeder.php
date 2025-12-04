<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AnimalType;
use App\Models\Pet;
use App\Models\AppointmentSlot;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Usuarios
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'), // contraseña: password
        ]);

        $user = User::create([
            'name' => 'Cliente Demo',
            'email' => 'user@example.com',
            'role' => 'user',
            'password' => Hash::make('password'), // contraseña: password
        ]);

        // 2) Tipos de animales
        $dog = AnimalType::create([
            'name' => 'Dog',
            'image_url' => 'https://source.unsplash.com/600x400/?dog',
        ]);

        $cat = AnimalType::create([
            'name' => 'Cat',
            'image_url' => 'https://source.unsplash.com/600x400/?cat',
        ]);

        $rabbit = AnimalType::create([
            'name' => 'Rabbit',
            'image_url' => 'https://source.unsplash.com/600x400/?rabbit',
        ]);

        // 3) Mascotas del usuario normal
        $firulais = Pet::create([
            'user_id' => $user->id,
            'animal_type_id' => $dog->id,
            'name' => 'Firulais',
            'gender' => 'male',
            'birth_date' => '2022-01-10',
            'notes' => 'Perro muy juguetón',
        ]);

        $michi = Pet::create([
            'user_id' => $user->id,
            'animal_type_id' => $cat->id,
            'name' => 'Michi',
            'gender' => 'female',
            'birth_date' => '2023-05-20',
            'notes' => 'Le gusta dormir todo el día',
        ]);

        // 4) Slots de citas (creados por el admin)
        $slot1 = AppointmentSlot::create([
            'starts_at' => Carbon::now()->addDay()->setTime(10, 0), // mañana 10:00
            'capacity' => 1,
            'booked' => 0,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $slot2 = AppointmentSlot::create([
            'starts_at' => Carbon::now()->addDay()->setTime(11, 0),
            'capacity' => 1,
            'booked' => 0,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // 5) Una cita de ejemplo (Firulais en el primer slot)
        Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $firulais->id,
            'appointment_slot_id' => $slot1->id,
            'status' => 'pending',
            'reason' => 'Vacunas',
        ]);

        // Actualizamos booked del slot
        $slot1->increment('booked');
    }
}

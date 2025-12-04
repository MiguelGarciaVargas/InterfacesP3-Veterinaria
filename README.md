# 🐾 Vet System – Laravel + Livewire

Sistema de veterinaria construido con **Laravel 11**, **Livewire** y **Breeze**, con gestión de:

- Roles de usuario (`admin`, `user`)
- Tipos de animales
- Mascotas
- Horarios de citas (slots)
- Citas de las mascotas
- Dashboard dinámico (distinto para admin y usuario)
- Importación masiva de horarios desde CSV

## ✅ Requerimientos

- PHP **8.3+**
- Composer
- Node.js + npm
- MySQL / MariaDB
- Laravel 11

## ⚙️ Instalación

```bash
git clone <url-del-repo>
cd vet-system
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env`:

```env
DB_DATABASE=vet_system
DB_USERNAME=root
DB_PASSWORD=
```

Luego:

```bash
php artisan migrate --seed
npm run dev
php artisan serve
```

## 🗃️ Usuarios generados por el seeder

**Admin:**
- email: `admin@example.com`
- password: `password`
- role: `admin`

**Usuario normal:**
- email: `user@example.com`
- password: `password`
- role: `user`

## 🔐 Autenticación

- Login → `/login`
- Registro → `/register`

## 🧭 Rutas principales

### Usuario
- `/` → Dashboard (mascotas + próximas citas)
- `/pets` → CRUD mascotas
- `/appointments` → gestión de citas

### Admin
- `/admin/animal-types` → CRUD tipos de animales
- `/admin/appointment-slots` → CRUD + importación CSV
- `/` → Dashboard admin con gráficas

## 📊 Dashboard Admin

- Gráfica de línea: citas últimos 30 días
- Gráfica de pastel: mascotas por tipo
- Accesos rápidos a módulos

## 📥 Importación CSV de horarios

Ejemplo de archivo:

```csv
date_time,capacity,is_active
2025-12-01 10:00,1,1
2025-12-01 11:00,1,1
2025-12-01 12:00,2,1
2025-12-02 09:00,1,1
2025-12-02 10:30,1,1
2025-12-03 16:00,1,0
```

## 🧩 Tecnologías

- Laravel 11
- Livewire
- Laravel Breeze
- TailwindCSS
- Chart.js

## 🚀 Correr el proyecto

```bash
npm run dev
php artisan serve
```

Abrir: http://127.0.0.1:8000

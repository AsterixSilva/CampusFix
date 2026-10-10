<<<<<<< HEAD
# CampusFix - Authentication & Authorization System

Sistem AUTH & USER dengan role-based authorization untuk CampusFix.

## Struktur Proyek

### Folder Utama

```
├── app/
│   ├── Models/
│   │   └── User.php                 # Model User dengan role constants
│   ├── Policies/
│   │   ├── UserPolicy.php           # Authorization untuk User
│   │   ├── MemberPolicy.php         # Member tidak boleh akses admin
│   │   ├── TechnicianPolicy.php     # Technician hanya lihat pekerjaan sesuai kewenangannya
│   │   ├── CoordinatorPolicy.php    # Coordinator dapat verifikasi & assignment
│   │   ├── AdminPolicy.php          # Admin dapat lihat data sesuai scope
│   │   └── SuperAdminPolicy.php     # Super Admin akses konfigurasi sistem
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── Admin/UserController.php
│   │   │   ├── Admin/SystemController.php
│   │   │   ├── Coordinator/AssignmentController.php
│   │   │   ├── Technician/WorkController.php
│   │   │   └── Member/ProfileController.php
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php      # Middleware role-based
│   │       ├── AdminMiddleware.php     # Admin access protection
│   │       ├── SuperAdminMiddleware.php # Super Admin access protection
│   │       ├── TechnicianMiddleware.php # Technician work access
│   │       └── MemberMiddleware.php    # Member area protection
│   └── Providers/
│       └── AuthServiceProvider.php    # Registrasi policy
├── database/
│   └── migrations/
│       └── 2024_10_10_000000_create_users_table.php
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── dashboards/
│   │   │   ├── member.blade.php
│   │   │   ├── technician.blade.php
│   │   │   ├── coordinator.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── superadmin.blade.php
│   │   └── admin/
│   │       ├── dashboard.blade.php
│   │       └── users/
│   │           ├── index.blade.php
│   │           └── create.blade.php
│   └── ...
├── routes/
│   └── web.php                        # Route definitions
├── config/
│   └── auth_roles.php                 # Role configuration
├── composer.json
└── README.md
```

## Roles & Authorization

### Member
- **Deskripsi**: Akses dasar, tidak boleh mengakses halaman admin
- **Akses yang Diizinkan**: 
  - Lihat profile sendiri
  - Edit profile sendiri
- **Dilarang**: Akses admin, semua halaman manajemen

### Technician
- **Deskripsi**: Hanya boleh melihat pekerjaan yang sesuai kewenangannya
- **Akses yang Diizinkan**:
  - Lihat dashboard technician
  - Lihat pekerjaan yang ditugaskan
  - Tandai pekerjaan selesai

### Coordinator
- **Deskripsi**: Dapat memverifikasi dan melakukan assignment sesuai scope
- **Akses yang Diizinkan**:
  - Lihat dashboard coordinator
  - Verifikasi assignment
  - Buat assignment untuk technician/member

### Admin
- **Deskripsi**: Dapat melihat data sesuai scope
- **Akses yang Diizinkan**:
  - Dashboard admin
  - Kelola user
  - Lihat semua data (kecuali Super Admin)

### Super Admin
- **Deskripsi**: Akses konfigurasi sistem
- **Akses yang Diizinkan**:
  - Dashboard super admin
  - Konfigurasi sistem
  - Kelola semua user (termasuk role lainnya)
  - Kelola peran

## Cara Penggunaan

### 1. Login
- Buka `/login`
- Masukkan email dan kata sandi

### 2. Registrasi
- Buka `/register`
- Pilih role yang diinginkan
- Form otomatis mengarahkan ke dashboard sesuai role

### 3. Akses Dashboard
Berdasarkan role:
- Member: `/member/dashboard`
- Technician: `/technician/dashboard`
- Coordinator: `/coordinator/dashboard`
- Admin: `/admin/dashboard`
- Super Admin: `/superadmin/dashboard`

## Middleware

- `role` - Middleware utama untuk cek role
- `admin` - Hanya untuk Admin/Super Admin
- `superadmin` - Hanya untuk Super Admin
- `technician` - Hanya untuk Technician/Admin/Super Admin
- `member` - Hanya untuk Member

## Authorization dengan Policy

Semua controller menggunakan Policy untuk authorization:

```php
// Contoh penggunaan di controller
public function index(Request $request)
{
    $this->authorize('viewAny', User::class);
    // ...
}
```

## Git Integration

Folder ini mencakup skrip untuk integrasi Git:

- Branch naming convention: `feature/{role}-xxx`, `fix/{role}-xxx`, `hotfix/xxx`
- Pull Request: Harus review oleh anggota lain
- Merge: Gunakan `--no-ff` untuk commit merge
- Conflict resolution: Selalu cek policy authorization

## Penyimpanan File

- **Database migrations**: `database/migrations/`
- **Controller**: `app/Http/Controllers/`
- **Policy**: `app/Policies/`
- **Middleware**: `app/Http/Middleware/`
- **View**: `resources/views/`
- **Routes**: `routes/web.php`
- **Config**: `config/auth_roles.php`

## Lingkungan Pengembangan

```bash
# Install dependencies
composer install

# Buat file .env
cp .env.example .env

# Generate key
php artisan key:generate

# Migration
php artisan migrate

# Run server
php artisan serve
```

## API Endpoints

### Authentication
- `POST /login` - Login
- `POST /logout` - Logout
- `POST /register` - Registrasi

### Protected Routes
- `GET /member/dashboard` - Dashboard Member
- `GET /technician/dashboard` - Dashboard Technician
- `GET /coordinator/dashboard` - Dashboard Coordinator
- `GET /admin/dashboard` - Dashboard Admin
- `GET /superadmin/dashboard` - Dashboard Super Admin

## Catatan untuk Anggota Tim

1. **Jangan ubah struktur database** tanpa konsensus
2. **Selalu gunakan Policy** untuk authorization, bukan inline checks
3. **Test role access** sebelum push
4. **Gunakan branch yang sesuai** dengan role yang diubah
5. **Review code** sebelum merge ke main

---
*Created for CampusFix - Team Member 1*
=======
# CampusFix

CampusFix tracks campus facility issues from report through verification, technician work, resolution, and reporter confirmation.

## Stack

- Laravel and PHP 8.3+
- MySQL 8
- Blade, Livewire, Alpine.js, Tailwind CSS
- Eloquent, Laravel Policies, Notifications, and Scheduler
- Database queue for development

## Issue lifecycle

`reported → verified → assigned → in_progress → resolved → closed`

Additional states are `rejected`, `on_hold`, `reopened`, and `merged`. Status changes are handled through the issue workflow service and recorded in append-only status history.

## Team workflow

`main` is the integration branch. Each member develops their assigned work on a separate branch for later integration.
>>>>>>> a340c0414c99d07e0b12ed42e0575b251605283e

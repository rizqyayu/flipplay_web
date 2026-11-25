# FlipPlay REST API — Demo Modul 4 (Laravel 12)

Pengembangan lanjut proyek **FlipPlay** dari simulasi OOP berbasis terminal (Modul 3) menjadi **REST API menggunakan Laravel 12** dengan database relasional, Eloquent ORM, Form Request Validation, API Resource, dan Custom Action relasi Many-to-Many (`belongsToMany`).

## Entitas & Relasi
- **`Media`** (`media`): `id`, `title` (unik), `category`, `genre`, `description`, `episodes`, `format`
- **`User`** (`users`): `id`, `name`, `email` (unik), `password`
- **Pivot Table (`media_user`)**: `media_id`, `user_id` (`belongsToMany`)

## Setup Awal
1. Pastikan PHP >= 8.2, Composer, dan MySQL (Laragon/XAMPP) sudah terpasang dan berjalan.
2. Buat database MySQL bernama `flipplay_db` (atau sesuaikan `DB_DATABASE` di `.env`).
3. Jika belum ada `vendor/`, jalankan:
   ```bash
   composer install
   ```
4. Pastikan `.env` sudah sesuai lalu jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
5. Cek seluruh daftar route API:
   ```bash
   php artisan route:list
   ```
6. Jalankan server development:
   ```bash
   php artisan serve
   ```
7. Jalankan automated feature test:
   ```bash
   php artisan test
   ```

## Daftar Endpoint API

### Media (CRUD + Filter Judul)
| Operasi | Method | Endpoint | Status Code |
|---|---|---|---|
| Daftar seluruh media (+ filter `?title=...`) | `GET` | `/api/media` | `200` |
| Detail satu media (+ daftar `users`) | `GET` | `/api/media/{media}` | `200` / `404` |
| Tambah media baru | `POST` | `/api/media` | `201` / `422` |
| Ubah data media | `PATCH` | `/api/media/{media}` | `200` / `422` / `404` |
| Hapus media | `DELETE` | `/api/media/{media}` | `200` / `404` |

### Pengguna & Koleksi Media (Custom Action)
| Operasi | Method | Endpoint | Status Code |
|---|---|---|---|
| Daftar seluruh pengguna (+ koleksi `media`) | `GET` | `/api/users` | `200` |
| Detail pengguna (+ koleksi `media`) | `GET` | `/api/users/{user}` | `200` / `404` |
| Tambah pengguna baru | `POST` | `/api/users` | `201` / `422` |
| Ubah data pengguna | `PATCH` | `/api/users/{user}` | `200` / `422` / `404` |
| Hapus pengguna | `DELETE` | `/api/users/{user}` | `200` / `404` |
| Tampilkan koleksi media milik pengguna | `GET` | `/api/users/{user}/collection` | `200` / `404` |
| Tambah media ke koleksi (`attach`) | `POST` | `/api/users/{user}/collection` | `201` / `409` / `422` |
| Hapus media dari koleksi (`detach`) | `DELETE` | `/api/users/{user}/collection/{media}` | `200` / `404` |

## Pengujian Postman
Import file `FlipPlay_Demo_Modul4.postman_collection.json` ke Postman untuk langsung menguji seluruh skenario **Happy Path** dan **Failure Path** (`422`, `404`, `409`).

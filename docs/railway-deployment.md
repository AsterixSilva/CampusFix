# Deploy CampusFix ke Railway

Repository ini tidak memerlukan TiDB. Aplikasi memakai driver Laravel `mysql`, yang juga digunakan untuk Railway MySQL. `.env.example` tetap berisi nilai MySQL lokal untuk development; kredensial produksi diatur sebagai Railway Variables dan tidak disimpan di Git.

## 1. Siapkan Railway MySQL

1. Buat project Railway dan tambahkan service **MySQL**.
2. Biarkan database berada di private network project. Jangan menambahkan TCP Proxy kecuali memang perlu mengakses database dari luar Railway.
3. Catat nama service MySQL karena nama itu dipakai pada variable reference di langkah berikutnya.

## 2. Deploy service aplikasi

1. Tambahkan service dari repository GitHub `AsterixSilva/CampusFix`, branch `main`.
2. Biarkan builder Railpack mendeteksi Laravel. Laravel akan dijalankan melalui PHP-FPM dan Caddy.
3. Atur **Build Command** menjadi:

   ```sh
   npm run build && php artisan storage:link
   ```

4. Atur **Pre-Deploy Command** menjadi:

   ```sh
   sh railway/init-app.sh
   ```

   Script tersebut menjalankan migrasi dan seeder idempotent. Seeder membuat kategori/lokasi/team awal, pesan informasi awal, serta super admin hanya bila email dan password awal diberikan.

5. Generate domain publik di **Settings → Networking** setelah deploy berhasil. Salin URL HTTPS hasilnya ke `APP_URL`.

## 3. Atur Variables pada service aplikasi

Isi variables berikut di Railway. Ganti `MySQL` pada reference dengan nama service database yang sebenarnya.

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<domain-yang-dibuat-railway>
APP_KEY=<kunci-aplikasi-laravel>
DB_CONNECTION=mysql
DB_URL=${{MySQL.MYSQL_URL}}
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
LOG_CHANNEL=stderr
```

Buat `APP_KEY` dengan `php artisan key:generate --show`, lalu simpan nilainya sebagai secret di Railway. Jika aplikasi sebelumnya sudah online, pertahankan `APP_KEY` yang sedang dipakai agar data terenkripsi lama tetap dapat dibaca.

Untuk database yang benar-benar baru, super admin awal dapat dibuat dengan variables berikut. Password harus minimal 12 karakter. Jangan commit password ke repository; setelah akun berhasil dibuat, hapus variable password dari Railway. Jika data user lama akan dipindahkan, jangan membuat akun awal memakai email yang sudah terpakai oleh akun non-super-admin.

```dotenv
CAMPUSFIX_SUPER_ADMIN_NAME=CampusFix UNIMED Super Admin
CAMPUSFIX_SUPER_ADMIN_EMAIL=<email-admin>
CAMPUSFIX_SUPER_ADMIN_PASSWORD=<password-minimal-12-karakter>
```

## 4. Simpan foto laporan secara persisten

Filesystem container Railway bersifat sementara. Tambahkan volume ke service aplikasi dan mount pada direktori `storage/app` milik service, biasanya `/app/storage/app` saat root directory Railpack adalah `/app`. Pastikan mount path sesuai root directory yang terlihat di pengaturan service. Ini menyimpan foto laporan dan foto laporan tamu melewati redeploy.

## 5. Jalankan antrean dan scheduler

Notifikasi menggunakan database queue, dan aplikasi memiliki tugas terjadwal untuk membersihkan laporan tamu kedaluwarsa. Buat dua service tambahan dari repository dan branch yang sama:

- **Worker**: gunakan start command `sh railway/run-worker.sh`.
- **Cron**: gunakan start command `sh railway/run-cron.sh`, lalu atur cron schedule `* * * * *`.

Salin variables koneksi database, `APP_KEY`, dan konfigurasi Laravel yang diperlukan ke kedua service tersebut. Jangan generate domain publik untuk worker atau cron.

## 6. Pindahkan data lama dan lepaskan TiDB

Migrasi Laravel membuat schema kosong; migrasi tidak menyalin data dari TiDB. Sebelum menghapus service TiDB atau credential-nya, buat backup/export database lama, impor data ke Railway MySQL, lalu periksa login, laporan, assignment, status history, dan foto. Setelah aplikasi Railway terverifikasi memakai `DB_URL` Railway MySQL, hapus variables TiDB dan service TiDB lama dari project Railway.

Repository ini tidak berisi nama provider TiDB atau kredensial database produksi. Periksa juga Variables di Railway dan file `.env` lokal yang diabaikan Git; keduanya tidak dapat dibersihkan lewat perubahan repository ini.

## Checklist deploy

- [ ] MySQL service aktif dan private.
- [ ] App service berhasil build dan health check `/up` merespons sukses.
- [ ] `DB_URL` mereferensikan Railway MySQL, bukan endpoint TiDB.
- [ ] Migrations dan seeder selesai tanpa error.
- [ ] Foto tetap dapat dibuka setelah redeploy.
- [ ] Worker memproses job dan cron berjalan setiap menit.
- [ ] Backup TiDB tersedia sebelum service lama dihapus.


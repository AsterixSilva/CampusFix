# CampusFix

CampusFix adalah platform pelaporan dan penyelesaian masalah fasilitas kampus. Mahasiswa dapat melaporkan kerusakan fasilitas, menyertakan lokasi dan foto, serta memberikan informasi mengenai tingkat urgensi masalah.

## Fitur Utama

* Pelaporan masalah fasilitas kampus.
* Pemilihan kategori dan lokasi secara bertingkat.
* Unggah foto sebagai bukti laporan.
* Penandaan masalah keselamatan dan kegiatan kelas yang terganggu.
* Pemantauan status serta penyelesaian laporan.

## Teknologi

* Laravel
* PHP
* MySQL
* Livewire
* HTML, CSS, dan JavaScript

## Persiapan Proyek

1. Clone repository CampusFix.
2. Jalankan `composer install`.
3. Salin `.env.example` menjadi `.env`.
4. Sesuaikan konfigurasi database pada `.env`.
5. Jalankan `php artisan key:generate`.
6. Jalankan `php artisan migrate --seed`.
7. Jalankan `php artisan storage:link`.
8. Jalankan `npm install` dan `npm run build`.
9. Jalankan `php artisan serve`.

## Struktur Pengembangan

Proyek dikembangkan secara berkelompok dengan pembagian tugas autentikasi, pelaporan dan lokasi, alur penyelesaian laporan, serta dashboard dan notifikasi.

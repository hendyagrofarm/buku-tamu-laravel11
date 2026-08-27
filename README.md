# Buku Tamu Digital — Laravel 11

Starter project buku tamu yang dibuat untuk:

- PHP 8.2 atau lebih baru
- Laravel 11
- MySQL/MariaDB
- Tampilan tablet/kiosk yang responsif seperti aplikasi ponsel
- Dashboard admin/resepsionis
- CRUD divisi dan pegawai
- Pendaftaran kunjungan, check-in, pencarian, dan check-out
- PWA sederhana agar dapat ditambahkan ke layar utama tablet

## Instalasi

1. Ekstrak ZIP dan masuk ke folder project.
2. Jalankan:

```bash
composer install
```

3. Salin konfigurasi environment:

Windows CMD:

```bat
copy .env.example .env
```

PowerShell/Linux/macOS:

```bash
cp .env.example .env
```

4. Buat database MySQL bernama `buku_tamu_db`.
5. Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` pada `.env`.
6. Jalankan:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

7. Buka mode tablet:

```text
http://127.0.0.1:8000
```

8. Login petugas:

```text
http://127.0.0.1:8000/petugas/login
```

Akun awal:

```text
Email    : admin@bukutamu.local
Password : password123
```

Segera ganti password admin sebelum digunakan pada server produksi.

## Catatan Tampilan

Project ini memakai Tailwind CSS melalui CDN agar dapat langsung dicoba tanpa `npm install`. Untuk server produksi yang akses internetnya dibatasi, pindahkan Tailwind ke proses build lokal/Vite.

## Struktur Fitur

- `/` — halaman utama kiosk
- `/kunjungan/baru` — formulir tamu
- `/check-out` — pencarian dan check-out tamu
- `/petugas/login` — login petugas
- `/admin` — dashboard petugas
- `/admin/divisions` — master divisi
- `/admin/employees` — master pegawai
- `/admin/visits` — seluruh data kunjungan

## Pemasangan pada Tablet

Buka alamat aplikasi dengan Chrome, lalu pilih **Tambahkan ke layar utama / Install app**. Manifest dan service worker dasar sudah disediakan.

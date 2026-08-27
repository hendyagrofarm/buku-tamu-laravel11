UPDATE TAMPILAN BUKU TAMU APP STYLE V3

1. Ekstrak ZIP ini, jangan dijalankan dari dalam ZIP.
2. Hentikan php artisan serve dengan Ctrl+C.
3. Klik dua kali PASANG-UPDATE.bat.
4. Tunggu sampai muncul [BERHASIL].
5. Jalankan kembali:
   cd C:\xampp\htdocs\buku-tamu-laravel11
   php artisan serve
6. Buka http://127.0.0.1:8000 lalu tekan Ctrl+F5.

Verifikasi manual:
Select-String -Path ".\resources\views\kiosk\home.blade.php" -Pattern "GUESTEASE_APP_STYLE_V3"

<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Buku Tamu Digital siap digunakan.');
})->purpose('Menampilkan pesan aplikasi');

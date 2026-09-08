<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | TRUST PROXY
        |--------------------------------------------------------------------------
        |
        | Dibutuhkan saat aplikasi production berada di belakang
        | Nginx Proxy Manager / reverse proxy.
        |
        | Browser:
        | https://portaltamu.agrofarm.id
        |
        | Proxy meneruskan ke backend:
        | http://10.10.0.5:80
        |
        | Dengan konfigurasi ini Laravel akan membaca header
        | X-Forwarded-* dengan benar.
        |
        */

        $middleware->trustProxies(
            at: '*',

            headers:
                Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
        );

    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
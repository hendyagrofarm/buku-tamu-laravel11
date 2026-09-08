<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><script src="https://cdn.tailwindcss.com"></script><title>Login Petugas</title></head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 p-5 antialiased">
<div class="mx-auto flex min-h-[calc(100vh-2.5rem)] max-w-md items-center">
    <div class="w-full rounded-[2rem] bg-white p-7 shadow-2xl sm:p-9">
        <a href="{{ route('kiosk.home') }}" class="text-sm font-semibold text-emerald-700">← Kembali ke mode tamu</a>
        <div class="mt-7"><p class="text-xs font-bold uppercase tracking-[.25em] text-emerald-700">Buku Tamu Digital</p><h1 class="mt-2 text-3xl font-bold text-slate-900">Login Petugas</h1><p class="mt-2 text-sm leading-6 text-slate-500">Akun petugas otomatis dibatasi sesuai lokasi yang ditetapkan Administrator.</p></div>
        @if(session('success'))<div class="mt-5 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('login.process') }}" class="mt-7 space-y-5">@csrf
            <div><label class="mb-2 block text-sm font-semibold">Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="mb-2 block text-sm font-semibold">Password</label><input type="password" name="password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></div>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-emerald-600"> Ingat saya</label>
            <button class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white shadow-lg shadow-emerald-900/20 hover:bg-emerald-800">Masuk ke Dashboard</button>
        </form>
        
    </div>
</div>
</body></html>

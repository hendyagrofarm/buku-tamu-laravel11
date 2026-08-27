@extends('layouts.kiosk')
@section('title', 'Terima Kasih')
@section('content')
<main class="flex min-h-screen items-center px-5 py-10 sm:px-8">
    <div class="w-full rounded-[2rem] bg-white p-7 text-center shadow-xl sm:p-10">
        <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-emerald-100 text-4xl text-emerald-600">✓</div>
        <p class="mt-6 text-sm font-bold uppercase tracking-[.2em] text-emerald-700">Kunjungan Tercatat</p>
        <h1 class="mt-2 text-3xl font-extrabold text-slate-900">Terima kasih, {{ $visit->visitor->name }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Rating kepuasan Anda berhasil disimpan.</p>
        <div class="mt-5 text-4xl tracking-wider text-amber-400">{{ str_repeat('★', $visit->satisfaction_rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $visit->satisfaction_rating) }}</span></div>
        <div class="mt-7 rounded-3xl bg-slate-900 p-6 text-white"><p class="text-xs uppercase tracking-widest text-slate-400">Nomor Kunjungan</p><p class="mt-2 text-3xl font-black tracking-wide">{{ $visit->visit_number }}</p></div>
        <dl class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 text-left text-sm">
            <div class="flex justify-between gap-4 p-4"><dt class="text-slate-500">Pegawai tujuan</dt><dd class="text-right font-bold">{{ $visit->employee_name }}</dd></div>
            <div class="flex justify-between gap-4 p-4"><dt class="text-slate-500">Divisi</dt><dd class="text-right font-bold">-</dd></div>
            <div class="flex justify-between gap-4 p-4"><dt class="text-slate-500">Waktu daftar</dt><dd class="text-right font-bold">{{ $visit->check_in_at->format('d/m/Y H:i') }}</dd></div>
        </dl>
        <a href="{{ route('kiosk.home') }}" class="mt-7 block w-full rounded-2xl bg-[#173b5b] px-5 py-4 font-bold text-white">Kembali ke Beranda</a>
    </div>
</main>
@endsection

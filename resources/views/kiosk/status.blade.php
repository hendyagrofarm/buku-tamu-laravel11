@extends('layouts.kiosk')
@section('title', 'Status Kunjungan')
@section('content')
<header class="sticky top-0 z-20 bg-[#173b5b] px-5 py-4 text-white">
    <div class="flex items-center gap-4">
        <a href="{{ route('kiosk.home') }}" class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-xl" aria-label="Kembali">←</a>
        <div><p class="text-xs text-blue-100">BukuTamu</p><h1 class="text-lg font-bold">Status Kunjungan Hari Ini</h1><p class="text-[10px] text-lime-300">{{ $location->name }}</p></div>
    </div>
</header>
<main class="space-y-3 px-4 py-5">
    <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm leading-6 text-[#173b5b]">Semua kunjungan hari ini ditampilkan langsung. Tidak ada pencarian.</div>
    @forelse($visits as $visit)
        <article class="rounded-[20px] bg-white p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $visit->satisfaction_rating ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A9 9 0 1118.879 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <div><h2 class="text-sm font-black text-slate-800">{{ $visit->visitor->name }}</h2><p class="mt-1 text-[10px] text-slate-400">{{ $visit->visit_number }}</p></div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-[9px] font-bold {{ $visit->satisfaction_rating ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">{{ $visit->satisfaction_rating ? 'Sudah Survey' : 'Belum Survey' }}</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-[11px]">
                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-slate-400">Pegawai tujuan</p><p class="mt-1 font-bold text-slate-700">{{ $visit->employee_name }}</p></div>
                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-slate-400">Jam datang</p><p class="mt-1 font-bold text-slate-700">{{ $visit->check_in_at->format('H:i') }} WIB</p></div>
                    </div>
                    @if(!$visit->satisfaction_rating)
                        <a href="{{ route('kiosk.survey.show', $visit) }}" class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs font-bold text-white">Isi Survey Kepuasan <span>★</span></a>
                    @else
                        <div class="mt-3 rounded-xl bg-amber-50 px-4 py-3 text-center text-lg tracking-wider text-amber-400">{{ str_repeat('★', $visit->satisfaction_rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $visit->satisfaction_rating) }}</span></div>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="rounded-[24px] bg-white px-6 py-12 text-center shadow-sm"><div class="text-5xl">📋</div><h2 class="mt-4 font-black text-slate-800">Belum ada kunjungan</h2><p class="mt-2 text-sm text-slate-500">Data tamu yang masuk hari ini akan tampil di halaman ini.</p></div>
    @endforelse
</main>
@endsection

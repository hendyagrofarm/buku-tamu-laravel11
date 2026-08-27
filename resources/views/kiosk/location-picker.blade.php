@extends('layouts.kiosk')
@section('title', 'Pilih Lokasi')
@section('content')
<main class="flex min-h-screen items-center px-5 py-8">
    <div class="w-full rounded-[2rem] bg-white p-6 shadow-xl sm:p-8">
        <div class="text-center"><p class="text-xs font-bold uppercase tracking-[.25em] text-lime-500">BukuTamu</p><h1 class="mt-2 text-2xl font-black text-[#173b5b]">Pilih Lokasi</h1><p class="mt-2 text-sm text-slate-500">Pilih lokasi tempat tamu melakukan check in.</p></div>
        <div class="mt-7 space-y-3">
            @forelse($locations as $location)
                <a href="{{ route('kiosk.location.select', $location) }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-blue-300 hover:bg-blue-50 active:scale-[.99]"><div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-100 text-xl text-blue-700">⌖</div><div class="flex-1"><p class="font-black text-slate-800">{{ $location->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $location->description ?: 'Lokasi Buku Tamu' }}</p></div><span class="text-xl text-lime-600">&gt;</span></a>
            @empty
                <div class="rounded-2xl bg-amber-50 p-5 text-center text-sm text-amber-800">Belum ada lokasi aktif. Hubungi Administrator.</div>
            @endforelse
        </div>
        <a href="{{ route('login') }}" class="mt-6 block text-center text-sm font-bold text-[#173b5b]">Login Petugas</a>
    </div>
</main>
@endsection

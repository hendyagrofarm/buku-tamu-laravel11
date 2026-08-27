{{-- GUESTEASE_APP_STYLE_V3 --}}
@extends('layouts.kiosk')

@section('title', 'Beranda Buku Tamu')

@section('content')
<div class="min-h-screen pb-24">
    {{-- Header aplikasi --}}
    <header class="relative min-h-[190px] bg-gradient-to-br from-[#173b5b] via-[#123653] to-[#0e2d47] px-5 text-white safe-top">
        <div class="flex items-center justify-between pt-2">
            <button type="button" id="openMenu" class="grid h-10 w-10 place-items-center rounded-xl text-white/90 transition active:bg-white/10" aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex items-center gap-2.5">
                <div class="text-right">
                    <h1 class="text-[22px] font-black tracking-tight">GuestEase</h1>
                    <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-[.18em] text-lime-300">Buku Tamu Digital</p>
                </div>
                <div class="grid h-10 w-10 place-items-center rounded-xl border-2 border-white/80">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5-5-5M15 12H3m10-9h6a2 2 0 012 2v14a2 2 0 01-2 2h-6" />
                    </svg>
                </div>
            </div>

            <a href="{{ route('login') }}" class="grid h-10 w-10 place-items-center rounded-xl text-white/90 transition active:bg-white/10" aria-label="Login petugas">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="5" r="2" />
                    <circle cx="12" cy="12" r="2" />
                    <circle cx="12" cy="19" r="2" />
                </svg>
            </a>
        </div>
    </header>

    {{-- Konten utama yang menumpuk ke header --}}
    <main class="relative z-10 -mt-[72px] px-4">
        @if(session('success'))
            <div class="mb-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Statistik real-time --}}
        <section class="overflow-hidden rounded-[22px] bg-white shadow-card">
            <div class="relative border-b border-slate-100 px-4 pb-3 pt-5 text-center">
                <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap rounded-full bg-lime-500 px-7 py-1.5 text-[11px] font-bold text-white shadow-md">
                    Data Realtime Hari Ini
                </div>
                <p id="realtimeClock" class="mt-1 text-[10px] font-medium text-slate-500">Memuat waktu...</p>
            </div>

            <div class="grid grid-cols-3 gap-2 px-3 py-4">
                <div class="text-center">
                    <p class="text-[29px] font-light leading-none text-orange-500">{{ $stats['today'] }}</p>
                    <div class="mx-auto mt-2 rounded-full bg-orange-500 px-2 py-1 text-[9px] font-semibold text-white">Tamu Hari Ini</div>
                </div>
                <div class="text-center">
                    <p class="text-[29px] font-light leading-none text-blue-500">{{ $stats['active'] }}</p>
                    <div class="mx-auto mt-2 rounded-full bg-blue-500 px-2 py-1 text-[9px] font-semibold text-white">Sedang Berkunjung</div>
                </div>
                <div class="text-center">
                    <p class="text-[29px] font-light leading-none text-violet-600">{{ $stats['completed'] }}</p>
                    <div class="mx-auto mt-2 rounded-full bg-violet-600 px-2 py-1 text-[9px] font-semibold text-white">Sudah Selesai</div>
                </div>
            </div>
        </section>

        {{-- Menu cepat --}}
        <section class="mt-3 grid grid-cols-4 gap-2.5">
            <a href="{{ route('kiosk.visit.create') }}" class="rounded-2xl bg-white px-2 py-3 text-center shadow-sm transition active:scale-95">
                <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm10 10v-2a4 4 0 00-3-3.87m-1-12a4 4 0 010 7.75" />
                    </svg>
                </div>
                <p class="mt-2 text-[10px] font-semibold leading-tight text-slate-600">Isi Data<br>Tamu</p>
            </a>

            <a href="{{ route('kiosk.checkout') }}" class="rounded-2xl bg-white px-2 py-3 text-center shadow-sm transition active:scale-95">
                <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-blue-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                </div>
                <p class="mt-2 text-[10px] font-semibold leading-tight text-slate-600">Status<br>Kunjungan</p>
            </a>

            <a href="{{ route('login') }}" class="rounded-2xl bg-white px-2 py-3 text-center shadow-sm transition active:scale-95">
                <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-rose-50 text-rose-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                </div>
                <p class="mt-2 text-[10px] font-semibold leading-tight text-slate-600">Login<br>Petugas</p>
            </a>

            <button type="button" id="openHelp" class="rounded-2xl bg-white px-2 py-3 text-center shadow-sm transition active:scale-95">
                <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-violet-50 text-violet-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.09 9a3 3 0 115.83 1c0 2-3 2-3 4m.08 4h.01M12 22a10 10 0 110-20 10 10 0 010 20z" />
                    </svg>
                </div>
                <p class="mt-2 text-[10px] font-semibold leading-tight text-slate-600">Pusat<br>Bantuan</p>
            </button>
        </section>

        {{-- Tombol aksi utama --}}
        <section class="mt-3 grid grid-cols-2 gap-3">
            <a href="{{ route('kiosk.visit.create') }}" class="flex min-h-[58px] items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-orange-400 to-orange-500 px-3 text-white shadow-md shadow-orange-200 transition active:scale-[.98]">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 006.5 22H20V4H6.5A2.5 2.5 0 004 6.5v13z" />
                </svg>
                <span class="text-sm font-extrabold italic">Buku Tamu</span>
            </a>

            <a href="{{ route('kiosk.checkout') }}" class="flex min-h-[58px] items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-sky-500 to-blue-700 px-3 text-white shadow-md shadow-blue-200 transition active:scale-[.98]">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-extrabold italic">Check-out</span>
            </a>
        </section>

        {{-- Kunjungan hari ini --}}
        <section class="mt-3 overflow-hidden rounded-[18px] bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">Kunjungan Hari Ini</h2>
                    <p class="mt-0.5 text-[9px] text-slate-400">Daftar kedatangan terbaru pada hari ini</p>
                </div>
                <a href="{{ route('login') }}" class="grid h-8 w-8 place-items-center rounded-full text-lime-500 transition active:bg-lime-50" aria-label="Buka dashboard petugas">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @forelse($recentVisits as $visit)
                <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-b-0">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $visit->status === 'active' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A9 9 0 1118.879 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-slate-800">{{ $visit->visitor->masked_name }}</p>
                        <p class="mt-1 truncate text-[10px] text-slate-500">Menemui {{ $visit->employee->name }} · {{ $visit->check_in_at->format('H:i') }} WIB</p>
                    </div>
                    <span class="rounded-full px-2 py-1 text-[9px] font-bold {{ $visit->status === 'active' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }}">
                        {{ $visit->status === 'active' ? 'Aktif' : 'Selesai' }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-7 text-center">
                    <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-emerald-50 text-emerald-500">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M5 11h14M7 15l3 3 7-7M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-bold text-slate-700">Belum ada kunjungan</p>
                    <p class="mt-1 text-[11px] leading-5 text-slate-400">Kunjungan yang didaftarkan hari ini akan tampil di bagian ini.</p>
                </div>
            @endforelse
        </section>
    </main>

    {{-- Navigasi bawah seperti aplikasi --}}
    <nav class="safe-bottom fixed inset-x-0 bottom-0 z-40 mx-auto max-w-xl border-t border-slate-200 bg-white/95 px-3 pt-2 shadow-[0_-6px_20px_rgba(15,23,42,.06)] backdrop-blur">
        <div class="grid grid-cols-4">
            <a href="{{ route('kiosk.home') }}" class="flex flex-col items-center gap-1 py-1 text-[#173b5b]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" />
                </svg>
                <span class="text-[9px] font-bold">Beranda</span>
            </a>
            <a href="{{ route('kiosk.visit.create') }}" class="flex flex-col items-center gap-1 py-1 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-[9px] font-semibold">Daftar</span>
            </a>
            <a href="{{ route('kiosk.checkout') }}" class="flex flex-col items-center gap-1 py-1 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[9px] font-semibold">Check-out</span>
            </a>
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 py-1 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A9 9 0 1118.879 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[9px] font-semibold">Petugas</span>
            </a>
        </div>
    </nav>
</div>

{{-- Drawer menu --}}
<div id="menuOverlay" class="fixed inset-0 z-50 hidden bg-slate-950/50 backdrop-blur-sm">
    <aside id="menuPanel" class="h-full w-[78%] max-w-xs -translate-x-full bg-white p-5 shadow-2xl transition-transform duration-300">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <p class="text-lg font-black text-[#173b5b]">GuestEase</p>
                <p class="text-[10px] font-semibold uppercase tracking-[.18em] text-lime-500">Buku Tamu Digital</p>
            </div>
            <button type="button" id="closeMenu" class="grid h-9 w-9 place-items-center rounded-xl bg-slate-100 text-slate-600">✕</button>
        </div>
        <div class="mt-5 space-y-2">
            <a href="{{ route('kiosk.home') }}" class="flex items-center gap-3 rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-[#173b5b]">Beranda</a>
            <a href="{{ route('kiosk.visit.create') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 active:bg-slate-100">Daftar Kunjungan</a>
            <a href="{{ route('kiosk.checkout') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 active:bg-slate-100">Check-out Tamu</a>
            <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 active:bg-slate-100">Login Petugas</a>
        </div>
    </aside>
</div>

{{-- Modal bantuan --}}
<div id="helpModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/50 p-4 backdrop-blur-sm sm:items-center">
    <div class="w-full max-w-md rounded-[24px] bg-white p-5 shadow-2xl">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-800">Bantuan Penggunaan</h3>
                <p class="mt-1 text-xs text-slate-500">Langkah singkat menggunakan buku tamu.</p>
            </div>
            <button type="button" id="closeHelp" class="grid h-9 w-9 place-items-center rounded-xl bg-slate-100 text-slate-600">✕</button>
        </div>
        <ol class="mt-5 space-y-3 text-sm text-slate-600">
            <li class="flex gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600">1</span><span>Tekan <strong>Buku Tamu</strong> untuk mengisi data kunjungan.</span></li>
            <li class="flex gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">2</span><span>Simpan nomor kunjungan yang muncul setelah pendaftaran.</span></li>
            <li class="flex gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-600">3</span><span>Tekan <strong>Check-out</strong> sebelum meninggalkan lokasi.</span></li>
        </ol>
        <button type="button" id="understandHelp" class="mt-5 w-full rounded-xl bg-[#173b5b] px-4 py-3 text-sm font-bold text-white">Saya Mengerti</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const clock = document.getElementById('realtimeClock');
        const formatter = new Intl.DateTimeFormat('id-ID', {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta',
        });

        const updateClock = () => {
            clock.textContent = formatter.format(new Date()).replaceAll('.', ':') + ' WIB';
        };

        updateClock();
        setInterval(updateClock, 1000);

        const menuOverlay = document.getElementById('menuOverlay');
        const menuPanel = document.getElementById('menuPanel');
        const openMenu = () => {
            menuOverlay.classList.remove('hidden');
            requestAnimationFrame(() => menuPanel.classList.remove('-translate-x-full'));
        };
        const closeMenu = () => {
            menuPanel.classList.add('-translate-x-full');
            setTimeout(() => menuOverlay.classList.add('hidden'), 250);
        };

        document.getElementById('openMenu').addEventListener('click', openMenu);
        document.getElementById('closeMenu').addEventListener('click', closeMenu);
        menuOverlay.addEventListener('click', (event) => {
            if (event.target === menuOverlay) closeMenu();
        });

        const helpModal = document.getElementById('helpModal');
        const openHelp = () => {
            helpModal.classList.remove('hidden');
            helpModal.classList.add('flex');
        };
        const closeHelp = () => {
            helpModal.classList.add('hidden');
            helpModal.classList.remove('flex');
        };

        document.getElementById('openHelp').addEventListener('click', openHelp);
        document.getElementById('closeHelp').addEventListener('click', closeHelp);
        document.getElementById('understandHelp').addEventListener('click', closeHelp);
        helpModal.addEventListener('click', (event) => {
            if (event.target === helpModal) closeHelp();
        });
    })();
</script>
@endpush

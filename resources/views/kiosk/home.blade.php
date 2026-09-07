@extends('layouts.kiosk')

@section('title', 'BukuTamu')

@section('content')
<div class="min-h-screen pb-24">

    <header class="relative min-h-[205px] bg-gradient-to-br from-[#173b5b] via-[#123653] to-[#0e2d47] px-5 text-white safe-top">

        <button
            type="button"
            id="openMenu"
            class="absolute left-5 top-7 z-20 grid h-10 w-10 place-items-center rounded-xl text-white/90"
            aria-label="Buka menu"
        >
            <svg
                class="h-7 w-7"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>


        <div class="absolute inset-x-0 top-5 z-10 flex flex-col items-center text-center pointer-events-none">

            <h1 class="text-[23px] font-black tracking-tight">
                BukuTamu
            </h1>

            <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-[.18em] text-lime-300">
                Check In Tamu Digital
            </p>

            <div class="mt-2 flex flex-col items-center gap-1.5 pointer-events-auto">

                <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-[9px] font-bold text-white">
                    Lokasi: {{ $location?->name ?? '-' }}
                </span>

                <a
                    href="{{ route('kiosk.location.picker') }}"
                    class="inline-flex items-center rounded-full border border-lime-400/80 bg-lime-500/10 px-4 py-1.5 text-[10px] font-bold text-lime-300 transition active:scale-95"
                >
                    Ganti Lokasi
                </a>

            </div>

        </div>

    </header>


    <main class="relative z-10 -mt-12 px-4">

        <section class="rounded-[22px] bg-white shadow-card">

            <div class="flex justify-center">

                <div class="-mt-3 rounded-full bg-lime-500 px-8 py-2 text-[11px] font-bold text-white shadow-md">
                    Data Realtime Hari Ini
                </div>

            </div>


            <div class="border-b border-slate-100 px-4 pb-3 pt-4 text-center">

                <p
                    id="realtimeClock"
                    class="text-[11px] font-medium text-slate-500"
                >
                    Memuat waktu...
                </p>

            </div>


            <div class="grid grid-cols-3 gap-2 px-3 py-4">

                <div class="text-center">

                    <p class="text-[29px] font-light leading-none text-orange-500">
                        {{ $stats['today'] ?? 0 }}
                    </p>

                    <div class="mx-auto mt-2 rounded-full bg-orange-500 px-2 py-1 text-[9px] font-semibold text-white">
                        Tamu Hari Ini
                    </div>

                </div>


                <div class="text-center">

                    <p class="text-[29px] font-light leading-none text-blue-500">
                        {{ $stats['pending_survey'] ?? 0 }}
                    </p>

                    <div class="mx-auto mt-2 rounded-full bg-blue-500 px-2 py-1 text-[9px] font-semibold text-white">
                        Belum Survey
                    </div>

                </div>


                <div class="text-center">

                    <p class="text-[29px] font-light leading-none text-violet-600">
                        {{ $stats['surveyed'] ?? 0 }}
                    </p>

                    <div class="mx-auto mt-2 rounded-full bg-violet-600 px-2 py-1 text-[9px] font-semibold text-white">
                        Sudah Survey
                    </div>

                </div>

            </div>

        </section>


        <section class="mt-3 grid grid-cols-4 gap-2.5">

            <a
                href="{{ route('kiosk.status') }}"
                class="flex min-h-[104px] flex-col items-center justify-center rounded-[18px] bg-white px-2 text-center shadow-sm"
            >

                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-emerald-500">

                    <svg
                        class="h-7 w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm10 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                        />
                    </svg>

                </div>

                <span class="mt-2 text-[10px] font-semibold leading-4 text-slate-700">
                    Data<br>Tamu
                </span>

            </a>


            <a
                href="{{ route('kiosk.status') }}"
                class="flex min-h-[104px] flex-col items-center justify-center rounded-[18px] bg-white px-2 text-center shadow-sm"
            >

                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-blue-500">

                    <svg
                        class="h-7 w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                        />
                    </svg>

                </div>

                <span class="mt-2 text-[10px] font-semibold leading-4 text-slate-700">
                    Status<br>Kunjungan
                </span>

            </a>


            <a
                href="{{ route('login') }}"
                class="flex min-h-[104px] flex-col items-center justify-center rounded-[18px] bg-white px-2 text-center shadow-sm"
            >

                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-rose-50 text-rose-500">

                    <svg
                        class="h-7 w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>

                </div>

                <span class="mt-2 text-[10px] font-semibold leading-4 text-slate-700">
                    Login<br>Petugas
                </span>

            </a>


            <button
                type="button"
                id="openHelp"
                class="flex min-h-[104px] flex-col items-center justify-center rounded-[18px] bg-white px-2 text-center shadow-sm"
            >

                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-violet-50 text-violet-500">

                    <svg
                        class="h-7 w-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8.228 9a3.001 3.001 0 015.824 1c0 2-3 2-3 4m.01 4h.01M12 22a10 10 0 110-20 10 10 0 010 20z"
                        />
                    </svg>

                </div>

                <span class="mt-2 text-[10px] font-semibold leading-4 text-slate-700">
                    Pusat<br>Bantuan
                </span>

            </button>

        </section>


        <section class="mt-3 grid grid-cols-2 gap-3">

            <a
                href="{{ route('kiosk.visit.create') }}"
                class="flex min-h-[58px] items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-orange-400 to-orange-500 px-3 text-white shadow-md"
            >

                <svg
                    class="h-7 w-7"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 006.5 22H20V4H6.5A2.5 2.5 0 004 6.5v13z"
                    />
                </svg>

                <span class="text-sm font-extrabold italic">
                    Check In Tamu
                </span>

            </a>


            <a
                href="{{ route('kiosk.survey.index') }}"
                class="flex min-h-[58px] items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-sky-500 to-blue-700 px-3 text-white shadow-md"
            >

                <span class="text-2xl">
                    ★
                </span>

                <span class="text-sm font-extrabold italic">
                    Survey Kepuasan
                </span>

            </a>

        </section>


        <section class="mt-3 overflow-hidden rounded-[18px] bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">

                <div>

                    <h2 class="text-[15px] font-bold text-slate-800">
                        Kunjungan Hari Ini
                    </h2>

                    <p class="mt-0.5 text-[9px] text-slate-400">
                        Semua status kunjungan hari ini
                    </p>

                </div>


                <a
                    href="{{ route('kiosk.status') }}"
                    class="grid h-8 w-8 place-items-center rounded-full text-lime-500"
                    aria-label="Lihat semua"
                >

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>

                </a>

            </div>


            @forelse($recentVisits ?? [] as $visit)

                <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-b-0">

                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $visit->satisfaction_rating ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">

                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M5.121 17.804A9 9 0 1118.879 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>

                    </div>


                    <div class="min-w-0 flex-1">

                        {{-- NAMA TAMU - AMAN JIKA RELASI VISITOR KOSONG --}}
                        <p class="truncate text-xs font-bold text-slate-800">
                            {{ $visit->visitor?->name ?? '-' }}
                        </p>


                        {{-- PEGAWAI YANG DITEMUI --}}
                        <p class="mt-1 truncate text-[10px] text-slate-500">

                            Menemui

                            @if(!empty($visit->employee_name))

                                {{ $visit->employee_name }}

                            @elseif($visit->employee)

                                {{ $visit->employee->name }}

                            @else

                                -

                            @endif


                            @if($visit->check_in_at)

                                · {{ $visit->check_in_at->format('H:i') }} WIB

                            @endif

                        </p>

                    </div>


                    <span class="rounded-full px-2 py-1 text-[9px] font-bold {{ $visit->satisfaction_rating ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">

                        {{ $visit->satisfaction_rating ? 'Sudah Survey' : 'Belum Survey' }}

                    </span>

                </div>

            @empty

                <div class="px-6 py-7 text-center">

                    <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-emerald-50 text-emerald-500">
                        📋
                    </div>

                    <p class="mt-3 text-sm font-bold text-slate-700">
                        Belum ada kunjungan
                    </p>

                    <p class="mt-1 text-[11px] leading-5 text-slate-400">
                        Kunjungan hari ini akan tampil di bagian ini.
                    </p>

                </div>

            @endforelse

        </section>

    </main>


    <nav class="safe-bottom fixed inset-x-0 bottom-0 z-40 mx-auto max-w-xl border-t border-slate-200 bg-white/95 px-3 pt-2 shadow-[0_-6px_20px_rgba(15,23,42,.06)] backdrop-blur">

        <div class="grid grid-cols-4">

            <a
                href="{{ route('kiosk.home') }}"
                class="flex flex-col items-center gap-1 py-1 text-[#173b5b]"
            >
                <span>⌂</span>
                <span class="text-[9px] font-bold">Beranda</span>
            </a>


            <a
                href="{{ route('kiosk.status') }}"
                class="flex flex-col items-center gap-1 py-1 text-slate-400"
            >
                <span>✓</span>
                <span class="text-[9px] font-semibold">Status</span>
            </a>


            <a
                href="{{ route('kiosk.survey.index') }}"
                class="flex flex-col items-center gap-1 py-1 text-slate-400"
            >
                <span>★</span>
                <span class="text-[9px] font-semibold">Survey</span>
            </a>


            <a
                href="{{ route('login') }}"
                class="flex flex-col items-center gap-1 py-1 text-slate-400"
            >
                <span>●</span>
                <span class="text-[9px] font-semibold">Petugas</span>
            </a>

        </div>

    </nav>

</div>


<div
    id="menuOverlay"
    class="fixed inset-0 z-50 hidden bg-slate-950/50 backdrop-blur-sm"
>

    <aside
        id="menuPanel"
        class="h-full w-[78%] max-w-xs -translate-x-full bg-white p-5 shadow-2xl transition-transform duration-300"
    >

        <div class="flex items-center justify-between border-b border-slate-100 pb-4">

            <div>

                <p class="text-lg font-black text-[#173b5b]">
                    BukuTamu
                </p>

                <p class="text-[10px] font-semibold uppercase tracking-[.18em] text-lime-500">
                    Check In Tamu Digital
                </p>

            </div>


            <button
                type="button"
                id="closeMenu"
                class="grid h-9 w-9 place-items-center rounded-xl bg-slate-100 text-slate-600"
            >
                ×
            </button>

        </div>


        <div class="mt-5 space-y-2">

            <a
                href="{{ route('kiosk.home') }}"
                class="block rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-[#173b5b]"
            >
                Beranda
            </a>


            <a
                href="{{ route('kiosk.visit.create') }}"
                class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600"
            >
                Check In Tamu
            </a>


            <a
                href="{{ route('kiosk.status') }}"
                class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600"
            >
                Status Kunjungan
            </a>


            <a
                href="{{ route('kiosk.survey.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600"
            >
                Survey Kepuasan
            </a>


            <a
                href="{{ route('login') }}"
                class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-600"
            >
                Login Petugas
            </a>

        </div>

    </aside>

</div>


<div
    id="helpModal"
    class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/50 p-4 backdrop-blur-sm sm:items-center"
>

    <div class="w-full max-w-md rounded-[24px] bg-white p-5 shadow-2xl">

        <div class="flex items-start justify-between">

            <div>

                <h3 class="text-lg font-black text-slate-800">
                    Bantuan Penggunaan
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Langkah singkat menggunakan BukuTamu.
                </p>

            </div>


            <button
                type="button"
                id="closeHelp"
                class="grid h-9 w-9 place-items-center rounded-xl bg-slate-100 text-slate-600"
            >
                ×
            </button>

        </div>


        <ol class="mt-5 space-y-3 text-sm text-slate-600">

            <li>
                1. Tekan <strong>Check In Tamu</strong> lalu isi data.
            </li>

            <li>
                2. Setelah tersimpan, pilih rating bintang.
            </li>

            <li>
                3. Semua status kunjungan hari ini tersedia di menu Status.
            </li>

        </ol>


        <button
            type="button"
            id="understandHelp"
            class="mt-5 w-full rounded-xl bg-[#173b5b] px-4 py-3 text-sm font-bold text-white"
        >
            Saya Mengerti
        </button>

    </div>

</div>

@endsection


@push('scripts')

<script>

(() => {

    const clock = document.getElementById('realtimeClock');

    const formatter = new Intl.DateTimeFormat(
        'id-ID',
        {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta'
        }
    );


    const updateClock = () => {

        if (clock) {

            clock.textContent =
                formatter.format(new Date()) + ' WIB';

        }

    };


    updateClock();

    setInterval(updateClock, 1000);


    const overlay =
        document.getElementById('menuOverlay');

    const panel =
        document.getElementById('menuPanel');


    const showMenu = () => {

        overlay.classList.remove('hidden');

        requestAnimationFrame(() =>
            panel.classList.remove('-translate-x-full')
        );

    };


    const hideMenu = () => {

        panel.classList.add('-translate-x-full');

        setTimeout(
            () => overlay.classList.add('hidden'),
            250
        );

    };


    document
        .getElementById('openMenu')
        ?.addEventListener(
            'click',
            showMenu
        );


    document
        .getElementById('closeMenu')
        ?.addEventListener(
            'click',
            hideMenu
        );


    overlay?.addEventListener(
        'click',
        e => {

            if (e.target === overlay) {

                hideMenu();

            }

        }
    );


    const help =
        document.getElementById('helpModal');


    const showHelp = () => {

        help.classList.remove('hidden');

        help.classList.add('flex');

    };


    const hideHelp = () => {

        help.classList.add('hidden');

        help.classList.remove('flex');

    };


    document
        .getElementById('openHelp')
        ?.addEventListener(
            'click',
            showHelp
        );


    document
        .getElementById('closeHelp')
        ?.addEventListener(
            'click',
            hideHelp
        );


    document
        .getElementById('understandHelp')
        ?.addEventListener(
            'click',
            hideHelp
        );


    help?.addEventListener(
        'click',
        e => {

            if (e.target === help) {

                hideHelp();

            }

        }
    );

})();

</script>

@endpush
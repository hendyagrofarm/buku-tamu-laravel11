@extends('layouts.kiosk')

@section('title', 'Survey Kepuasan')

@section('content')


{{-- HEADER --}}
<header class="sticky top-0 z-20 bg-[#173b5b] px-5 py-4 text-white">

    <div class="flex items-center gap-4">

        <a
            href="{{ route('kiosk.home') }}"
            class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-xl"
        >
            ←
        </a>


        <div>

            <p class="text-xs text-blue-100">
                BukuTamu
            </p>

            <h1 class="text-lg font-bold">
                Survey Kepuasan
            </h1>

            <p class="text-[10px] text-lime-300">
                {{ $location->name }}
            </p>

        </div>

    </div>

</header>



<main class="space-y-3 px-4 py-5">


    {{-- INFORMASI --}}
    <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4">

        <div class="flex items-start gap-3">

            <div class="text-2xl">
                ⭐
            </div>

            <div>

                <p class="font-bold text-[#173b5b]">
                    Survey saat akan pulang
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Silakan pilih nama tamu yang akan meninggalkan lokasi,
                    kemudian berikan penilaian 1 sampai 5 bintang.
                </p>

            </div>

        </div>

    </div>



    {{-- JUMLAH BELUM SURVEY --}}
    @if($visits->count() > 0)

        <div class="flex items-center justify-between px-1 py-2">

            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                Belum Survey
            </p>

            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                {{ $visits->count() }} Tamu
            </span>

        </div>

    @endif



    {{-- LIST TAMU --}}
    @forelse($visits as $visit)

        <a
            href="{{ route('kiosk.survey.show', $visit) }}"
            class="flex items-center gap-3 rounded-[20px] bg-white p-4 shadow-sm transition active:scale-[.99]"
        >

            {{-- ICON --}}
            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-amber-50 text-2xl text-amber-400">
                ★
            </div>


            {{-- INFORMASI TAMU --}}
            <div class="min-w-0 flex-1">

                <h2 class="truncate text-sm font-black text-slate-800">
                    {{ $visit->visitor?->name ?? '-' }}
                </h2>


                <p class="mt-1 truncate text-[11px] text-slate-500">

                    Menemui
                    {{ $visit->employee_name ?: '-' }}

                    <span class="mx-1">
                        ·
                    </span>

                    @if($visit->check_in_at)

                        Check In
                        {{
                            $visit->check_in_at
                                ->format('H:i')
                        }}
                        WIB

                    @endif

                </p>


                <p class="mt-1 text-[10px] text-slate-400">
                    {{ $visit->visit_number }}
                </p>

            </div>


            {{-- PANAH --}}
            <svg
                class="h-5 w-5 shrink-0 text-blue-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                />
            </svg>

        </a>


    @empty


        {{-- TIDAK ADA SURVEY --}}
        <div class="rounded-[24px] bg-white px-6 py-12 text-center shadow-sm">

            <div class="text-5xl">
                ✅
            </div>


            <h2 class="mt-4 font-black text-slate-800">
                Tidak ada survey yang menunggu
            </h2>


            <p class="mt-2 text-sm leading-6 text-slate-500">
                Semua tamu hari ini sudah mengisi survey kepuasan
                atau belum ada tamu yang melakukan check in.
            </p>


            <a
                href="{{ route('kiosk.home') }}"
                class="mt-5 inline-flex rounded-xl bg-[#173b5b] px-5 py-3 text-sm font-bold text-white"
            >
                Kembali ke Beranda
            </a>

        </div>


    @endforelse


</main>

@endsection
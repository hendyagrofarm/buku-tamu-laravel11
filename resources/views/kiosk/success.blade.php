@extends('layouts.kiosk')

@section('title', $visit->satisfaction_rating ? 'Terima Kasih' : 'Check In Berhasil')

@section('content')

<main class="flex min-h-screen items-center px-5 py-10 sm:px-8">

    <div class="w-full rounded-[2rem] bg-white p-7 text-center shadow-xl sm:p-10">


        @if($visit->satisfaction_rating)


            {{-- ========================================================= --}}
            {{-- SURVEY BERHASIL --}}
            {{-- ========================================================= --}}

            <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-emerald-100 text-4xl text-emerald-600">
                ✓
            </div>


            <p class="mt-6 text-sm font-bold uppercase tracking-[.2em] text-emerald-700">
                Survey Berhasil
            </p>


            <h1 class="mt-2 text-3xl font-extrabold text-slate-900">
                Terima kasih,
                {{ $visit->visitor?->name ?? 'Tamu' }}
            </h1>


            <p class="mt-3 text-sm leading-6 text-slate-500">
                Terima kasih telah memberikan penilaian terhadap pelayanan kami.
            </p>


            {{-- RATING --}}
            <div class="mt-5 text-4xl tracking-wider text-amber-400">

                {{
                    str_repeat(
                        '★',
                        (int) $visit->satisfaction_rating
                    )
                }}

                <span class="text-slate-200">

                    {{
                        str_repeat(
                            '★',
                            5 - (int) $visit->satisfaction_rating
                        )
                    }}

                </span>

            </div>


            {{-- NOMOR KUNJUNGAN --}}
            <div class="mt-7 rounded-3xl bg-slate-900 p-6 text-white">

                <p class="text-xs uppercase tracking-widest text-slate-400">
                    Nomor Kunjungan
                </p>

                <p class="mt-2 text-3xl font-black tracking-wide">
                    {{ $visit->visit_number }}
                </p>

            </div>


            {{-- DETAIL --}}
            <dl class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 text-left text-sm">

                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Nama tamu
                    </dt>

                    <dd class="text-right font-bold">
                        {{ $visit->visitor?->name ?? '-' }}
                    </dd>

                </div>


                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Pegawai tujuan
                    </dt>

                    <dd class="text-right font-bold">
                        {{ $visit->employee_name ?: '-' }}
                    </dd>

                </div>


                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Waktu Check In
                    </dt>

                    <dd class="text-right font-bold">

                        @if($visit->check_in_at)

                            {{
                                $visit->check_in_at
                                    ->format('d/m/Y H:i')
                            }}
                            WIB

                        @else

                            -

                        @endif

                    </dd>

                </div>


                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Waktu Survey
                    </dt>

                    <dd class="text-right font-bold">

                        @if($visit->surveyed_at)

                            {{
                                $visit->surveyed_at
                                    ->format('d/m/Y H:i')
                            }}
                            WIB

                        @else

                            -

                        @endif

                    </dd>

                </div>

            </dl>


            <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">

                <p class="font-bold text-emerald-700">
                    Terima kasih atas kunjungan Anda
                </p>

                <p class="mt-1 text-sm leading-6 text-emerald-700/80">
                    Semoga perjalanan Anda menyenangkan dan sampai jumpa kembali.
                </p>

            </div>


        @else


            {{-- ========================================================= --}}
            {{-- CHECK IN BERHASIL --}}
            {{-- ========================================================= --}}

            <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-blue-100 text-4xl text-[#173b5b]">
                ✓
            </div>


            <p class="mt-6 text-sm font-bold uppercase tracking-[.2em] text-blue-700">
                Check In Berhasil
            </p>


            <h1 class="mt-2 text-3xl font-extrabold text-slate-900">

                Selamat datang,
                {{ $visit->visitor?->name ?? 'Tamu' }}

            </h1>


            <p class="mt-3 text-sm leading-6 text-slate-500">
                Data kunjungan Anda telah berhasil disimpan.
            </p>


            {{-- NOMOR KUNJUNGAN --}}
            <div class="mt-7 rounded-3xl bg-slate-900 p-6 text-white">

                <p class="text-xs uppercase tracking-widest text-slate-400">
                    Nomor Kunjungan
                </p>

                <p class="mt-2 text-3xl font-black tracking-wide">
                    {{ $visit->visit_number }}
                </p>

            </div>


            {{-- DETAIL KUNJUNGAN --}}
            <dl class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 text-left text-sm">

                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Nama tamu
                    </dt>

                    <dd class="text-right font-bold">
                        {{ $visit->visitor?->name ?? '-' }}
                    </dd>

                </div>


                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Pegawai tujuan
                    </dt>

                    <dd class="text-right font-bold">
                        {{ $visit->employee_name ?: '-' }}
                    </dd>

                </div>


                <div class="flex justify-between gap-4 p-4">

                    <dt class="text-slate-500">
                        Waktu Check In
                    </dt>

                    <dd class="text-right font-bold">

                        @if($visit->check_in_at)

                            {{
                                $visit->check_in_at
                                    ->format('d/m/Y H:i')
                            }}
                            WIB

                        @else

                            -

                        @endif

                    </dd>

                </div>

            </dl>


            {{-- INFORMASI SURVEY --}}
            <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-5">

                <div class="text-3xl">
                    ⭐
                </div>

                <p class="mt-2 font-bold text-[#173b5b]">
                    Survey diisi saat akan pulang
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Setelah kunjungan selesai, silakan buka menu
                    <strong>Survey Kepuasan</strong>
                    di halaman utama untuk memberikan penilaian.
                </p>

            </div>


            <p class="mt-5 text-sm font-bold text-slate-700">
                Terima kasih dan selamat berkunjung.
            </p>


        @endif


        {{-- KEMBALI KE BERANDA --}}
        <a
            href="{{ route('kiosk.home') }}"
            class="mt-7 block w-full rounded-2xl bg-[#173b5b] px-5 py-4 font-bold text-white transition active:scale-[.99]"
        >
            Kembali ke Beranda
        </a>

    </div>

</main>

@endsection
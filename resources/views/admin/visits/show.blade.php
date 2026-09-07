@extends('layouts.admin')

@section('title','Detail Kunjungan')
@section('page-title','Detail Kunjungan')

@section('content')

<div class="mb-5">

    <a
        href="{{ route('admin.visits.index') }}"
        class="text-sm font-bold text-emerald-700"
    >
        ← Kembali
    </a>

</div>


<div class="grid gap-6 lg:grid-cols-3">


    {{-- DATA UTAMA --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">

        <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-start sm:justify-between">

            <div>

                <p class="text-xs uppercase tracking-widest text-slate-500">
                    Nomor Kunjungan
                </p>

                <h3 class="mt-1 text-2xl font-black">
                    {{ $visit->visit_number }}
                </h3>

            </div>


            <span
                class="w-fit rounded-full px-3 py-1 text-xs font-bold
                {{
                    $visit->satisfaction_rating
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-blue-50 text-blue-600'
                }}"
            >

                {{
                    $visit->satisfaction_rating
                    ? 'Sudah Survey'
                    : 'Belum Survey'
                }}

            </span>

        </div>


        <dl class="mt-5 grid gap-5 sm:grid-cols-2">


            <div>

                <dt class="text-xs uppercase text-slate-500">
                    Nama tamu
                </dt>

                <dd class="mt-1 font-bold">
                    {{ $visit->visitor?->name ?? '-' }}
                </dd>

            </div>


            <div>

                <dt class="text-xs uppercase text-slate-500">
                    Perusahaan
                </dt>

                <dd class="mt-1 font-bold">
                    {{ $visit->visitor?->company ?: '-' }}
                </dd>

            </div>


            <div>

                <dt class="text-xs uppercase text-slate-500">
                    Telepon
                </dt>

                <dd class="mt-1 font-bold">
                    {{ $visit->visitor?->phone ?? '-' }}
                </dd>

            </div>


            <div>

                <dt class="text-xs uppercase text-slate-500">
                    Pegawai tujuan
                </dt>

                <dd class="mt-1 font-bold">

                    {{
                        $visit->employee_name
                        ?: ($visit->employee?->name ?? '-')
                    }}

                </dd>

            </div>


            <div>

                <dt class="text-xs uppercase text-slate-500">
                    Divisi
                </dt>

                <dd class="mt-1 font-bold">
                    {{
                        $visit->employee?->division?->name
                        ?: '-'
                    }}
                </dd>

            </div>


            <div>

                <dt class="text-xs uppercase text-slate-500">
                    Jumlah orang
                </dt>

                <dd class="mt-1 font-bold">
                    {{ $visit->number_of_people ?? 1 }}
                </dd>

            </div>


            <div class="sm:col-span-2">

                <dt class="text-xs uppercase text-slate-500">
                    Keperluan
                </dt>

                <dd class="mt-1 leading-6">
                    {{ $visit->purpose }}
                </dd>

            </div>

        </dl>

    </div>



    {{-- BAGIAN KANAN --}}
    <div class="space-y-5">


        {{-- WAKTU --}}
        <div class="rounded-3xl bg-slate-900 p-6 text-white">

            <p class="text-xs uppercase text-slate-400">
                Waktu daftar
            </p>

            <p class="mt-2 text-xl font-bold">

                {{
                    $visit->check_in_at
                        ? $visit->check_in_at->format('d/m/Y H:i')
                        : '-'
                }}

            </p>


            <p class="mt-5 text-xs uppercase text-slate-400">
                Waktu survey
            </p>

            <p class="mt-2 text-xl font-bold">

                {{
                    $visit->surveyed_at?->format('d/m/Y H:i')
                    ?: '-'
                }}

            </p>

        </div>



        {{-- RATING --}}
        <div class="rounded-3xl border border-amber-100 bg-amber-50 p-6 text-center">

            <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
                Rating Kepuasan
            </p>


            @if($visit->satisfaction_rating)

                <div class="mt-4 text-3xl tracking-wider text-amber-400">

                    {{
                        str_repeat(
                            '★',
                            $visit->satisfaction_rating
                        )
                    }}

                    <span class="text-slate-200">

                        {{
                            str_repeat(
                                '★',
                                5 - $visit->satisfaction_rating
                            )
                        }}

                    </span>

                </div>


                <p class="mt-2 font-black text-amber-800">

                    {{ $visit->satisfaction_rating }}
                    dari 5

                </p>

            @else

                <p class="mt-4 text-sm font-semibold text-slate-500">
                    Survey belum diisi
                </p>

            @endif

        </div>

    </div>

</div>



{{-- FOTO IDENTITAS --}}
<div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                Identitas Tamu
            </p>

            <h3 class="mt-1 text-lg font-black text-slate-800">
                Foto KTP / SIM
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Foto identitas hanya dapat dilihat oleh admin atau petugas yang berwenang.
            </p>

        </div>


        @if($visit->identity_photo)

            <a
                href="{{ route('admin.visits.identity', $visit) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#173b5b] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#102e47]"
            >

                <span>
                    👁
                </span>

                Lihat Identitas

            </a>

        @endif

    </div>


    @if($visit->identity_photo)

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">

            <img
                src="{{ route('admin.visits.identity', $visit) }}"
                alt="Foto Identitas {{ $visit->visitor?->name ?? 'Tamu' }}"
                class="max-h-[520px] w-full object-contain"
                loading="lazy"
            >

        </div>


        <div class="mt-3 flex items-start gap-2 rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-800">

            <span>
                🔒
            </span>

            <p>
                Foto ini merupakan data pribadi. Gunakan hanya untuk kebutuhan pencatatan dan keamanan kunjungan.
            </p>

        </div>

    @else

        <div class="mt-5 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center">

            <div class="text-4xl">
                📷
            </div>

            <p class="mt-3 text-sm font-bold text-slate-600">
                Foto identitas tidak tersedia
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Kunjungan ini kemungkinan dibuat sebelum fitur foto identitas ditambahkan.
            </p>

        </div>

    @endif

</div>

@endsection
@extends('layouts.admin')

@section('title', 'Kunjungan')

@section('page-title', 'Data Kunjungan')

@section('content')


{{-- FILTER --}}
<form
    method="GET"
    action="{{ route('admin.visits.index') }}"
    class="mb-5 rounded-2xl border border-slate-200 bg-white p-4"
>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-12">


        {{-- LOKASI --}}
        @if(auth()->user()->isAdmin())

            <div class="xl:col-span-2">

                <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500">
                    Lokasi
                </label>

                <select
                    name="location_id"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                >

                    <option value="">
                        Semua lokasi
                    </option>

                    @foreach($locations as $loc)

                        <option
                            value="{{ $loc->id }}"
                            @selected(
                                request('location_id')
                                == $loc->id
                            )
                        >
                            {{ $loc->name }}
                        </option>

                    @endforeach

                </select>

            </div>

        @endif



        {{-- PENCARIAN --}}
        <div
            class="{{
                auth()->user()->isAdmin()
                    ? 'xl:col-span-3'
                    : 'xl:col-span-4'
            }}"
        >

            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500">
                Pencarian
            </label>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nomor, nama, perusahaan..."
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >

        </div>



        {{-- STATUS --}}
        <div class="xl:col-span-2">

            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500">
                Status
            </label>

            <select
                name="status"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >

                <option value="">
                    Semua status
                </option>

                <option
                    value="active"
                    @selected(
                        request('status')
                        === 'active'
                    )
                >
                    Belum Survey
                </option>

                <option
                    value="completed"
                    @selected(
                        request('status')
                        === 'completed'
                    )
                >
                    Sudah Survey
                </option>

            </select>

        </div>



        {{-- TANGGAL AWAL --}}
        <div class="xl:col-span-2">

            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500">
                Tanggal Awal
            </label>

            <input
                type="date"
                name="date_start"
                value="{{ request('date_start') }}"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >

        </div>



        {{-- TANGGAL AKHIR --}}
        <div class="xl:col-span-2">

            <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-500">
                Tanggal Akhir
            </label>

            <input
                type="date"
                name="date_end"
                value="{{ request('date_end') }}"
                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
            >

        </div>



        {{-- BUTTON FILTER --}}
        <div class="flex items-end xl:col-span-1">

            <button
                type="submit"
                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
            >
                Filter
            </button>

        </div>

    </div>



    {{-- BAGIAN BAWAH FILTER --}}
    <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">


        {{-- INFORMASI PERIODE --}}
        <div class="text-xs leading-5 text-slate-500">

            @if(
                request('date_start')
                || request('date_end')
            )

                <span>
                    Periode:
                </span>

                <strong class="text-slate-700">

                    @if(request('date_start'))

                        {{
                            \Carbon\Carbon::parse(
                                request('date_start')
                            )->format('d/m/Y')
                        }}

                    @else

                        Awal

                    @endif


                    <span class="mx-1">
                        s/d
                    </span>


                    @if(request('date_end'))

                        {{
                            \Carbon\Carbon::parse(
                                request('date_end')
                            )->format('d/m/Y')
                        }}

                    @else

                        Sekarang

                    @endif

                </strong>

            @else

                Menampilkan semua tanggal.

            @endif

        </div>



        {{-- ACTION --}}
        <div class="flex flex-wrap gap-2">


            {{-- RESET --}}
            <a
                href="{{ route('admin.visits.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
            >
                Reset Filter
            </a>



            {{-- EXPORT --}}
            <a
                href="{{ route(
                    'admin.visits.export',
                    request()->query()
                ) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700"
            >

                <span>
                    ↓
                </span>

                Export Excel

            </a>

        </div>

    </div>

</form>



{{-- TABLE --}}
<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">


            {{-- HEADER --}}
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">

                <tr>

                    <th class="px-5 py-3">
                        Nomor
                    </th>

                    <th class="px-5 py-3">
                        Lokasi
                    </th>

                    <th class="px-5 py-3">
                        Tamu
                    </th>

                    <th class="px-5 py-3">
                        Tujuan
                    </th>

                    <th class="px-5 py-3">
                        Waktu
                    </th>

                    <th class="px-5 py-3">
                        Rating
                    </th>

                    <th class="px-5 py-3">
                        Status
                    </th>

                    <th class="px-5 py-3">
                        Identitas
                    </th>

                </tr>

            </thead>



            {{-- BODY --}}
            <tbody class="divide-y divide-slate-100">

                @forelse($visits as $visit)

                    <tr class="hover:bg-slate-50">


                        {{-- NOMOR KUNJUNGAN --}}
                        <td class="whitespace-nowrap px-5 py-4">

                            <a
                                href="{{ route(
                                    'admin.visits.show',
                                    $visit
                                ) }}"
                                class="font-bold text-emerald-700 hover:text-emerald-800"
                            >
                                {{ $visit->visit_number }}
                            </a>

                        </td>



                        {{-- LOKASI --}}
                        <td class="px-5 py-4">

                            {{
                                $visit->location?->name
                                ?? '-'
                            }}

                        </td>



                        {{-- TAMU --}}
                        <td class="px-5 py-4">

                            <p class="font-bold">

                                {{
                                    $visit->visitor?->name
                                    ?? '-'
                                }}

                            </p>

                            <p class="mt-1 text-xs text-slate-500">

                                {{
                                    $visit->visitor?->company
                                    ?? '-'
                                }}

                            </p>

                        </td>



                        {{-- PEGAWAI TUJUAN --}}
                        <td class="px-5 py-4">

                            <p>

                                {{
                                    $visit->employee_name
                                    ?: (
                                        $visit
                                            ->employee
                                            ?->name
                                        ?? '-'
                                    )
                                }}

                            </p>


                            <p class="mt-1 text-xs text-slate-500">

                                {{
                                    $visit
                                        ->employee
                                        ?->division
                                        ?->name
                                    ?? '-'
                                }}

                            </p>

                        </td>



                        {{-- WAKTU --}}
                        <td class="whitespace-nowrap px-5 py-4">

                            <p>

                                @if($visit->check_in_at)

                                    {{
                                        $visit
                                            ->check_in_at
                                            ->format(
                                                'd/m/Y H:i'
                                            )
                                    }}

                                @else

                                    -

                                @endif

                            </p>


                            <p class="mt-1 text-xs text-slate-500">

                                @if($visit->surveyed_at)

                                    {{
                                        $visit
                                            ->surveyed_at
                                            ->format(
                                                'd/m/Y H:i'
                                            )
                                    }}

                                @else

                                    Belum survey

                                @endif

                            </p>

                        </td>



                        {{-- RATING --}}
                        <td class="px-5 py-4">

                            @if($visit->satisfaction_rating)

                                <span class="text-lg tracking-wide text-amber-400">

                                    {{
                                        str_repeat(
                                            '★',
                                            $visit
                                                ->satisfaction_rating
                                        )
                                    }}

                                </span>

                            @else

                                <span class="text-slate-400">
                                    -
                                </span>

                            @endif

                        </td>



                        {{-- STATUS --}}
                        <td class="px-5 py-4">

                            <span
                                class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-bold
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

                        </td>



                        {{-- IDENTITAS --}}
                        <td class="px-5 py-4">

                            @if($visit->identity_photo)

                                <a
                                    href="{{ route(
                                        'admin.visits.identity',
                                        $visit
                                    ) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-200"
                                >

                                    <span>
                                        👁
                                    </span>

                                    Lihat

                                </a>

                            @else

                                <span class="text-xs text-slate-400">
                                    -
                                </span>

                            @endif

                        </td>


                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="px-5 py-12 text-center"
                        >

                            <div class="mx-auto max-w-sm">

                                <p class="text-sm font-bold text-slate-600">
                                    Data tidak ditemukan
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-400">
                                    Tidak ada kunjungan yang sesuai dengan filter yang dipilih.
                                </p>

                                @if(
                                    request()->hasAny([
                                        'location_id',
                                        'search',
                                        'status',
                                        'date_start',
                                        'date_end',
                                    ])
                                )

                                    <a
                                        href="{{ route(
                                            'admin.visits.index'
                                        ) }}"
                                        class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white"
                                    >
                                        Reset Filter
                                    </a>

                                @endif

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>



{{-- PAGINATION --}}
<div class="mt-5">

    {{ $visits->links() }}

</div>


@endsection
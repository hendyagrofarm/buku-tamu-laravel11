@extends('layouts.kiosk')

@section('title', 'Survey Kepuasan')

@section('content')


<main class="flex min-h-screen items-center px-5 py-8 sm:px-8">

    <div class="w-full rounded-[2rem] bg-white p-6 text-center shadow-xl sm:p-9">


        {{-- ICON --}}
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-amber-100 text-3xl text-amber-500">
            ★
        </div>


        {{-- JUDUL --}}
        <p class="mt-5 text-xs font-bold uppercase tracking-[.18em] text-blue-700">
            Survey Kepuasan
        </p>


        <h1 class="mt-2 text-2xl font-black text-slate-900">
            Bagaimana pelayanan kami?
        </h1>


        <p class="mt-2 text-sm leading-6 text-slate-500">
            Sebelum pulang, silakan berikan penilaian terhadap pelayanan selama kunjungan Anda.
        </p>



        {{-- DATA TAMU --}}
        <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-left text-sm">


            {{-- NAMA --}}
            <div class="flex justify-between gap-3">

                <span class="text-slate-400">
                    Nama
                </span>

                <strong class="text-right">
                    {{ $visit->visitor?->name ?? '-' }}
                </strong>

            </div>


            {{-- MENEMUI --}}
            <div class="mt-2 flex justify-between gap-3">

                <span class="text-slate-400">
                    Menemui
                </span>

                <strong class="text-right">
                    {{ $visit->employee_name ?: '-' }}
                </strong>

            </div>


            {{-- NOMOR --}}
            <div class="mt-2 flex justify-between gap-3">

                <span class="text-slate-400">
                    Nomor
                </span>

                <strong class="text-right">
                    {{ $visit->visit_number }}
                </strong>

            </div>


            {{-- WAKTU CHECK IN --}}
            <div class="mt-2 flex justify-between gap-3">

                <span class="text-slate-400">
                    Check In
                </span>

                <strong class="text-right">

                    @if($visit->check_in_at)

                        {{
                            $visit->check_in_at
                                ->format('d/m/Y H:i')
                        }}
                        WIB

                    @else

                        -

                    @endif

                </strong>

            </div>

        </div>



        {{-- ERROR --}}
        @if($errors->any())

            <div class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $errors->first() }}
            </div>

        @endif



        {{-- FORM RATING --}}
        <form
            method="POST"
            action="{{ route('kiosk.survey.store', $visit) }}"
            class="mt-6"
        >

            @csrf


            <input
                type="hidden"
                name="rating"
                id="ratingInput"
                value="{{ old('rating') }}"
            >



            <p class="mb-3 text-sm font-bold text-slate-700">
                Pilih rating Anda
            </p>



            {{-- BINTANG --}}
            <div
                class="flex justify-center gap-1 sm:gap-2"
                role="radiogroup"
                aria-label="Rating kepuasan"
            >

                @for($i = 1; $i <= 5; $i++)

                    <button
                        type="button"
                        data-rating="{{ $i }}"
                        class="star-button p-1 text-[46px] leading-none text-slate-200 transition active:scale-90"
                        aria-label="{{ $i }} bintang"
                    >
                        ★
                    </button>

                @endfor

            </div>



            {{-- LABEL RATING --}}
            <p
                id="ratingLabel"
                class="mt-3 min-h-6 text-sm font-bold text-slate-500"
            >
                Silakan pilih bintang
            </p>



            {{-- TOMBOL KIRIM --}}
            <button
                type="submit"
                id="submitRating"
                disabled
                class="mt-5 w-full rounded-2xl bg-slate-300 px-5 py-4 text-base font-extrabold text-white transition disabled:cursor-not-allowed"
            >
                Kirim Survey
            </button>


        </form>



        {{-- BATAL --}}
        <a
            href="{{ route('kiosk.survey.index') }}"
            class="mt-4 inline-flex text-sm font-bold text-slate-400 hover:text-slate-600"
        >
            ← Kembali pilih tamu
        </a>


    </div>

</main>

@endsection



@push('scripts')

<script>

(() => {

    const input =
        document.getElementById('ratingInput');

    const stars = [
        ...document.querySelectorAll(
            '.star-button'
        )
    ];

    const label =
        document.getElementById('ratingLabel');

    const submit =
        document.getElementById('submitRating');


    const labels = [
        '',
        'Kurang Puas',
        'Cukup',
        'Baik',
        'Puas',
        'Sangat Puas'
    ];


    const applyRating = (rating) => {

        input.value = rating;


        stars.forEach(
            (star, index) => {

                star.classList.toggle(
                    'text-amber-400',
                    index < rating
                );

                star.classList.toggle(
                    'text-slate-200',
                    index >= rating
                );

            }
        );


        label.textContent =
            `${rating} Bintang — ${labels[rating]}`;


        label.className =
            'mt-3 min-h-6 text-sm font-bold text-amber-600';


        submit.disabled = false;


        submit.className =
            'mt-5 w-full rounded-2xl bg-[#173b5b] px-5 py-4 text-base font-extrabold text-white shadow-lg transition active:scale-[.99]';

    };


    stars.forEach(
        star => {

            star.addEventListener(
                'click',
                () => {

                    applyRating(
                        Number(
                            star.dataset.rating
                        )
                    );

                }
            );

        }
    );


    /*
     * Kalau validasi gagal dan browser kembali
     * ke halaman ini, rating lama tetap muncul.
     */

    if (
        input.value &&
        Number(input.value) >= 1 &&
        Number(input.value) <= 5
    ) {

        applyRating(
            Number(input.value)
        );

    }

})();

</script>

@endpush
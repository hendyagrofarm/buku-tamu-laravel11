@extends('layouts.kiosk')

@section('title', 'Check In Tamu')

@section('content')

<header class="sticky top-0 z-20 bg-[#173b5b] px-5 py-4 text-white sm:px-8">
    <div class="flex items-center gap-4">

        <a
            href="{{ route('kiosk.home') }}"
            class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-xl"
            aria-label="Kembali"
        >
            ←
        </a>

        <div>
            <p class="text-xs text-blue-100">
                BukuTamu
            </p>

            <h1 class="text-lg font-bold">
                Check In Tamu
            </h1>
        </div>

    </div>
</header>


<main class="px-5 py-6 sm:px-8 sm:py-8">

    <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-[#173b5b]">
        Lokasi:
        <strong>{{ $location->name }}</strong>.
        Isi data tamu. Setelah disimpan, survey kepuasan akan terbuka otomatis.
    </div>


    @if($errors->any())

        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

            <p class="font-bold">
                Periksa kembali data berikut:
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('kiosk.visit.store') }}"
        enctype="multipart/form-data"
        class="space-y-5"
    >

        @csrf


        {{-- NAMA --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Nama lengkap *
            </label>

            <input
                name="name"
                value="{{ old('name') }}"
                required
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                placeholder="Nama sesuai identitas"
            >

        </div>


        {{-- TELEPON --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Nomor telepon *
            </label>

            <input
                name="phone"
                value="{{ old('phone') }}"
                inputmode="tel"
                required
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                placeholder="08xxxxxxxxxx"
            >

        </div>


        {{-- PERUSAHAAN --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Asal perusahaan/instansi *
            </label>

            <input
                name="company"
                value="{{ old('company') }}"
                required
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                placeholder="Nama perusahaan / instansi"
            >

        </div>


        {{-- PEGAWAI --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Pegawai yang Ditemui *
            </label>

            <input
                name="employee_name"
                value="{{ old('employee_name') }}"
                required
                maxlength="150"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                placeholder="Ketik nama pegawai yang ditemui"
            >

            <p class="mt-2 text-xs text-slate-500">
                Silakan ketik nama pegawai yang ingin ditemui.
            </p>

        </div>


        {{-- KEPERLUAN --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Keperluan kunjungan *
            </label>

            <textarea
                name="purpose"
                rows="4"
                required
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                placeholder="Jelaskan tujuan kunjungan secara singkat"
            >{{ old('purpose') }}</textarea>

        </div>


        {{-- JUMLAH ORANG --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Jumlah orang *
            </label>

            <input
                type="number"
                min="1"
                max="20"
                name="number_of_people"
                value="{{ old('number_of_people', 1) }}"
                required
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
            >

        </div>


        {{-- FOTO IDENTITAS --}}
        <div>

            <label class="mb-2 block text-sm font-bold">
                Foto Identitas KTP / SIM *
            </label>


            <div class="rounded-2xl border border-slate-300 bg-white p-4">

                <div
                    id="identityPlaceholder"
                    class="flex min-h-[150px] flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-4 text-center"
                >

                    <div class="text-4xl">
                        📷
                    </div>

                    <p class="mt-2 text-sm font-bold text-slate-700">
                        Foto KTP atau SIM
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Tekan tombol di bawah untuk mengambil foto identitas.
                    </p>

                </div>


                <div
                    id="identityPreviewWrapper"
                    class="hidden"
                >

                    <img
                        id="identityPreview"
                        src=""
                        alt="Preview Foto Identitas"
                        class="max-h-[300px] w-full rounded-xl bg-slate-100 object-contain"
                    >

                </div>


                <input
                    type="file"
                    id="identity_photo"
                    name="identity_photo"
                    accept="image/jpeg,image/png,image/webp,image/*"
                    capture="environment"
                    required
                    class="hidden"
                >


                <label
                    for="identity_photo"
                    class="mt-4 flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-blue-50 px-4 py-3 text-sm font-bold text-blue-700 active:scale-[.99]"
                >

                    <span class="text-lg">
                        📷
                    </span>

                    <span id="identityButtonText">
                        Ambil Foto KTP / SIM
                    </span>

                </label>


                <button
                    type="button"
                    id="removeIdentityPhoto"
                    class="mt-2 hidden w-full rounded-xl bg-red-50 px-4 py-3 text-sm font-bold text-red-600"
                >
                    Hapus Foto
                </button>


                <p class="mt-3 text-center text-[11px] leading-5 text-slate-400">
                    Gunakan kamera belakang dan pastikan tulisan pada identitas terlihat jelas.
                </p>

                <p
                    id="identityFileError"
                    class="mt-2 hidden text-center text-xs font-semibold text-red-600"
                ></p>

            </div>

        </div>


        {{-- PRIVACY --}}
        <label class="flex items-start gap-3 rounded-2xl bg-slate-100 p-4 text-sm leading-6 text-slate-600">

            <input
                type="checkbox"
                name="privacy"
                value="1"
                class="mt-1 h-5 w-5 rounded border-slate-300 text-blue-700"
                @checked(old('privacy'))
            >

            <span>
                Saya menyetujui penggunaan data dan foto identitas ini untuk pencatatan dan keamanan kunjungan.
            </span>

        </label>


        {{-- SUBMIT --}}
        <button
            type="submit"
            id="submitButton"
            class="w-full rounded-2xl bg-[#173b5b] px-5 py-4 text-base font-extrabold text-white shadow-lg shadow-blue-900/20 active:scale-[.99]"
        >
            Simpan dan Isi Survey
        </button>

    </form>

</main>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const input =
        document.getElementById('identity_photo');

    const preview =
        document.getElementById('identityPreview');

    const previewWrapper =
        document.getElementById('identityPreviewWrapper');

    const placeholder =
        document.getElementById('identityPlaceholder');

    const removeButton =
        document.getElementById('removeIdentityPhoto');

    const buttonText =
        document.getElementById('identityButtonText');

    const fileError =
        document.getElementById('identityFileError');


    const maxFileSize =
        5 * 1024 * 1024;


    input.addEventListener('change', function () {

        fileError.classList.add('hidden');

        fileError.textContent = '';


        const file = this.files[0];


        if (!file) {

            resetPreview();

            return;

        }


        if (!file.type.startsWith('image/')) {

            fileError.textContent =
                'File harus berupa gambar.';

            fileError.classList.remove('hidden');

            this.value = '';

            resetPreview();

            return;

        }


        if (file.size > maxFileSize) {

            fileError.textContent =
                'Ukuran foto maksimal 5 MB.';

            fileError.classList.remove('hidden');

            this.value = '';

            resetPreview();

            return;

        }


        const reader =
            new FileReader();


        reader.onload = function (event) {

            preview.src =
                event.target.result;

            previewWrapper.classList.remove('hidden');

            placeholder.classList.add('hidden');

            removeButton.classList.remove('hidden');

            buttonText.textContent =
                'Ambil Ulang Foto';

        };


        reader.readAsDataURL(file);

    });


    removeButton.addEventListener('click', function () {

        input.value = '';

        resetPreview();

    });


    function resetPreview() {

        preview.src = '';

        previewWrapper.classList.add('hidden');

        placeholder.classList.remove('hidden');

        removeButton.classList.add('hidden');

        buttonText.textContent =
            'Ambil Foto KTP / SIM';

    }

});

</script>

@endpush
@extends('layouts.kiosk')
@section('title', 'Check In Tamu')
@section('content')
<header class="sticky top-0 z-20 bg-[#173b5b] px-5 py-4 text-white sm:px-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('kiosk.home') }}" class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-xl" aria-label="Kembali">←</a>
        <div><p class="text-xs text-blue-100">BukuTamu</p><h1 class="text-lg font-bold">Check In Tamu</h1></div>
    </div>
</header>
<main class="px-5 py-6 sm:px-8 sm:py-8">
    <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-[#173b5b]">Lokasi: <strong>{{ $location->name }}</strong>. Isi data tamu. Setelah disimpan, survey kepuasan akan terbuka otomatis.</div>
    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-bold">Periksa kembali data berikut:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('kiosk.visit.store') }}" class="space-y-5">@csrf
        <div><label class="mb-2 block text-sm font-bold">Nama lengkap *</label><input name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100" placeholder="Nama sesuai identitas"></div>
        <div><label class="mb-2 block text-sm font-bold">Nomor telepon *</label><input name="phone" value="{{ old('phone') }}" inputmode="tel" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100" placeholder="08xxxxxxxxxx"></div>
        <div><label class="mb-2 block text-sm font-bold">Asal perusahaan/instansi *</label><input name="company" value="{{ old('company') }}" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></div>
        <div><label class="mb-2 block text-sm font-bold">Pegawai yang Ditemui *</label><input name="employee_name" value="{{ old('employee_name') }}" required maxlength="150" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100" placeholder="Ketik nama pegawai yang ditemui"><p class="mt-2 text-xs text-slate-500">Silakan ketik nama pegawai yang ingin ditemui.</p></div>
        <div><label class="mb-2 block text-sm font-bold">Keperluan kunjungan *</label><textarea name="purpose" rows="4" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100" placeholder="Jelaskan tujuan kunjungan secara singkat">{{ old('purpose') }}</textarea></div>
        <div><label class="mb-2 block text-sm font-bold">Jumlah orang *</label><input type="number" min="1" max="20" name="number_of_people" value="{{ old('number_of_people', 1) }}" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100"></div>
        <label class="flex items-start gap-3 rounded-2xl bg-slate-100 p-4 text-sm leading-6 text-slate-600"><input type="checkbox" name="privacy" value="1" class="mt-1 h-5 w-5 rounded border-slate-300 text-blue-700" @checked(old('privacy'))><span>Saya menyetujui penggunaan data ini untuk pencatatan dan keamanan kunjungan.</span></label>
        <button class="w-full rounded-2xl bg-[#173b5b] px-5 py-4 text-base font-extrabold text-white shadow-lg shadow-blue-900/20 active:scale-[.99]">Simpan dan Isi Survey</button>
    </form>
</main>
@endsection

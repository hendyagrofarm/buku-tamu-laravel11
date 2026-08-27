@extends('layouts.admin')
@section('title','Tambah Lokasi')
@section('page-title','Tambah Lokasi')
@section('content')<form method="POST" action="{{ route('admin.locations.store') }}" class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @include('admin.locations._form')</form>@endsection

@extends('layouts.admin')
@section('title','Tambah Pegawai')
@section('page-title','Tambah Pegawai')
@section('content')<form method="POST" action="{{ route('admin.employees.store') }}" class="max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @include('admin.employees._form')</form>@endsection

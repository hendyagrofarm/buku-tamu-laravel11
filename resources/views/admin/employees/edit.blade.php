@extends('layouts.admin')
@section('title','Edit Pegawai')
@section('page-title','Edit Pegawai')
@section('content')<form method="POST" action="{{ route('admin.employees.update',$employee) }}" class="max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @method('PUT') @include('admin.employees._form')</form>@endsection

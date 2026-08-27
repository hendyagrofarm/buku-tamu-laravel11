@extends('layouts.admin')
@section('title','Tambah Divisi')
@section('page-title','Tambah Divisi')
@section('content')<form method="POST" action="{{ route('admin.divisions.store') }}" class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @include('admin.divisions._form')</form>@endsection

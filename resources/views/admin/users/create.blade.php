@extends('layouts.admin')
@section('title','Tambah Petugas')
@section('page-title','Tambah Petugas')
@section('content')<form method="POST" action="{{ route('admin.users.store') }}" class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @include('admin.users._form',['user'=>null])</form>@endsection

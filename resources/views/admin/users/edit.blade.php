@extends('layouts.admin')
@section('title','Edit Petugas')
@section('page-title','Edit Petugas')
@section('content')<form method="POST" action="{{ route('admin.users.update',$user) }}" class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @method('PUT') @include('admin.users._form')</form>@endsection

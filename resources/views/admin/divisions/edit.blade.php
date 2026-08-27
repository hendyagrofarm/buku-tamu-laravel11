@extends('layouts.admin')
@section('title','Edit Divisi')
@section('page-title','Edit Divisi')
@section('content')<form method="POST" action="{{ route('admin.divisions.update',$division) }}" class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @method('PUT') @include('admin.divisions._form')</form>@endsection

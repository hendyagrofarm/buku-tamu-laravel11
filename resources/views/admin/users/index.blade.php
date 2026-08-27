@extends('layouts.admin')
@section('title','Petugas')
@section('page-title','Master Petugas')
@section('content')
<div class="mb-5 flex items-center justify-between"><div><p class="text-sm text-slate-500">Akun petugas dibatasi berdasarkan lokasi.</p></div><a href="{{ route('admin.users.create') }}" class="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white">+ Tambah Petugas</a></div>
<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Lokasi</th><th class="px-5 py-3">Hak Akses</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr></thead><tbody class="divide-y divide-slate-100">
@foreach($users as $user)<tr><td class="px-5 py-4 font-bold">{{ $user->name }}</td><td class="px-5 py-4">{{ $user->email }}</td><td class="px-5 py-4">{{ $user->location?->name ?? 'Semua Lokasi' }}</td><td class="px-5 py-4">{{ $user->isAdmin() ? 'Administrator' : 'Petugas' }}</td><td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td><td class="px-5 py-4 text-right"><a href="{{ route('admin.users.edit',$user) }}" class="font-bold text-blue-700">Edit</a></td></tr>@endforeach
</tbody></table></div></div><div class="mt-5">{{ $users->links() }}</div>
@endsection

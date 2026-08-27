@extends('layouts.admin')
@section('title','Lokasi')
@section('page-title','Master Lokasi')
@section('content')
<div class="mb-5 flex items-center justify-between"><div><p class="text-sm text-slate-500">Kelola Pabrik, HO, cabang, atau lokasi lain.</p></div><a href="{{ route('admin.locations.create') }}" class="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white">+ Tambah Lokasi</a></div>
<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Lokasi</th><th class="px-5 py-3">Petugas</th><th class="px-5 py-3">Pegawai</th><th class="px-5 py-3">Kunjungan</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr></thead><tbody class="divide-y divide-slate-100">
@foreach($locations as $location)<tr><td class="px-5 py-4"><p class="font-bold">{{ $location->name }}</p><p class="text-xs text-slate-400">{{ $location->slug }}</p></td><td class="px-5 py-4">{{ $location->users_count }}</td><td class="px-5 py-4">{{ $location->employees_count }}</td><td class="px-5 py-4">{{ $location->visits_count }}</td><td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $location->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $location->is_active ? 'Aktif' : 'Nonaktif' }}</span></td><td class="px-5 py-4 text-right"><a href="{{ route('admin.locations.edit',$location) }}" class="font-bold text-blue-700">Edit</a></td></tr>@endforeach
</tbody></table></div></div><div class="mt-5">{{ $locations->links() }}</div>
@endsection

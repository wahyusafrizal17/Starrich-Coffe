@extends('layouts.admin')

@section('title', 'Tambahan pesanan')

@section('page_header')
    <div>
        <h1>Tambahan pesanan</h1>
        <p>Opsi tambahan di kasir (biji, susu, dll.) untuk menu minuman/kopi.</p>
    </div>
    <a href="{{ route('admin.order-addons.create') }}" class="vx-btn vx-btn-primary">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah opsi
    </a>
@endsection

@section('content')
    <div class="vx-table-wrap">
        <div class="overflow-x-auto">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="vx-text-end">Harga</th>
                        <th>Status</th>
                        <th class="vx-text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($addons as $addon)
                        <tr>
                            <td class="text-sm text-slate-600">{{ $addon->urutan }}</td>
                            <td><span class="font-mono text-xs font-semibold text-slate-700">{{ $addon->kode }}</span></td>
                            <td class="font-semibold text-slate-900">{{ $addon->label }}</td>
                            <td class="vx-text-end">{{ format_rupiah($addon->harga) }}</td>
                            <td>
                                @if ($addon->is_active)
                                    <span class="vx-badge vx-badge-primary">Aktif</span>
                                @else
                                    <span class="vx-badge">Nonaktif</span>
                                @endif
                            </td>
                            <td class="vx-text-end">
                                <div class="vx-table-actions justify-end">
                                    <a href="{{ route('admin.order-addons.edit', $addon) }}" class="vx-btn-icon" title="Edit" aria-label="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.06 19.59a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.order-addons.destroy', $addon) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tambahan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="vx-btn-icon is-danger" title="Hapus" aria-label="Hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56.515c.34-.059.68-.114 1.022-.165m11.538 0a48.667 48.667 0 0 0-7.5 0M9.75 5.625V4.875A1.125 1.125 0 0 1 10.875 3.75h2.25A1.125 1.125 0 0 1 14.25 4.875v.75"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-sm text-slate-500">
                                Belum ada tambahan. <a href="{{ route('admin.order-addons.create') }}" class="font-semibold text-blue-600 hover:underline">Tambah opsi</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="vx-table-foot">{{ $addons->links() }}</div>
    </div>
@endsection

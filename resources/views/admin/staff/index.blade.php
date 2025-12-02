@extends('layouts.adminLayout')

@section('title', 'Kelola Staff')
@section('page-title', 'Kelola Staff')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header dengan Stats & Action --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-brew-dark">Daftar Kasir</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Total {{ $stats['totalStaff'] }} kasir terdaftar
                </p>
            </div>
            <a href="{{ route('admin.staff.create') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-brew-brown hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Kasir
            </a>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama atau email kasir..."
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-brew-gold focus:ring-2 focus:ring-brew-gold/20 outline-none transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2.5 bg-brew-brown text-white rounded-lg hover:bg-brew-dark transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    @if ($search)
                        <a href="{{ route('admin.staff.index') }}"
                            class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Staff Table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if ($staff->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada kasir</h3>
                    <p class="text-gray-500 mb-4">Tambahkan kasir pertama untuk memulai.</p>
                    <a href="{{ route('admin.staff.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-brew-gold text-brew-dark font-medium rounded-lg hover:bg-brew-brown hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Kasir
                    </a>
                </div>
            @else
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Terdaftar
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($staff as $cashier)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                                                <span
                                                    class="text-brew-dark font-bold">{{ strtoupper(substr($cashier->name, 0, 1)) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $cashier->name }}</div>
                                                <div class="text-sm text-gray-500">Kasir</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $cashier->email }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-sm text-gray-500">{{ $cashier->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.staff.edit', $cashier->id) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('admin.staff.destroy', $cashier->id) }}"
                                                onsubmit="return confirm('Yakin hapus kasir {{ $cashier->name }}?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    @foreach ($staff as $cashier)
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-brew-gold rounded-full flex items-center justify-center flex-shrink-0">
                                        <span
                                            class="text-brew-dark font-bold text-lg">{{ strtoupper(substr($cashier->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="font-medium text-gray-900">{{ $cashier->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $cashier->email }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Terdaftar
                                            {{ $cashier->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('admin.staff.edit', $cashier->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.staff.destroy', $cashier->id) }}"
                                        onsubmit="return confirm('Yakin hapus kasir {{ $cashier->name }}?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($staff->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $staff->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

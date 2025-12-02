@extends('layouts.adminLayout')

@section('title', 'All Orders')
@section('page-title', 'Semua Pesanan')

@section('sidebar')
    @include('components.sidebarAdmin')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-2xl font-bold text-brew-dark">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-orange-500">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Preparing</p>
                <p class="text-2xl font-bold text-blue-500">{{ $stats['preparing'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Completed</p>
                <p class="text-2xl font-bold text-green-500">{{ $stats['completed'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500">Cancelled</p>
                <p class="text-2xl font-bold text-red-500">{{ $stats['cancelled'] ?? 0 }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="No. Order / Customer..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                        <option value="">Semua Status</option>
                        <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>
                            Pending Payment</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing
                        </option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brew-gold focus:border-brew-gold">
                </div>

                <button type="submit"
                    class="px-6 py-2 bg-brew-gold text-brew-dark font-semibold rounded-lg hover:bg-yellow-500 transition-colors">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 text-gray-600 hover:text-brew-dark">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Orders List --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if ($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Order</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Customer</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Kasir</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-600">Items</th>
                                <th class="text-right py-4 px-6 text-sm font-semibold text-gray-600">Total</th>
                                <th class="text-center py-4 px-6 text-sm font-semibold text-gray-600">Status</th>
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Waktu</th>
                                <th class="text-right py-4 px-6 text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <span class="font-mono font-medium text-brew-dark">{{ $order->order_number }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div>
                                            <p class="font-medium text-brew-dark">
                                                {{ $order->customer?->name ?? 'Walk-in' }}
                                            </p>
                                            @if ($order->customer_mood)
                                                <span class="text-xs text-gray-500">
                                                    Mood: {{ ucfirst($order->customer_mood) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-600">
                                        {{ $order->cashier?->name ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-center text-gray-600">
                                        {{ $order->orderItems->count() }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-medium text-brew-dark">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @php
                                            $statusConfig = [
                                                'pending_payment' => [
                                                    'class' => 'bg-orange-100 text-orange-700',
                                                    'label' => 'Pending',
                                                ],
                                                'paid' => ['class' => 'bg-blue-100 text-blue-700', 'label' => 'Paid'],
                                                'preparing' => [
                                                    'class' => 'bg-yellow-100 text-yellow-700',
                                                    'label' => 'Preparing',
                                                ],
                                                'ready' => [
                                                    'class' => 'bg-purple-100 text-purple-700',
                                                    'label' => 'Ready',
                                                ],
                                                'completed' => [
                                                    'class' => 'bg-green-100 text-green-700',
                                                    'label' => 'Completed',
                                                ],
                                                'cancelled' => [
                                                    'class' => 'bg-red-100 text-red-700',
                                                    'label' => 'Cancelled',
                                                ],
                                            ];
                                            $config = $statusConfig[$order->status] ?? [
                                                'class' => 'bg-gray-100 text-gray-700',
                                                'label' => ucfirst($order->status),
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 {{ $config['class'] }} text-xs rounded-full">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-500">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="text-brew-gold hover:text-brew-brown transition-colors">
                                            Detail →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-6 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-16 text-gray-500">
                    <span class="text-5xl block mb-4">📋</span>
                    <p class="text-lg">Belum ada pesanan</p>
                    <p class="text-sm mt-2">Pesanan akan muncul di sini setelah customer melakukan order</p>
                </div>
            @endif
        </div>
    </div>
@endsection

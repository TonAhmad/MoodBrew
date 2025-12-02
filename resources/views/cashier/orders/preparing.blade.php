@extends('layouts.cashierLayout')

@section('title', 'Pesanan Sedang Diproses')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brew-dark">Sedang Diproses</h1>
            <p class="text-gray-600">Pesanan yang sedang disiapkan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('cashier.orders.pending') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pending ({{ $stats['pending'] ?? 0 }})
            </a>
            <a href="{{ route('cashier.orders.completed') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Selesai ({{ $stats['completed'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Processing Indicator -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-blue-800">{{ $orders->count() }} Pesanan Sedang Diproses</p>
                <p class="text-sm text-blue-600">Tekan "Siap" ketika pesanan sudah selesai dibuat</p>
            </div>
        </div>
    </div>

    <!-- Orders Grid -->
    @if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <p class="text-gray-500">Tidak ada pesanan yang sedang diproses</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 overflow-hidden">
            <!-- Order Header -->
            <div class="bg-blue-50 px-4 py-3 flex items-center justify-between">
                <div>
                    <span class="font-bold text-brew-dark">#{{ $order->order_number }}</span>
                    <p class="text-xs text-gray-500">{{ $order->paid_at ? $order->paid_at->diffForHumans() : $order->created_at->diffForHumans() }}</p>
                </div>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full flex items-center">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-1 animate-pulse"></span>
                    Diproses
                </span>
            </div>

            <!-- Order Items -->
            <div class="p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">{{ $order->customer_name ?? 'Walk-in Customer' }}</p>
                <div class="space-y-2 mb-4">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">
                            <span class="font-medium">{{ $item->quantity }}x</span> {{ $item->menuItem->name ?? 'Item' }}
                        </span>
                    </div>
                    @if($item->notes)
                    <p class="text-xs text-gray-400 italic ml-4">📝 {{ $item->notes }}</p>
                    @endif
                    @endforeach
                </div>

                @if($order->notes)
                <div class="bg-yellow-50 p-2 rounded-lg mb-4">
                    <p class="text-xs text-yellow-700">
                        <span class="font-medium">Catatan:</span> {{ $order->notes }}
                    </p>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <form action="{{ route('cashier.orders.status', $order) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="ready">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Siap
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto refresh every 30 seconds
    setTimeout(() => {
        window.location.reload();
    }, 30000);
</script>
@endpush
@endsection

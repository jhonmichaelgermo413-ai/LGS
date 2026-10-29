{{-- Save as: resources/views/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Orders
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('orders.create') }}"
                       class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                        + New Order
                    </a>
                @endif
            </div>

            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup Date</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            @php $paymentStatus = $order->payment?->status; @endphp
                            <tr>
                                <td class="px-6 py-4">#{{ $order->id }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 capitalize">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    @if ($paymentStatus === 'paid')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Paid</span>
                                    @elseif ($paymentStatus === 'pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $order->scheduled_pickup_at?->format('M d, Y h:i A') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('orders.show', $order) }}" class="text-laundry-teal hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No orders yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (in_array(auth()->user()->role, ['staff', 'admin']))
        {{-- Demo: staff/admin see new payments without pressing refresh. Delete when not needed. --}}
        <script>setTimeout(function () { window.location.reload(); }, 10000);</script>
    @endif
</x-app-layout>
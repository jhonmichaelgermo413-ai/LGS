{{-- Save as: resources/views/orders/gcash-checkout.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            GCash Payment
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">

                <div class="bg-laundry-teal text-white p-6 text-center">
                    <p class="text-sm opacity-80">You are paying</p>
                    <p class="text-3xl font-bold mt-1">₱{{ number_format($order->total_amount, 2) }}</p>
                    <p class="text-sm opacity-80 mt-1">Order #{{ $order->id }} — LaundryGo</p>
                </div>

                <div class="p-6">
                    {{-- QR code (drawn by the script at the bottom) --}}
                    <div id="qr-box" class="mx-auto flex h-64 w-64 items-center justify-center rounded-xl border border-gray-200 bg-white p-2">
                        <span class="text-sm text-gray-400">Loading QR code…</span>
                    </div>
                    <p class="mt-3 text-center text-xs text-gray-500">Demo wallet. No real money is used.</p>

                    <ol class="mt-6 space-y-3 text-sm text-gray-700 list-decimal list-inside">
                        <li>Point your phone's camera at the code and tap the link that appears.</li>
                        <li>Check the amount of <strong>₱{{ number_format($order->total_amount, 2) }}</strong> and tap <strong>Pay</strong>.</li>
                        <li>This page updates by itself once the payment goes through.</li>
                    </ol>

                    <div id="status" aria-live="polite"
                         class="mt-6 rounded-lg bg-gray-50 p-3 text-center text-sm text-gray-600">
                        <span class="mr-2 inline-block h-2 w-2 animate-pulse rounded-full bg-laundry-teal"></span>Waiting for payment…
                    </div>

                    


                    <a href="{{ route('orders.show', $order) }}"
                       class="block text-center text-sm text-gray-500 mt-4 hover:underline">
                        Cancel and go back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>
    <script>
        (function () {
            // 1. Draw the QR code
            var box = document.getElementById('qr-box');
            try {
                var qr = qrcode(0, 'M');
                qr.addData(@json($walletUrl));
                qr.make();
                box.innerHTML = qr.createSvgTag({ scalable: true, margin: 2 });
                var svg = box.firstElementChild;
                svg.style.width = '100%';
                svg.style.height = '100%';
            } catch (e) {
                box.innerHTML = '<span class="px-4 text-center text-sm text-red-600">Could not draw the QR code. Please refresh the page.</span>';
            }

            // 2. Check every 3 seconds if the phone has paid
            var statusUrl = @json(route('orders.payment.status', $order));
            var nextUrl = @json(route('orders.show', $order));
            var statusEl = document.getElementById('status');

            var timer = setInterval(function () {
                fetch(statusUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (data) {
                        if (data && data.paid) {
                            clearInterval(timer);
                            statusEl.className = 'mt-6 rounded-lg bg-green-50 p-3 text-center text-sm font-medium text-green-800';
                            statusEl.textContent = 'Payment received. Taking you to your order…';
                            setTimeout(function () { window.location.href = nextUrl; }, 1500);
                        }
                    })
                    .catch(function () { /* try again on the next tick */ });
            }, 3000);
        })();
    </script>
</x-app-layout>
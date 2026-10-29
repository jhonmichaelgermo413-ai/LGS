{{--
    resources/views/partials/payment-section.blade.php
    Needs resources/views/partials/sample-qr.blade.php. Include this on the landing page with:
    @include('partials.payment-section')
    The notches and hole use bg-sky-50 to match the section background. If you change
    the section color, change those three too.
--}}
<section id="payment" class="bg-sky-50 px-4 py-16 sm:px-6">
    <div class="mx-auto grid max-w-5xl items-center gap-12 md:grid-cols-2">

        {{-- Instructions --}}
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Pay for your laundry with GCash
            </h2>
            <p class="mt-3 max-w-md text-slate-600">
                Scan the code, enter your total, and send. No need to bring exact change.
            </p>

            <ol class="mt-8 space-y-5">
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">1</span>
                    <p class="text-slate-700">Open <strong>GCash</strong> and tap <strong>Scan QR</strong>. Any bank or e-wallet app that supports InstaPay works too.</p>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">2</span>
                    <p class="text-slate-700">Type the total from your laundry receipt and confirm the payment.</p>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">3</span>
                    <p class="text-slate-700">Screenshot the GCash receipt and show it when you pick up your clothes.</p>
                </li>
            </ol>
        </div>

        {{-- Claim-tag style QR card --}}
        <div class="mx-auto w-full max-w-xs">
            <div class="relative rounded-3xl bg-white shadow-xl ring-1 ring-slate-200">
                {{-- hang-tag hole --}}
                <div class="absolute left-1/2 top-4 h-4 w-4 -translate-x-1/2 rounded-full bg-sky-50 ring-1 ring-slate-200"></div>

                <div class="px-6 pb-5 pt-11 text-center">
                    <p class="text-xl font-semibold text-slate-900">Scan to pay</p>
                    <p class="mt-1 text-sm text-slate-500">Works with GCash and other InstaPay apps</p>
                </div>

                {{-- perforated line --}}
                <div class="relative">
                    <div class="absolute -left-3 -top-3 h-6 w-6 rounded-full bg-sky-50"></div>
                    <div class="absolute -right-3 -top-3 h-6 w-6 rounded-full bg-sky-50"></div>
                    <div class="border-t-2 border-dashed border-slate-200"></div>
                </div>

                <div class="p-5">
                    <div class="flex justify-center">
                        @include('partials.sample-qr')
                    </div>
                </div>
            </div>

            <p class="mt-5 text-center text-sm text-slate-500">Sample QR for demo only</p>
        </div>

    </div>
</section>
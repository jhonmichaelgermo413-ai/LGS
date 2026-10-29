{{-- Save as: resources/views/demo-wallet/show.blade.php --}}
{{-- Standalone page (no layout, no Vite) because it opens on a phone that is not logged in. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Wallet</title>
    <style>
        :root { --teal: #0f766e; --ink: #1f2937; --muted: #6b7280; --bg: #f3f4f6; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
               padding: 16px; background: var(--bg); color: var(--ink);
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .card { width: 100%; max-width: 380px; background: #fff; border-radius: 16px; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .08); }
        .head { background: var(--teal); color: #fff; padding: 28px 20px; text-align: center; }
        .head small { display: block; font-size: 14px; opacity: .85; }
        .amount { font-size: 40px; font-weight: 700; margin: 6px 0; }
        .body { padding: 22px 20px 26px; }
        .row { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0;
               border-bottom: 1px solid #e5e7eb; font-size: 15px; }
        .row span:first-child { color: var(--muted); }
        .row span:last-child { text-align: right; font-weight: 600; word-break: break-all; }
        button { width: 100%; margin-top: 22px; padding: 16px; border: 0; border-radius: 12px;
                 background: var(--teal); color: #fff; font-size: 17px; font-weight: 600; }
        button:focus-visible { outline: 3px solid #99f6e4; outline-offset: 2px; }
        .note { margin: 16px 0 0; font-size: 13px; line-height: 1.5; color: var(--muted); text-align: center; }
        .check { display: block; width: 64px; height: 64px; margin: 0 auto 14px; }
    </style>
</head>
<body>
<main class="card">
    @if ($paid)
        <div class="head">
            <small>Payment successful</small>
            <div class="amount">₱{{ number_format($order->payment->amount, 2) }}</div>
            <small>Order #{{ $order->id }} — LaundryGo</small>
        </div>
        <div class="body">
            <svg class="check" viewBox="0 0 64 64" role="img" aria-label="Paid">
                <circle cx="32" cy="32" r="30" fill="#dcfce7"/>
                <path d="M19 33l9 9 17-19" fill="none" stroke="#15803d" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="row"><span>Reference no.</span><span>{{ $order->payment->reference_number }}</span></div>
            <div class="row"><span>Paid at</span><span>{{ $order->payment->paid_at?->format('M d, Y h:i A') }}</span></div>
            <p class="note">You can go back to your computer. It updates by itself.</p>
        </div>
    @else
        <div class="head">
            <small>Pay to LaundryGo</small>
            <div class="amount">₱{{ number_format($order->total_amount, 2) }}</div>
            <small>Order #{{ $order->id }}</small>
        </div>
        <div class="body">
            <div class="row"><span>Pay to</span><span>LaundryGo</span></div>
            <div class="row"><span>Order</span><span>#{{ $order->id }}</span></div>
            <div class="row"><span>Amount</span><span>₱{{ number_format($order->total_amount, 2) }}</span></div>

            <form method="POST" action="{{ $payUrl }}">
                @csrf
                <button type="submit">Pay ₱{{ number_format($order->total_amount, 2) }}</button>
            </form>
            <p class="note">Demo wallet for a school project. No real money is sent.</p>
        </div>
    @endif
</main>
</body>
</html>
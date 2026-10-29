<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Driver/staff records a manual payment as RECEIVED (COD collected in person,
     * or confirming a GCash payment handed over in person).
     */
    public function store(Request $request, Order $order)
    {
        $user = Auth::user();

        $canRecord = in_array($user->role, ['staff', 'admin']) || $order->driver_id === $user->id;

        abort_unless($canRecord, 403, 'You are not allowed to record payment for this order.');

        $validated = $request->validate([
            'method' => 'required|in:cod,gcash',
            'reference_number' => 'required_if:method,gcash|nullable|string|max:100',
        ]);

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'amount' => $order->total_amount,
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment recorded.');
    }

    /**
     * Customer selects "Cash on Delivery" - this just records their CHOICE.
     * It stays "pending" (not yet paid) until the driver/staff confirms
     * the cash was actually received upon delivery.
     */
    public function selectCod(Order $order)
    {
        $user = Auth::user();

        abort_unless($order->customer_id === $user->id, 403, 'This is not your order.');
        abort_if($order->payment && $order->payment->status === 'paid', 403, 'This order is already paid.');

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'cod',
                'reference_number' => null,
                'amount' => $order->total_amount,
                'status' => 'pending',
                'paid_at' => null,
            ]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Cash on Delivery selected. Please prepare the exact amount for the driver.');
    }

    /**
     * Show the GCash checkout page with a QR code (customer only, for their own unpaid order).
     * The QR contains a signed link to the demo wallet page (DemoWalletController).
     */
    public function showGcashCheckout(Order $order)
    {
        $user = Auth::user();

        abort_unless($order->customer_id === $user->id, 403, 'This is not your order.');
        abort_if($order->payment && $order->payment->status === 'paid', 403, 'This order is already paid.');

        // The phone must be able to reach this address. If you open the site on the PC with
        // http://127.0.0.1:8000, set DEMO_WALLET_URL in .env to your PC's LAN address,
        // e.g. DEMO_WALLET_URL=http://192.168.1.5:8000
                $relative = URL::temporarySignedRoute('wallet.show', now()->addMinutes(30), ['order' => $order->id], false);
        $base = rtrim(env('DEMO_WALLET_URL') ?: request()->getSchemeAndHttpHost(), '/');
        $walletUrl = $base . $relative;

        return view('orders.gcash-checkout', compact('order', 'walletUrl'));
    }

    /**
     * The checkout page asks this every few seconds to find out if the phone has paid.
     */
    public function status(Order $order)
    {
        abort_unless($order->customer_id === Auth::id(), 403);

        return response()->json([
            'paid' => $order->payment()->where('status', 'paid')->exists(),
        ]);
    }

    /**
     * Debug-only shortcut ("Simulate scan" button) for testing without a phone.
     * It is disabled automatically when APP_DEBUG=false.
     */
    public function confirmGcashPayment(Order $order)
    {
        abort_unless(config('app.debug'), 404);

        $user = Auth::user();

        abort_unless($order->customer_id === $user->id, 403, 'This is not your order.');
        abort_if($order->payment && $order->payment->status === 'paid', 403, 'This order is already paid.');

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'gcash',
                'reference_number' => 'GC' . now()->format('ymd') . strtoupper(Str::random(8)),
                'amount' => $order->total_amount,
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment successful via GCash!');
    }
}
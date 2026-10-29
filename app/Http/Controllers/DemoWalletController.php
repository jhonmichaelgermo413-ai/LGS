<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DemoWalletController extends Controller
{
    public function show(Order $order)
    {
        $order->load('payment');
        $paid = $order->payment?->status === 'paid';

        $payUrl = $paid
            ? null
            : URL::temporarySignedRoute('wallet.pay', now()->addMinutes(10), ['order' => $order->id], false);

        return view('demo-wallet.show', compact('order', 'paid', 'payUrl'));
    }

    public function pay(Order $order)
    {
        $order->load('payment');

        if ($order->payment?->status !== 'paid') {
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
        }

        $next = URL::temporarySignedRoute('wallet.show', now()->addMinutes(30), ['order' => $order->id], false);

        return response()->noContent(303, ['Location' => $next]);
    }
}
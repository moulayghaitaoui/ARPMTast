<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'items.product'])
            ->leftJoin('cart_items', function ($join) {
                $join->on('orders.id', '=', 'cart_items.order_id')
                    ->orderBy('cart_items.created_at', 'desc');
            })
            ->select('orders.*', 'cart_items.created_at as last_added_to_cart')
            ->get()
            ->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'total_amount' => $order->items->sum(fn ($item) => $item->price * $item->quantity),
                    'items_count' => $order->items->count(),
                    'last_added_to_cart' => $order->last_added_to_cart,
                    'completed_order_exists' => $order->status === 'completed',
                    'created_at' => $order->created_at,
                    'completed_at' => $order->completed_at,
                ];
            })
            ->sortByDesc('completed_at');

        return view('orders.index', ['orders' => $orders]);
    }
}

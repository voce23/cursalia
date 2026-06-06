<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class StudentOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('buyer_id', Auth::id())
            ->with('orderItems.course')
            ->latest()
            ->paginate(10);

        return view('student.orders.index', compact('orders'));
    }
}

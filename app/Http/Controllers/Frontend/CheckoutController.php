<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __invoke()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('course.instructor')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->course->discount > 0
            ? $item->course->discount
            : $item->course->price);

        return view('frontend.checkout', compact('cartItems', 'subtotal'));
    }
}

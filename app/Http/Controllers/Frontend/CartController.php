<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Mostrar el carrito del usuario autenticado.
     */
    public function index(): View
    {
        $cartItems = Cart::with(['course.instructor', 'course.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $subtotal = $cartItems->sum(fn (Cart $item) => $item->course->discount > 0
            ? $item->course->discount
            : $item->course->price);

        return view('frontend.cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Agregar un curso al carrito (vía fetch / AJAX).
     */
    public function addToCart(Course $course): JsonResponse
    {
        // Solo estudiantes pueden agregar al carrito
        if (Auth::user()->role === 'instructor') {
            return response()->json(['message' => 'Los instructores no pueden comprar cursos.'], 403);
        }

        // ¿Ya está en el carrito?
        if (Cart::where('user_id', Auth::id())->where('course_id', $course->id)->exists()) {
            return response()->json(['message' => 'Este curso ya está en tu carrito.'], 409);
        }

        Cart::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ]);

        $cartCount = Cart::where('user_id', Auth::id())->count();

        return response()->json([
            'message' => '¡Curso agregado al carrito!',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Eliminar un item del carrito.
     */
    public function removeFromCart(Cart $cart): RedirectResponse
    {
        // Solo el dueño puede eliminar
        abort_unless($cart->user_id === Auth::id(), 403);

        $cart->delete();

        flash()->success('Curso eliminado del carrito.');

        return redirect()->route('cart.index');
    }
}

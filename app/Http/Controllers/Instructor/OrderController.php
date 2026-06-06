<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $query = OrderItem::query()
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', Auth::id());
            })
            ->with(['course:id,title,instructor_id', 'order.customer:id,name,email'])
            ->latest();

        $totalGross = (clone $query)->sum('price');
        $totalCommission = (clone $query)->sum('platform_earning');
        $totalInstructor = (clone $query)->sum('instructor_earning');

        $sales = $query->paginate(15);

        return view('instructor.orders.index', compact(
            'sales',
            'totalGross',
            'totalCommission',
            'totalInstructor'
        ));
    }
}

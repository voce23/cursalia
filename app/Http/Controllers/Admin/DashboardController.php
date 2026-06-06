<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today()->toDateString();
        $year  = now()->year;
        $month = now()->month;

        // Ventas: TTL 5 min (datos financieros recientes)
        $todaySales = (float) Cache::remember("admin.sales.today.{$today}", 300, fn () =>
            Order::query()->whereDate('created_at', today())->sum('paid_amount')
        );

        $weekSales = (float) Cache::remember('admin.sales.week', 300, fn () =>
            Order::query()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('paid_amount')
        );

        $monthSales = (float) Cache::remember("admin.sales.month.{$year}.{$month}", 300, fn () =>
            Order::query()->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('paid_amount')
        );

        $yearSales = (float) Cache::remember("admin.sales.year.{$year}", 300, fn () =>
            Order::query()->whereYear('created_at', $year)->sum('paid_amount')
        );

        // Conteos: TTL 5 min
        $totalOrders      = Cache::remember('admin.count.orders', 300, fn () => Order::query()->count());
        $pendingCourses   = Cache::remember('admin.count.courses.pending', 300, fn () => Course::query()->where('is_approved', 'pending')->count());
        $rejectedCourses  = Cache::remember('admin.count.courses.rejected', 300, fn () => Course::query()->where('is_approved', 'rejected')->count());
        $approvedCourses  = Cache::remember('admin.count.courses.approved', 300, fn () => Course::query()->where('is_approved', 'approved')->count());
        $totalStudents    = Cache::remember('admin.count.students', 300, fn () => User::query()->where('role', 'student')->count());
        $totalInstructors = Cache::remember('admin.count.instructors', 300, fn () => User::query()->where('role', 'instructor')->count());

        // Listas recientes: TTL 2 min
        $recentCourses = Course::query()
            ->with('instructor:id,name')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'instructor_id', 'is_approved', 'created_at']);

        $recentBlogs = Blog::query()
            ->with('author:id,name')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'admin_id', 'status', 'created_at']);

        $recentOrders = Order::query()
            ->with('customer:id,name')
            ->latest()
            ->take(5)
            ->get(['id', 'invoice_id', 'buyer_id', 'paid_amount', 'status', 'created_at']);

        // Top cursos e instructores (query costosa)
        $topCourses = OrderItem::query()
            ->select('course_id')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('SUM(price) as gross_revenue')
            ->with('course:id,title,instructor_id')
            ->groupBy('course_id')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        $topInstructors = OrderItem::query()
            ->join('courses', 'order_items.course_id', '=', 'courses.id')
            ->join('users', 'courses.instructor_id', '=', 'users.id')
            ->select('courses.instructor_id', 'users.name')
            ->selectRaw('COUNT(order_items.id) as sales_count')
            ->selectRaw('SUM(order_items.instructor_earning) as total_earnings')
            ->groupBy('courses.instructor_id', 'users.name')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        // Gráfico mensual
        $monthlySalesRaw = Order::query()
            ->selectRaw('MONTH(created_at) as month_number')
            ->selectRaw('SUM(paid_amount) as total_sales')
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('month_number');

        $monthlySalesLabels = [];
        $monthlySalesData = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlySalesLabels[] = Carbon::create(now()->year, $month, 1)->translatedFormat('M');
            $monthlySalesData[] = round((float) optional($monthlySalesRaw->get($month))->total_sales, 2);
        }

        return view('admin.dashboard', compact(
            'todaySales',
            'weekSales',
            'monthSales',
            'yearSales',
            'totalOrders',
            'pendingCourses',
            'rejectedCourses',
            'approvedCourses',
            'totalStudents',
            'totalInstructors',
            'recentCourses',
            'recentBlogs',
            'recentOrders',
            'topCourses',
            'topInstructors',
            'monthlySalesLabels',
            'monthlySalesData',
        ));
    }
}

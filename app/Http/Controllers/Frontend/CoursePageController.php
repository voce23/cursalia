<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLanguage;
use App\Models\CourseLevel;
use App\Models\FeaturedCategorySection;
use App\Models\FeaturedInstructor;
use App\Models\FeatureSection;
use App\Models\HeroSection;
use App\Models\HomeMiscSection;
use App\Models\LatestCourseSection;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoursePageController extends Controller
{
    /**
     * Página de inicio con cursos destacados + categorías dinámicas.
     */
    public function home()
    {
        // NOTA: estas secciones NO se cachean con Cache::remember porque
        // serializar/deserializar modelos Eloquent es frágil en algunos
        // hostings compartidos (provoca "incomplete object" al releer del
        // caché). Las consultas directas son suficientes para esta página.
        $hero = HeroSection::query()->first();
        $featuredCategorySection = FeaturedCategorySection::query()->first();
        $latestCourseSection = LatestCourseSection::query()->first();
        $aboutSection = AboutSection::query()->first();
        $homeMiscSection = HomeMiscSection::query()->first();

        $features = FeatureSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        $featuredCourses = Course::query()
            ->where('is_approved', 'approved')
            ->where('status', 'active')
            ->with(['instructor', 'category'])
            ->withCount('lessons')
            ->latest()
            ->take((int) ($latestCourseSection?->limit_items ?: 4))
            ->get();

        $categories = CourseCategory::query()
            ->whereNull('parent_id')
            ->withCount(['allCourses' => fn ($q) => $q
                ->where('courses.is_approved', 'approved')
                ->where('courses.status', 'active'),
            ])
            ->orderByDesc('all_courses_count')
            ->take((int) ($featuredCategorySection?->limit_items ?: 10))
            ->get();

        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(12)
            ->get();

        $featuredInstructors = FeaturedInstructor::query()
            ->with('user')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(12)
            ->get();

        $latestBlogs = Blog::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with('category')
            ->latest('published_at')
            ->take(3)
            ->get();

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('frontend.home.index', compact(
            'hero',
            'features',
            'featuredCategorySection',
            'latestCourseSection',
            'aboutSection',
            'homeMiscSection',
            'featuredCourses',
            'categories',
            'brands',
            'featuredInstructors',
            'testimonials',
            'latestBlogs'
        ));
    }

    /**
     * Listado público de cursos aprobados y activos.
     */
    public function index(Request $request)
    {
        $categorySlug = $request->string('category')->toString();
        $subcategorySlug = $request->string('subcategory')->toString();
        $levelSlug = $request->string('level')->toString();
        $languageSlug = $request->string('language')->toString();
        $price = $request->string('price')->toString();
        $sort = $request->string('sort')->toString();

        $courses = Course::query()
            ->where('is_approved', 'approved')
            ->where('status', 'active')
            ->with(['instructor', 'category', 'level', 'language'])
            ->withCount('lessons')
            ->withAvg('reviews', 'rating')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('seo_description', 'LIKE', "%{$search}%");
                });
            })
            ->when($categorySlug, function ($q) use ($categorySlug) {
                // Eager load subcategories para evitar segunda query cuando es categoría padre
                $category = CourseCategory::query()
                    ->where('slug', $categorySlug)
                    ->with('subcategories:id,parent_id')
                    ->first();
                if ($category) {
                    if (is_null($category->parent_id)) {
                        $ids = $category->subcategories->pluck('id')->push($category->id)->all();
                        $q->whereIn('category_id', $ids);
                    } else {
                        $q->where('category_id', $category->id);
                    }
                }
            })
            ->when($subcategorySlug, function ($q) use ($subcategorySlug) {
                $subcategory = CourseCategory::query()
                    ->where('slug', $subcategorySlug)
                    ->whereNotNull('parent_id')
                    ->first();
                if ($subcategory) {
                    $q->where('category_id', $subcategory->id);
                }
            })
            ->when($levelSlug, function ($q) use ($levelSlug) {
                $level = CourseLevel::query()->where('slug', $levelSlug)->first();
                if ($level) {
                    $q->where('course_level_id', $level->id);
                }
            })
            ->when($languageSlug, function ($q) use ($languageSlug) {
                $language = CourseLanguage::query()->where('slug', $languageSlug)->first();
                if ($language) {
                    $q->where('course_language_id', $language->id);
                }
            })
            ->when($price === 'free', fn ($q) => $q->where('price', 0))
            ->when($price === 'paid', fn ($q) => $q->where('price', '>', 0))
            ->when($sort === 'oldest', fn ($q) => $q->oldest())
            ->when($sort === 'price_low', fn ($q) => $q->orderByRaw('CASE WHEN discount > 0 THEN discount ELSE price END asc'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByRaw('CASE WHEN discount > 0 THEN discount ELSE price END desc'))
            ->when($sort === 'rating', fn ($q) => $q->orderByDesc('reviews_avg_rating'))
            ->when(! in_array($sort, ['oldest', 'price_low', 'price_high', 'rating'], true), fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = CourseCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->with('subcategories:id,parent_id,name,slug')
            ->get(['id', 'name', 'slug']);

        $levels = CourseLevel::query()->orderBy('name')->get(['id', 'name', 'slug']);
        $languages = CourseLanguage::query()->orderBy('name')->get(['id', 'name', 'slug']);

        return view('frontend.courses.index', compact('courses', 'categories', 'levels', 'languages'));
    }

    /**
     * Detalle público de un curso.
     */
    public function show(string $slug)
    {
        $course = Course::query()
            ->where('slug', $slug)
            ->where('is_approved', 'approved')
            ->where('status', 'active')
            ->with(['instructor', 'category', 'level', 'language', 'chapters.lessons'])
            ->withCount('lessons')
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        $reviews = $course->reviews()
            ->with('user:id,name,image')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $myReview = null;
        $canReview = false;
        $isEnrolled = false;

        if (Auth::check()) {
            $isEnrolled = $course->enrollments()
                ->where('user_id', Auth::id())
                ->where('have_access', true)
                ->exists();

            $myReview = $course->reviews()->where('user_id', Auth::id())->first();
            $canReview = $isEnrolled;
        }

        $paymentsActive = \App\Http\Controllers\Admin\PaymentSettingController::isActive();

        return view('frontend.courses.show', compact('course', 'reviews', 'myReview', 'canReview', 'isEnrolled', 'paymentsActive'));
    }
}

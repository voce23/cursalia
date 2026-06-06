<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutSectionUpdateRequest;
use App\Http\Requests\Admin\FeaturedCategorySectionUpdateRequest;
use App\Http\Requests\Admin\FeatureSectionUpdateRequest;
use App\Http\Requests\Admin\HeroSectionUpdateRequest;
use App\Http\Requests\Admin\LatestCourseSectionUpdateRequest;
use App\Models\AboutSection;
use App\Models\FeaturedCategorySection;
use App\Models\FeatureSection;
use App\Models\HeroSection;
use App\Models\LatestCourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeSectionController extends Controller
{
    public function index(): View
    {
        $hero = HeroSection::firstOrCreate(['id' => 1], [
            'title' => 'Aprende las habilidades del',
            'highlight_text' => 'futuro',
        ]);

        $features = FeatureSection::query()->orderBy('sort_order')->get();

        $featuredCategorySection = FeaturedCategorySection::firstOrCreate(['id' => 1], [
            'title' => 'Explora por Categoría',
            'subtitle' => 'Encuentra el curso perfecto para ti entre nuestras áreas de conocimiento',
            'limit_items' => 10,
        ]);

        $latestCourseSection = LatestCourseSection::firstOrCreate(['id' => 1], [
            'title' => 'Cursos Destacados',
            'subtitle' => 'Los más populares entre nuestros estudiantes',
            'limit_items' => 4,
        ]);

        $aboutSection = AboutSection::firstOrCreate(['id' => 1], [
            'title' => 'Sobre Nuestra Plataforma',
            'subtitle' => 'Aprendizaje práctico con resultados reales',
            'content' => '<p>Somos una plataforma enfocada en cursos de alta calidad, con instructores expertos y contenido actualizado para tu crecimiento profesional.</p>',
            'button_text' => 'Conoce Más',
            'button_url' => '/about',
        ]);

        return view('admin.home-sections.index', compact(
            'hero',
            'features',
            'featuredCategorySection',
            'latestCourseSection',
            'aboutSection'
        ));
    }

    public function updateHero(HeroSectionUpdateRequest $request): RedirectResponse
    {
        $hero = HeroSection::firstOrCreate(['id' => 1]);

        $data = $request->only([
            'badge_text',
            'title',
            'highlight_text',
            'description',
            'primary_button_text',
            'primary_button_url',
            'secondary_button_text',
            'secondary_button_url',
            'hero_overlay_opacity',
        ]);

        if ($request->hasFile('hero_image')) {
            if ($hero->hero_image) {
                Storage::disk('public')->delete($hero->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('home', 'public');
        }

        $hero->update($data);

        flash()->success('Sección Hero actualizada correctamente.');

        return redirect()->route('admin.home-sections.index');
    }

    public function updateFeatures(FeatureSectionUpdateRequest $request): RedirectResponse
    {
        foreach ($request->validated('features') as $featureData) {
            FeatureSection::query()
                ->where('id', $featureData['id'])
                ->update([
                    'icon' => $featureData['icon'] ?? null,
                    'title' => $featureData['title'],
                    'description' => $featureData['description'] ?? null,
                    'sort_order' => $featureData['sort_order'],
                    'is_active' => (bool) ($featureData['is_active'] ?? false),
                ]);
        }

        flash()->success('Sección Features actualizada correctamente.');

        return redirect()->route('admin.home-sections.index');
    }

    public function updateFeaturedCategories(FeaturedCategorySectionUpdateRequest $request): RedirectResponse
    {
        $section = FeaturedCategorySection::firstOrCreate(['id' => 1]);
        $section->update($request->validated());

        flash()->success('Sección de categorías destacadas actualizada correctamente.');

        return redirect()->route('admin.home-sections.index');
    }

    public function updateLatestCourses(LatestCourseSectionUpdateRequest $request): RedirectResponse
    {
        $section = LatestCourseSection::firstOrCreate(['id' => 1]);
        $section->update($request->validated());

        flash()->success('Sección de últimos cursos actualizada correctamente.');

        return redirect()->route('admin.home-sections.index');
    }

    public function updateAbout(AboutSectionUpdateRequest $request): RedirectResponse
    {
        $section = AboutSection::firstOrCreate(['id' => 1]);

        $data = $request->only([
            'title',
            'subtitle',
            'content',
            'button_text',
            'button_url',
        ]);

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
            $data['image'] = $request->file('image')->store('home', 'public');
        }

        $section->update($data);

        flash()->success('Sección About actualizada correctamente.');

        return redirect()->route('admin.home-sections.index');
    }
}

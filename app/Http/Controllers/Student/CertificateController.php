<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CertificateBuilder;
use App\Models\CertificateBuilderItem;
use App\Models\Course;
use App\Models\LessonCompletion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificateController extends Controller
{
    private const DEFAULT_ITEMS = [
        'title' => ['x_position' => 170, 'y_position' => 130],
        'subtitle' => ['x_position' => 170, 'y_position' => 225],
        'description' => ['x_position' => 170, 'y_position' => 315],
        'signature' => ['x_position' => 840, 'y_position' => 560],
    ];

    public function download(Course $course): BinaryFileResponse
    {
        $user = Auth::user();

        $hasAccess = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('have_access', true)
            ->exists();

        abort_unless($hasAccess, 403);
        abort_unless($course->certificate, 404);

        $totalLessons = $course->lessons()->count();
        abort_if($totalLessons === 0, 404);

        $completedCount = LessonCompletion::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();

        abort_unless($completedCount >= $totalLessons, 403);

        $certificate = CertificateBuilder::first();
        abort_if(! $certificate || ! $certificate->background, 404);

        $items = CertificateBuilderItem::query()
            ->whereIn('element_id', array_keys(self::DEFAULT_ITEMS))
            ->get()
            ->keyBy('element_id');

        $replacements = [
            '[student_name]' => $user->name,
            '{{student_name}}' => $user->name,
            '[course_name]' => $course->title,
            '{{course_name}}' => $course->title,
            '[date]' => now()->format('d/m/Y'),
            '{{date}}' => now()->format('d/m/Y'),
            '[platform_name]' => config('app.name'),
            '{{platform_name}}' => config('app.name'),
            '[instructor_name]' => $course->instructor?->name ?? '',
            '{{instructor_name}}' => $course->instructor?->name ?? '',
        ];

        $backgroundPath = public_path('storage/' . $certificate->background);
        abort_if(! is_file($backgroundPath), 404);

        $signaturePath = null;
        if ($certificate->signature && Storage::disk('public')->exists($certificate->signature)) {
            $signaturePath = public_path('storage/' . $certificate->signature);
        }

        $pdf = Pdf::loadView('student.certificates.pdf', [
            'certificate' => $certificate,
            'items' => $items,
            'defaultItems' => self::DEFAULT_ITEMS,
            'backgroundPath' => $backgroundPath,
            'signaturePath' => $signaturePath,
            'renderedTitle' => strtr((string) $certificate->title, $replacements),
            'renderedSubtitle' => strtr((string) $certificate->sub_title, $replacements),
            'renderedDescription' => strtr((string) $certificate->description, $replacements),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('certificado-' . $course->slug . '.pdf');
    }
}
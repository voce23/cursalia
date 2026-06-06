{{-- ════════════════════════════════════════════════════════════════════
     Tema dinámico Cursalia · sobrescribe los tokens Tailwind con las
     variables CSS de la marca actual (general_settings.brand_color, etc.).
     ════════════════════════════════════════════════════════════════════ --}}
@php
    $brand  = $generalSetting->brand_color  ?? '#10B981';
    $accent = $generalSetting->accent_color ?? '#FB7185';
    $sun    = $generalSetting->sun_color    ?? '#FBBF24';
    $ink    = $generalSetting->ink_color    ?? '#1F2933';

    // Genera la escala 50-900 a partir del color base aplicando mezcla con
    // blanco/negro (función simple sin dependencias).
    $shades = function (string $hex): array {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            $hex = '10B981';
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $mix = function (int $c, int $with, float $w): int {
            return (int) round($c * (1 - $w) + $with * $w);
        };
        $hexOf = fn ($r, $g, $b) => sprintf('#%02x%02x%02x', $r, $g, $b);
        return [
            50  => $hexOf($mix($r, 255, 0.92), $mix($g, 255, 0.92), $mix($b, 255, 0.92)),
            100 => $hexOf($mix($r, 255, 0.84), $mix($g, 255, 0.84), $mix($b, 255, 0.84)),
            200 => $hexOf($mix($r, 255, 0.68), $mix($g, 255, 0.68), $mix($b, 255, 0.68)),
            300 => $hexOf($mix($r, 255, 0.50), $mix($g, 255, 0.50), $mix($b, 255, 0.50)),
            400 => $hexOf($mix($r, 255, 0.25), $mix($g, 255, 0.25), $mix($b, 255, 0.25)),
            500 => $hexOf($r, $g, $b),
            600 => $hexOf($mix($r, 0, 0.18), $mix($g, 0, 0.18), $mix($b, 0, 0.18)),
            700 => $hexOf($mix($r, 0, 0.35), $mix($g, 0, 0.35), $mix($b, 0, 0.35)),
            800 => $hexOf($mix($r, 0, 0.55), $mix($g, 0, 0.55), $mix($b, 0, 0.55)),
            900 => $hexOf($mix($r, 0, 0.70), $mix($g, 0, 0.70), $mix($b, 0, 0.70)),
        ];
    };

    $brandS  = $shades($brand);
    $accentS = $shades($accent);
    $sunS    = $shades($sun);

    $fontDisplay = $generalSetting->font_display ?? 'Poppins';
    $fontBody    = $generalSetting->font_body    ?? 'Inter';
@endphp

<style>
    /*
     * Estos custom properties tienen MAYOR especificidad que los del @theme
     * de Tailwind 4 (que llegan vía build), por lo que sobrescriben en
     * tiempo de render sin necesidad de recompilar.
     */
    :root {
        --color-brand-50:  {{ $brandS[50]  }};
        --color-brand-100: {{ $brandS[100] }};
        --color-brand-200: {{ $brandS[200] }};
        --color-brand-300: {{ $brandS[300] }};
        --color-brand-400: {{ $brandS[400] }};
        --color-brand-500: {{ $brandS[500] }};
        --color-brand-600: {{ $brandS[600] }};
        --color-brand-700: {{ $brandS[700] }};
        --color-brand-800: {{ $brandS[800] }};
        --color-brand-900: {{ $brandS[900] }};

        --color-coral-100: {{ $accentS[100] }};
        --color-coral-200: {{ $accentS[200] }};
        --color-coral-300: {{ $accentS[300] }};
        --color-coral-400: {{ $accentS[400] }};
        --color-coral-500: {{ $accentS[500] }};
        --color-coral-600: {{ $accentS[600] }};

        --color-sun-100: {{ $sunS[100] }};
        --color-sun-200: {{ $sunS[200] }};
        --color-sun-300: {{ $sunS[300] }};
        --color-sun-400: {{ $sunS[400] }};
        --color-sun-500: {{ $sunS[500] }};

        --color-ink-900: {{ $ink }};

        --font-sans:    '{{ $fontBody }}',    ui-sans-serif, system-ui, sans-serif;
        --font-display: '{{ $fontDisplay }}', '{{ $fontBody }}', ui-sans-serif, sans-serif;
    }
</style>

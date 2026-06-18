{{-- Botón flotante de WhatsApp. Aparece SOLO si:
     - el switch está activo (whatsapp_enabled)
     - hay número configurado
     - la llave de activación es válida (validación offline, sin API)
     El número y el mensaje ya existen en general_settings. --}}
@php
    // Cargamos SIEMPRE fresco desde la BD (no del $generalSetting compartido,
    // que puede venir cacheado sin los campos de WhatsApp). data_get() es
    // acceso seguro por si falta una propiedad.
    $gs = \App\Models\GeneralSetting::query()->first();
    $waNumber = preg_replace('/[^0-9]/', '', (string) data_get($gs, 'whatsapp_number'));
    $waKey = (string) data_get($gs, 'whatsapp_key');
    $waOn = $gs
        && data_get($gs, 'whatsapp_enabled')
        && $waNumber !== ''
        && $waKey !== ''
        && \App\Helpers\ActivationKey::validate($waKey, 'WA');
@endphp
@if ($waOn)
    @php
        $waMsg = data_get($gs, 'whatsapp_default_message') ?: 'Hola, vengo desde tu academia y quiero más información.';
        $waHref = 'https://wa.me/'.$waNumber.'?text='.rawurlencode($waMsg);
    @endphp
    <a href="{{ $waHref }}" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp"
       style="position:fixed;right:20px;bottom:20px;z-index:9999;width:60px;height:60px;border-radius:50%;
              background:linear-gradient(135deg,#25D366,#128C7E);box-shadow:0 8px 24px rgba(18,140,126,.45);
              display:flex;align-items:center;justify-content:center;color:#fff;font-size:30px;
              transition:transform .15s ease"
       onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
@endif

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Certificado · {{ $courseTitle }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root { --green:#10B981; --green-d:#047857; --ink:#1f2933; --soft:#6b7280; }
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Montserrat',-apple-system,Segoe UI,Roboto,Arial,sans-serif; background:#eef2f1; color:var(--ink); padding:26px; }
  .bar { max-width:1040px; margin:0 auto 18px; display:flex; gap:12px; justify-content:center; }
  .btn { display:inline-flex; align-items:center; gap:8px; border:0; border-radius:999px; padding:12px 24px; font-size:15px; font-weight:700; cursor:pointer; text-decoration:none; }
  .btn-green { background:var(--green); color:#fff; }
  .btn-ghost { background:#fff; color:var(--ink); border:1px solid #d1d5db; }

  .cert { max-width:1040px; margin:0 auto; aspect-ratio:1.414/1; background:#fffdf8;
          border:3px solid var(--green-d); box-shadow:0 18px 50px rgba(15,23,42,.12);
          padding:14px; }
  .cert-inner { width:100%; height:100%; border:1.5px solid #cfa15a; padding:6% 9%;
                display:flex; flex-direction:column; align-items:center; text-align:center; position:relative; }
  .academy { font-size:15px; letter-spacing:.22em; text-transform:uppercase; font-weight:700; color:var(--green-d); }
  .ribbon { width:54px; height:4px; background:var(--green); border-radius:3px; margin:14px 0 6px; }
  .title { font-family:'Playfair Display',Georgia,serif; font-size:38px; font-weight:800; color:var(--ink); margin-top:6px; }
  .lead { font-size:15px; color:var(--soft); margin-top:18px; }
  .name { font-family:'Playfair Display',Georgia,serif; font-size:46px; font-weight:700; color:var(--green-d); margin-top:8px;
          border-bottom:2px solid #e9e2d2; padding-bottom:10px; }
  .course { font-family:'Playfair Display',Georgia,serif; font-size:26px; font-weight:600; color:var(--ink); margin-top:14px; font-style:italic; }
  .footer { margin-top:auto; width:100%; display:flex; align-items:flex-end; justify-content:space-between; gap:20px; padding-top:26px; }
  .col { display:flex; flex-direction:column; align-items:center; min-width:200px; }
  .sig-line { width:200px; border-top:1.5px solid #9aa3a0; }
  .sig-name { font-weight:700; margin-top:6px; font-size:15px; }
  .sig-sub { font-size:12px; color:var(--soft); }
  .seal { width:84px; height:84px; border-radius:50%; background:linear-gradient(135deg,var(--green),var(--green-d));
          color:#fff; display:grid; place-items:center; font-size:34px; box-shadow:0 6px 16px rgba(4,120,87,.35); }
  .code { position:absolute; bottom:14px; left:50%; transform:translateX(-50%); font-size:11px; color:#9aa3a0; letter-spacing:.05em; }

  @media print {
    @page { size: A4 landscape; margin: 8mm; }
    body { background:#fff; padding:0; }
    .no-print { display:none !important; }
    .cert { box-shadow:none; max-width:none; width:100%; height:100%; border-color:var(--green-d); }
  }
</style>
</head>
<body>

<div class="bar no-print">
    <button class="btn btn-green" onclick="window.print()">🖨️ Guardar como PDF / Imprimir</button>
    <a class="btn btn-ghost" href="javascript:history.back()">← Volver</a>
</div>

<div class="cert">
    <div class="cert-inner">
        <p class="academy">{{ $academy }}</p>
        <div class="ribbon"></div>
        <h1 class="title">Certificado de finalización</h1>

        <p class="lead">Se otorga el presente certificado a</p>
        <p class="name">{{ $studentName }}</p>

        <p class="lead">por haber completado satisfactoriamente el curso</p>
        <p class="course">«{{ $courseTitle }}»</p>

        <div class="footer">
            <div class="col">
                <span class="sig-line"></span>
                <span class="sig-name">{{ $instructor ?: $academy }}</span>
                <span class="sig-sub">{{ $instructor ? 'Instructor' : 'Dirección' }}</span>
            </div>
            <div class="col">
                <div class="seal">✓</div>
            </div>
            <div class="col">
                <span class="sig-line"></span>
                <span class="sig-name">{{ $date }}</span>
                <span class="sig-sub">Fecha de emisión</span>
            </div>
        </div>

        <p class="code">Código de verificación: {{ $code }}</p>
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $subject ?? 'Tone Trainer' }}</title></head>
<body style="margin:0;padding:0;background:#0f0f0f;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0f0f0f;padding:20px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;max-width:600px;width:100%;">

      {{-- Header --}}
      <tr><td style="background:linear-gradient(135deg,#1a0800,#2d1100);padding:32px 40px;text-align:center;">
        <div style="font-size:36px;margin-bottom:8px;">🏋️</div>
        <h1 style="margin:0;color:#FF4500;font-size:24px;font-weight:800;letter-spacing:2px;">TONE TRAINER</h1>
        <p style="margin:6px 0 0;color:#888;font-size:13px;">Tu plataforma de bienestar</p>
      </td></tr>

      {{-- Body --}}
      <tr><td style="padding:36px 40px;">
        @if($action === 'add')
          <h2 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 8px;">🥗 Nueva Comida Asignada</h2>
          <p style="color:#aaa;font-size:14px;margin:0 0 24px;">Hola <strong style="color:#fff;">{{ $user->name }}</strong>, tu nutricionista ha añadido una nueva comida a tu plan nutricional:</p>
          <div style="background:#222;border:1px solid #2a2a2a;border-left:4px solid #FF4500;border-radius:10px;padding:18px 20px;margin-bottom:24px;">
            <div style="font-size:14px;color:#e0e0e0;line-height:1.7;">{!! $details !!}</div>
          </div>
        @elseif($action === 'delete')
          <h2 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 8px;">🗑️ Comida Eliminada</h2>
          <p style="color:#aaa;font-size:14px;margin:0 0 24px;">Hola <strong style="color:#fff;">{{ $user->name }}</strong>, se ha eliminado una comida de tu plan nutricional.</p>
        @elseif($action === 'reset')
          <h2 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 8px;">🔄 Plan Nutricional Reseteado</h2>
          <p style="color:#aaa;font-size:14px;margin:0 0 24px;">Hola <strong style="color:#fff;">{{ $user->name }}</strong>, tu plan nutricional ha sido reseteado. Próximamente recibirás un nuevo plan.</p>
        @endif

        <a href="{{ config('app.url') }}/dashboard/nutricion"
           style="display:inline-block;background:linear-gradient(90deg,#FF4500,#ff6347);color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;font-size:14px;">
          Ver Mi Plan Nutricional →
        </a>
      </td></tr>

      {{-- Footer --}}
      <tr><td style="background:#111;padding:20px 40px;text-align:center;border-top:1px solid #222;">
        <p style="color:#555;font-size:11px;margin:0;">© {{ date('Y') }} Tone Trainer. Este correo fue enviado automáticamente, no respondas a este mensaje.</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>

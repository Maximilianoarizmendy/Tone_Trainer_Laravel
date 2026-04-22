<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0f0f0f;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0f0f0f;padding:20px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#1a1a1a;border-radius:16px;overflow:hidden;max-width:600px;width:100%;">

      <tr><td style="background:linear-gradient(135deg,#1a0800,#2d1100);padding:32px 40px;text-align:center;">
        <div style="font-size:36px;margin-bottom:8px;">🔐</div>
        <h1 style="margin:0;color:#FF4500;font-size:24px;font-weight:800;letter-spacing:2px;">TONE TRAINER</h1>
        <p style="margin:6px 0 0;color:#888;font-size:13px;">Recuperación de Contraseña</p>
      </td></tr>

      <tr><td style="padding:36px 40px;">
        <h2 style="color:#fff;font-size:18px;font-weight:700;margin:0 0 8px;">¿Olvidaste tu contraseña?</h2>
        <p style="color:#aaa;font-size:14px;margin:0 0 24px;">Hola <strong style="color:#fff;">{{ $user->name }}</strong>, recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>

        <div style="text-align:center;margin-bottom:28px;">
          <a href="{{ $resetUrl }}"
             style="display:inline-block;background:linear-gradient(90deg,#FF4500,#ff6347);color:#fff;text-decoration:none;padding:14px 36px;border-radius:10px;font-weight:700;font-size:15px;">
            Restablecer Contraseña →
          </a>
        </div>

        <div style="background:#222;border-radius:8px;padding:14px 18px;margin-bottom:24px;">
          <p style="color:#666;font-size:12px;margin:0;">⏳ Este enlace expirará en <strong style="color:#aaa;">1 hora</strong>.</p>
        </div>

        <p style="color:#555;font-size:12px;margin:0;">Si no solicitaste este cambio, puedes ignorar este correo y tu contraseña permanecerá igual.</p>
        <p style="color:#555;font-size:12px;margin:12px 0 0;">Si el botón no funciona, copia este enlace en tu navegador:<br>
          <span style="color:#FF4500;word-break:break-all;font-size:11px;">{{ $resetUrl }}</span>
        </p>
      </td></tr>

      <tr><td style="background:#111;padding:20px 40px;text-align:center;border-top:1px solid #222;">
        <p style="color:#555;font-size:11px;margin:0;">© {{ date('Y') }} Tone Trainer. Correo automático, no respondas a este mensaje.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>

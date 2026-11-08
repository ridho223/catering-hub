<!DOCTYPE html>
<html lang="id">
<body style="margin:0;padding:0;background:#f3f6f4;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6f4;padding:32px 12px;">
  <tr>
    <td align="center">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:14px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
        <tr>
          <td style="background:#198754;padding:26px 32px;text-align:center;">
            <div style="color:#ffffff;font-size:22px;font-weight:bold;letter-spacing:0.3px;">{{ config('app.name') }}</div>
          </td>
        </tr>
        <tr>
          <td style="padding:32px 32px 8px;color:#212529;font-size:15px;line-height:1.65;">
            <h2 style="margin:0 0 12px;font-size:20px;color:#145c38;">{{ $greeting }}</h2>
            @foreach ($lines as $line)
              <p style="margin:0 0 12px;">{{ $line }}</p>
            @endforeach
          </td>
        </tr>
        <tr>
          <td align="center" style="padding:12px 32px 24px;">
            <a href="{{ $url }}" style="background:#198754;color:#ffffff;text-decoration:none;font-weight:bold;font-size:15px;padding:14px 32px;border-radius:10px;display:inline-block;">{{ $actionText }}</a>
          </td>
        </tr>
        <tr>
          <td style="padding:0 32px 28px;color:#6c757d;font-size:13px;line-height:1.6;">
            @foreach ($notes as $note)
              <p style="margin:0 0 10px;">{{ $note }}</p>
            @endforeach
            <p style="margin:0 0 10px;">Kalau tombol tidak berfungsi, salin dan tempel link ini di browser:</p>
            <p style="margin:0;word-break:break-all;"><a href="{{ $url }}" style="color:#198754;">{{ $url }}</a></p>
          </td>
        </tr>
        <tr>
          <td style="background:#f8faf8;padding:18px 32px;text-align:center;color:#8a948d;font-size:12px;line-height:1.5;">
            &copy; {{ date('Y') }} {{ config('app.name') }}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
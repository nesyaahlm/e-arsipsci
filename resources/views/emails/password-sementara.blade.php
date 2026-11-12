<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Akun E-Arsip</title>
</head>
<body style="margin:0; padding:24px; background:#f1f7f5; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:520px; margin:0 auto; background:#ffffff; border-radius:12px; padding:28px; border:1px solid #d6ebe5;">
        <h2 style="margin:0 0 4px; color:#0f766e;">E-Arsip PT Sucofindo</h2>
        <p style="margin:0 0 20px; color:#6b7280; font-size:13px;">Sistem Elektronik Arsip</p>

        <p>Halo <strong>{{ $user->name }}</strong>,</p>
        <p>Akunmu sudah dibuat oleh admin. Berikut data untuk masuk:</p>

        <table style="width:100%; border-collapse:collapse; margin:16px 0;">
            <tr>
                <td style="padding:8px 0; color:#6b7280; width:130px;">Email</td>
                <td style="padding:8px 0;"><strong>{{ $user->email }}</strong></td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#6b7280;">Password sementara</td>
                <td style="padding:8px 0;"><strong style="font-family:monospace; font-size:16px;">{{ $passwordSementara }}</strong></td>
            </tr>
        </table>

        <p style="margin:24px 0;">
            <a href="{{ route('login') }}" style="background:#0f766e; color:#ffffff; text-decoration:none; padding:10px 20px; border-radius:8px; display:inline-block;">Masuk ke E-Arsip</a>
        </p>

        <p style="font-size:13px; color:#6b7280;">Demi keamanan, segera ganti password setelah login lewat menu Profile.</p>
    </div>
</body>
</html>
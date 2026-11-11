<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; }
        .card { background-color: #ffffff; max-width: 500px; margin: 0 auto; border-radius: 8px; padding: 30px; border: 1px solid #e5e7eb; }
        .btn { display: inline-block; background-color: #1b6b63; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 11px; color: #6b7280; text-align: center; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color: #1b6b63; margin-top: 0;">E-ARSIP PT SUCOFINDO</h2>
        <p>Halo <strong>{{ $name }}</strong>,</p>
        <p>Kami menerima permintaan untuk mengatur ulang password akun Anda di <strong>Sistem E-Arsip PT Sucofindo Cabang Cilacap</strong>.</p>
        <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="btn">Atur Ulang Password</a>
        </div>

        <p style="margin-top: 25px; font-size: 13px; color: #4b5563;">
            Tautan ini hanya berlaku selama <strong>30 menit</strong>. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">
        <div class="footer">
            &copy; 2026 PT Sucofindo Cabang Cilacap<br>
            Jl. Soekarno Hatta No.283, Kesugihan, Kabupaten Cilacap, Jawa Tengah 53274
        </div>
    </div>
</body>
</html>
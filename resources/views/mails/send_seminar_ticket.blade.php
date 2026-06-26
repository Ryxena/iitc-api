<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Seminar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            padding: 20px;
            color: #333;
        }
        .ticket {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #4A90E2;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        .qr {
            text-align: center;
            margin-top: 30px;
        }
        .qr img {
            width: 150px;
            height: 150px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2>Pendaftaran Seminar Berhasil - Tiket Seminar</h2>
            <p><strong>Investasi Skill dan Pengembangan Karier di Dunia Teknologi</strong></p>
        </div>
        <div class="info">
            <p><strong>Nama Peserta:</strong> {{ $participantName }}</p>
            <p><strong>Email:</strong> {{ $participantEmail }}</p>
            <p><strong>Tanggal:</strong> Sabtu, 27 September 2025</p>
            <p><strong>Lokasi:</strong> Aula Gedung FBIS Universitas Amikom Purwokerto </p>
            <a href="{{ config('app.web_url') }}/dashboard/seminar" 
                style="display:inline-block;
                        background-color:#4A90E2;
                        color:white;
                        padding:10px 20px;
                        border-radius:5px;
                        text-decoration:none;
                        font-weight:bold;">
                    Lihat Tiket
                </a>
        </div>

        <div class="footer">
            Terima kasih telah mendaftar. Harap tunjukkan tiket ini saat memasuki lokasi seminar.
        </div>
    </div>
</body>
</html>

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
            <h2>Pendaftaran Seminar Gagal</h2>
            <p><strong>Mohon maaf {{ $participantName }} anda belum memenuhi persyaratan</strong></p>
            <p><strong>Catatan:</strong></p>
            <blockquote>
                {{ $reason }}
            </blockquote>
        </div>
        <div class="info">
            <ol>
                <li><strong>Follow Instagram IITC Intermedia</strong></li>
                <li><strong>Share Infromasi Lomba IITC</strong></li>
                <li><strong>Share Informasi Seminar</strong></li>
            </ol>
            <br>
            <a href="{{ config('app.web_url') }}/dashboard/seminar" 
                style="display:inline-block;
                        background-color:#4A90E2;
                        color:white;
                        padding:10px 20px;
                        border-radius:5px;
                        text-decoration:none;
                        font-weight:bold;">
                    Upload Ulang
                </a>
        </div>

        <div class="footer">
            Terima kasih atas antusiasme anda dalam acara seminar anda. Kami tunggu kehadiran anda.
        </div>
    </div>
</body>
</html>

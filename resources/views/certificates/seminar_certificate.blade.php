<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $participantName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        @page {
            size: 297mm 210mm landscape;
            margin: 0;
        }

        body {
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        /* ── Background image ── */
        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
        }

        /* ── All text blocks use absolute positioning for reliable DomPDF rendering ── */

        .org {
            position: absolute;
            top: 28mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #5a3e00;
            font-weight: bold;
        }

        .org-sub {
            position: absolute;
            top: 34mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            letter-spacing: 1px;
            color: #7a6040;
        }

        .cert-title {
            position: absolute;
            top: 44mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Serif', serif;
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #1a237e;
        }

        .awarded-label {
            position: absolute;
            top: 60mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Serif', serif;
            font-size: 9pt;
            font-style: italic;
            color: #666;
        }

        .name-underline {
            position: absolute;
            top: 71mm;
            left: 74mm;
            width: 149mm;
            height: 0.4mm;
            background: #c8a84b;
        }

        .participant-name {
            position: absolute;
            top: 63mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Serif', serif;
            font-size: 26pt;
            font-weight: bold;
            color: #1a237e;
        }

        .event-label {
            position: absolute;
            top: 77mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            color: #444;
        }

        .event-name {
            position: absolute;
            top: 84mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Serif', serif;
            font-size: 11pt;
            font-weight: bold;
            font-style: italic;
            color: #1a237e;
        }

        .event-date {
            position: absolute;
            top: 93mm;
            left: 0;
            width: 297mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #555;
        }

        /* ── Signature blocks ── */
        .sig-line-left {
            position: absolute;
            top: 155mm;
            left: 40mm;
            width: 60mm;
            height: 0.3mm;
            background: #333;
        }

        .sig-line-right {
            position: absolute;
            top: 155mm;
            left: 197mm;
            width: 60mm;
            height: 0.3mm;
            background: #333;
        }

        .sig-name-left {
            position: absolute;
            top: 157mm;
            left: 40mm;
            width: 60mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            font-weight: bold;
            color: #333;
        }

        .sig-title-left {
            position: absolute;
            top: 162mm;
            left: 40mm;
            width: 60mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: #666;
        }

        .sig-name-right {
            position: absolute;
            top: 157mm;
            left: 197mm;
            width: 60mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            font-weight: bold;
            color: #333;
        }

        .sig-title-right {
            position: absolute;
            top: 162mm;
            left: 197mm;
            width: 60mm;
            text-align: center;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: #666;
        }

        .cert-number {
            position: absolute;
            top: 198mm;
            left: 220mm;
            width: 70mm;
            text-align: right;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 6.5pt;
            color: #999;
        }
    </style>
</head>
<body>

    {{-- Background: your own template image via $templatePath --}}
    <img class="bg" src="{{ $templatePath }}" alt="">

    <div class="org">IITC &mdash; Informatics and Technology Competition Club</div>
    <div class="org-sub">Universitas Amikom Purwokerto</div>

    <div class="cert-title">Certificate of Participation</div>

    <div class="awarded-label">This certificate is proudly awarded to</div>

    <div class="participant-name">{{ $participantName }}</div>
    <div class="name-underline"></div>

    <div class="event-label">for attending the seminar</div>
    <div class="event-name">&ldquo;Investasi Skill dan Pengembangan Karier di Dunia Teknologi&rdquo;</div>
    <div class="event-date">Sabtu, 27 September 2025 &nbsp;&bull;&nbsp; Aula Gedung FBIS, Universitas Amikom Purwokerto</div>

    {{-- Signatures --}}
    <div class="sig-line-left"></div>
    <div class="sig-name-left">Ketua Pelaksana IITC</div>
    <div class="sig-title-left">Organizing Committee Chair</div>

    <div class="sig-line-right"></div>
    <div class="sig-name-right">Dekan FBIS</div>
    <div class="sig-title-right">Dean, Faculty of Informatics</div>

    <div class="cert-number">No. {{ $certificateNumber }}</div>

</body>
</html>

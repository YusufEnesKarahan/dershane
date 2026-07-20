<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisans Pasif - Erişim Engellendi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            font-family: system-ui, -apple-system, sans-serif;
            color: #e2e8f0;
        }
        .container {
            text-align: center;
            max-width: 520px;
            padding: 3rem 2rem;
        }
        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.95); }
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #f87171;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.05rem;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 0.75rem;
        }
        .badge {
            display: inline-block;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 1.5rem;
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔒</div>
        <h1>Lisans Pasif Durumda</h1>
        <p>
            Bu web sitesinin yayın lisansı merkezi yönetim paneli (HQ) tarafından
            pasif duruma getirilmiştir.
        </p>
        <p>
            Lütfen sistem yöneticinizle iletişime geçin.
        </p>
        <div class="badge">503 — Hizmet Geçici Olarak Durduruldu</div>
        <div class="footer">
            Powered by HQ Central Management Platform
        </div>
    </div>
</body>
</html>

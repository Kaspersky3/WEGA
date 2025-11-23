<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WEGA Auth' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #7551FF;
            --secondary: #FF6E7F;
            --dark: #0f172a;
            --glass: rgba(15, 23, 42, 0.72);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Space Grotesk', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at 10% 20%, #1e1b4b 0%, #020617 55%);
            color: #e2e8f0;
            display: flex;
            align-items: stretch;
        }
        .auth-page {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        }
        .auth-hero {
            padding: clamp(2rem, 5vw, 4rem);
            position: relative;
            overflow: hidden;
        }
        .auth-hero::after {
            content: '';
            position: absolute;
            inset: 20% auto auto 40%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255,110,127,0.4), transparent 70%);
            filter: blur(40px);
            z-index: 0;
        }
        .auth-hero-content {
            position: relative;
            z-index: 1;
            max-width: 540px;
        }
        .auth-hero h1 {
            font-size: clamp(2.2rem, 4vw, 3.2rem);
            margin-bottom: 1rem;
            color: #f8fafc;
        }
        .auth-hero p {
            color: #cbd5f5;
            line-height: 1.6;
            font-size: 1.05rem;
        }
        .auth-card-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 5vw, 4rem);
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(24px);
        }
        .auth-card {
            width: min(420px, 100%);
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 28px;
            padding: clamp(2rem, 3vw, 3rem);
            box-shadow: 0 20px 70px rgba(0,0,0,0.55);
        }
        .auth-card h2 {
            margin: 0 0 0.5rem;
            font-size: 2rem;
            color: #f1f5f9;
        }
        .auth-card .subtitle {
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.95rem 1.1rem;
            border-radius: 14px;
            background: rgba(15,23,42,0.6);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #f8fafc;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: rgba(117,81,255,0.8);
            box-shadow: 0 0 0 3px rgba(117,81,255,0.3);
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #cbd5f5;
        }
        .btn-primary {
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.95rem 1.4rem;
            border-radius: 16px;
            width: 100%;
            cursor: pointer;
            font-size: 1.05rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(117,81,255,0.35);
        }
        .form-footer {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: #cbd5f5;
            text-align: center;
        }
        .form-footer a {
            color: #fff;
            font-weight: 600;
            text-decoration: none;
        }
        .alert {
            padding: 0.9rem 1rem;
            border-radius: 14px;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #bbf7d0;
            border: 1px solid rgba(34,197,94,0.2);
        }
        .alert-error {
            background: rgba(248, 113, 113, 0.1);
            color: #fecdd3;
            border: 1px solid rgba(248, 113, 113, 0.3);
        }
        .invalid-feedback {
            color: #fecaca;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }
        @media (max-width: 900px) {
            .auth-page {
                grid-template-columns: 1fr;
            }
            .auth-hero {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="auth-page">
        <section class="auth-hero">
            <div class="auth-hero-content">
                <p style="letter-spacing: .2em; text-transform: uppercase; font-weight: 600; color: rgba(255,255,255,0.6);">WEGA PLATFORM</p>
                <h1>{{ $heroTitle ?? 'Pilotez votre performance avec élégance' }}</h1>
                <p>{{ $heroSubtitle ?? "Une expérience d'identification pensée pour des équipes ambitieuses : sécurité maximale, fluidité, et un soin particulier porté au design." }}</p>
            </div>
        </section>
        <section class="auth-card-wrapper">
            <div class="auth-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <div>
                        <span style="font-size:0.85rem; text-transform:uppercase; letter-spacing:.2em; color:#94a3b8;">{{ $badge ?? 'WEGA AUTH' }}</span>
                        <h2>{{ $title ?? 'Connexion' }}</h2>
                        <p class="subtitle">{{ $subtitle ?? 'Rejoignez votre espace sécurisé' }}</p>
                    </div>
                    <div style="width:48px;height:48px;border-radius:14px;background:rgba(117,81,255,0.15);display:flex;align-items:center;justify-content:center;color:#fff;">
                        <i class="{{ $icon ?? 'bi bi-shield-lock' }}" style="font-size:1.4rem;"></i>
                    </div>
                </div>
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Oups !</strong>
                        <ul style="margin:0.5rem 0 0 1rem; padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </section>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WEGA Auth' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Variables du design system WEGA */
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --primary-light: #6366F1;
            --primary-50: #EEF2FF;
            --success: #10B981;
            --success-bg: #D1FAE5;
            --success-text: #065F46;
            --danger: #EF4444;
            --danger-bg: #FEE2E2;
            --danger-text: #991B1B;
            --warning: #F59E0B;
            --warning-bg: #FEF3C7;
            --warning-text: #92400E;
            --info: #3B82F6;
            --info-bg: #DBEAFE;
            --info-text: #1E40AF;
            --bg-primary: #FFFFFF;
            --bg-secondary: #F8FAFC;
            --bg-tertiary: #F1F5F9;
            --border: #E2E8F0;
            --border-dark: #CBD5E1;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-muted: #64748B;
            --text-inverse: #FFFFFF;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        * { 
            box-sizing: border-box; 
        }
        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .auth-page {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .auth-hero {
            padding: 2rem;
            position: relative;
        }
        .auth-hero-content {
            max-width: 500px;
        }
        .auth-hero-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            background: var(--primary-50);
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
        }
        .auth-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }
        .auth-hero p {
            color: var(--text-secondary);
            line-height: 1.7;
            font-size: 1.125rem;
            margin-bottom: 0;
        }
        .auth-card-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-xl);
        }
        .auth-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        .auth-card-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            background: var(--primary-50);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }
        .auth-card-icon i {
            font-size: 1.75rem;
        }
        .auth-card-badge {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            display: block;
        }
        .auth-card h2 {
            margin: 0 0 0.5rem;
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .auth-card .subtitle {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            margin-bottom: 0;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            background: var(--bg-primary);
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }
        .form-control:hover {
            border-color: var(--border-dark);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .form-control::placeholder {
            color: var(--text-muted);
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        .btn-primary {
            background: var(--primary);
            border: none;
            color: var(--text-inverse);
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            border-radius: var(--radius-md);
            width: 100%;
            cursor: pointer;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .form-footer {
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-align: center;
        }
        .form-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.9375rem;
            border: 1px solid;
        }
        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: var(--success);
        }
        .alert-error, .alert-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
            border-color: var(--danger);
        }
        .alert ul {
            margin: 0.5rem 0 0 1.25rem;
            padding: 0;
        }
        .invalid-feedback {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.375rem;
            font-weight: 500;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-check input[type="checkbox"] {
            width: 1.125rem;
            height: 1.125rem;
            cursor: pointer;
            accent-color: var(--primary);
        }
        .form-check label {
            margin-bottom: 0;
            font-weight: 500;
            font-size: 0.9375rem;
            color: var(--text-secondary);
            cursor: pointer;
        }
        @media (max-width: 968px) {
            .auth-page {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .auth-hero {
                text-align: center;
                padding: 1rem;
            }
            .auth-hero h1 {
                font-size: 2rem;
            }
            .auth-card {
                padding: 2rem;
            }
        }
        @media (max-width: 640px) {
            body {
                padding: 0.5rem;
            }
            .auth-card {
                padding: 1.5rem;
                border-radius: var(--radius-lg);
            }
            .auth-card h2 {
                font-size: 1.5rem;
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
                <span class="auth-hero-badge">WEGA Platform</span>
                <h1>{{ $heroTitle ?? 'Pilotez votre performance avec élégance' }}</h1>
                <p>{{ $heroSubtitle ?? "Une expérience d'identification pensée pour des équipes ambitieuses : sécurité maximale, fluidité, et un soin particulier porté au design." }}</p>
            </div>
        </section>
        <section class="auth-card-wrapper">
            <div class="auth-card">
                <div class="auth-card-header">
                    <div>
                        <span class="auth-card-badge">{{ $badge ?? 'WEGA AUTH' }}</span>
                        <h2>{{ $title ?? 'Connexion' }}</h2>
                        <p class="subtitle">{{ $subtitle ?? 'Rejoignez votre espace sécurisé' }}</p>
                    </div>
                    <div class="auth-card-icon">
                        <i class="{{ $icon ?? 'bi bi-shield-lock' }}"></i>
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


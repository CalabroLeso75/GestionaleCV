<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi — Gestionale CV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d2137 0%, #1a3a5c 50%, #0f4c35 100%);
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        body::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: rgba(46, 125, 50, 0.15);
            border-radius: 50%;
            top: -150px; right: -100px;
        }
        body::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            background: rgba(0, 102, 204, 0.15);
            border-radius: 50%;
            bottom: -100px; left: -80px;
        }

        .login-wrapper {
            display: flex;
            width: 900px;
            max-width: 95vw;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(20px);
            box-shadow: 0 40px 60px rgba(0,0,0,0.4);
            z-index: 1;
        }

        /* Left panel */
        .login-brand {
            flex: 1;
            background: linear-gradient(160deg, rgba(46,125,50,0.4), rgba(13,33,55,0.6));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .login-brand img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            padding: 10px;
            margin-bottom: 24px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }

        .login-brand h1 {
            color: #fff;
            font-size: 1.8em;
            font-weight: 700;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .login-brand p {
            color: rgba(255,255,255,0.6);
            font-size: 0.85em;
            text-align: center;
            line-height: 1.5;
        }

        .brand-badge {
            margin-top: 30px;
            padding: 6px 16px;
            background: rgba(46,125,50,0.3);
            border: 1px solid rgba(46,125,50,0.5);
            border-radius: 20px;
            color: #81c784;
            font-size: 0.75em;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Right panel — form */
        .login-form-panel {
            flex: 1;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-panel h2 {
            color: #fff;
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .login-form-panel .subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 0.85em;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 0.8em;
            font-weight: 500;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95em;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, background 0.2s;
            outline: none;
        }

        .form-group input:focus {
            border-color: rgba(46,125,50,0.8);
            background: rgba(255,255,255,0.1);
        }

        .form-group input::placeholder { color: rgba(255,255,255,0.3); }

        .error-msg {
            color: #f87171;
            font-size: 0.78em;
            margin-top: 4px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }

        .remember-row input[type="checkbox"] { accent-color: #2e7d32; }

        .remember-row label {
            color: rgba(255,255,255,0.6);
            font-size: 0.82em;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2e7d32, #43a047);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(46,125,50,0.4);
        }

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: rgba(255,255,255,0.4);
            font-size: 0.8em;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: rgba(255,255,255,0.7); }

        .session-alert {
            background: rgba(46,125,50,0.2);
            border: 1px solid rgba(46,125,50,0.4);
            border-radius: 8px;
            padding: 10px 14px;
            color: #a5d6a7;
            font-size: 0.82em;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .login-brand { display: none; }
            .login-form-panel { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">

        {{-- Brand panel --}}
        <div class="login-brand">
            <img src="{{ asset('images/logoCalabriaVerde.png') }}" alt="Logo Calabria Verde">
            <h1>Gestionale CV</h1>
            <p>Sistema Informativo Aziendale<br>Calabria Verde</p>
            <span class="brand-badge">🌿 Area Riservata</span>
        </div>

        {{-- Form panel --}}
        <div class="login-form-panel">
            <h2>Bentornato</h2>
            <p class="subtitle">Inserisci le tue credenziali per accedere</p>

            @if (session('status'))
                <div class="session-alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Indirizzo Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="nome@calabriaverde.eu"
                           required autofocus autocomplete="username">
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Ricordami su questo dispositivo</label>
                </div>

                <button type="submit" class="btn-login">Accedi al Gestionale →</button>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Password dimenticata?</a>
                @endif
            </form>
        </div>

    </div>
</body>
</html>

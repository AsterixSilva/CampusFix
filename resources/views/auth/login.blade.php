<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CampusFix</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --background: #0a0a1a;
            --card: #1a1a2e;
            --border: #2d2d44;
            --foreground: #ffffff;
            --muted-foreground: #a0a0c0;
            --accent: #6a5af0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, var(--background) 0%, #1a0a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--card);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: var(--accent);
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .logo p {
            color: var(--muted-foreground);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: var(--foreground);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--foreground);
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-group input::placeholder {
            color: var(--muted-foreground);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .remember input {
            width: auto;
            margin: 0;
        }

        .forgot a {
            color: var(--accent);
            font-size: 0.85rem;
            text-decoration: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #5a4ae0;
        }

        .register-link {
            text-align: center;
            margin-top: 24px;
            color: var(--muted-foreground);
        }

        .register-link a {
            color: var(--accent);
            text-decoration: none;
        }

        .error-message {
            background: rgba(255, 100, 100, 0.1);
            border: 1px solid rgba(255, 100, 100, 0.3);
            border-radius: 8px;
            padding: 12px;
            color: #ff6b6b;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <h1>CampusFix</h1>
                <p>Sistem Kelas dan Pengelolaan Tugas</p>
            </div>

            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autofocus autocomplete="email" placeholder="nama@contoh.com">
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                </div>

                <div class="remember-forgot">
                    <label class="remember">
                        <input type="checkbox" name="remember" id="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn">Masuk</button>
            </form>

            <div class="register-link">
                <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</body>
</html>

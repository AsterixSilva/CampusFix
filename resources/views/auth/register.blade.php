<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - CampusFix</title>
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

        .register-container {
            width: 100%;
            max-width: 480px;
        }

        .register-card {
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
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 16px;
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

        .role-selection {
            margin-top: 20px;
        }

        .role-options {
            display: grid;
            gap: 10px;
        }

        .role-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .role-option:hover {
            border-color: var(--accent);
            background: rgba(106, 90, 240, 0.1);
        }

        .role-option input {
            width: auto;
        }

        .role-label {
            flex: 1;
            color: var(--foreground);
            cursor: pointer;
        }

        .role-label strong {
            display: block;
            margin-bottom: 2px;
        }

        .role-label span {
            color: var(--muted-foreground);
            font-size: 0.8rem;
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

        .login-link {
            text-align: center;
            margin-top: 24px;
            color: var(--muted-foreground);
        }

        .login-link a {
            color: var(--accent);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="logo">
                <h1>Daftar Akun Baru</h1>
                <p>Selamat datang di CampusFix</p>
            </div>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required autocomplete="name" placeholder="Nama Anda" value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="email" placeholder="email@contoh.com" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
                </div>

                <button type="submit" class="btn">Daftar</button>
            </form>

            <div class="login-link">
                <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a></p>
            </div>
        </div>
    </div>
</body>
</html>

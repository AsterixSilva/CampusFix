<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat User Baru - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --background: #0a0a1a; --card: #1a1a2e; --border: #2d2d44;
            --foreground: #ffffff; --muted-foreground: #a0a0c0; --accent: #6a5af0;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--background); color: var(--foreground); }
        .container { max-width: 600px; margin: 30px auto; padding: 20px; }
        .card { background: var(--card); border-radius: 12px; padding: 24px; border: 1px solid var(--border); }
        .card h2 { color: var(--foreground); margin-bottom: 16px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; color: var(--muted-foreground); margin-bottom: 6px; }
        input, select { width: 100%; padding: 10px 12px; background: var(--background); border: 1px solid var(--border); border-radius: 6px; color: var(--foreground); }
        input:focus { outline: none; border-color: var(--accent); }
        .btn { display: inline-block; padding: 10px 20px; background: var(--accent); color: white; border-radius: 6px; text-decoration: none; margin-top: 8px; }
        .btn:hover { background: #5a4ae0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Buat User Baru</h2>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>
    </div>
</body>
</html>
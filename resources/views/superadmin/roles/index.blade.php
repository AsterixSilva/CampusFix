<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title Kelola Role - Super Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --background: #0a0a1a; --card: #1a1a2e; --border: #2d2d44;
            --foreground: #ffffff; --muted-foreground: #a0a0c0; --accent: #6a5af0;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--background); color: var(--foreground); }
        .container { max-width: 800px; margin: 30px auto; padding: 20px; }
        .card { background: var(--card); border-radius: 12px; padding: 24px; border: 1px solid var(--border); }
        .card h2 { color: var(--foreground); margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--muted-foreground); font-weight: 600; }
        .role-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--background); border-radius: 8px; margin-bottom: 8px; }
        .role-name { font-weight: 600; }
        .role-desc { font-size: 0.85rem; color: var(--muted-foreground); }
        .btn { padding: 6px 12px; background: var(--accent); color: white; border-radius: 4px; font-size: 0.85rem; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Kelola Role</h2>
            @if(session('success'))
                <div style="background:rgba(40,167,69,0.1); border:1px solid #28a745; padding:12px; border-radius:8px; margin-bottom:16px; color:#28a745;">
                    {{ session('success') }}
                </div>
            @endif
            @foreach($roles as $role)
            <div class="role-item">
                <div>
                    <div class="role-name">{{ $role['label'] }}</div>
                    <div class="role-desc">{{ $role['description'] }}</div>
                </div>
                <span style="color:var(--muted-foreground); font-size:0.85rem;">{{ $role['name'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
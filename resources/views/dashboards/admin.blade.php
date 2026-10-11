<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - CampusFix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --background: #0a0a1a; --card: #1a1a2e; --border: #2d2d44;
            --foreground: #ffffff; --muted-foreground: #a0a0c0; --accent: #6a5af0; --warning: #ffc107;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--background); color: var(--foreground); }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding: 20px; background: var(--card); border-radius: 12px; border: 1px solid var(--border); }
        .header h1 { color: var(--accent); font-size: 1.5rem; }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .role-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(255, 193, 7, 0.2); color: var(--warning); }
        .content { display: grid; gap: 20px; }
        .card { background: var(--card); border-radius: 12px; padding: 24px; border: 1px solid var(--border); }
        .card h2 { color: var(--foreground); margin-bottom: 16px; font-size: 1.2rem; }
        .welcome-alert { background: rgba(255, 193, 7, 0.1); border-left: 4px solid var(--warning); padding: 16px; border-radius: 8px; margin-bottom: 20px; }
        .welcome-alert h3 { color: var(--warning); margin-bottom: 8px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .stat-card { background: var(--background); padding: 16px; border-radius: 8px; border: 1px solid var(--border); text-align: center; }
        .stat-card .number { font-size: 2rem; font-weight: bold; color: var(--accent); }
        .stat-card .label { font-size: 0.85rem; color: var(--muted-foreground); margin-top: 4px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>{{ $title }}</h1>
            <div class="user-info">
                <span class="role-badge">{{ $user->role_label }}</span>
                <span>{{ $user->name }}</span>
            </div>
        </div>
        <div class="content">
            <div class="welcome-alert">
                <h3>Selamat Datang, Admin!</h3>
                <p>Silakan kelola data pengguna dan sistem sesuai kebutuhan.</p>
            </div>
            <div class="card">
                <h2>Statistik Pengguna</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="number">{{ $users->count() }}</div>
                        <div class="label">Total Pengguna</div>
                    </div>
                    <div class="stat-card">
                        <div class="number">{{ $users->where('role', 'admin')->count() + $users->where('role', 'super_admin')->count() }}</div>
                        <div class="label">Admin</div>
                    </div>
                    <div class="stat-card">
                        <div class="number">{{ $users->where('role', 'coordinator')->count() }}</div>
                        <div class="label">Coordinator</div>
                    </div>
                    <div class="stat-card">
                        <div class="number">{{ $users->where('role', 'technician')->count() }}</div>
                        <div class="label">Technician</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
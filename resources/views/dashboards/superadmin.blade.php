<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - CampusFix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --background: #0a0a1a; --card: #1a1a2e; --border: #2d2d44;
            --foreground: #ffffff; --muted-foreground: #a0a0c0; --accent: #6a5af0; --superadmin: #dc3545;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--background); color: var(--foreground); }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding: 20px; background: var(--card); border-radius: 12px; border: 1px solid var(--border); }
        .header h1 { color: var(--superadmin); font-size: 1.5rem; }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .role-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(220, 53, 69, 0.2); color: var(--superadmin); }
        .content { display: grid; gap: 20px; }
        .card { background: var(--card); border-radius: 12px; padding: 24px; border: 1px solid var(--border); }
        .card h2 { color: var(--foreground); margin-bottom: 16px; font-size: 1.2rem; }
        .welcome-alert { background: rgba(220, 53, 69, 0.1); border-left: 4px solid var(--superadmin); padding: 16px; border-radius: 8px; margin-bottom: 20px; }
        .welcome-alert h3 { color: var(--superadmin); margin-bottom: 8px; }
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
        .action-card { background: var(--background); padding: 16px; border-radius: 8px; border: 1px solid var(--border); }
        .action-card h4 { color: var(--foreground); margin-bottom: 8px; }
        .action-card p { color: var(--muted-foreground); font-size: 0.85rem; }
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
                <h3>Selamat Datang, Super Admin!</h3>
                <p>Anda memiliki akses konfigurasi sistem lengkap.</p>
            </div>
            <div class="card">
                <h2>Aksi Sistem</h2>
                <div class="action-grid">
                    <div class="action-card">
                        <h4>Kelola Pengguna</h4>
                        <p>Manage semua akun pengguna di sistem</p>
                    </div>
                    <div class="action-card">
                        <h4>Kelola Peran</h4>
                        <p>Atur peran dan hak akses pengguna</p>
                    </div>
                    <div class="action-card">
                        <h4>Setting Sistem</h4>
                        <p>Konfigurasi parameter sistem utama</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
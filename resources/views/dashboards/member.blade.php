<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Member - CampusFix</title>
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
            background: var(--background);
            color: var(--foreground);
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        .header h1 {
            color: var(--accent);
            font-size: 1.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .role-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            background: rgba(106, 90, 240, 0.2);
            color: var(--accent);
        }
        
        .content {
            display: grid;
            gap: 20px;
        }
        
        .card {
            background: var(--card);
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--border);
        }
        
        .card h2 {
            color: var(--foreground);
            margin-bottom: 16px;
            font-size: 1.2rem;
        }
        
        .welcome-alert {
            background: rgba(106, 90, 240, 0.1);
            border-left: 4px solid var(--accent);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .welcome-alert h3 {
            color: var(--accent);
            margin-bottom: 8px;
        }
        
        .role-info {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
        
        .role-info p {
            color: var(--muted-foreground);
            margin-bottom: 4px;
        }
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
                <h3>Selamat Datang, {{ $user->name }}!</h3>
                <p>Anda masuk sebagai <strong>{{ $user->role_label }}</strong>. Silakan pilih menu untuk melanjutkan kerjaan Anda.</p>
            </div>
            
            <div class="card">
                <h2>Informasi Akun</h2>
                <div class="role-info">
                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Peran:</strong> {{ $user->role_label }}</p>
                    <p><strong>Status:</strong> {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</p>
                </div>
            </div>
            
            <div class="card">
                <h2>Hak Akses yang Diberikan</h2>
                <p>Anda memiliki hak akses untuk:</p>
                <ul style="margin-top: 10px; margin-left: 20px; color: var(--muted-foreground);">
                    <li>Melengkapi profil pribadi</li>
                    <li>Menampilkan informasi profil sendiri</li>
                    <li>Mengakses tugas yang diberikan kepada anda</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
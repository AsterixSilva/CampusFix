<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CampusFix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --background: #0a0a1a; --card: #1a1a2e; --border: #2d2d44;
            --foreground: #ffffff; --muted-foreground: #a0a0c0; --accent: #6a5af0;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: var(--background); color: var(--foreground); }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding: 20px; background: var(--card); border-radius: 12px; border: 1px solid var(--border); }
        .header h1 { color: var(--accent); font-size: 1.5rem; }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .content { display: grid; gap: 20px; }
        .card { background: var(--card); border-radius: 12px; padding: 24px; border: 1px solid var(--border); }
        .card h2 { color: var(--foreground); margin-bottom: 16px; font-size: 1.2rem; }
        .btn { display: inline-block; padding: 8px 16px; background: var(--accent); color: white; border-radius: 6px; text-decoration: none; font-size: 0.9rem; transition: background 0.2s; }
        .btn:hover { background: #5a4ae0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--muted-foreground); font-weight: 600; }
        .role-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 500; }
        .badge-member { background: rgba(106, 90, 240, 0.2); color: #6a5af0; }
        .badge-technician { background: rgba(40, 167, 69, 0.2); color: #28a745; }
        .badge-coordinator { background: rgba(23, 162, 184, 0.2); color: #17a2b8; }
        .badge-admin { background: rgba(23, 162, 184, 0.2); color: #17a2b8; }
        .badge-super-admin { background: rgba(220, 53, 69, 0.2); color: #dc3545; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>{{ $title }}</h1>
            <div class="user-info">
                <span class="role-badge badge-admin">{{ $user->role_label }}</span>
                <span>{{ $user->name }}</span>
                <a href="{{ route('logout') }}" method="POST" style="display:inline;">
                    <button class="btn" style="background:#dc3545;">Logout</button>
                </a>
            </div>
        </div>
        
        <div class="content">
            <div class="card">
                <h2>Users</h2>
                <a href="{{ route('admin.users.create') }}" class="btn">Tambah User</a>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="role-badge badge-{{ str_replace('_', '-', $user->role) }}">{{ $user->role_label }}</span></td>
                            <td>{{ $user->is_active ? 'Aktif' : 'Non-aktif' }}</td>
                            <td><a href="{{ route('admin.users.show', $user) }}" class="btn">Lihat</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(this.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: new FormData(this)
            }).then(() => window.location.href = '{{ route("logout") }}');
        });
    </script>
    @endpush
</body>
</html>
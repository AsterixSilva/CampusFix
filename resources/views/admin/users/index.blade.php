<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
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
        .role-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; }
        .badge-member { background: rgba(106, 90, 240, 0.2); color: #6a5af0; }
        .badge-technician { background: rgba(40, 167, 69, 0.2); color: #28a745; }
        .badge-coordinator { background: rgba(23, 162, 184, 0.2); color: #17a2b8; }
        .badge-admin { background: rgba(23, 162, 184, 0.2); color: #17a2b8; }
        .badge-super-admin { background: rgba(220, 53, 69, 0.2); color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Daftar Pengguna</h2>
            @if(session('success'))
                <div style="background:rgba(40,167,69,0.1); border:1px solid #28a745; padding:12px; border-radius:8px; margin-bottom:16px; color:#28a745;">
                    {{ session('success') }}
                </div>
            @endif
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
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" style="color:var(--accent); text-decoration:none; margin-right:16px;">Lihat</a>
                            <a href="{{ route('admin.users.edit', $user) }}" style="color:var(--accent); text-decoration:none; margin-right:16px;">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" style="color:#dc3545; background:none; border:none; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment - Coordinator</title>
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
        .status-pending { background: rgba(255, 193, 7, 0.1); padding: 4px 8px; border-radius: 4px; color: #ffc107; }
        .status-approved { background: rgba(40, 167, 69, 0.1); padding: 4px 8px; border-radius: 4px; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Assignment yang Perlu Diverifikasi</h2>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pic</th>
                        <th>Duari</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->title ?? '-' }}</td>
                        <td>{{ $assignment->assigned_to->name ?? '-' }}</td>
                        <td>{{ $assignment->due_date?->format('d/m/Y') ?? '-' }}</td>
                        <td><span class="status-pending">Pending</span></td>
                        <td>
                            <a href="{{ route('coordinator.assignments.verify', $assignment) }}" style="color:var(--accent);">Verifikasi</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
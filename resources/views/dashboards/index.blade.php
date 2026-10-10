<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · CampusFix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('member.dashboard') }}" class="text-2xl font-bold text-blue-700">CampusFix</a>
                <h1 class="mt-2 text-3xl font-semibold">{{ $title }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $user->name }} · {{ $user->role_label }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-100">Keluar</button>
            </form>
        </header>

        <nav class="flex flex-wrap gap-3" aria-label="Aksi cepat">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Ringkasan issue">
            @foreach ([
                'Total issue' => $totalIssues,
                'Issue aktif' => $activeIssues,
                'Menunggu verifikasi' => $counts['reported'],
                'Selesai' => $counts['resolved'] + $counts['closed'],
            ] as $label => $value)
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-600">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-bold">{{ $value }}</p>
                </article>
            @endforeach
            @if ($userCount !== null)
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-600">Pengguna terdaftar</p>
                    <p class="mt-2 text-3xl font-bold">{{ $userCount }}</p>
                </article>
            @endif
            @if ($averageFeedback !== null)
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-600">Rata-rata feedback</p>
                    <p class="mt-2 text-3xl font-bold">{{ number_format((float) $averageFeedback, 1) }} / 5</p>
                </article>
            @endif
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold">Issue terbaru</h2>
            </div>
            @if ($recentIssues->isEmpty())
                <p class="px-5 py-8 text-sm text-slate-600">Belum ada issue pada ruang kerja ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-600">
                            <tr>
                                <th class="px-5 py-3 font-medium">Issue</th>
                                <th class="px-5 py-3 font-medium">Kategori</th>
                                <th class="px-5 py-3 font-medium">Lokasi</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentIssues as $issue)
                                <tr>
                                    <td class="px-5 py-3">
                                        <a class="font-medium text-blue-700 hover:underline" href="{{ route('issues.show', $issue) }}">
                                            #{{ $issue->id }} · {{ $issue->title }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3">{{ $issue->category?->name ?? '—' }}</td>
                                    <td class="px-5 py-3">{{ $issue->location?->full_path ?? '—' }}</td>
                                    <td class="px-5 py-3">{{ str($issue->status)->replace('_', ' ')->title() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold">Notifikasi</h2>
                <span class="text-sm text-slate-600">{{ $unreadNotificationCount }} belum dibaca</span>
            </div>
            @if ($notifications->isEmpty())
                <p class="px-5 py-6 text-sm text-slate-600">Belum ada notifikasi.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($notifications as $notification)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/50' }}">
                            <div>
                                <p class="font-medium">{{ $notification->data['issue_title'] ?? 'Issue CampusFix' }}</p>
                                <p class="mt-1 text-sm text-slate-600">
                                    Status {{ ! empty($notification->data['from_status']) ? str($notification->data['from_status'])->replace('_', ' ')->title().' → ' : '' }}{{ str($notification->data['to_status'] ?? 'updated')->replace('_', ' ')->title() }}
                                    · {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if (! $notification->read_at && ! empty($notification->data['issue_id']))
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button class="text-sm font-medium text-blue-700 hover:underline">Buka dan tandai dibaca</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </main>
</body>
</html>

@props(['title' => 'Beranda'])

@php
    $user = auth()->user();
    $navigation = match ($user?->role) {
        \App\Models\User::ROLE_MEMBER => [
            ['label' => 'Beranda', 'route' => route('member.dashboard'), 'patterns' => ['member.dashboard'], 'icon' => '⌂'],
            ['label' => 'Laporan saya', 'route' => route('member.dashboard').'#my-issues', 'patterns' => [], 'icon' => '▤'],
            ['label' => 'Fasilitas kampus', 'route' => route('member.dashboard').'#categories', 'patterns' => [], 'icon' => '▧'],
            ['label' => 'Konfirmasi laporan', 'route' => route('reporter.issues.confirmation'), 'patterns' => ['reporter.*'], 'icon' => '✓'],
        ],
        \App\Models\User::ROLE_TECHNICIAN => [
            ['label' => 'Beranda', 'route' => route('technician.dashboard'), 'patterns' => ['technician.dashboard'], 'icon' => '⌂'],
            ['label' => 'Antrean teknisi', 'route' => route('technician.issues.index'), 'patterns' => ['technician.issues.*'], 'icon' => '▤'],
        ],
        \App\Models\User::ROLE_COORDINATOR => [
            ['label' => 'Beranda', 'route' => route('coordinator.dashboard'), 'patterns' => ['coordinator.dashboard'], 'icon' => '⌂'],
            ['label' => 'Verifikasi laporan', 'route' => route('coordinator.issues.review'), 'patterns' => ['coordinator.issues.review'], 'icon' => '✓'],
            ['label' => 'Penugasan teknisi', 'route' => route('coordinator.issues.assign'), 'patterns' => ['coordinator.issues.assign'], 'icon' => '⇄'],
        ],
        default => [
            ['label' => 'Beranda', 'route' => route('dashboard'), 'patterns' => ['*dashboard'], 'icon' => '⌂'],
            ['label' => 'Kelola pengguna', 'route' => route('admin.users.index'), 'patterns' => ['admin.users.*'], 'icon' => '♙'],
            ['label' => 'Informasi kampus', 'route' => route('admin.announcements.index'), 'patterns' => ['admin.announcements.*'], 'icon' => '◫'],
            ...($user?->role === \App\Models\User::ROLE_SUPER_ADMIN ? [
                ['label' => 'Daftar role', 'route' => route('superadmin.roles.index'), 'patterns' => ['superadmin.roles.*'], 'icon' => '⚙'],
            ] : []),
        ],
    };
    $unreadCount = $user?->unreadNotifications()->count() ?? 0;
    $memberIssueCount = $user?->role === \App\Models\User::ROLE_MEMBER
        ? \App\Models\Issue::query()->whereHas('reporters', fn ($query) => $query->whereKey($user->getKey()))->count()
        : null;
    $initials = collect(explode(' ', trim($user?->name ?? 'CF')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
@endphp

<div class="cf-portal-frame">
    <aside class="cf-sidebar" aria-label="Navigasi CampusFix">
        <a href="{{ route('dashboard') }}" class="cf-sidebar-brand">
            <x-campusfix-mark />
            <span>CampusFix <small class="block text-[.62rem] font-medium tracking-[.16em] text-slate-500">UNIMED</small></span>
        </a>

        <nav class="cf-side-nav" aria-label="Menu utama">
            @foreach ($navigation as $item)
                <a href="{{ $item['route'] }}" @if (collect($item['patterns'])->contains(fn ($pattern) => request()->routeIs($pattern))) aria-current="page" @endif>
                    <span class="cf-side-icon" aria-hidden="true">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                    @if ($item['label'] === 'Laporan saya' && $memberIssueCount !== null)
                        <span class="cf-side-count">{{ $memberIssueCount }}</span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('help') }}" @if (request()->routeIs('help')) aria-current="page" @endif>
                <span class="cf-side-icon" aria-hidden="true">?</span>
                <span>Pusat bantuan</span>
            </a>
        </nav>

        <section class="cf-sidebar-note">
            <strong>Kampus nyaman, mulai dari kita.</strong>
            <p>Satu laporanmu membantu banyak teman.</p>
            @if ($user?->role === \App\Models\User::ROLE_MEMBER)
                <a href="{{ route('issues.create') }}" class="mt-3 inline-flex text-xs font-bold text-teal-900">Laporkan masalah <span class="ml-1" aria-hidden="true">→</span></a>
            @endif
        </section>
    </aside>

    <div class="cf-frame-main">
        <header class="cf-frame-topbar">
            <p class="cf-frame-context">{{ $title }}</p>
            <div class="cf-frame-actions">
                <time class="cf-frame-date" datetime="{{ now('Asia/Jakarta')->toIso8601String() }}" data-campus-date data-timezone="Asia/Jakarta">{{ now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</time>
                <time class="cf-frame-time" datetime="{{ now('Asia/Jakarta')->toIso8601String() }}" data-campus-clock data-timezone="Asia/Jakarta" aria-label="Waktu sekarang di Universitas Negeri Medan">{{ now('Asia/Jakarta')->format('H:i:s') }}</time>
                <a href="{{ route('dashboard') }}#notifications" aria-label="Notifikasi, {{ $unreadCount }} belum dibaca" class="relative inline-grid size-9 place-items-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-teal-800">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Zm-8 12h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @if ($unreadCount > 0)<span class="absolute right-1 top-1 size-2 rounded-full bg-rose-500"></span>@endif
                </a>
                <span class="cf-frame-avatar" aria-label="{{ $user?->name }}">{{ $initials }}</span>
                <form method="POST" action="{{ route('logout') }}" class="cf-frame-logout">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-teal-200 hover:text-teal-800">Keluar</button>
                </form>
            </div>
        </header>

        <x-campus-announcement-ticker />

        <main class="cf-frame-content">
            @if (session('success'))
                <div role="status" class="cf-alert cf-alert--success mb-5">{{ session('success') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

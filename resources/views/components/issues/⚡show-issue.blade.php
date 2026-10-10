<?php

use App\Models\Issue;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component {
    public Issue $issue;

    public function mount(Issue $issue): void
    {
        Gate::authorize('view', $issue);

        $this->issue = $issue->load([
            'category',
            'location.parent',
            'mergedInto',
            'reporters',
            'attachments',
            'statusHistories.actor',
            'comments.user',
            'assignments.team',
            'assignments.technician',
            'assignments.resolution',
            'resolutions.resolvedBy',
        ]);
    }

    public function render()
    {
        return $this->view();
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">
    <div>
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">
            &larr; Kembali ke Dashboard
        </a>

        <h1 class="mt-4 text-2xl font-bold">
            Detail Laporan #{{ $issue->id }}
        </h1>
    </div>

    <div class="space-y-4 rounded-lg border p-5">
        <div>
            <h2 class="text-lg font-semibold">
                {{ $issue->title }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Status: {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
            </p>
            <p class="mt-1 text-sm text-gray-500">Priority: {{ ucfirst($issue->priority) }}</p>
            @if ($issue->mergedInto)
                <p class="mt-2 text-sm text-amber-800">
                    This duplicate was merged into
                    <a class="font-medium underline" href="{{ route('issues.show', $issue->mergedInto) }}">
                        issue #{{ $issue->mergedInto->getKey() }}
                    </a>.
                </p>
            @endif
        </div>

        <div>
            <h3 class="font-semibold">Kategori</h3>
            <p>{{ $issue->category->name ?? 'Tidak tersedia' }}</p>
        </div>

        <div>
            <h3 class="font-semibold">Lokasi</h3>
            <p>{{ $issue->location?->full_path ?? 'Tidak tersedia' }}</p>
        </div>

        <div>
            <h3 class="font-semibold">Deskripsi</h3>
            <p class="whitespace-pre-line">{{ $issue->description }}</p>
        </div>

        <div>
            <h3 class="font-semibold">Pelapor</h3>
            <p>{{ $issue->reporters->pluck('name')->join(', ') ?: 'Tidak tersedia' }}</p>
        </div>

        @if (auth()->user()?->role === \App\Models\User::ROLE_MEMBER && $issue->status !== \App\Enums\IssueStatus::Merged->value)
            <livewire:issues.me-too :issue="$issue" />
        @endif

        <div>
            <h3 class="font-semibold">Informasi Tambahan</h3>

            <p>
                Keselamatan:
                {{ $issue->safety_flag ? 'Ya, perlu perhatian' : 'Tidak ditandai' }}
            </p>

            <p>
                Kelas/kegiatan terhambat:
                {{ $issue->class_blocked ? 'Ya' : 'Tidak' }}
            </p>
        </div>

        @if ($issue->attachments->isNotEmpty())
            <div>
                <h3 class="font-semibold">Lampiran Foto</h3>

                <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($issue->attachments as $attachment)
                        <div>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                    alt="{{ $attachment->original_name }}" class="max-h-64 rounded-lg border object-contain">
                            </a>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $attachment->original_name }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($issue->resolutions->isNotEmpty())
            <div>
                <h3 class="font-semibold">Resolution</h3>
                @foreach ($issue->resolutions as $resolution)
                    <div class="mt-2 rounded border p-3">
                        <p><span class="font-medium">Root cause:</span> {{ $resolution->root_cause }}</p>
                        <p><span class="font-medium">Action taken:</span> {{ $resolution->action_taken }}</p>
                        <p><span class="font-medium">Parts used:</span> {{ $resolution->parts_used ?: '—' }}</p>
                        <p><span class="font-medium">Minutes spent:</span> {{ $resolution->minutes_spent }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div>
            <h3 class="font-semibold">Status history</h3>
            <ol class="mt-2 space-y-2">
                @forelse ($issue->statusHistories as $history)
                    <li class="border-l-2 border-blue-200 pl-3 text-sm">
                        <p class="font-medium">
                            {{ $history->from_status ? ucfirst(str_replace('_', ' ', $history->from_status)).' → ' : '' }}
                            {{ ucfirst(str_replace('_', ' ', $history->to_status)) }}
                        </p>
                        <p class="text-gray-500">
                            {{ $history->actor?->name ?? 'System' }} · {{ $history->created_at?->format('d M Y H:i') }}
                        </p>
                        @if ($history->reason)<p class="mt-1">{{ $history->reason }}</p>@endif
                    </li>
                @empty
                    <li class="text-sm text-gray-500">Belum ada riwayat status.</li>
                @endforelse
            </ol>
        </div>

        <div>
            <h3 class="font-semibold">Progress updates</h3>
            <ul class="mt-2 space-y-2">
                @forelse ($issue->comments as $comment)
                    <li class="rounded bg-gray-50 p-3 text-sm">
                        <p>{{ $comment->body }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $comment->user->name }} · {{ $comment->created_at?->format('d M Y H:i') }}</p>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">Belum ada pembaruan pekerjaan.</li>
                @endforelse
            </ul>
        </div>
    </div>

</div>

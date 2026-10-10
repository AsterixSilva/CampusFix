<?php

use App\Models\Issue;
use Livewire\Component;

new class extends Component {
    public Issue $issue;

    public function mount(Issue $issue): void
    {
        $this->issue = $issue->load([
            'category',
            'location.parent',
            'reporter',
            'attachments',
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
        <a href="{{ route('issues.create') }}" class="text-blue-600 hover:underline">
            &larr; Kembali ke Form Laporan
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
            <p>{{ $issue->reporter->name ?? 'Tidak tersedia' }}</p>
        </div>

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
    </div>

</div>
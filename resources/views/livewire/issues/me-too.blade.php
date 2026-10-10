<div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="font-medium text-blue-900">{{ $affectedCount }} orang terdampak</p>
            <p class="text-sm text-blue-800">Konfirmasi jika kamu juga mengalami masalah yang sama.</p>
        </div>
        @if ($canConfirm && ! $hasConfirmed && ! $isMerged)
            <button wire:click="confirmImpact" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Saya juga terdampak
            </button>
        @elseif ($hasConfirmed)
            <span class="text-sm font-medium text-blue-900">Dampakmu sudah tercatat</span>
        @endif
    </div>
    @if (session()->has('me-too-status'))
        <p role="status" class="mt-2 text-sm text-emerald-800">{{ session('me-too-status') }}</p>
    @endif
</div>

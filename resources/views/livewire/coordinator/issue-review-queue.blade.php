<section class="mx-auto max-w-5xl space-y-6 p-6">
    <header>
        <h1 class="text-2xl font-semibold text-slate-900">Issue verification</h1>
        <p class="mt-1 text-sm text-slate-600">Review new reports before assigning work.</p>
    </header>

    @if (session()->has('workflow-status'))
        <div role="status" class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
            {{ session('workflow-status') }}
        </div>
    @endif

    @forelse ($issues as $issue)
        <article wire:key="issue-review-{{ $issue->getKey() }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ $issue->title ?: 'Issue #'.$issue->getKey() }}
                    </h2>
                    <p class="text-sm text-slate-600">{{ $issue->description }}</p>
                    <p class="text-xs text-slate-500">Reported {{ $issue->created_at?->diffForHumans() }}</p>
                </div>

                <form wire:submit.prevent="verify({{ $issue->getKey() }})" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="priority-{{ $issue->getKey() }}" class="block text-xs font-medium text-slate-600">Priority</label>
                        <select id="priority-{{ $issue->getKey() }}" wire:model="priorities.{{ $issue->getKey() }}" class="mt-1 rounded-lg border-slate-300 text-sm">
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">
                        Verify
                    </button>
                </form>
            </div>

            <form wire:submit.prevent="reject({{ $issue->getKey() }})" class="mt-5 space-y-2 border-t border-slate-100 pt-4">
                <label for="rejection-reason-{{ $issue->getKey() }}" class="block text-sm font-medium text-slate-700">
                    Rejection reason
                </label>
                <textarea
                    id="rejection-reason-{{ $issue->getKey() }}"
                    wire:model.defer="rejectionReasons.{{ $issue->getKey() }}"
                    rows="2"
                    maxlength="2000"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                    placeholder="Explain why this report is rejected."
                ></textarea>
                @error('rejectionReasons.'.$issue->getKey())
                    <p class="text-sm text-red-700">{{ $message }}</p>
                @enderror
                <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                    Reject report
                </button>
            </form>

            @if ($mergeCandidates->contains(fn ($candidate) => $candidate->getKey() !== $issue->getKey()
                && (string) $candidate->category_id === (string) $issue->category_id
                && (string) $candidate->location_id === (string) $issue->location_id))
                <form wire:submit.prevent="mergeDuplicate({{ $issue->getKey() }})" class="mt-4 flex flex-wrap items-end gap-3 border-t border-slate-100 pt-4">
                    <div class="min-w-64 flex-1">
                        <label for="duplicate-target-{{ $issue->getKey() }}" class="block text-sm font-medium text-slate-700">Merge as duplicate of</label>
                        <select id="duplicate-target-{{ $issue->getKey() }}" wire:model="duplicateTargets.{{ $issue->getKey() }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                            <option value="">Choose the canonical issue</option>
                            @foreach ($mergeCandidates as $candidate)
                                @if ($candidate->getKey() !== $issue->getKey()
                                    && (string) $candidate->category_id === (string) $issue->category_id
                                    && (string) $candidate->location_id === (string) $issue->location_id)
                                    <option value="{{ $candidate->getKey() }}">#{{ $candidate->getKey() }} · {{ $candidate->title }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('duplicateTargets.'.$issue->getKey())
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-medium text-amber-800 hover:bg-amber-50">
                        Merge duplicate
                    </button>
                </form>
            @endif
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-600">
            No reports are waiting for verification.
        </p>
    @endforelse

    {{ $issues->links() }}
</section>

<section class="mx-auto max-w-5xl space-y-6 p-6">
    <header>
        <h1 class="text-2xl font-semibold text-slate-900">Confirm issue resolution</h1>
        <p class="mt-1 text-sm text-slate-600">Confirm that the reported problem is fixed, or reopen it with a reason.</p>
    </header>

    @if (session()->has('workflow-status'))
        <div role="status" class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
            {{ session('workflow-status') }}
        </div>
    @endif

    @forelse ($issues as $issue)
        @php
            $status = $issue->status instanceof \App\Enums\IssueStatus
                ? $issue->status->value
                : (string) $issue->status;
        @endphp

        <article wire:key="reporter-confirmation-{{ $issue->getKey() }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $issue->title ?: 'Issue #'.$issue->getKey() }}
                </h2>
                <p class="mt-1 text-sm text-slate-600">{{ $issue->description }}</p>
                <p class="mt-2 text-xs text-slate-500">
                    Status: {{ ucwords(str_replace('_', ' ', $status)) }}
                    @if ($issue->location)
                        · {{ $issue->location->full_path }}
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-4">
                @if ($status === 'resolved')
                    <form wire:submit.prevent="closeIssue({{ $issue->getKey() }})" class="w-full space-y-2">
                        <label for="feedback-rating-{{ $issue->getKey() }}" class="block text-sm font-medium text-slate-700">How was the resolution?</label>
                        <select id="feedback-rating-{{ $issue->getKey() }}" wire:model="feedbackForms.{{ $issue->getKey() }}.rating" required class="rounded-lg border-slate-300 text-sm">
                            <option value="">Choose a rating</option>
                            <option value="5">5 · Very satisfied</option>
                            <option value="4">4 · Satisfied</option>
                            <option value="3">3 · Okay</option>
                            <option value="2">2 · Unsatisfied</option>
                            <option value="1">1 · Very unsatisfied</option>
                        </select>
                        @error('feedbackForms.'.$issue->getKey().'.rating')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                        <textarea wire:model="feedbackForms.{{ $issue->getKey() }}.comment" rows="2" maxlength="2000" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Optional feedback"></textarea>
                        @error('feedbackForms.'.$issue->getKey().'.comment')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800">
                            Confirm and close
                        </button>
                    </form>
                @endif

                <form wire:submit.prevent="reopenIssue({{ $issue->getKey() }})" class="w-full space-y-2">
                    <label for="reopen-reason-{{ $issue->getKey() }}" class="block text-sm font-medium text-slate-700">
                        {{ $status === 'closed' ? 'Reopen this issue' : 'Problem is still present' }}
                    </label>
                    <textarea
                        id="reopen-reason-{{ $issue->getKey() }}"
                        wire:model.defer="reopenReasons.{{ $issue->getKey() }}"
                        rows="2"
                        maxlength="2000"
                        required
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="Explain why more work is needed."
                    ></textarea>
                    @error('reopenReasons.'.$issue->getKey())
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-medium text-amber-800 hover:bg-amber-50">
                        Reopen issue
                    </button>
                </form>
            </div>
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-600">
            No resolved issues require confirmation.
        </p>
    @endforelse

    {{ $issues->links() }}
</section>

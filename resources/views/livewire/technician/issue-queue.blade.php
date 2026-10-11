<section class="mx-auto max-w-5xl space-y-6 p-6">
    <header>
        <h1 class="text-2xl font-semibold text-slate-900">Assigned issues</h1>
        <p class="mt-1 text-sm text-slate-600">Accept an assignment, record work updates, and submit the resolution.</p>
    </header>

    @if (session()->has('workflow-status'))
        <div role="status" class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
            {{ session('workflow-status') }}
        </div>
    @endif

    @forelse ($assignments as $assignment)
        @php
            $issue = $assignment->issue;
            $status = $issue->status instanceof \App\Enums\IssueStatus
                ? $issue->status->value
                : (string) $issue->status;
        @endphp

        <article wire:key="assignment-{{ $assignment->getKey() }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ $issue->title ?: 'Issue #'.$issue->getKey() }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $issue->description }}</p>
                    <p class="mt-2 text-xs text-slate-500">
                        Status: {{ ucwords(str_replace('_', ' ', $status)) }}
                        · Assigned {{ $assignment->assigned_at?->diffForHumans() }}
                    </p>
                </div>

                @if ($assignment->accepted_at === null)
                    <button
                        type="button"
                        wire:click="accept({{ $assignment->getKey() }})"
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800"
                    >
                        Accept assignment
                    </button>
                @elseif ($status === 'assigned' || $status === 'on_hold')
                    <button
                        type="button"
                        wire:click="startWork({{ $issue->getKey() }})"
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800"
                    >
                        {{ $status === 'on_hold' ? 'Resume work' : 'Start work' }}
                    </button>
                @endif
            </div>

            @if (in_array($status, ['in_progress', 'on_hold'], true))
                <div class="border-t border-slate-100 pt-4">
                    <h3 class="text-sm font-semibold text-slate-800">Progress updates</h3>

                    @foreach ($commentsByIssue->get($issue->getKey(), collect()) as $comment)
                        <p class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
                            {{ $comment->body }}
                            <span class="ml-2 text-xs text-slate-500">{{ $comment->created_at }}</span>
                        </p>
                    @endforeach

                    <form wire:submit.prevent="updateProgress({{ $issue->getKey() }})" class="mt-3 space-y-2">
                        <textarea
                            wire:model.defer="progressUpdates.{{ $issue->getKey() }}"
                            rows="2"
                            maxlength="5000"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            placeholder="Describe the work completed or next step."
                        ></textarea>
                        @error('progressUpdates.'.$issue->getKey())
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Save progress
                        </button>
                    </form>
                </div>
            @endif

            @if ($status === 'in_progress')
                <form wire:submit.prevent="hold({{ $issue->getKey() }})" class="space-y-2 border-t border-slate-100 pt-4">
                    <label class="block text-sm font-medium text-slate-700">Put on hold</label>
                    <input
                        type="text"
                        wire:model.defer="holdReasons.{{ $issue->getKey() }}"
                        maxlength="2000"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="Why is work paused?"
                    >
                    @error('holdReasons.'.$issue->getKey())
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-medium text-amber-800 hover:bg-amber-50">
                        Put on hold
                    </button>
                </form>

                <form wire:submit.prevent="resolve({{ $issue->getKey() }})" class="space-y-3 border-t border-slate-100 pt-4">
                    <h3 class="text-sm font-semibold text-slate-800">Resolution record</h3>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Root cause</label>
                        <textarea wire:model.defer="resolutionForms.{{ $issue->getKey() }}.root_cause" rows="2" maxlength="5000" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></textarea>
                        @error('resolutionForms.'.$issue->getKey().'.root_cause')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Action taken</label>
                        <textarea wire:model.defer="resolutionForms.{{ $issue->getKey() }}.action_taken" rows="2" maxlength="5000" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></textarea>
                        @error('resolutionForms.'.$issue->getKey().'.action_taken')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Parts used</label>
                            <input type="text" wire:model.defer="resolutionForms.{{ $issue->getKey() }}.parts_used" maxlength="5000" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                            @error('resolutionForms.'.$issue->getKey().'.parts_used')
                                <p class="text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Minutes spent</label>
                            <input type="number" min="0" wire:model.defer="resolutionForms.{{ $issue->getKey() }}.minutes_spent" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                            @error('resolutionForms.'.$issue->getKey().'.minutes_spent')
                                <p class="text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800">
                        Submit resolution
                    </button>
                </form>
            @endif
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-600">
            No active assignments.
        </p>
    @endforelse

    {{ $assignments->links() }}
</section>

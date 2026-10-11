<section class="mx-auto max-w-5xl space-y-6 p-6">
    <header>
        <h1 class="text-2xl font-semibold text-slate-900">Assign verified issues</h1>
        <p class="mt-1 text-sm text-slate-600">Choose a team and one of its technicians for each verified issue.</p>
    </header>

    @if (session()->has('workflow-status'))
        <div role="status" class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
            {{ session('workflow-status') }}
        </div>
    @endif

    @forelse ($issues as $issue)
        <article wire:key="issue-assignment-{{ $issue->getKey() }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $issue->title ?: 'Issue #'.$issue->getKey() }}
                </h2>
                <p class="mt-1 text-sm text-slate-600">{{ $issue->description }}</p>
                <p class="mt-2 text-xs text-slate-500">
                    Status: {{ ucwords(str_replace('_', ' ', $issue->status instanceof \App\Enums\IssueStatus ? $issue->status->value : $issue->status)) }}
                </p>
            </div>

            <form wire:submit.prevent="assign({{ $issue->getKey() }})" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                <div>
                    <label for="team-{{ $issue->getKey() }}" class="block text-sm font-medium text-slate-700">Team</label>
                    <select id="team-{{ $issue->getKey() }}" wire:model.defer="teamIds.{{ $issue->getKey() }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        <option value="">Select a team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->getKey() }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('teamIds.'.$issue->getKey())
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="technician-{{ $issue->getKey() }}" class="block text-sm font-medium text-slate-700">Technician</label>
                    <select id="technician-{{ $issue->getKey() }}" wire:model.defer="technicianIds.{{ $issue->getKey() }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        <option value="">Select a technician</option>
                        @foreach ($technicians as $technician)
                            @if ($technician->teams->contains('id', (int) ($teamIds[$issue->getKey()] ?? 0)))
                                <option value="{{ $technician->getKey() }}">{{ $technician->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('technicianIds.'.$issue->getKey())
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">
                    {{ $issue->status === \App\Enums\IssueStatus::Assigned->value ? 'Reassign' : 'Assign' }}
                </button>
            </form>
        </article>
    @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-600">
            No verified issues are waiting for assignment.
        </p>
    @endforelse

    {{ $issues->links() }}
</section>

<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Issue;
use App\Models\Location;
use App\Services\IssueWorkflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public $category_id = '';
    public $location_id = '';
    public $campus_id = '';
    public $faculty_id = '';
    public $building_id = '';
    public $floor_id = '';
    public bool $safety_flag = false;
    public bool $class_blocked = false;
    public $photo;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'exists:categories,id'],

            'campus_id' => ['required', 'integer', 'exists:locations,id'],
            'faculty_id' => ['required', 'integer', 'exists:locations,id'],
            'building_id' => ['required', 'integer', 'exists:locations,id'],
            'floor_id' => ['required', 'integer', 'exists:locations,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],

            'safety_flag' => ['boolean'],
            'class_blocked' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }


    public function save(IssueWorkflow $workflow): void
    {
        $validated = $this->validate();

        $selectedLocation = Location::with('parent.parent.parent.parent')
            ->find($validated['location_id']);

        $floor = $selectedLocation?->parent;
        $building = $floor?->parent;
        $faculty = $building?->parent;
        $campus = $faculty?->parent;

        $isValidLocation =
            $selectedLocation !== null
            && in_array($selectedLocation->type, ['room', 'area'], true)
            && $floor !== null
            && $floor->type === 'floor'
            && $building !== null
            && $building->type === 'building'
            && $faculty !== null
            && $faculty->type === 'faculty'
            && $campus !== null
            && $campus->type === 'campus'
            && (int) $campus->id === (int) $validated['campus_id']
            && (int) $faculty->id === (int) $validated['faculty_id']
            && (int) $building->id === (int) $validated['building_id']
            && (int) $floor->id === (int) $validated['floor_id'];

        if (!$isValidLocation) {
            $this->addError(
                'location_id',
                'Lokasi tidak sesuai dengan pilihan kampus, fakultas, gedung, atau lantai.'
            );

            return;
        }

        // Pelapor harus sudah login.
        if (!Auth::check()) {
            $this->addError(
                'form',
                'Anda harus login sebelum mengirim laporan.'
            );

            return;
        }

        $photoPath = null;

        try {
            // Simpan foto jika pengguna mengunggahnya.
            if ($this->photo) {
                $photoPath = $this->photo->store('issues', 'public');
            }

            DB::transaction(function () use ($validated, $photoPath, $workflow) {
                // Simpan laporan.
                $issue = Issue::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'category_id' => $validated['category_id'],
                    'location_id' => $validated['location_id'],
                    'status' => 'reported',
                    'safety_flag' => $validated['safety_flag'],
                    'class_blocked' => $validated['class_blocked'],
                ]);

                // Hubungkan pengguna sebagai pelapor melalui tabel issue_user.
                $issue->reporters()->attach(Auth::id(), [
                    'relationship' => 'reporter',
                ]);

                $workflow->recordInitialReported($issue, Auth::user());

                // Simpan metadata foto jika ada.
                if ($photoPath) {
                    Attachment::create([
                        'issue_id' => $issue->id,
                        'original_name' => $this->photo->getClientOriginalName(),
                        'file_path' => $photoPath,
                        'mime_type' => $this->photo->getMimeType(),
                        'file_size' => $this->photo->getSize(),
                    ]);
                }
            });

            session()->flash(
                'success',
                'Laporan berhasil disimpan.'
            );

            $this->reset([
                'title',
                'description',
                'category_id',
                'location_id',
                'safety_flag',
                'class_blocked',
                'photo',
            ]);
        } catch (\Throwable $e) {
            // Hapus foto jika penyimpanan database gagal.
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $e;
        }
    }

    public function updatedCampusId(): void
    {
        $this->faculty_id = '';
        $this->building_id = '';
        $this->floor_id = '';
        $this->location_id = '';
    }

    public function updatedFacultyId(): void
    {
        $this->building_id = '';
        $this->floor_id = '';
        $this->location_id = '';
    }

    public function updatedBuildingId(): void
    {
        $this->floor_id = '';
        $this->location_id = '';
    }

    public function updatedFloorId(): void
    {
        $this->location_id = '';
    }

    public function render()
    {
        $possibleDuplicates = collect();
        $searchTerms = array_values(array_filter(
            preg_split('/\s+/', mb_strtolower(trim($this->title))) ?: [],
            static fn (string $term): bool => mb_strlen($term) >= 3,
        ));

        if ($searchTerms !== [] && $this->category_id && $this->location_id) {
            $possibleDuplicates = Issue::query()
                ->with(['category', 'location'])
                ->where('category_id', $this->category_id)
                ->where('location_id', $this->location_id)
                ->whereNull('merged_into_issue_id')
                ->whereIn('status', ['reported', 'verified', 'assigned', 'in_progress', 'on_hold', 'reopened'])
                ->where(function ($query) use ($searchTerms): void {
                    foreach ($searchTerms as $term) {
                        $query->orWhere('title', 'like', '%'.$term.'%');
                    }
                })
                ->latest()
                ->limit(5)
                ->get();
        }

        return $this->view([
            'categories' => Category::where('is_active', true)
                ->orderBy('name')
                ->get(),

            'campuses' => Location::where('type', 'campus')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(),

            'faculties' => Location::where('type', 'faculty')
                ->where('parent_id', $this->campus_id ?: null)
                ->orderBy('name')
                ->get(),

            'buildings' => Location::where('type', 'building')
                ->where('parent_id', $this->faculty_id ?: null)
                ->orderBy('name')
                ->get(),

            'floors' => Location::where('type', 'floor')
                ->where('parent_id', $this->building_id ?: null)
                ->orderBy('name')
                ->get(),

            'finalLocations' => Location::whereIn('type', ['room', 'area'])
                ->where('parent_id', $this->floor_id ?: null)
                ->orderBy('name')
                ->get(),
            'possibleDuplicates' => $possibleDuplicates,
        ]);
    }
}
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">
    @if (session()->has('success'))
        <div class="rounded bg-green-100 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @error('form')
        <div class="rounded bg-red-100 p-3 text-red-800">
            {{ $message }}
        </div>
    @enderror

    @if ($errors->any())
        <div class="rounded bg-red-100 p-3 text-red-800">
            <p class="font-bold">Laporan belum berhasil disimpan:</p>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Buat Laporan Fasilitas
        </h1>

        <p class="mt-1 text-sm text-gray-600">
            Laporkan masalah fasilitas kampus agar dapat ditindaklanjuti.
        </p>
    </div>

    @if (session()->has('info'))
        <div class="rounded-lg bg-green-50 p-4 text-green-800">
            {{ session('info') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-5">
        <div>
            <label for="title" class="mb-1 block font-medium">
                Judul Masalah
            </label>

            <input id="title" type="text" wire:model="title" placeholder="Contoh: Wi-Fi lantai 3 tidak berfungsi"
                class="w-full rounded-lg border border-gray-300 p-3">

            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($possibleDuplicates->isNotEmpty())
                <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="font-medium text-amber-900">Mungkin ada laporan yang sama di lokasi ini</p>
                    <p class="mt-1 text-sm text-amber-800">Buka salah satu laporan. Jika masalahnya sama, gunakan tombol “Saya juga terdampak”.</p>
                    <ul class="mt-2 space-y-1 text-sm">
                        @foreach ($possibleDuplicates as $duplicate)
                            <li>
                                <a class="font-medium text-blue-700 underline" href="{{ route('issues.show', $duplicate) }}">
                                    #{{ $duplicate->getKey() }} · {{ $duplicate->title }}
                                </a>
                                <span class="text-slate-600">({{ str($duplicate->status)->replace('_', ' ')->title() }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div>
            <label for="category_id" class="mb-1 block font-medium">
                Kategori Masalah
            </label>

            <select id="category_id" wire:model="category_id" class="w-full rounded-lg border border-gray-300 p-3">
                <option value="">Pilih kategori masalah</option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-4">
            <div>
                <label for="campus_id" class="mb-1 block font-medium">
                    Kampus
                </label>
                <select id="campus_id" wire:model.live="campus_id" class="w-full rounded-lg border border-gray-300 p-3">
                    <option value="">Pilih kampus</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <p class="text-sm text-gray-500">
            ID Kampus terpilih: {{ $campus_id ?: 'Belum terbaca' }}
        </p>
        <div>
            <label for="faculty_id" class="mb-1 block font-medium">
                Fakultas
            </label>
            <select id="faculty_id" wire:model.live="faculty_id" @disabled(!$campus_id)
                class="w-full rounded-lg border p-2">
                <option value="">Pilih Fakultas</option>

                @foreach ($faculties as $faculty)
                    <option value="{{ $faculty->id }}">
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>

            @error('faculty_id')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <p class="text-sm text-gray-500">
            ID Fakultas terpilih: {{ $faculty_id ?: 'Belum terbaca' }}
        </p>


        <div>
            <label for="building_id" class="mb-1 block font-medium">
                Gedung
            </label>
            <select id="building_id" wire:model.live="building_id" @disabled(!$faculty_id)
                class="w-full rounded-lg border border-gray-300 p-3">
                <option value="">Pilih gedung</option>
                @foreach ($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                @endforeach
            </select>
            @error('building_id')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="floor_id" class="mb-1 block font-medium">
                Lantai
            </label>
            <select id="floor_id" wire:model.live="floor_id" @disabled(!$building_id)
                class="w-full rounded-lg border border-gray-300 p-3">
                <option value="">Pilih lantai</option>
                @foreach ($floors as $floor)
                    <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="location_id" class="mb-1 block font-medium">
                Ruangan atau Area
            </label>
            <select id="location_id" wire:model="location_id" @disabled(!$floor_id)
                class="w-full rounded-lg border border-gray-300 p-3">
                <option value="">Pilih ruangan atau area</option>
                @foreach ($finalLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            @error('location_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>



        <div>
            <label for="description" class="mb-1 block font-medium">
                Deskripsi Masalah
            </label>

            <textarea id="description" wire:model="description" rows="4"
                placeholder="Jelaskan masalah yang ditemukan..."
                class="w-full rounded-lg border border-gray-300 p-3"></textarea>

            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="photo" class="mb-1 block font-medium">
                Foto Bukti (Opsional)
            </label>

            <input id="photo" type="file" wire:model="photo" accept="image/*"
                class="w-full rounded-lg border border-gray-300 p-3">

            <p class="mt-1 text-sm text-gray-500">
                Maksimal ukuran foto 2 MB.
            </p>

            @error('photo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500">
                Memproses foto...
            </div>

            @if ($photo)
                <img src="{{ $photo->temporaryUrl() }}" alt="Pratinjau foto laporan"
                    class="mt-3 max-h-64 rounded-lg object-cover">
            @endif
        </div>

        <div class="space-y-3 rounded-lg border border-gray-200 p-4">
            <label class="flex items-start gap-3">
                <input type="checkbox" wire:model="safety_flag" class="mt-1">

                <span>
                    <span class="font-medium">Masalah keselamatan</span>
                    <span class="block text-sm text-gray-600">
                        Masalah ini berpotensi membahayakan pengguna kampus.
                    </span>
                </span>
            </label>

            <label class="flex items-start gap-3">
                <input type="checkbox" wire:model="class_blocked" class="mt-1">

                <span>
                    <span class="font-medium">Kegiatan kelas terganggu</span>
                    <span class="block text-sm text-gray-600">
                        Masalah ini menghambat kegiatan belajar atau mengajar.
                    </span>
                </span>
            </label>
        </div>


        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="rounded bg-blue-600 px-4 py-2 text-white">
            <span wire:loading.remove wire:target="save">
                Kirim Laporan
            </span>
            <span wire:loading wire:target="save">
                Memeriksa...
            </span>
        </button>
    </form>
</div>

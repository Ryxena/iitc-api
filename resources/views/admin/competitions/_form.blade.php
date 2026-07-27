{{-- Shared form fields for create/edit competition modals --}}
<div class="grid gap-4" style="grid-template-columns: 1fr 1fr;">

    {{-- Name --}}
    <div style="grid-column: 1 / -1;">
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Nama Kompetisi <span style="color:#f87171">*</span></label>
        <input type="text" name="name" value="{{ old('name', $comp?->name) }}"
               class="form-input" placeholder="e.g. Web Development Challenge" required>
    </div>

    {{-- Deadline --}}
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Deadline <span style="color:#f87171">*</span></label>
        <input type="date" name="deadline" value="{{ old('deadline', $comp ? \Carbon\Carbon::parse($comp->deadline)->format('Y-m-d') : '') }}"
               class="form-input" required>
    </div>

    {{-- Max Members --}}
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Maks Anggota <span style="color:#f87171">*</span></label>
        <input type="number" name="max_members" value="{{ old('max_members', $comp?->max_members ?? 3) }}"
               class="form-input" min="1" max="10" required>
    </div>

    {{-- Price --}}
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Harga (Rp) <span style="color:#f87171">*</span></label>
        <input type="number" name="price" value="{{ old('price', $comp?->price ?? 0) }}"
               class="form-input" min="0" step="1000" required>
    </div>

    {{-- Event --}}
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Event <span style="color:#f87171">*</span></label>
        <select name="event_id" class="form-input" required>
            @foreach($allEvents as $event)
                <option value="{{ $event->id }}"
                    {{ old('event_id', $comp?->event_id ?? $activeEvent?->id) == $event->id ? 'selected' : '' }}>
                    {{ $event->name }} {{ $event->is_active ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Guide Book --}}
    <div style="grid-column: 1 / -1;">
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Link Panduan (opsional)</label>
        <input type="url" name="guide_book" value="{{ old('guide_book', $comp?->guide_book) }}"
               class="form-input" placeholder="https://drive.google.com/...">
    </div>

    {{-- Description --}}
    <div style="grid-column: 1 / -1;">
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Deskripsi (opsional)</label>
        <textarea name="description" rows="3" class="form-input" placeholder="Deskripsi singkat kompetisi...">{{ old('description', $comp?->description) }}</textarea>
    </div>

    {{-- Categories --}}
    @if($allCategories->isNotEmpty())
        <div style="grid-column: 1 / -1;">
            <label class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Kategori</label>
            <div class="flex flex-wrap gap-2">
                @foreach($allCategories as $cat)
                    <label class="flex items-center gap-2 cursor-pointer px-3 py-1.5 rounded-lg border transition-all"
                           style="border-color: var(--border); background: rgba(255,255,255,.04);">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                               {{ ($comp && $comp->categories->contains($cat->id)) ? 'checked' : '' }}
                               style="accent-color: var(--accent);">
                        <span class="text-sm" style="color: var(--text-main)">{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

</div>

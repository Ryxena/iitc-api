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

    {{-- Group WA --}}
    <div style="grid-column: 1 / -1;">
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Link Grup WhatsApp (opsional)</label>
        <input type="url" name="group_wa" value="{{ old('group_wa', $comp?->group_wa) }}"
               class="form-input" placeholder="https://chat.whatsapp.com/...">
    </div>

    {{-- Description --}}
    <div style="grid-column: 1 / -1;">
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Deskripsi (opsional)</label>
        <textarea name="description" rows="3" class="form-input" placeholder="Deskripsi singkat kompetisi...">{{ old('description', $comp?->description) }}</textarea>
    </div>

    {{-- Cover Image --}}
    <div style="grid-column: 1 / -1;" class="cover-input-container">
        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-muted)">Cover Kompetisi (opsional)</label>
        <input type="file" name="cover" accept="image/*" class="form-input comp-cover-input" style="padding: 6px 14px;"
               onchange="previewCompCover(this)">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Format: JPG, PNG, WebP. Maks 3MB.</p>
        
        {{-- Live preview for new upload --}}
        <img class="comp-cover-preview" src="" alt="Preview"
             style="display:none; max-height:140px; margin-top:10px; border-radius:8px; object-fit:cover; border:1px solid var(--border);">
             
        {{-- Current cover shown in edit mode --}}
        <div class="current-cover-container" style="display: none; margin-top: 12px; padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,.02);">
            <p style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">Cover saat ini:</p>
            <div class="flex items-start gap-4">
                <img class="current-cover-img" src="" alt="Current cover"
                     style="max-height:100px; border-radius:8px; object-fit:cover; border:1px solid var(--border);">
                <label class="flex items-center gap-2 cursor-pointer mt-1">
                    <input type="checkbox" name="delete_cover" value="1" style="accent-color: #f87171;">
                    <span class="text-sm" style="color: #f87171;">Hapus cover</span>
                </label>
            </div>
        </div>
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

<script>
function previewCompCover(input) {
    const container = input.closest('.cover-input-container');
    const preview = container.querySelector('.comp-cover-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}
</script>

<div class="space-y-4">
    {{-- Year --}}
    <div>
        <label for="lw-year" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Tahun <span style="color:#DC2626">*</span>
        </label>
        <select id="lw-year" name="year" required class="form-input">
            <option value="">— Pilih Tahun —</option>
            @for($y = 2020; $y <= $currentYear; $y++)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    {{-- Project Name --}}
    <div>
        <label for="lw-project-name" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Nama Proyek <span style="color:#DC2626">*</span>
        </label>
        <input type="text" id="lw-project-name" name="project_name" required class="form-input" placeholder="e.g. Aplikasi IoT Smart Farming">
    </div>

    {{-- Project Description --}}
    <div>
        <label for="lw-project-desc" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">Deskripsi Proyek</label>
        <textarea id="lw-project-desc" name="project_description" class="form-input" rows="3" placeholder="Deskripsi singkat proyek..."></textarea>
    </div>

    {{-- Institution --}}
    <div>
        <label for="lw-institution" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">Institusi</label>
        <input type="text" id="lw-institution" name="institution" class="form-input" placeholder="e.g. Universitas Gadjah Mada">
    </div>

    {{-- Competition Name --}}
    <div>
        <label for="lw-competition" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Nama Kompetisi <span style="color:#DC2626">*</span>
        </label>
        <input type="text" id="lw-competition" name="competition_name" required class="form-input" placeholder="e.g. IoT Innovation Challenge">
    </div>

    {{-- Rank --}}
    <div>
        <label for="lw-rank" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Peringkat <span style="color:#DC2626">*</span>
        </label>
        <input type="number" id="lw-rank" name="rank" required min="1" class="form-input" placeholder="1">
    </div>

    {{-- Award Title --}}
    <div>
        <label for="lw-award" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Gelar Juara <span style="color:#DC2626">*</span>
        </label>
        <input type="text" id="lw-award" name="award_title" required class="form-input" placeholder="e.g. Juara 1">
    </div>

    {{-- Image --}}
    <div>
        <label for="lw-image" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">Gambar Proyek</label>
        <input type="file" id="lw-image" name="image" accept="image/*" class="form-input" style="padding: 6px 14px;"
               onchange="previewImage(this, 'lw-preview')">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Format: JPG, PNG, WebP. Maks 2MB.</p>
        <img id="lw-preview" src="" alt="Preview"
             style="display:none; max-height:120px; margin-top:12px; border-radius:8px; object-fit:contain; border: 1px solid var(--border);">
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
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
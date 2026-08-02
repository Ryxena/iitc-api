<div class="space-y-4">
    {{-- Name --}}
    <div>
        <label for="mp-name" class="form-label">Nama Media Partner <span class="text-red-500">*</span></label>
        <input type="text" id="mp-name" name="name" required class="form-input" placeholder="e.g. Kompas, Tribun, CNN Indonesia">
    </div>

    {{-- Image --}}
    <div>
        <label for="mp-image" class="form-label">Logo / Gambar</label>
        <input type="file" id="mp-image" name="image" accept="image/*" class="form-input" style="padding: 6px 14px;"
               onchange="previewImage(this, 'mp-preview')">
        <p class="text-xs mt-1" style="color: var(--text-muted);">Format: JPG, PNG, WebP. Maks 2MB.</p>
        <img id="mp-preview" src="" alt="Preview" class="mt-3 rounded-lg object-contain"
             style="display:none; max-height:120px; border: 1px solid var(--border);">
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

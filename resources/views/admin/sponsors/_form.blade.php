<div class="space-y-4">
    {{-- Name --}}
    <div>
        <label for="sp-name" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Nama Sponsor <span style="color:#DC2626">*</span>
        </label>
        <input type="text" id="sp-name" name="name" required class="form-input" placeholder="e.g. PT. Teknologi Nusantara">
    </div>

    {{-- Tier --}}
    <div>
        <label for="sp-tier" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Tier <span style="color:#DC2626">*</span>
        </label>
        <select id="sp-tier" name="tier" required class="form-input">
            <option value="">— Pilih Tier —</option>
            <option value="platinum">💎 Platinum</option>
            <option value="gold">🥇 Gold</option>
            <option value="silver">🥈 Silver</option>
            <option value="bronze">🥉 Bronze</option>
        </select>
    </div>

    {{-- Image --}}
    <div>
        <label for="sp-image" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">Logo / Gambar</label>
        <input type="file" id="sp-image" name="image" accept="image/*" class="form-input" style="padding: 6px 14px;"
               onchange="previewSponsorImage(this, 'sp-preview')">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Format: JPG, PNG, WebP. Maks 2MB.</p>
        <img id="sp-preview" src="" alt="Preview"
             style="display:none; max-height:120px; margin-top:12px; border-radius:8px; object-fit:contain; border: 1px solid var(--border);">
    </div>
</div>

<script>
function previewSponsorImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

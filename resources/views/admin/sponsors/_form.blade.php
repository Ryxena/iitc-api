<style>
    .tier-pill {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: #fff;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: border-color .15s, background .15s, color .15s;
        user-select: none;
        white-space: nowrap;
    }
    .tier-pill:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: #EEF2FF;
    }
    .tier-pill.selected {
        border-color: var(--accent);
        background: var(--accent);
        color: #fff;
    }
    .tier-pill.selected:hover {
        background: var(--accent-hover);
        border-color: var(--accent-hover);
        color: #fff;
    }
</style>

<div class="space-y-4">
    {{-- Name --}}
    <div>
        <label for="sp-name" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
            Nama Sponsor <span style="color:#DC2626">*</span>
        </label>
        <input type="text" id="sp-name" name="name" required class="form-input" placeholder="e.g. PT. Teknologi Nusantara">
    </div>

    {{-- Tier — custom pill radio group --}}
    <div>
        <label style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:8px;">
            Tier <span style="color:#DC2626">*</span>
        </label>
        <input type="hidden" id="sp-tier-value" name="tier" value="">
        <div style="display:flex; flex-wrap:wrap; gap:8px;" id="sp-tier-group">
            @foreach(['platinum' => '💎 Platinum', 'gold' => '🥇 Gold', 'silver' => '🥈 Silver', 'bronze' => '🥉 Bronze'] as $val => $label)
                <button type="button"
                        class="tier-pill"
                        data-tier="{{ $val }}"
                        onclick="selectTier('{{ $val }}')">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        {{-- Invisible required guard --}}
        <input type="text" tabindex="-1" required
               style="opacity:0; width:0; height:0; padding:0; border:0; position:absolute;"
               id="sp-tier-guard">
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
function selectTier(val) {
    document.getElementById('sp-tier-value').value = val;
    document.getElementById('sp-tier-guard').value = val;   // satisfies required
    document.querySelectorAll('#sp-tier-group .tier-pill').forEach(function(btn) {
        btn.classList.toggle('selected', btn.dataset.tier === val);
    });
}

function previewSponsorImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

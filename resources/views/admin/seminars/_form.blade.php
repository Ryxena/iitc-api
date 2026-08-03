<div class="space-y-4">
    {{-- Title --}}
    <div>
        <label for="title" class="form-label">Judul Seminar <span class="text-red-500">*</span></label>
        <input type="text" id="title" name="title" required class="form-input" placeholder="e.g. Seminar National Cyber Security 2026">
    </div>

    {{-- Speaker --}}
    <div>
        <label for="speaker" class="form-label">Pembicara / Speaker</label>
        <input type="text" id="speaker" name="speaker" class="form-input" placeholder="e.g. Dr. Jane Doe, M.Kom">
    </div>

    {{-- Date & Time + Location --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="start_date" class="form-label">Tanggal Buka Registrasi</label>
            <input type="date" id="start_date" name="start_date" class="form-input">
        </div>
        <div>
            <label for="end_date" class="form-label">Tanggal Tutup Registrasi</label>
            <input type="date" id="end_date" name="end_date" class="form-input">
        </div>
        <div>
            <label for="date_time" class="form-label">Waktu & Tanggal (Pelaksanaan)</label>
            <input type="datetime-local" id="date_time" name="date_time" class="form-input">
        </div>
        <div>
            <label for="location" class="form-label">Lokasi / Link Venue</label>
            <input type="text" id="location" name="location" class="form-input" placeholder="e.g. Zoom / Auditorium Main Hall">
        </div>
    </div>

    {{-- Poster & Registration Link --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Poster (Opsional)</label>
            <input type="file" name="poster" accept="image/*" class="form-input">
            <small class="text-xs text-muted block mt-1">Format: JPG, PNG, GIF. Max: 5MB</small>
            
            <div class="poster-preview-container mt-3 hidden p-2 border rounded" style="border-color: var(--border); background: rgba(255,255,255,0.02)">
                <p class="text-xs text-muted mb-2">Poster saat ini:</p>
                <img class="poster-preview-img w-full max-w-[200px] h-auto object-cover rounded shadow-sm mb-2" src="" alt="Poster">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="delete_poster" value="1" class="delete-poster-checkbox w-4 h-4 rounded" style="accent-color: var(--danger);">
                    <label class="text-xs font-medium text-red-400 cursor-pointer" onclick="this.previousElementSibling.click()">Hapus foto ini</label>
                </div>
            </div>
        </div>
        <div>
            <label for="registration_link" class="form-label">Link Registrasi Eksternal</label>
            <input type="url" id="registration_link" name="registration_link" class="form-input" placeholder="e.g. https://forms.gle/...">
        </div>
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="form-label">Deskripsi Seminar</label>
        <textarea id="description" name="description" rows="3" class="form-input" placeholder="Deskripsi lengkap mengenai topik dan acara seminar..."></textarea>
    </div>

    {{-- Is Active --}}
    <div class="flex items-center gap-3 pt-2">
        <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 rounded" style="accent-color: var(--accent);">
        <label for="is_active" class="text-sm font-medium text-main cursor-pointer">
            Aktifkan Seminar (Ditampilkan di publik)
        </label>
    </div>
</div>

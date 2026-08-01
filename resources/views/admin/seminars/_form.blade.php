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
            <label for="date_time" class="form-label">Waktu & Tanggal</label>
            <input type="datetime-local" id="date_time" name="date_time" class="form-input">
        </div>
        <div>
            <label for="location" class="form-label">Lokasi / Link Venue</label>
            <input type="text" id="location" name="location" class="form-input" placeholder="e.g. Zoom / Auditorium Main Hall">
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

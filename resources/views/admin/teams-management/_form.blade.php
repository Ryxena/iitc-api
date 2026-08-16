<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-white mb-1">Email Ketua Tim (Leader)</label>
        <input type="email" name="leader_email" class="form-input" placeholder="Masukkan email user..." required>
        <p class="text-xs text-muted mt-1">Email user harus sudah terdaftar di sistem.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-1">Kompetisi</label>
        <select name="competition_id" class="form-input" required>
            <option value="">-- Pilih Kompetisi --</option>
            @foreach($competitions as $comp)
                <option value="{{ $comp->id }}">{{ $comp->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-1">Nama Tim</label>
        <input type="text" name="name" class="form-input" placeholder="Nama tim (opsional)">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-white mb-1">Kode Tim</label>
            <input type="text" name="code" class="form-input" placeholder="Kode unik">
        </div>
        <div>
            <label class="block text-sm font-medium text-white mb-1">Status (Aktif)</label>
            <select name="is_active" class="form-input">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-white mb-1">Judul / Title</label>
        <input type="text" name="title" class="form-input" placeholder="Judul karya/submission (opsional)">
    </div>
</div>

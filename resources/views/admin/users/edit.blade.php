<x-admin-layout 
    title="Edit User" 
    subtitle="Edit informasi pengguna: {{ $user->name }}">

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium" style="color: var(--text-muted); display:inline-flex; align-items:center; gap:6px; text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar User
        </a>
    </div>

    <div class="card max-w-2xl">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PATCH')

            <div class="space-y-4 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-main mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-main mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-main mb-1">No. WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-main mb-1">Password Baru <span class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution" class="block text-sm font-medium text-main mb-1">Institusi / Sekolah</label>
                    <input type="text" id="institution" name="institution" value="{{ old('institution', $user->participant?->institution) }}" class="form-input">
                    @error('institution')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grade" class="block text-sm font-medium text-main mb-1">Tingkat / Angkatan</label>
                    <input type="text" id="grade" name="grade" value="{{ old('grade', $user->participant?->grade) }}" class="form-input" placeholder="Contoh: SMA Kelas 12, Mahasiswa Semester 4">
                    @error('grade')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 justify-end pt-4 border-t" style="border-color: var(--border);">
                <a href="{{ route('admin.users.index') }}" class="btn-ghost text-sm">Batal</a>
                <button type="submit" class="btn-primary text-sm" style="padding: 10px 16px;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-admin-layout>

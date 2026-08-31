<x-admin-layout title="Sertifikat Seminar" subtitle="Kelola sertifikat peserta seminar — upload & status">

    {{-- STATS MINI --}}
    <div class="grid gap-4 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr))">
        <div class="card flex items-center gap-4" style="padding: 16px 20px">
            <div class="stat-icon stat-icon-blue flex-shrink-0" style="width:40px;height:40px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-muted">Total Pemenang</p>
                <p class="text-xl font-bold text-main">{{ number_format($totalCount) }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-4" style="padding: 16px 20px">
            <div class="stat-icon stat-icon-green flex-shrink-0" style="width:40px;height:40px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-muted">Dengan Sertifikat</p>
                <p class="text-xl font-bold text-main">{{ number_format($withCertificateCount) }}</p>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card p-0 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b" style="border-color: var(--border)">
            <p class="text-sm font-medium text-muted">
                Menampilkan <strong class="text-main">{{ $rows->count() }}</strong> peserta dari
                <strong class="text-main">{{ $winners->total() }}</strong> team
            </p>
            <form method="GET" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemenang..." class="form-input" style="width: 220px; padding: 6px 12px;">
                <button type="submit" class="btn-primary" style="padding: 6px 16px; font-size: 13px;">Cari</button>
                @if($search)
                    <a href="{{ route('admin.seminar.certificates') }}" class="btn-ghost" style="padding: 6px 16px; font-size: 13px;">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-mini">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Pemenang</th>
                        <th>Tim / Kompetisi</th>
                        <th>Prestasi</th>
                        <th>Sertifikat</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        @php
                            $winner = $row->winner;
                            $team = $row->team;
                            $user = $row->user;
                            $reg = $row->reg;
                        @endphp
                        <tr>
                            <td style="border-right: none;">
                                <span class="text-sm text-muted">{{ $i + 1 }}</span>
                            </td>
                            <td style="border-right: none;">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                         style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-main text-sm">{{ $user?->name ?? 'User Dihapus' }}</p>
                                        <p class="text-xs text-muted">{{ $user?->email ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="border-right: none;">
                                @if($team)
                                    <p class="font-medium text-sm text-main">{{ $team->name }}</p>
                                    <p class="text-xs text-muted">{{ $team->competition?->name ?? '—' }}</p>
                                @else
                                    <span class="text-sm text-muted">—</span>
                                @endif
                            </td>
                            <td style="border-right: none;">
                                <span class="badge badge-valid flex items-center gap-1" style="width: fit-content;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $winner->award_title }} (Rank {{ $winner->rank }})
                                </span>
                            </td>
                            <td style="border-right: none;">
                                @if($reg && $reg->certificate_path)
                                    <span class="badge badge-valid flex items-center gap-1" style="width: fit-content;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        File
                                    </span>
                                    @if($reg->certificate_number)
                                        <p class="text-xs text-muted mt-1">{{ $reg->certificate_number }}</p>
                                    @endif
                                @elseif($reg && $reg->certificate_number)
                                    <span class="badge badge-info flex items-center gap-1" style="width: fit-content;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        No. Sertifikat
                                    </span>
                                    <p class="text-xs text-muted mt-1">{{ $reg->certificate_number }}</p>
                                @else
                                    <span class="text-sm text-muted">—</span>
                                @endif
                            </td>
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($reg && $reg->certificate_path)
                                        <a href="{{ Storage::disk('public')->url($reg->certificate_path) }}"
                                           target="_blank" class="btn-ghost" style="padding: 4px 10px; font-size: 12px;">
                                            Lihat
                                        </a>
                                    @endif
                                    @if($user)
                                        <button onclick="openUploadModal('{{ $user->id }}', '{{ addslashes($user->name ?? '') }}')"
                                                class="btn-primary" style="padding: 4px 12px; font-size: 12px;">
                                            Upload
                                        </button>
                                    @else
                                        <span class="text-sm text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-muted text-sm">Tidak ada pemenang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($winners->hasPages())
            <div class="p-4 border-t" style="border-color: var(--border);">
                {{ $winners->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL: Upload Certificate --}}
    <div id="upload-modal" class="fixed inset-0 z-50" style="display: none; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
        <div class="card" style="width: 560px; max-width: 90vw; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-main">Upload Sertifikat</h3>
                <button onclick="closeUploadModal()" class="btn-ghost" style="padding: 4px 8px; font-size: 18px; line-height: 1;">&times;</button>
            </div>
            <form id="form-upload" method="POST" action="{{ route('admin.seminar.certificates.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" id="upload-user-id">
                <p class="text-sm text-muted mb-4">Upload untuk: <strong id="upload-user-name" class="text-main"></strong></p>
                <div class="mb-4">
                    <label class="text-sm font-medium text-muted block mb-1">File Sertifikat</label>
                    <input type="file" name="certificate" accept=".jpg,.jpeg,.png,.pdf" required
                           class="form-input" style="padding: 8px 12px; width: 100%;">
                    <p class="text-xs text-muted mt-1">Format: JPG, PNG, PDF. Maks 5 MB.</p>
                </div>
                <div class="flex items-center gap-3 justify-end">
                    <button type="button" onclick="closeUploadModal()" class="btn-ghost" style="padding: 8px 16px;">Batal</button>
                    <button type="submit" class="btn-primary" style="padding: 8px 16px;">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal(userId, userName) {
            document.getElementById('upload-user-id').value = userId;
            document.getElementById('upload-user-name').textContent = userName;
            document.getElementById('upload-modal').style.display = 'flex';
        }
        function closeUploadModal() {
            document.getElementById('upload-modal').style.display = 'none';
        }

        document.getElementById('upload-modal').addEventListener('click', function(e) {
            if (e.target === this) closeUploadModal();
        });
    </script>
</x-admin-layout>
<x-admin-layout title="Manajemen Seminar" subtitle="Daftar peserta seminar & verifikasi kehadiran">

    {{-- ============================================================ --}}
    {{-- STATS MINI --}}
    {{-- ============================================================ --}}
    <div class="grid gap-4 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr))">
        <div class="card flex items-center gap-4" style="padding: 16px 20px">
            <div class="stat-icon stat-icon-blue flex-shrink-0" style="width:40px;height:40px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-muted">Total Peserta</p>
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
                <p class="text-xs font-medium text-muted">Sudah Hadir</p>
                <p class="text-xl font-bold text-main">{{ number_format($attendedCount) }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-4" style="padding: 16px 20px">
            <div class="stat-icon stat-icon-amber flex-shrink-0" style="width:40px;height:40px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-muted">Belum Hadir</p>
                <p class="text-xl font-bold text-main">{{ number_format($totalCount - $attendedCount) }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTER + BULK ACTION BAR --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Attended filter tabs --}}
            <div class="flex gap-1 p-1 rounded-lg border" style="background: rgba(255,255,255,.04); border-color: var(--border);">
                @foreach(['ALL' => 'Semua', 'NO' => 'Belum Hadir', 'YES' => 'Sudah Hadir'] as $val => $label)
                    <a href="{{ route('admin.seminar.index', ['attended' => $val, 'search' => $search]) }}"
                       class="px-4 py-1.5 font-medium rounded-md transition-all text-sm"
                       style="{{ $attended === $val
                            ? 'background: var(--accent); color: #fff; font-weight: 600;'
                            : 'color: var(--text-muted);' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.seminar.index') }}" class="flex-1 min-w-[200px] flex gap-2">
                <input type="hidden" name="attended" value="{{ $attended }}">
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="seminar-search" type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama atau email..." class="form-input pl-10">
                </div>
                <button id="btn-seminar-search" type="submit" class="btn-primary" style="white-space:nowrap">Cari</button>
                @if($search)
                    <a href="{{ route('admin.seminar.index', ['attended' => $attended]) }}" class="btn-ghost" style="white-space:nowrap">Reset</a>
                @endif
            </form>

            {{-- Export --}}
            <a id="btn-export-seminars" href="{{ route('admin.export.seminars') }}" class="btn-ghost flex items-center gap-2" style="white-space:nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- BULK ACTION BAR (visible when checkboxes selected) --}}
    {{-- ============================================================ --}}
    <div id="bulk-bar" style="display:none; background: rgba(99,102,241,.12); border: 1px solid rgba(99,102,241,.3); border-radius: 10px; padding: 12px 18px; margin-bottom: 16px; display: none;">
        <form id="form-bulk-verify" method="POST" action="{{ route('admin.seminar.bulk-verify') }}" class="flex items-center gap-4">
            @csrf
            <div id="bulk-hidden-inputs"></div>
            <span id="bulk-count-label" class="text-sm font-medium" style="color: #a5b4fc;">0 peserta dipilih</span>
            <button id="btn-bulk-verify" type="submit" class="btn-primary" style="padding: 6px 16px; font-size: 13px;">
                ✅ Tandai Hadir
            </button>
            <button type="button" onclick="clearSelection()" class="btn-ghost" style="padding: 6px 16px; font-size: 13px;">Batal</button>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card p-0 overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b" style="border-color: var(--border)">
            <p class="text-sm font-medium text-muted">
                Menampilkan <strong class="text-main">{{ $registrations->count() }}</strong> dari
                <strong class="text-main">{{ $registrations->total() }}</strong> peserta
            </p>
            <label class="flex items-center gap-2 cursor-pointer text-sm text-muted">
                <input id="chk-select-all" type="checkbox" onchange="toggleAll(this)" style="accent-color: var(--accent);">
                Pilih Semua
            </label>
        </div>

        @if($registrations->isEmpty())
            <div class="py-20 text-center" style="background: rgba(255,255,255,.02)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
                </svg>
                <p class="text-sm font-medium text-muted">Tidak ada peserta yang sesuai filter.</p>
            </div>
        @else
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none; width: 44px;"></th>
                        <th style="border-right: none;">No</th>
                        <th style="border-right: none;">Peserta</th>
                        <th style="border-right: none;">Kontak</th>
                        <th class="text-center" style="border-right: none;">Kehadiran</th>
                        <th style="border-right: none;">Sertifikat</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $i => $reg)
                        <tr>
                            {{-- Checkbox --}}
                            <td style="border-right: none;">
                                <input type="checkbox"
                                       class="row-checkbox"
                                       value="{{ $reg->user_id }}"
                                       onchange="onCheckboxChange()"
                                       style="accent-color: var(--accent);">
                            </td>

                            {{-- No --}}
                            <td style="border-right: none;">
                                <span class="text-sm text-muted">{{ $registrations->firstItem() + $i }}</span>
                            </td>

                            {{-- Peserta --}}
                            <td style="border-right: none;">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                         style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                        {{ strtoupper(substr($reg->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-main text-sm">{{ $reg->user?->name ?? '—' }}</p>
                                        <p class="text-xs text-muted mt-0.5">{{ $reg->user?->email ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kontak --}}
                            <td style="border-right: none;">
                                <p class="text-sm text-main">{{ $reg->user?->phone ?? '—' }}</p>
                            </td>

                            {{-- Kehadiran --}}
                            <td class="text-center" style="border-right: none;">
                                @if($reg->attended)
                                    <span class="badge badge-valid">Hadir</span>
                                @else
                                    <span class="badge badge-pending">Belum</span>
                                @endif
                            </td>

                            {{-- Sertifikat --}}
                            <td style="border-right: none;">
                                @if($reg->certificate_number)
                                    <p class="text-xs font-mono text-muted">{{ $reg->certificate_number }}</p>
                                @else
                                    <span class="text-xs text-muted">—</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    @if(! $reg->attended)
                                        <form method="POST" action="{{ route('admin.seminar.verify', $reg->user_id) }}">
                                            @csrf
                                            <input type="hidden" name="is_approve" value="1">
                                            <button id="btn-attend-{{ $reg->user_id }}" type="submit" class="btn-primary" style="padding: 5px 12px; font-size: 12px;">
                                                ✓ Hadir
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.seminar.verify', $reg->user_id) }}"
                                              onsubmit="return confirm('Batalkan kehadiran {{ addslashes($reg->user?->name ?? '') }}?')">
                                            @csrf
                                            <input type="hidden" name="is_approve" value="0">
                                            <button id="btn-unattend-{{ $reg->user_id }}" type="submit" class="btn-danger" style="padding: 5px 12px; font-size: 12px;">
                                                Batal Hadir
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($registrations->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: rgba(255,255,255,.02);">
                    {{ $registrations->links() }}
                </div>
            @endif
        @endif
    </div>

    <script>
    const bulkBar    = document.getElementById('bulk-bar');
    const countLabel = document.getElementById('bulk-count-label');
    const hiddenWrap = document.getElementById('bulk-hidden-inputs');

    function getChecked() {
        return [...document.querySelectorAll('.row-checkbox:checked')];
    }

    function onCheckboxChange() {
        const checked = getChecked();
        if (checked.length > 0) {
            bulkBar.style.display = 'block';
            countLabel.textContent = checked.length + ' peserta dipilih';
            // Sync hidden inputs
            hiddenWrap.innerHTML = checked.map(cb =>
                `<input type="hidden" name="user_ids[]" value="${cb.value}">`
            ).join('');
        } else {
            bulkBar.style.display = 'none';
        }
        // Sync select-all state
        const all = document.querySelectorAll('.row-checkbox');
        document.getElementById('chk-select-all').checked = all.length > 0 && checked.length === all.length;
    }

    function toggleAll(master) {
        document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = master.checked; });
        onCheckboxChange();
    }

    function clearSelection() {
        document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = false; });
        document.getElementById('chk-select-all').checked = false;
        onCheckboxChange();
    }
    </script>

</x-admin-layout>

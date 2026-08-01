<x-admin-layout title="Manajemen Data Seminar" subtitle="Kelola daftar acara seminar, lokasi, pembicara, dan status publikasi">

    {{-- ============================================================ --}}
    {{-- STATS MINI & HEADER BUTTON --}}
    {{-- ============================================================ --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="card flex items-center gap-4" style="padding: 12px 20px">
                <div class="stat-icon stat-icon-blue flex-shrink-0" style="width:36px;height:36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Total Seminar</p>
                    <p class="text-lg font-bold text-main">{{ number_format($totalCount) }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-4" style="padding: 12px 20px">
                <div class="stat-icon stat-icon-green flex-shrink-0" style="width:36px;height:36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Aktif / Dipublikasi</p>
                    <p class="text-lg font-bold text-main">{{ number_format($activeCount) }}</p>
                </div>
            </div>
        </div>

        <button id="btn-open-create-seminar" type="button" class="btn-primary flex items-center gap-2" onclick="openModal('modal-create-seminar')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Seminar
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTER BAR --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Status filter tabs --}}
            <div class="flex gap-1 p-1 rounded-lg border" style="background: rgba(255,255,255,.04); border-color: var(--border);">
                @foreach(['ALL' => 'Semua', 'ACTIVE' => 'Aktif', 'INACTIVE' => 'Nonaktif'] as $val => $label)
                    <a href="{{ route('admin.seminars.index', ['status' => $val, 'search' => $search]) }}"
                       class="px-4 py-1.5 font-medium rounded-md transition-all text-sm"
                       style="{{ $status === $val
                            ? 'background: var(--accent); color: #fff; font-weight: 600;'
                            : 'color: var(--text-muted);' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.seminars.index') }}" class="flex-1 min-w-[200px] flex gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="seminar-search" type="text" name="search" value="{{ $search }}"
                           placeholder="Cari judul, pembicara, atau lokasi..." class="form-input pl-10">
                </div>
                <button id="btn-seminar-search" type="submit" class="btn-primary" style="white-space:nowrap">Cari</button>
                @if($search)
                    <a href="{{ route('admin.seminars.index', ['status' => $status]) }}" class="btn-ghost" style="white-space:nowrap">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card p-0 overflow-hidden">
        @if($seminars->isEmpty())
            <div class="py-20 text-center" style="background: rgba(255,255,255,.02)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p class="text-sm font-medium text-muted">Belum ada data seminar. Klik "Tambah Seminar" untuk membuat baru.</p>
            </div>
        @else
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">No</th>
                        <th style="border-right: none;">Judul & Deskripsi</th>
                        <th style="border-right: none;">Pembicara</th>
                        <th style="border-right: none;">Waktu & Tanggal</th>
                        <th style="border-right: none;">Lokasi</th>
                        <th class="text-center" style="border-right: none;">Status</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seminars as $i => $sem)
                        <tr>
                            {{-- No --}}
                            <td style="border-right: none;">
                                <span class="text-sm text-muted">{{ $seminars->firstItem() + $i }}</span>
                            </td>

                            {{-- Judul & Deskripsi --}}
                            <td style="border-right: none; max-width: 260px;">
                                <p class="font-semibold text-main text-sm">{{ $sem->title }}</p>
                                @if($sem->description)
                                    <p class="text-xs text-muted mt-0.5 line-clamp-2" title="{{ $sem->description }}">
                                        {{ Str::limit($sem->description, 80) }}
                                    </p>
                                @endif
                            </td>

                            {{-- Pembicara --}}
                            <td style="border-right: none;">
                                <span class="text-sm text-main font-medium">{{ $sem->speaker ?? '—' }}</span>
                            </td>

                            {{-- Waktu --}}
                            <td style="border-right: none;">
                                @if($sem->date_time)
                                    <p class="text-sm text-main">{{ $sem->date_time->format('d M Y') }}</p>
                                    <p class="text-xs text-muted mt-0.5">{{ $sem->date_time->format('H:i') }} WIB</p>
                                @else
                                    <span class="text-xs text-muted">—</span>
                                @endif
                            </td>

                            {{-- Lokasi --}}
                            <td style="border-right: none;">
                                <span class="text-sm text-main">{{ $sem->location ?? '—' }}</span>
                            </td>

                            {{-- Status --}}
                            <td class="text-center" style="border-right: none;">
                                @if($sem->is_active)
                                    <span class="badge badge-valid">Aktif</span>
                                @else
                                    <span class="badge badge-pending">Nonaktif</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Toggle Active Button --}}
                                    <form method="POST" action="{{ route('admin.seminars.toggle-active', $sem->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button id="btn-toggle-seminar-{{ $sem->id }}" type="submit" class="btn-ghost" style="padding: 5px 10px; font-size: 12px;"
                                                title="{{ $sem->is_active ? 'Nonaktifkan Seminar' : 'Aktifkan Seminar' }}">
                                            {{ $sem->is_active ? '⏸ Nonaktifkan' : '▶️ Aktifkan' }}
                                        </button>
                                    </form>

                                    {{-- Edit Button --}}
                                    <button id="btn-edit-seminar-{{ $sem->id }}" type="button" class="btn-ghost" style="padding: 5px 10px; font-size: 12px;"
                                            onclick="openEditSeminarModal({{ json_encode([
                                                'id'          => $sem->id,
                                                'title'       => $sem->title,
                                                'speaker'     => $sem->speaker,
                                                'date_time'   => $sem->date_time ? $sem->date_time->format('Y-m-d\TH:i') : '',
                                                'location'    => $sem->location,
                                                'description' => $sem->description,
                                                'is_active'   => $sem->is_active,
                                            ]) }})">
                                        Edit
                                    </button>

                                    {{-- Delete Form --}}
                                    <form method="POST" action="{{ route('admin.seminars.destroy', $sem->id) }}"
                                          onsubmit="return confirm('Hapus seminar {{ addslashes($sem->title) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button id="btn-delete-seminar-{{ $sem->id }}" type="submit" class="btn-danger" style="padding: 5px 10px; font-size: 12px;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($seminars->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: rgba(255,255,255,.02);">
                    {{ $seminars->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CREATE SEMINAR --}}
    {{-- ============================================================ --}}
    <div id="modal-create-seminar" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 600px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Tambah Acara Seminar</h2>
                <button type="button" onclick="closeModal('modal-create-seminar')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.seminars.store') }}">
                @csrf
                @include('admin.seminars._form')
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-create-seminar')" class="btn-ghost">Batal</button>
                    <button id="btn-create-seminar-submit" type="submit" class="btn-primary">Simpan Seminar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: EDIT SEMINAR --}}
    {{-- ============================================================ --}}
    <div id="modal-edit-seminar" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 600px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Edit Acara Seminar</h2>
                <button type="button" onclick="closeModal('modal-edit-seminar')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-edit-seminar" method="POST" action="">
                @csrf
                @method('PATCH')
                @include('admin.seminars._form')
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-edit-seminar')" class="btn-ghost">Batal</button>
                    <button id="btn-edit-seminar-submit" type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    function openEditSeminarModal(data) {
        const form = document.getElementById('form-edit-seminar');
        form.action = '/admin/seminars/' + data.id;

        form.querySelector('[name="title"]').value       = data.title ?? '';
        form.querySelector('[name="speaker"]').value     = data.speaker ?? '';
        form.querySelector('[name="date_time"]').value   = data.date_time ?? '';
        form.querySelector('[name="location"]').value    = data.location ?? '';
        form.querySelector('[name="description"]').value = data.description ?? '';
        form.querySelector('[name="is_active"]').checked = Boolean(data.is_active);

        openModal('modal-edit-seminar');
    }
    </script>

</x-admin-layout>

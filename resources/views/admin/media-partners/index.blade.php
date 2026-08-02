<x-admin-layout title="Manajemen Media Partner" subtitle="Kelola daftar media partner yang mendukung IITC">

    {{-- ============================================================ --}}
    {{-- STATS & HEADER BUTTON --}}
    {{-- ============================================================ --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="card flex items-center gap-4" style="padding: 12px 20px">
                <div class="stat-icon stat-icon-blue flex-shrink-0" style="width:36px;height:36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--text-muted)">Total Media Partner</p>
                    <p class="text-lg font-bold text-white">{{ number_format($totalCount) }}</p>
                </div>
            </div>
        </div>

        <button id="btn-open-create-mp" type="button" class="btn-primary flex items-center gap-2"
                onclick="openModal('modal-create-mp')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Media Partner
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- SEARCH BAR --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('admin.media-partners.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="mp-search" type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama media partner..." class="form-input pl-10">
            </div>
            <button id="btn-mp-search" type="submit" class="btn-primary" style="white-space:nowrap">Cari</button>
            @if($search)
                <a href="{{ route('admin.media-partners.index') }}" class="btn-ghost" style="white-space:nowrap">Reset</a>
            @endif
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card p-0 overflow-hidden">
        @if($mediaPartners->isEmpty())
            <div class="py-20 text-center" style="background: rgba(255,255,255,.02)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h3"/>
                </svg>
                <p class="text-sm font-medium" style="color: var(--text-muted)">Belum ada media partner. Klik "Tambah Media Partner" untuk membuat baru.</p>
            </div>
        @else
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">No</th>
                        <th style="border-right: none;">Logo</th>
                        <th style="border-right: none;">Nama</th>
                        <th style="border-right: none;">Ditambahkan</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mediaPartners as $i => $mp)
                        <tr>
                            {{-- No --}}
                            <td style="border-right: none; width: 48px;">
                                <span class="text-sm" style="color: var(--text-muted)">{{ $mediaPartners->firstItem() + $i }}</span>
                            </td>

                            {{-- Logo --}}
                            <td style="border-right: none; width: 80px;">
                                @if($mp->image)
                                    <img src="{{ $mp->image }}" alt="{{ $mp->name }}"
                                         class="rounded-lg object-contain"
                                         style="width: 56px; height: 40px; background: rgba(255,255,255,.06); padding: 4px; border: 1px solid var(--border);">
                                @else
                                    <div class="rounded-lg flex items-center justify-center"
                                         style="width: 56px; height: 40px; background: rgba(255,255,255,.06); border: 1px solid var(--border);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            {{-- Nama --}}
                            <td style="border-right: none;">
                                <p class="font-semibold text-sm">{{ $mp->name }}</p>
                            </td>

                            {{-- Ditambahkan --}}
                            <td style="border-right: none;">
                                <span class="text-sm" style="color: var(--text-muted)">{{ $mp->created_at->format('d M Y') }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Edit --}}
                                    <button id="btn-edit-mp-{{ $mp->id }}" type="button" class="btn-ghost"
                                            style="padding: 5px 10px; font-size: 12px;"
                                            onclick="openEditMpModal({{ json_encode(['id' => $mp->id, 'name' => $mp->name, 'image' => $mp->image]) }})">
                                        Edit
                                    </button>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.media-partners.destroy', $mp->id) }}"
                                          onsubmit="return confirm('Hapus media partner {{ addslashes($mp->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button id="btn-delete-mp-{{ $mp->id }}" type="submit" class="btn-danger" style="padding: 5px 10px; font-size: 12px;">
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
            @if($mediaPartners->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: rgba(255,255,255,.02);">
                    {{ $mediaPartners->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CREATE --}}
    {{-- ============================================================ --}}
    <div id="modal-create-mp" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 520px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-lg">Tambah Media Partner</h2>
                <button type="button" onclick="closeModal('modal-create-mp')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.media-partners.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.media-partners._form')
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-create-mp')" class="btn-ghost">Batal</button>
                    <button id="btn-create-mp-submit" type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: EDIT --}}
    {{-- ============================================================ --}}
    <div id="modal-edit-mp" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 520px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-lg">Edit Media Partner</h2>
                <button type="button" onclick="closeModal('modal-edit-mp')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-edit-mp" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('POST')
                @include('admin.media-partners._form')

                {{-- Current image display --}}
                <div id="mp-edit-current-img-wrap" class="mt-2" style="display:none;">
                    <p class="text-xs mb-1" style="color: var(--text-muted)">Gambar saat ini (biarkan kosong jika tidak ingin mengganti):</p>
                    <img id="mp-edit-current-img" src="" alt="Current"
                         class="rounded-lg object-contain"
                         style="max-height: 80px; border: 1px solid var(--border); padding: 4px;">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-edit-mp')" class="btn-ghost">Batal</button>
                    <button id="btn-edit-mp-submit" type="submit" class="btn-primary">Simpan Perubahan</button>
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

    function openEditMpModal(data) {
        const form    = document.getElementById('form-edit-mp');
        const baseUrl = '{{ url("/admin/media-partners") }}';
        form.action   = baseUrl + '/' + data.id;

        form.querySelector('[name="name"]').value = data.name ?? '';

        // Reset file input
        const fileInput = form.querySelector('[name="image"]');
        fileInput.value = '';

        // Show existing image
        const wrap = document.getElementById('mp-edit-current-img-wrap');
        const img  = document.getElementById('mp-edit-current-img');
        if (data.image) {
            img.src = data.image;
            wrap.style.display = 'block';
        } else {
            wrap.style.display = 'none';
        }

        // Reset live preview
        const preview = document.getElementById('mp-preview');
        preview.src = '';
        preview.style.display = 'none';

        openModal('modal-edit-mp');
    }
    </script>

</x-admin-layout>

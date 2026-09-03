<x-admin-layout title="Manajemen Juara Lampau" subtitle="Kelola data juara dari tahun-tahun sebelumnya">

    @php
        $currentYear = (int) now()->format('Y');
    @endphp

    {{-- ============================================================ --}}
    {{-- STATS & HEADER BUTTON --}}
    {{-- ============================================================ --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <div class="card flex items-center gap-4" style="padding: 12px 20px">
                <div class="stat-icon stat-icon-blue flex-shrink-0" style="width:36px;height:36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--text-muted)">Total Juara Lampau</p>
                    <p class="text-lg font-bold text-white">{{ number_format($legacyWinners->total()) }}</p>
                </div>
            </div>
        </div>

        <button id="btn-open-create" type="button" class="btn-primary flex items-center gap-2"
                onclick="openModal('modal-create')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Juara Lampau
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    @if($legacyWinners->isEmpty())
        <div class="card py-20 text-center">
            <p class="text-sm" style="color: var(--text-muted)">Belum ada data juara lampau.</p>
        </div>
    @else
        <div class="card p-0 overflow-hidden">
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none; width: 48px;">No</th>
                        <th style="border-right: none; width: 64px;">Gambar</th>
                        <th style="border-right: none;">Nama Proyek</th>
                        <th style="border-right: none;">Kompetisi</th>
                        <th style="border-right: none;">Tahun</th>
                        <th style="border-right: none;">Peringkat</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($legacyWinners as $i => $lw)
                        <tr>
                            <td style="border-right: none;">
                                <span class="text-sm" style="color: var(--text-muted)">{{ $legacyWinners->firstItem() + $i }}</span>
                            </td>

                            <td style="border-right: none;">
                                @if($lw->image)
                                    <img src="{{ $lw->image }}" alt="{{ $lw->project_name }}"
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

                            <td style="border-right: none;">
                                <p class="font-semibold text-sm">{{ $lw->project_name }}</p>
                                @if($lw->institution)
                                    <p class="text-xs" style="color: var(--text-muted)">{{ $lw->institution }}</p>
                                @endif
                            </td>

                            <td style="border-right: none;">
                                <span class="text-sm">{{ $lw->competition_name }}</span>
                            </td>

                            <td style="border-right: none;">
                                <span class="badge badge-valid">{{ $lw->year }}</span>
                            </td>

                            <td style="border-right: none;">
                                <span class="text-sm font-semibold">#{{ $lw->rank }} — {{ $lw->award_title }}</span>
                            </td>

                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    <button id="btn-edit-{{ $lw->id }}" type="button" class="btn-ghost"
                                            style="padding: 5px 10px; font-size: 12px;"
                                            onclick="openEditModal({{ json_encode([
                                                'id' => $lw->id,
                                                'year' => $lw->year,
                                                'project_name' => $lw->project_name,
                                                'project_description' => $lw->project_description,
                                                'institution' => $lw->institution,
                                                'competition_name' => $lw->competition_name,
                                                'rank' => $lw->rank,
                                                'award_title' => $lw->award_title,
                                                'image' => $lw->image,
                                            ]) }})">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.legacy-winners.destroy', $lw->id) }}"
                                          onsubmit="return confirm('Hapus data juara {{ addslashes($lw->project_name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button id="btn-delete-{{ $lw->id }}" type="submit" class="btn-danger" style="padding: 5px 10px; font-size: 12px;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($legacyWinners->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: rgba(255,255,255,.02);">
                    {{ $legacyWinners->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: CREATE --}}
    {{-- ============================================================ --}}
    <div id="modal-create" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 560px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Tambah Juara Lampau</h2>
                <button type="button" onclick="closeModal('modal-create')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.legacy-winners.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.legacy-winners._form')
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-create')" class="btn-ghost">Batal</button>
                    <button id="btn-create-submit" type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: EDIT --}}
    {{-- ============================================================ --}}
    <div id="modal-edit" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 560px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Edit Juara Lampau</h2>
                <button type="button" onclick="closeModal('modal-edit')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-edit" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('POST')
                @include('admin.legacy-winners._form')

                <div id="edit-current-img-wrap" class="mt-2" style="display:none;">
                    <p class="text-xs mb-1" style="color: var(--text-muted)">Gambar saat ini (biarkan kosong jika tidak ingin mengganti):</p>
                    <img id="edit-current-img" src="" alt="Current"
                         class="rounded-lg object-contain"
                         style="max-height: 80px; border: 1px solid var(--border); padding: 4px;">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-edit')" class="btn-ghost">Batal</button>
                    <button id="btn-edit-submit" type="submit" class="btn-primary">Simpan Perubahan</button>
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

    function openEditModal(data) {
        const form    = document.getElementById('form-edit');
        const baseUrl = '{{ url("/admin/legacy-winners") }}';
        form.action   = baseUrl + '/' + data.id;

        form.querySelector('[name="year"]').value = data.year ?? '';
        form.querySelector('[name="project_name"]').value = data.project_name ?? '';
        form.querySelector('[name="project_description"]').value = data.project_description ?? '';
        form.querySelector('[name="institution"]').value = data.institution ?? '';
        form.querySelector('[name="competition_name"]').value = data.competition_name ?? '';
        form.querySelector('[name="rank"]').value = data.rank ?? '';
        form.querySelector('[name="award_title"]').value = data.award_title ?? '';

        const fileInput = form.querySelector('[name="image"]');
        fileInput.value = '';

        const wrap = document.getElementById('edit-current-img-wrap');
        const img  = document.getElementById('edit-current-img');
        if (data.image) {
            img.src = data.image;
            wrap.style.display = 'block';
        } else {
            wrap.style.display = 'none';
        }

        const preview = document.getElementById('lw-preview');
        if (preview) {
            preview.src = '';
            preview.style.display = 'none';
        }

        openModal('modal-edit');
    }
    </script>
</x-admin-layout>
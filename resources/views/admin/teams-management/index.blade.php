<x-admin-layout title="Manajemen Tim" subtitle="Kelola data tim pada event aktif">

    {{-- HEADER ROW: Event badge --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            @if($activeEvent)
                <span class="badge badge-valid">{{ $activeEvent->name }}</span>
            @else
                <span class="badge badge-invalid">Tidak ada event aktif</span>
            @endif
        </div>
        
        <form method="GET" action="{{ route('admin.teams-management.index') }}" class="flex items-center gap-3">
            <select name="competition_id" class="form-input" onchange="this.form.submit()" style="width: 200px; padding-top: 6px; padding-bottom: 6px;">
                <option value="">Semua Kompetisi</option>
                @foreach($competitions as $comp)
                    <option value="{{ $comp->id }}" {{ $competitionId == $comp->id ? 'selected' : '' }}>
                        {{ $comp->name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode tim..." class="form-input" style="width: 250px; padding-top: 6px; padding-bottom: 6px;">
            <button type="submit" class="btn-primary" style="padding-top: 6px; padding-bottom: 6px;">Cari</button>
        </form>
    </div>

    {{-- TEAM LIST --}}
    @if($teams->isEmpty())
        <div class="card py-20 text-center">
            <p class="text-sm text-muted">Belum ada data tim yang ditemukan.</p>
        </div>
    @else
        <div class="card p-0 overflow-hidden mb-6">
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">Nama Tim / Kode</th>
                        <th style="border-right: none;">Ketua</th>
                        <th style="border-right: none;">Kompetisi</th>
                        <th style="border-right: none;">Institusi</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                        <tr>
                            <td style="border-right: none;">
                                <p class="font-semibold text-main text-sm">{{ $team->name ?? '-' }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $team->code ?? 'Tanpa Kode' }}</p>
                            </td>
                            <td style="border-right: none;">
                                <p class="font-semibold text-main text-sm">{{ $team->leader->name ?? '-' }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $team->leader->email ?? '-' }}</p>
                            </td>
                            <td style="border-right: none;">
                                <span class="badge" style="background: rgba(99,102,241,.12); color: #a5b4fc; border: 1px solid rgba(99,102,241,.25); font-size: 11px;">
                                    {{ $team->competition->name ?? '-' }}
                                </span>
                            </td>
                            <td style="border-right: none;">
                                <span class="text-sm text-main">{{ $team->leader->participant->institution ?? '-' }}</span>
                            </td>
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="btn-ghost" style="padding: 5px 12px; font-size: 12px;"
                                            onclick="openEditModal({{ json_encode([
                                                'id'             => $team->id,
                                                'name'           => $team->name,
                                                'code'           => $team->code,
                                                'title'          => $team->title,
                                                'leader_email'   => $team->leader->email ?? '',
                                                'competition_id' => $team->competition_id,
                                                'is_active'      => $team->is_active,
                                            ]) }})">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.teams-management.destroy', $team->id) }}"
                                          onsubmit="return confirm('Hapus tim {{ addslashes($team->name ?? $team->code) }}? Semua anggota tim mungkin juga akan terpengaruh.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" style="padding: 5px 12px; font-size: 12px;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div>
            {{ $teams->links() }}
        </div>
    @endif

    {{-- MODAL: EDIT --}}
    <div id="modal-edit" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 600px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Edit Tim</h2>
                <button type="button" onclick="closeModal('modal-edit')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-edit" method="POST" action="">
                @csrf
                @method('PATCH')
                @include('admin.teams-management._form', ['competitions' => $competitions])
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
    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    function openEditModal(data) {
        const form = document.getElementById('form-edit');
        
        // Wait, route action prefix depends on subfolder deployment. 
        // We can just use the route helper in Blade to get base url, or rely on absolute path if it is /admin/...
        // But since we can't use blade route helper easily in JS with dynamic IDs without string replace,
        // let's just use string replace.
        let baseUrl = "{{ route('admin.teams-management.update', ':id') }}";
        form.action = baseUrl.replace(':id', data.id);

        form.querySelector('[name="name"]').value           = data.name || '';
        form.querySelector('[name="code"]').value           = data.code || '';
        form.querySelector('[name="title"]').value          = data.title || '';
        form.querySelector('[name="leader_email"]').value   = data.leader_email || '';
        form.querySelector('[name="competition_id"]').value = data.competition_id || '';
        form.querySelector('[name="is_active"]').value      = data.is_active ? '1' : '0';

        openModal('modal-edit');
    }
    </script>

</x-admin-layout>

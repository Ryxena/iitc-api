<x-admin-layout title="Manajemen Kompetisi" subtitle="Kelola kompetisi pada event aktif">

    {{-- ============================================================ --}}
    {{-- HEADER ROW: Event badge + Add button --}}
    {{-- ============================================================ --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            @if($activeEvent)
                <span class="badge badge-valid">{{ $activeEvent->name }}</span>
            @else
                <span class="badge badge-invalid">Tidak ada event aktif</span>
            @endif
        </div>
        <button id="btn-open-create-modal" type="button" class="btn-primary flex items-center gap-2"
                onclick="openModal('modal-create')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kompetisi
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- COMPETITION LIST --}}
    {{-- ============================================================ --}}
    @if($competitions->isEmpty())
        <div class="card py-20 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color: var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <p class="text-sm text-muted">Belum ada kompetisi untuk event aktif. Klik "Tambah Kompetisi" untuk memulai.</p>
        </div>
    @else
        <div class="card p-0 overflow-hidden">
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">Kompetisi</th>
                        <th style="border-right: none;">Kategori</th>
                        <th class="text-center" style="border-right: none;">Tim</th>
                        <th style="border-right: none;">Deadline</th>
                        <th class="text-center" style="border-right: none;">Maks Anggota</th>
                        <th style="border-right: none;">Harga</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($competitions as $comp)
                        <tr>
                            {{-- Name + slug --}}
                            <td style="border-right: none;">
                                <p class="font-semibold text-main text-sm">{{ $comp->name }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $comp->slug }}</p>
                            </td>

                            {{-- Categories --}}
                            <td style="border-right: none;">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($comp->categories as $cat)
                                        <span class="badge" style="background: rgba(99,102,241,.12); color: #a5b4fc; border: 1px solid rgba(99,102,241,.25); font-size: 11px;">{{ $cat->name }}</span>
                                    @empty
                                        <span class="text-xs text-muted">—</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Teams count --}}
                            <td class="text-center" style="border-right: none;">
                                <span class="font-bold text-main text-sm">{{ $comp->teams_count }}</span>
                            </td>

                            {{-- Deadline --}}
                            <td style="border-right: none;">
                                <p class="text-sm text-main">{{ \Carbon\Carbon::parse($comp->deadline)->format('d M Y') }}</p>
                                @php $daysLeft = \Carbon\Carbon::parse($comp->deadline)->diffInDays(now(), false); @endphp
                                @if($daysLeft > 0)
                                    <p class="text-xs mt-0.5" style="color: #f87171;">Berakhir {{ $daysLeft }} hari lalu</p>
                                @else
                                    <p class="text-xs mt-0.5" style="color: #34d399;">{{ abs($daysLeft) }} hari lagi</p>
                                @endif
                            </td>

                            {{-- Max members --}}
                            <td class="text-center" style="border-right: none;">
                                <span class="text-sm text-main">{{ $comp->max_members }}</span>
                            </td>

                            {{-- Price --}}
                            <td style="border-right: none;">
                                <span class="text-sm text-main">Rp {{ number_format($comp->price, 0, ',', '.') }}</span>
                            </td>

                            {{-- Actions --}}
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    <button id="btn-edit-{{ $comp->slug }}" type="button" class="btn-ghost" style="padding: 5px 12px; font-size: 12px;"
                                            onclick="openEditModal({{ json_encode([
                                                'slug'        => $comp->slug,
                                                'name'        => $comp->name,
                                                'deadline'    => \Carbon\Carbon::parse($comp->deadline)->format('Y-m-d'),
                                                'max_members' => $comp->max_members,
                                                'price'       => $comp->price,
                                                'description' => $comp->description,
                                                'guide_book'  => $comp->guide_book,
                                                'categories'  => $comp->categories->pluck('id')->toArray(),
                                            ]) }})">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.competitions.destroy', $comp->slug) }}"
                                          onsubmit="return confirm('Hapus kompetisi {{ addslashes($comp->name) }}? Tim yang terdaftar tidak akan terhapus secara otomatis.')">
                                        @csrf
                                        @method('DELETE')
                                        <button id="btn-delete-comp-{{ $comp->slug }}" type="submit" class="btn-danger" style="padding: 5px 12px; font-size: 12px;">
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
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: CREATE --}}
    {{-- ============================================================ --}}
    <div id="modal-create" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 600px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Tambah Kompetisi</h2>
                <button type="button" onclick="closeModal('modal-create')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.competitions.store') }}">
                @csrf
                @include('admin.competitions._form', ['comp' => null, 'allCategories' => $allCategories, 'allEvents' => $allEvents, 'activeEvent' => $activeEvent])
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
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 600px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Edit Kompetisi</h2>
                <button type="button" onclick="closeModal('modal-edit')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-edit" method="POST" action="">
                @csrf
                @method('PATCH')
                @include('admin.competitions._form', ['comp' => null, 'allCategories' => $allCategories, 'allEvents' => $allEvents, 'activeEvent' => $activeEvent])
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
        form.action = '/admin/competitions/' + data.slug;

        form.querySelector('[name="name"]').value         = data.name;
        form.querySelector('[name="deadline"]').value     = data.deadline;
        form.querySelector('[name="max_members"]').value  = data.max_members;
        form.querySelector('[name="price"]').value        = data.price;
        form.querySelector('[name="description"]').value  = data.description ?? '';
        form.querySelector('[name="guide_book"]').value   = data.guide_book ?? '';

        // Reset and re-check categories
        form.querySelectorAll('[name="categories[]"]').forEach(cb => {
            cb.checked = data.categories.includes(parseInt(cb.value));
        });

        openModal('modal-edit');
    }
    </script>

</x-admin-layout>

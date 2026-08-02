<x-admin-layout title="Manajemen Sponsor" subtitle="Kelola daftar sponsor dan tier yang mendukung IITC">

    @php
        $tierLabels = [
            'platinum' => ['label' => '💎 Platinum', 'style' => 'background: rgba(168,85,247,.15); color: #c084fc; border: 1px solid rgba(168,85,247,.3);'],
            'gold'     => ['label' => '🥇 Gold',     'style' => 'background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3);'],
            'silver'   => ['label' => '🥈 Silver',   'style' => 'background: rgba(148,163,184,.15); color: #94a3b8; border: 1px solid rgba(148,163,184,.3);'],
            'bronze'   => ['label' => '🥉 Bronze',   'style' => 'background: rgba(180,83,9,.15); color: #fb923c; border: 1px solid rgba(180,83,9,.3);'],
        ];
    @endphp

    <style>
        .tier-tab {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .tier-tab:hover {
            background: #F3F4F6;
            color: var(--text-main);
        }
        .tier-tab.tier-active {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
        }
        .tier-tab.tier-active:hover {
            background: var(--accent-hover);
            color: #fff;
        }
    </style>

    {{-- ============================================================ --}}
    {{-- STATS & HEADER BUTTON --}}
    {{-- ============================================================ --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Total --}}
            <div class="card flex items-center gap-4" style="padding: 12px 20px">
                <div class="stat-icon stat-icon-blue flex-shrink-0" style="width:36px;height:36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium" style="color: var(--text-muted)">Total Sponsor</p>
                    <p class="text-lg font-bold text-white">{{ number_format($totalCount) }}</p>
                </div>
            </div>

            {{-- Per-tier mini badges --}}
            @foreach($tierCounts as $t => $count)
                @if($count > 0)
                    <div class="badge" style="{{ $tierLabels[$t]['style'] }}">
                        {{ $tierLabels[$t]['label'] }}: {{ $count }}
                    </div>
                @endif
            @endforeach
        </div>

        <button id="btn-open-create-sponsor" type="button" class="btn-primary flex items-center gap-2"
                onclick="openModal('modal-create-sponsor')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Sponsor
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTER BAR --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Tier filter tabs --}}
            <div style="display: flex; gap: 4px; padding: 4px; border-radius: 8px; border: 1px solid var(--border); background: #F9FAFB;">
                @foreach(['ALL' => 'Semua', 'platinum' => '💎 Platinum', 'gold' => '🥇 Gold', 'silver' => '🥈 Silver', 'bronze' => '🥉 Bronze'] as $val => $label)
                    <a href="{{ route('admin.sponsors.index', ['tier' => $val, 'search' => $search]) }}"
                       class="tier-tab {{ strtolower($tier) === strtolower($val) ? 'tier-active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.sponsors.index') }}" class="flex-1 min-w-[200px] flex gap-2">
                <input type="hidden" name="tier" value="{{ $tier }}">
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="sponsor-search" type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama sponsor..." class="form-input pl-10">
                </div>
                <button id="btn-sponsor-search" type="submit" class="btn-primary" style="white-space:nowrap">Cari</button>
                @if($search)
                    <a href="{{ route('admin.sponsors.index', ['tier' => $tier]) }}" class="btn-ghost" style="white-space:nowrap">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card p-0 overflow-hidden">
        @if($sponsors->isEmpty())
            <div class="py-20 text-center" style="background: rgba(255,255,255,.02)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium" style="color: var(--text-muted)">Belum ada sponsor. Klik "Tambah Sponsor" untuk membuat baru.</p>
            </div>
        @else
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">No</th>
                        <th style="border-right: none;">Logo</th>
                        <th style="border-right: none;">Nama</th>
                        <th style="border-right: none;">Tier</th>
                        <th style="border-right: none;">Ditambahkan</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sponsors as $i => $sp)
                        <tr>
                            {{-- No --}}
                            <td style="border-right: none; width: 48px;">
                                <span class="text-sm" style="color: var(--text-muted)">{{ $sponsors->firstItem() + $i }}</span>
                            </td>

                            {{-- Logo --}}
                            <td style="border-right: none; width: 80px;">
                                @if($sp->image)
                                    <img src="{{ $sp->image }}" alt="{{ $sp->name }}"
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
                                <p class="font-semibold text-sm text-white">{{ $sp->name }}</p>
                            </td>

                            {{-- Tier --}}
                            <td style="border-right: none;">
                                <span class="badge" style="{{ $tierLabels[$sp->tier]['style'] ?? '' }}">
                                    {{ $tierLabels[$sp->tier]['label'] ?? $sp->tier }}
                                </span>
                            </td>

                            {{-- Ditambahkan --}}
                            <td style="border-right: none;">
                                <span class="text-sm" style="color: var(--text-muted)">{{ $sp->created_at->format('d M Y') }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td style="border-right: none; text-align: right;">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Edit --}}
                                    <button id="btn-edit-sponsor-{{ $sp->id }}" type="button" class="btn-ghost"
                                            style="padding: 5px 10px; font-size: 12px;"
                                            onclick="openEditSponsorModal({{ json_encode(['id' => $sp->id, 'name' => $sp->name, 'tier' => $sp->tier, 'image' => $sp->image]) }})">
                                        Edit
                                    </button>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.sponsors.destroy', $sp->id) }}"
                                          onsubmit="return confirm('Hapus sponsor {{ addslashes($sp->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button id="btn-delete-sponsor-{{ $sp->id }}" type="submit" class="btn-danger" style="padding: 5px 10px; font-size: 12px;">
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
            @if($sponsors->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: rgba(255,255,255,.02);">
                    {{ $sponsors->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CREATE --}}
    {{-- ============================================================ --}}
    <div id="modal-create-sponsor" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 520px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Tambah Sponsor</h2>
                <button type="button" onclick="closeModal('modal-create-sponsor')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.sponsors.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.sponsors._form')
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-create-sponsor')" class="btn-ghost">Batal</button>
                    <button id="btn-create-sponsor-submit" type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: EDIT --}}
    {{-- ============================================================ --}}
    <div id="modal-edit-sponsor" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 520px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Edit Sponsor</h2>
                <button type="button" onclick="closeModal('modal-edit-sponsor')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-edit-sponsor" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('POST')
                @include('admin.sponsors._form')

                {{-- Current image display --}}
                <div id="sp-edit-current-img-wrap" class="mt-2" style="display:none;">
                    <p class="text-xs mb-1" style="color: var(--text-muted)">Logo saat ini (biarkan kosong jika tidak ingin mengganti):</p>
                    <img id="sp-edit-current-img" src="" alt="Current"
                         class="rounded-lg object-contain"
                         style="max-height: 80px; border: 1px solid var(--border); padding: 4px;">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-edit-sponsor')" class="btn-ghost">Batal</button>
                    <button id="btn-edit-sponsor-submit" type="submit" class="btn-primary">Simpan Perubahan</button>
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

    function openEditSponsorModal(data) {
        const form    = document.getElementById('form-edit-sponsor');
        const baseUrl = '{{ url("/admin/sponsors") }}';
        form.action   = baseUrl + '/' + data.id;

        form.querySelector('[name="name"]').value = data.name ?? '';

        // Set tier select value
        form.querySelector('[name="tier"]').value = data.tier ?? '';

        // Reset file input
        const fileInput = form.querySelector('[name="image"]');
        fileInput.value = '';

        // Show existing image
        const wrap = document.getElementById('sp-edit-current-img-wrap');
        const img  = document.getElementById('sp-edit-current-img');
        if (data.image) {
            img.src = data.image;
            wrap.style.display = 'block';
        } else {
            wrap.style.display = 'none';
        }

        // Reset live preview
        const preview = document.getElementById('sp-preview');
        preview.src = '';
        preview.style.display = 'none';

        openModal('modal-edit-sponsor');
    }
    </script>

</x-admin-layout>

<x-admin-layout title="Manajemen User" subtitle="Daftar semua pengguna terdaftar">

    {{-- ============================================================ --}}
    {{-- FILTER + SEARCH BAR --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-4">

            {{-- Search --}}
            <div class="flex-1 min-w-[220px] relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    id="user-search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, email, atau nomor HP..."
                    class="form-input pl-10"
                >
            </div>

            {{-- Competition Filter --}}
            @if($competitions->isNotEmpty())
                <select id="competition-filter" name="competition" class="form-input" style="width: auto; min-width: 200px;">
                    <option value="">Semua Kompetisi</option>
                    @foreach($competitions as $comp)
                        <option value="{{ $comp->id }}" {{ $competitionId == $comp->id ? 'selected' : '' }}>
                            {{ $comp->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button id="btn-user-search" type="submit" class="btn-primary" style="white-space:nowrap">Cari</button>
            @if($search || $competitionId)
                <a href="{{ route('admin.users.index') }}" class="btn-ghost" style="white-space:nowrap">Reset</a>
            @endif
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card p-0 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border)">
            <p class="text-sm font-medium text-muted">
                Menampilkan <strong class="text-main">{{ $users->count() }}</strong> dari
                <strong class="text-main">{{ $users->total() }}</strong> user
            </p>
        </div>

        @if($users->isEmpty())
            <div class="py-20 text-center" style="background: rgba(255,255,255,.02)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                <p class="text-sm font-medium text-muted">Tidak ada user yang sesuai filter.</p>
            </div>
        @else
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">User</th>
                        <th style="border-right: none;">Kontak</th>
                        <th style="border-right: none;">Institusi</th>
                        <th style="border-right: none;">Bergabung</th>
                        <th style="border-right: none; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            {{-- User --}}
                            <td style="border-right: none;">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 text-white"
                                         style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-main text-sm">{{ $user->name }}</p>
                                        <p class="text-xs text-muted mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kontak --}}
                            <td style="border-right: none;">
                                <p class="text-sm text-main">{{ $user->phone ?? '—' }}</p>
                            </td>

                            {{-- Institusi --}}
                            <td style="border-right: none;">
                                <p class="text-sm text-main">{{ $user->participant?->institution ?? '—' }}</p>
                                @if($user->participant?->grade)
                                    <p class="text-xs text-muted mt-0.5">{{ $user->participant->grade }}</p>
                                @endif
                            </td>

                            {{-- Bergabung --}}
                            <td style="border-right: none;">
                                <p class="text-sm text-main">{{ $user->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $user->created_at->format('H:i') }}</p>
                            </td>

                            {{-- Aksi --}}
                            <td style="border-right: none; text-align: right;">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="btn-ghost" style="padding: 5px 12px; font-size: 12px;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                          onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button id="btn-delete-user-{{ $user->id }}" type="submit" class="btn-danger" style="padding: 5px 12px; font-size: 12px;">
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
            @if($users->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: rgba(255,255,255,.02);">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</x-admin-layout>

<x-admin-layout title="Validasi Payment" subtitle="Daftar bukti pembayaran lomba tim">

    {{-- ============================================================ --}}
    {{-- FILTER + SEARCH BAR --}}
    {{-- ============================================================ --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-center gap-4">

            {{-- Status tabs --}}
            <div class="flex gap-1 p-1 bg-gray-50 rounded-lg border" style="background: #F3F4F6; border-color: var(--border);">
                @foreach(['ALL' => 'Semua', 'PENDING' => 'Pending', 'VALID' => 'Valid', 'INVALID' => 'Ditolak'] as $val => $label)
                    <a href="{{ route('admin.payments.index', ['status' => $val, 'search' => request('search')]) }}"
                       class="px-4 py-1.5 font-medium rounded-md transition-all text-sm"
                       style="{{ $status === $val
                            ? 'background: #fff; color: var(--text-main); box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-weight: 600;'
                            : 'color: var(--text-muted);' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <div class="flex-1 min-w-[220px] relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    id="payment-search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari tim, kompetisi, atau ketua..."
                    class="form-input pl-10"
                >
            </div>

            <button id="btn-search" type="submit" class="btn-primary" style="white-space:nowrap">Cari</button>
            @if($search)
                <a href="{{ route('admin.payments.index', ['status' => $status]) }}" class="btn-ghost" style="white-space:nowrap">Reset</a>
            @endif
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card p-0 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border)">
            <p class="text-sm font-medium text-muted">
                Menampilkan <strong class="text-main">{{ $teams->count() }}</strong> dari
                <strong class="text-main">{{ $teams->total() }}</strong> tim
            </p>
            <a href="{{ route('admin.export.teams') }}" class="btn-ghost text-sm py-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </a>
        </div>

        @if($teams->isEmpty())
            <div class="py-20 text-center bg-gray-50" style="background: #F9FAFB;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" style="color: #D1D5DB;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-muted">Tidak ada payment yang sesuai filter.</p>
            </div>
        @else
            <table style="border: none;">
                <thead>
                    <tr>
                        <th style="border-right: none;">Tim</th>
                        <th style="border-right: none;">Kompetisi</th>
                        <th style="border-right: none;">Ketua</th>
                        <th style="border-right: none;" class="text-center">Anggota</th>
                        <th style="border-right: none;">Upload</th>
                        <th style="border-right: none;">Status</th>
                        <th style="border-right: none;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                        <tr>
                            {{-- Tim --}}
                            <td style="border-right: none;">
                                <div class="flex items-center gap-3">
                                    @if($team->avatar)
                                        <img src="{{ $team->avatar }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border" style="border-color: var(--border);" alt="{{ $team->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                             style="background: #EEF2FF; color: var(--accent);">
                                            {{ strtoupper(substr($team->name ?? 'T', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-main text-sm">{{ $team->name ?? '—' }}</p>
                                        <p class="text-xs text-muted mt-0.5">{{ $team->code ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kompetisi --}}
                            <td style="border-right: none;">
                                <p class="text-sm font-medium text-main">{{ $team->competition->name ?? '—' }}</p>
                            </td>

                            {{-- Ketua --}}
                            <td style="border-right: none;">
                                <p class="text-sm font-medium text-main">{{ $team->leader->name ?? '—' }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $team->leader->email ?? '—' }}</p>
                            </td>

                            {{-- Anggota --}}
                            <td class="text-center" style="border-right: none;">
                                <span class="text-sm font-semibold text-main">{{ $team->members_count + 1 }}</span>
                                <span class="text-xs text-muted">/{{ $team->competition->max_members ?? '?' }}</span>
                            </td>

                            {{-- Tanggal upload --}}
                            <td style="border-right: none;">
                                <p class="text-sm font-medium text-main">
                                    {{ $team->payment?->updated_at?->format('d M Y') ?? '—' }}
                                </p>
                                <p class="text-xs text-muted mt-0.5">
                                    {{ $team->payment?->updated_at?->format('H:i') }}
                                </p>
                            </td>

                            {{-- Status --}}
                            <td style="border-right: none;">
                                @php $ps = $team->paymentStatus?->status; @endphp
                                @if(! $team->payment)
                                    <span class="badge" style="background: #F3F4F6; color: #6B7280; border: 1px solid #E5E7EB;">Belum Upload</span>
                                @elseif($ps === 'VALID')
                                    <span class="badge badge-valid">Valid</span>
                                @elseif($ps === 'INVALID')
                                    <span class="badge badge-invalid text-red-700" style="background: #FEF2F2; color: #B91C1C; border-color: #FECACA;">Ditolak</span>
                                @else
                                    <span class="badge badge-pending">Pending</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td style="border-right: none; text-align: right;">
                                <a href="{{ route('admin.payments.show', $team->id) }}"
                                   class="text-sm font-medium" style="color: var(--accent); text-decoration: none;">
                                    Periksa →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($teams->hasPages())
                <div class="p-6 border-t" style="border-color: var(--border); background: #F9FAFB;">
                    {{ $teams->links() }}
                </div>
            @endif
        @endif
    </div>

</x-admin-layout>

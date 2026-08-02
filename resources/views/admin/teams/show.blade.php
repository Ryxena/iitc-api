<x-admin-layout
    title="Detail Tim"
    subtitle="Tim: {{ $team->name ?? '—' }} · {{ $team->competition->name ?? '—' }}"
>

    <div class="mb-5">
        <a href="{{ route('admin.teams.recap') }}" class="text-sm font-medium" style="color: var(--text-muted); display:inline-flex; align-items:center; gap:6px; text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Rekap Tim
        </a>
    </div>

    <div class="grid gap-6" style="grid-template-columns: 1fr 1fr">

        {{-- ============================================================ --}}
        {{-- LEFT: Info Tim & Submission --}}
        {{-- ============================================================ --}}
        <div class="space-y-6">

            {{-- Info Tim --}}
            <div class="card">
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Tim
                </h2>
                <dl class="space-y-1">
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Nama Tim</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Kode Tim</dt>
                        <dd><span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700 font-medium" style="background: #F3F4F6;">{{ $team->code ?? '—' }}</span></dd>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Kompetisi</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->competition->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Judul Karya</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->title ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <dt class="text-sm text-muted">Total Anggota</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->members->count() + 1 }} orang (termasuk ketua)</dd>
                    </div>
                </dl>
            </div>

            {{-- Submission --}}
            <div class="card">
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    File / Link Submission
                </h2>

                @if($team->submission || $team->submission_file_name)
                    <div class="rounded-lg border p-4 bg-gray-50 flex items-center justify-between" style="border-color: var(--border)">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">Sudah Submit</p>
                                <p class="text-xs font-medium text-gray-500 mt-0.5 truncate max-w-[200px]">{{ $team->submission_file_name ?? $team->submission }}</p>
                            </div>
                        </div>
                        @if($team->submission)
                            <a href="{{ filter_var($team->submission, FILTER_VALIDATE_URL) ? $team->submission : '/storage/' . $team->submission }}" target="_blank"
                               class="inline-flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs py-2 px-4 rounded-lg shadow-sm transition-colors border border-indigo-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Buka Link
                            </a>
                        @endif
                    </div>
                @else
                    <div class="py-12 text-center rounded-lg border border-dashed" style="background: #F9FAFB; border-color: #D1D5DB;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-gray-300" style="color: #9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-muted">Belum ada karya yang disubmit.</p>
                    </div>
                @endif
            </div>
            
            {{-- Status Payment --}}
            <div class="card">
                <h3 class="text-xs font-semibold mb-3 text-muted uppercase tracking-wider">Status Pembayaran</h3>
                @php $ps = $team->paymentStatus; @endphp
                @if(!$ps)
                    <span class="badge badge-pending">Belum Diproses</span>
                @elseif($ps->status === 'VALID')
                    <span class="badge badge-valid">Terverifikasi</span>
                @elseif($ps->status === 'INVALID')
                    <div>
                        <span class="badge" style="background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA;">Ditolak</span>
                        @if($ps->reason)
                            <div class="text-sm mt-3 p-3 rounded-md border" style="background: #FEF2F2; border-color: #FECACA; color: #991B1B;">
                                <span class="font-semibold">Alasan:</span> {{ $ps->reason }}
                            </div>
                        @endif
                    </div>
                @else
                    <span class="badge badge-pending">Pending</span>
                @endif
                
                @if($team->payment && $team->payment->path)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ str_starts_with($team->payment->path, 'http') ? $team->payment->path : '/storage/' . $team->payment->path }}" target="_blank" 
                           class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat Bukti Transfer
                        </a>
                    </div>
                @endif
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT: Info Ketua + Anggota --}}
        {{-- ============================================================ --}}
        <div class="space-y-6">

            {{-- Info Ketua --}}
            <div class="card relative">
                <span class="absolute top-4 right-4 bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Ketua</span>
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Ketua Tim
                </h2>
                <div class="flex items-center gap-4 mb-4 pb-4 border-b" style="border-color: var(--border)">
                    @if($team->leader?->participant?->avatar)
                        <img src="/storage/{{ $team->leader->participant->avatar }}" class="w-12 h-12 rounded-full border object-cover" style="border-color: var(--border)" alt="{{ $team->leader->name }}">
                    @else
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-semibold"
                             style="background: #EEF2FF; color: var(--accent);">
                            {{ strtoupper(substr($team->leader->name ?? 'K', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-medium text-main">{{ $team->leader->name ?? '—' }}</p>
                        <p class="text-sm text-muted">{{ $team->leader->email ?? '—' }}</p>
                    </div>
                </div>
                <dl class="space-y-1 mb-4">
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">No. HP</dt>
                        <dd class="text-sm font-medium text-main">
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $team->leader->phone ?? '') }}" target="_blank" class="text-indigo-600 hover:underline">{{ $team->leader->phone ?? '—' }}</a>
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Institusi</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->leader->participant->institution ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Angkatan</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->leader->participant->grade ?? '—' }}</dd>
                    </div>
                </dl>
                <div class="flex items-center gap-3">
                    @if($team->leader?->participant?->photo_identity)
                        <a href="/storage/{{ $team->leader->participant->photo_identity }}" target="_blank" class="flex-1 text-center bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-medium text-xs py-2 rounded-lg transition-colors">
                            Lihat Kartu Identitas
                        </a>
                    @endif
                    @if($team->leader?->participant?->twibbon)
                        <a href="/storage/{{ $team->leader->participant->twibbon }}" target="_blank" class="flex-1 text-center bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-100 font-medium text-xs py-2 rounded-lg transition-colors">
                            Lihat Twibbon
                        </a>
                    @endif
                </div>
            </div>

            {{-- Anggota --}}
            @if($team->members->isNotEmpty())
                <div class="card">
                    <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        Anggota Tim
                    </h2>
                    <div class="space-y-4">
                        @foreach($team->members as $idx => $member)
                            <div class="p-4 rounded-lg border bg-white shadow-sm relative" style="border-color: var(--border)">
                                <span class="absolute top-3 right-3 text-gray-300 font-bold text-xs">#{{ $idx + 1 }}</span>
                                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-100">
                                    @if($member->participant?->avatar)
                                        <img src="/storage/{{ $member->participant->avatar }}" class="w-10 h-10 rounded-full border object-cover flex-shrink-0" style="border-color: var(--border)" alt="{{ $member->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"
                                             style="background: #FFF7ED; color: #EA580C;">
                                            {{ strtoupper(substr($member->name ?? 'A', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0 pr-6">
                                        <p class="text-sm font-medium text-main truncate">{{ $member->name }}</p>
                                        <p class="text-xs text-muted truncate">{{ $member->email }}</p>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-600 mb-3 space-y-1">
                                    <p><strong>HP:</strong> {{ $member->phone ?? '-' }}</p>
                                    <p><strong>Institusi:</strong> {{ $member->participant->institution ?? '-' }} ({{ $member->participant->grade ?? '-' }})</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($member->participant?->photo_identity)
                                        <a href="/storage/{{ $member->participant->photo_identity }}" target="_blank" class="flex-1 text-center bg-gray-50 border border-gray-200 text-gray-600 hover:bg-gray-100 font-medium text-[10px] py-1.5 rounded transition-colors">
                                            ID Card
                                        </a>
                                    @endif
                                    @if($member->participant?->twibbon)
                                        <a href="/storage/{{ $member->participant->twibbon }}" target="_blank" class="flex-1 text-center bg-indigo-50 border border-indigo-100 text-indigo-600 hover:bg-indigo-100 font-medium text-[10px] py-1.5 rounded transition-colors">
                                            Twibbon
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>

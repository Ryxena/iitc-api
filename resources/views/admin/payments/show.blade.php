<x-admin-layout
    title="Detail Payment"
    subtitle="Tim: {{ $team->name ?? '—' }} · {{ $team->competition->name ?? '—' }}"
>

    <div class="mb-5">
        <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium" style="color: var(--text-muted); display:inline-flex; align-items:center; gap:6px; text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid gap-6" style="grid-template-columns: 1fr 1fr">

        {{-- ============================================================ --}}
        {{-- LEFT: Bukti Bayar + Form Validasi --}}
        {{-- ============================================================ --}}
        <div class="space-y-6">

            {{-- Proof of Payment --}}
            <div class="card">
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bukti Pembayaran
                </h2>

                @if($team->payment?->transfer_receipt)
                    <div class="rounded-lg overflow-hidden border" style="border-color: var(--border)">
                        <img
                            id="proof-img"
                            src="{{ $team->payment->transfer_receipt }}"
                            alt="Bukti Bayar"
                            class="w-full object-contain cursor-zoom-in"
                            style="max-height: 480px; background: #F9FAFB;"
                            onclick="document.getElementById('img-modal').classList.remove('hidden')"
                            onerror="this.style.display='none'; document.getElementById('img-error').style.display='flex'"
                        >
                        <div id="img-error" class="bg-gray-50" style="display:none; justify-content:center; align-items:center; padding:48px; flex-direction:column; gap:12px; background: #F9FAFB;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300" style="color: #D1D5DB" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-muted">Gambar tidak dapat dimuat</p>
                        </div>
                    </div>
                    <a href="{{ $team->payment->transfer_receipt }}" target="_blank"
                       class="mt-4 text-sm font-medium"
                       style="color: var(--accent); display:inline-flex; align-items:center; gap:6px; text-decoration:none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Buka di tab baru
                    </a>
                @else
                    <div class="py-12 text-center rounded-lg border border-dashed" style="background: #F9FAFB; border-color: #D1D5DB;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-gray-300" style="color: #9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-muted">Belum ada bukti pembayaran.</p>
                    </div>
                @endif
            </div>

            {{-- Status saat ini --}}
            <div class="card">
                <h3 class="text-xs font-semibold mb-3 text-muted uppercase tracking-wider">Status Saat Ini</h3>
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
                
                @if($ps?->updated_at)
                    <p class="text-xs mt-3 text-muted">
                        Terakhir diperbarui: {{ $ps->updated_at->format('d M Y, H:i') }}
                    </p>
                @endif
            </div>

            {{-- Approve / Reject Form --}}
            @if($team->payment)
                <div class="card" x-data="{ isApprove: null, reason: '' }">
                    <h2 class="font-semibold text-main mb-5 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Validasi Payment
                    </h2>

                    <form
                        method="POST"
                        action="{{ route('admin.payments.update', $team->id) }}"
                        onsubmit="return confirm(isApprove == 1 ? 'Approve payment tim ini?' : 'Tolak payment tim ini?')"
                    >
                        @csrf
                        @method('PATCH')

                        {{-- Pilihan --}}
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <label>
                                <input type="radio" name="is_approve" value="1" id="radio-approve" class="sr-only" required>
                                <div class="selectable-card">
                                    <div class="icon-ring">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-check" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold" style="color: var(--text-main)">Approve</p>
                                    <p class="text-xs mt-0.5 text-muted">Payment valid</p>
                                </div>
                            </label>
                            <label>
                                <input type="radio" name="is_approve" value="0" id="radio-reject" class="sr-only" required>
                                <div class="selectable-card">
                                     <div class="icon-ring">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-check" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold" style="color: var(--text-main)">Tolak</p>
                                    <p class="text-xs mt-0.5 text-muted">Payment ditolak</p>
                                </div>
                            </label>
                        </div>

                        {{-- Alasan (tampil saat reject) --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-1 text-main">
                                Alasan <span class="text-xs text-muted font-normal">(opsional untuk approve, wajib jika tolak)</span>
                            </label>
                            <textarea
                                id="reason-input"
                                name="reason"
                                rows="3"
                                placeholder="Contoh: Bukti bayar blur, nominal tidak sesuai, dll."
                                class="form-input resize-none"
                            ></textarea>
                        </div>

                        <button id="btn-submit-validation" type="submit" class="btn-primary w-full">
                            Simpan Keputusan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT: Info Tim + Anggota --}}
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
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Total Anggota</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->members->count() + 1 }} orang (termasuk ketua)</dd>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <dt class="text-sm text-muted">Submission</dt>
                        <dd>
                            @if($team->submission)
                                <a href="{{ $team->submission }}" target="_blank" class="text-sm font-medium" style="color: var(--accent); text-decoration: none;">Lihat File ↗</a>
                            @else
                                <span class="text-sm text-muted">Belum submit</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Info Ketua --}}
            <div class="card">
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Ketua Tim
                </h2>
                <div class="flex items-center gap-4 mb-4 pb-4 border-b" style="border-color: var(--border)">
                    @if($team->leader?->participant?->avatar)
                        <img src="{{ $team->leader->participant->avatar }}" class="w-12 h-12 rounded-full border object-cover" style="border-color: var(--border)" alt="{{ $team->leader->name }}">
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
                <dl class="space-y-1">
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">No. HP</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->leader->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Institusi</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->leader->participant->institution ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-muted">Angkatan</dt>
                        <dd class="text-sm font-medium text-main">{{ $team->leader->participant->grade ?? '—' }}</dd>
                    </div>
                </dl>
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
                    <div class="space-y-3">
                        @foreach($team->members as $member)
                            <div class="flex items-center gap-3 p-3 rounded-lg border" style="background: #F9FAFB; border-color: var(--border)">
                                @if($member->participant?->avatar)
                                    <img src="{{ $member->participant->avatar }}" class="w-8 h-8 rounded-full border object-cover flex-shrink-0" style="border-color: var(--border)" alt="{{ $member->name }}">
                                @else
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0"
                                         style="background: #FFF7ED; color: #EA580C;">
                                        {{ strtoupper(substr($member->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-main truncate">{{ $member->name }}</p>
                                    <p class="text-xs text-muted truncate">{{ $member->email }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- IMAGE LIGHTBOX MODAL --}}
    {{-- ============================================================ --}}
    <div id="img-modal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(4px)"
         onclick="this.classList.add('hidden')">
        <img src="{{ $team->payment?->transfer_receipt }}"
             class="max-w-full max-h-full rounded-lg shadow-2xl cursor-zoom-out"
             alt="Bukti Bayar">
        <button
            id="btn-close-modal"
            onclick="document.getElementById('img-modal').classList.add('hidden')"
            class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center text-white bg-black bg-opacity-50 hover:bg-opacity-80 transition-colors"
            style="border: none;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

</x-admin-layout>

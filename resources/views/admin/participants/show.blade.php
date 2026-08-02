<x-admin-layout
    title="Detail Individu"
    subtitle="Peserta: {{ $participant->user->name ?? '—' }}"
>

    <div class="mb-5">
        <a href="{{ route('admin.participants.recap') }}" class="text-sm font-medium" style="color: var(--text-muted); display:inline-flex; align-items:center; gap:6px; text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Rekap Individu
        </a>
    </div>

    <div class="grid gap-6" style="grid-template-columns: 1fr 1fr">

        {{-- ============================================================ --}}
        {{-- LEFT: Info Peserta --}}
        {{-- ============================================================ --}}
        <div class="space-y-6">

            <div class="card relative">
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informasi Peserta
                </h2>
                <div class="flex items-center gap-4 mb-4 pb-4 border-b" style="border-color: var(--border)">
                    @if($participant->avatar)
                        <img src="/storage/{{ $participant->avatar }}" class="w-16 h-16 flex-shrink-0 rounded-full border object-cover" style="border-color: var(--border)" alt="{{ $participant->user->name ?? '—' }}">
                    @else
                        <div class="w-16 h-16 flex-shrink-0 rounded-full flex items-center justify-center text-xl font-semibold"
                             style="background: #EEF2FF; color: var(--accent);">
                            {{ strtoupper(substr($participant->user->name ?? 'P', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-lg text-main">{{ $participant->user->name ?? '—' }}</p>
                        <p class="text-sm text-muted">{{ $participant->user->email ?? '—' }}</p>
                    </div>
                </div>
                <dl class="space-y-1 mb-4">
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Email</dt>
                        <dd class="text-sm font-medium text-main">{{ $participant->user->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">No. HP</dt>
                        <dd class="text-sm font-medium text-main">
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $participant->user->phone ?? '') }}" target="_blank" class="text-indigo-600 hover:underline">{{ $participant->user->phone ?? '—' }}</a>
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Institusi</dt>
                        <dd class="text-sm font-medium text-main">{{ $participant->institution ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Angkatan / Kelas</dt>
                        <dd class="text-sm font-medium text-main">{{ $participant->grade ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Jenis Kelamin</dt>
                        <dd class="text-sm font-medium text-main">
                            @php
                                $genderMap = [
                                    'laki-laki' => 'Laki-laki',
                                    'perempuan'  => 'Perempuan',
                                    'L'          => 'Laki-laki',
                                    'P'          => 'Perempuan',
                                ];
                                $genderDisplay = $genderMap[$participant->gender ?? ''] ?? ($participant->gender ?? '—');
                            @endphp
                            @if($participant->gender)
                                <span class="inline-flex items-center gap-1.5">
                                    @if(in_array(strtolower($participant->gender), ['laki-laki', 'l']))
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3h5m0 0v5m0-5l-6.5 6.5M9 3a6 6 0 100 12A6 6 0 009 3z"/>
                                        </svg>
                                    @elseif(in_array(strtolower($participant->gender), ['perempuan', 'p']))
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a6 6 0 100-12 6 6 0 000 12zm0 0v3m0 3h0m-3-3h6"/>
                                        </svg>
                                    @endif
                                    {{ $genderDisplay }}
                                </span>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">No. Identitas Siswa / NIM</dt>
                        <dd class="text-sm font-medium text-main font-mono tracking-wide">{{ $participant->student_id_number ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b" style="border-color: var(--border)">
                        <dt class="text-sm text-muted">Tim Lomba</dt>
                        <dd class="text-sm font-medium text-right">
                            @php
                                $allTeams = $participant->user ? $participant->user->teams->merge($participant->user->asMembers) : collect();
                            @endphp
                            @if($allTeams->isNotEmpty())
                                <div class="flex flex-col gap-1 items-end">
                                    @foreach($allTeams as $team)
                                        <a href="{{ route('admin.teams.recap.show', $team->id) }}" class="inline-flex items-center px-2 py-1 rounded bg-indigo-50 text-indigo-700 text-xs border border-indigo-200 font-medium hover:bg-indigo-100 transition-colors">
                                            {{ $team->name }} ({{ $team->competition->name ?? '?' }})
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 italic">Tidak ada tim</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT: Dokumen Upload (Twibbon & ID Card) --}}
        {{-- ============================================================ --}}
        <div class="space-y-6">

            <div class="card">
                <h2 class="font-semibold text-main mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Dokumen Upload
                </h2>
                
                {{-- Twibbon --}}
                <div class="border rounded-xl p-4 bg-gray-50 flex flex-col items-center justify-center text-center">
                    <p class="text-sm font-bold text-gray-700 mb-3">Twibbon</p>
                    @if($participant->twibbon)
                        <div class="w-full aspect-square bg-gray-200 rounded-lg overflow-hidden mb-3 border border-gray-300">
                            <img src="{{ $participant->twibbon }}" alt="Twibbon" class="w-full h-full object-cover cursor-zoom-in" onclick="showImageModal('{{ $participant->twibbon }}')">
                        </div>
                        <a href="{{ $participant->twibbon }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-xs py-2 rounded-lg transition-colors shadow-sm" style="text-decoration: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Lihat Penuh
                        </a>
                    @else
                        <div class="w-full aspect-square bg-gray-100 rounded-lg border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs">Belum diupload</span>
                        </div>
                    @endif
                </div>

            </div>



        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- IMAGE LIGHTBOX MODAL --}}
    {{-- ============================================================ --}}
    <div id="img-modal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(4px)"
         onclick="this.classList.add('hidden')">
        <img id="modal-img-src" src=""
             class="max-w-full max-h-full rounded-lg shadow-2xl cursor-zoom-out"
             alt="Preview Dokumen">
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

    <script>
        function showImageModal(src) {
            document.getElementById('modal-img-src').src = src;
            document.getElementById('img-modal').classList.remove('hidden');
        }
    </script>

</x-admin-layout>

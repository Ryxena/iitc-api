<x-admin-layout title="Recap Peserta Lomba" subtitle="Rekapitulasi data tim dan peserta kompetisi (Format LPJ)">

    {{-- STATS CARDS - LIGHT MODE --}}
    <div class="grid gap-5 mb-8" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))">

        {{-- Total Tim --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-indigo-50 text-indigo-600 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Tim</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totalTeamsCount) }}</p>
                <p class="text-xs font-medium text-gray-400 mt-0.5">Seluruh tim terdaftar</p>
            </div>
        </div>

        {{-- Tim Validated --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tim Terverifikasi</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($validatedCount) }}</p>
                <p class="text-xs font-medium text-emerald-500 mt-0.5">Payment Valid</p>
            </div>
        </div>

        {{-- Tim Pending --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Payment</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($pendingCount) }}</p>
                <p class="text-xs font-medium text-amber-500 mt-0.5">Menunggu Validasi</p>
            </div>
        </div>

        {{-- Total Individual Participants --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-purple-50 text-purple-600 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Individu</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totalParticipants) }}</p>
                <p class="text-xs font-medium text-gray-400 mt-0.5">Ketua + Anggota</p>
            </div>
        </div>

    </div>

    {{-- COMPETITION BREAKDOWN BADGES --}}
    @if($competitionBreakdown->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Breakdown Per Cabang Lomba</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($competitionBreakdown as $comp)
                    @php
                        $isActive = (string)$competitionId === (string)$comp->id;
                        $tabClasses = $isActive 
                            ? 'bg-sky-100 text-sky-800' 
                            : 'text-gray-600 hover:text-sky-800 hover:bg-gray-50';
                    @endphp
                    <a href="{{ route('admin.teams.recap', array_merge(request()->query(), ['competition_id' => $isActive ? 'ALL' : $comp->id])) }}" 
                       class="flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $tabClasses }}">
                        <span>{{ $comp->name }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-700">
                            {{ $comp->teams_count }} Tim ({{ $comp->validated_teams_count }} Valid)
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.teams.recap') }}" class="flex items-center justify-between gap-4">
            
            {{-- Left Group: Search & Filters --}}
            <div class="flex gap-3 items-center flex-1">
                {{-- Search --}}
                <div class="relative min-w-[240px]">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari tim, kode, ketua, email..."
                           class="w-full bg-white border border-gray-300 text-gray-900 rounded-xl !pl-10 !pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow" />
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute !left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Event Select --}}
                @if($allEvents->count() > 1)
                    <select name="event_id" onchange="this.form.submit()" class="px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 outline-none w-auto">
                        @foreach($allEvents as $ev)
                            <option value="{{ $ev->id }}" {{ $selectedEventId == $ev->id ? 'selected' : '' }}>
                                Event: {{ $ev->name ?? 'Event #'.$ev->id }} {{ $ev->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                @endif

                {{-- Competition Select --}}
                <select name="competition_id" onchange="this.form.submit()" class="px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 outline-none w-auto">
                    <option value="ALL">Semua Lomba</option>
                    @foreach($competitions as $c)
                        <option value="{{ $c->id }}" {{ $competitionId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Payment Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 outline-none w-auto">
                    <option value="ALL" {{ $status === 'ALL' ? 'selected' : '' }}>Semua Status Payment</option>
                    <option value="VALID" {{ $status === 'VALID' ? 'selected' : '' }}>Valid (Terverifikasi)</option>
                    <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>Pending / Belum Upload</option>
                    <option value="INVALID" {{ $status === 'INVALID' ? 'selected' : '' }}>Invalid (Ditolak)</option>
                </select>

                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 font-medium rounded-xl text-sm px-4 py-2 transition-colors">
                    Filter
                </button>
                @if($search || $status !== 'ALL' || $competitionId !== 'ALL')
                    <a href="{{ route('admin.teams.recap', ['event_id' => $selectedEventId]) }}" class="text-sm text-red-500 hover:text-red-700 hover:underline font-medium ml-1">
                        Reset
                    </a>
                @endif
            </div>


        </form>
    </div>

    {{-- TEAMS DATATABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden text-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider" style="width: 50px">#</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tim & Kode</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kompetisi</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ketua Tim</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Institusi</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status Submission</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Daftar</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                
                @forelse($teams as $index => $team)
                    @php
                        $hasSubmission = !empty($team->submission) || !empty($team->submission_file_name);
                        
                        $badgeBg = $hasSubmission ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        $statusLabel = $hasSubmission ? 'Sudah Submit' : 'Belum Submit';
                    @endphp
                    
                    {{-- MAIN ROW --}}
                    <tbody class="border-b border-gray-200 last:border-b-0 bg-white">
                        
                        {{-- MAIN ROW --}}
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 align-middle text-sm font-medium text-gray-500">
                                {{ $teams->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <p class="font-semibold text-gray-900">{{ $team->name ?? 'Tanpa Nama' }}</p>
                                <p class="inline-block text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded mt-1">Code: {{ $team->code ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $team->competition->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <p class="font-semibold text-gray-900">{{ $team->leader->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $team->leader->email ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-4 align-middle text-sm font-medium text-gray-700">
                                {{ $team->leader->participant->institution ?? '-' }}
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $team->members_count + 1 }} Orang
                                </span>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeBg }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-4 px-4 align-middle text-xs font-medium text-gray-500">
                                {{ $team->created_at ? $team->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4 align-middle text-right">
                                <a href="{{ route('admin.teams.recap.show', $team->id) }}"
                                   class="text-sm font-medium hover:underline" style="color: var(--accent); text-decoration: none;">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center py-16 bg-white">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-base font-bold text-gray-600">Tidak ada data tim ditemukan</p>
                                    <p class="text-sm mt-1 text-gray-400">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>

        @if($teams->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $teams->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>

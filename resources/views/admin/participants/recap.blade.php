<x-admin-layout title="Recap Individu" subtitle="Rekapitulasi data individu (Ketua & Anggota)">

    {{-- STATS CARDS - LIGHT MODE --}}
    <div class="grid gap-5 mb-8" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))">

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

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.participants.recap') }}" class="flex items-center justify-between gap-4">
            
            {{-- Left Group: Search & Filters --}}
            <div class="flex gap-3 items-center flex-1">
                {{-- Search --}}
                <div class="relative min-w-[240px]">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama, email, no HP, atau institusi..."
                           class="w-full bg-white border border-gray-300 text-gray-900 rounded-xl !pl-10 !pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow" />
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute !left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 font-medium rounded-xl text-sm px-4 py-2 transition-colors">
                    Filter
                </button>
                @if($search)
                    <a href="{{ route('admin.participants.recap') }}" class="text-sm text-red-500 hover:text-red-700 hover:underline font-medium ml-1">
                        Reset
                    </a>
                @endif
            </div>

            {{-- Right Group: Export Controls --}}
            <div class="flex gap-3 items-center">
                <a href="{{ route('admin.teams.recap.export', ['export_type' => 'participants']) }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-medium bg-white text-gray-900 border-gray-300 hover:bg-gray-50 shadow-sm transition-colors" title="Export CSV Data Individu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Export Data Individu
                </a>
            </div>
        </form>
    </div>

    {{-- PARTICIPANTS DATATABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden text-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider" style="width: 50px">#</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Institusi & Kelas</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tim & Lomba</th>
                        <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                
                @forelse($participants as $index => $participant)
                    
                    <tbody class="border-b border-gray-200 last:border-b-0 bg-white">
                        
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 align-middle text-sm font-medium text-gray-500">
                                {{ $participants->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <div class="flex items-center gap-3">
                                    @if($participant->avatar)
                                        <img src="/storage/{{ $participant->avatar }}" class="w-8 h-8 rounded-full border border-gray-200 object-cover flex-shrink-0" alt="{{ $participant->user->name ?? '—' }}">
                                    @else
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-indigo-50 text-indigo-700 flex-shrink-0">
                                            {{ strtoupper(substr($participant->user->name ?? 'A', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $participant->user->name ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <p class="text-sm font-medium text-gray-900">{{ $participant->user->email ?? '-' }}</p>
                                <p class="text-xs font-medium text-gray-500 mt-0.5">{{ $participant->user->phone ?? 'No HP -' }}</p>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <p class="text-sm font-bold text-gray-800">{{ $participant->institution ?? '-' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $participant->grade ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                @php
                                    $allTeams = $participant->user ? $participant->user->teams->merge($participant->user->asMembers) : collect();
                                @endphp
                                @if($allTeams->isNotEmpty())
                                    <div class="flex flex-col gap-1">
                                        @foreach($allTeams as $team)
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-gray-50 text-gray-700 text-xs border border-gray-200 font-medium">
                                                {{ $team->name }} ({{ $team->competition->name ?? '?' }})
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada tim</span>
                                @endif
                            </td>
                            
                            <td class="py-4 px-4 align-middle text-right">
                                <a href="{{ route('admin.participants.recap.show', $participant->user_id) }}"
                                   class="text-sm font-medium hover:underline" style="color: var(--accent); text-decoration: none;">
                                    Detail →
                                </a>
                            </td>
                        </tr>

                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-16 bg-white">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-base font-bold text-gray-600">Tidak ada data individu ditemukan</p>
                                    <p class="text-sm mt-1 text-gray-400">Coba sesuaikan kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>

        @if($participants->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $participants->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>

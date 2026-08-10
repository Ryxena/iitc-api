<x-admin-layout title="Manajemen Juara" subtitle="Tentukan juara untuk setiap kompetisi pada event aktif">

    @if(!$activeEvent)
        <div class="card py-20 text-center">
            <span class="badge badge-invalid">Tidak ada event aktif</span>
            <p class="text-sm text-muted mt-3">Tidak dapat mengelola juara karena tidak ada event yang sedang aktif.</p>
        </div>
    @else
        <div class="mb-6">
            <span class="badge badge-valid">{{ $activeEvent->name }}</span>
        </div>

        @if($competitions->isEmpty())
            <div class="card py-20 text-center">
                <p class="text-sm text-muted">Belum ada kompetisi untuk event aktif ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($competitions as $comp)
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-main mb-4">{{ $comp->name }} <span class="text-xs text-muted font-normal ml-2">({{ $comp->teams->count() }} Tim VALID)</span></h3>
                        
                        <div class="overflow-x-auto">
                            <table style="border: none; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="border-right: none; width: 150px;">Kode Tim</th>
                                        <th style="border-right: none;">Nama Tim</th>
                                        <th style="border-right: none;">Ketua</th>
                                        <th style="border-right: none;">Status Juara Saat Ini</th>
                                        <th style="border-right: none; text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($comp->teams as $team)
                                        <tr>
                                            <td style="border-right: none;">{{ $team->code }}</td>
                                            <td style="border-right: none;"><strong>{{ $team->name }}</strong></td>
                                            <td style="border-right: none;">
                                                {{ $team->leader->name ?? '-' }}<br>
                                                <small class="text-muted">{{ $team->leader->participant->institution ?? '-' }}</small>
                                            </td>
                                            <td style="border-right: none;">
                                                @if($team->winner)
                                                    <span class="badge badge-valid">Peringkat: {{ $team->winner->rank }} - {{ $team->winner->award_title }}</span>
                                                @else
                                                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted);">Belum Juara</span>
                                                @endif
                                            </td>
                                            <td style="border-right: none; text-align: right;">
                                                <button type="button" class="btn-primary" style="padding: 5px 12px; font-size: 12px;"
                                                        onclick="openWinnerModal({{ json_encode([
                                                            'team_id' => $team->id,
                                                            'team_name' => $team->name,
                                                            'rank' => $team->winner->rank ?? '',
                                                            'award_title' => $team->winner->award_title ?? ''
                                                        ]) }})">
                                                    @if($team->winner) Edit Juara @else Set Juara @endif
                                                </button>
                                                
                                                @if($team->winner)
                                                <form method="POST" action="{{ route('admin.winners.destroy', $team->id) }}" style="display:inline-block;"
                                                      onsubmit="return confirm('Hapus status juara untuk tim ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-danger" style="padding: 5px 12px; font-size: 12px;">Hapus</button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted" style="border-right: none;">Tidak ada tim yang VALID di kompetisi ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- MODAL --}}
    <div id="modal-winner" class="modal-backdrop" style="display:none; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,.55); backdrop-filter:blur(4px); overflow-y:auto;">
        <div class="modal-panel" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; max-width: 500px; margin: 60px auto; padding: 32px;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-white text-lg">Set Juara: <span id="modal-team-name"></span></h2>
                <button type="button" onclick="closeModal('modal-winner')" style="color: var(--text-muted); background:none; border:none; cursor:pointer; font-size:20px;">✕</button>
            </div>

            <form id="form-winner" method="POST" action="{{ route('admin.winners.store') }}">
                @csrf
                <input type="hidden" name="team_id" id="input-team-id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-main mb-1">Peringkat (Angka)</label>
                    <input type="number" name="rank" id="input-rank" class="form-input w-full" placeholder="1, 2, 3..." required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-main mb-1">Gelar (Award Title)</label>
                    <input type="text" name="award_title" id="input-award-title" class="form-input w-full" placeholder="Juara 1 / Harapan 1" required>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('modal-winner')" class="btn-ghost">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openWinnerModal(data) {
            document.getElementById('modal-team-name').innerText = data.team_name;
            document.getElementById('input-team-id').value = data.team_id;
            document.getElementById('input-rank').value = data.rank;
            document.getElementById('input-award-title').value = data.award_title;
            
            document.getElementById('modal-winner').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
            document.body.style.overflow = '';
        }
    </script>
</x-admin-layout>

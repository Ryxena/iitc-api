<x-admin-layout title="Nomor Sertifikat" subtitle="Kelola nomor surat sertifikat finalis (D) dan partisipan (F)">

    @if(!$event)
        <div class="card py-20 text-center">
            <span class="badge badge-invalid">Tidak ada event</span>
            <p class="text-sm text-muted mt-3">Belum ada event yang dapat dikelola nomornya.</p>
        </div>
    @else

        {{-- STATS --}}
        <div class="grid gap-4 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr))">
            <div class="card flex items-center gap-4" style="padding: 16px 20px">
                <div>
                    <p class="text-xs font-medium text-muted">Total Orang</p>
                    <p class="text-xl font-bold text-main">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-4" style="padding: 16px 20px">
                <div>
                    <p class="text-xs font-medium text-muted">Sudah Bernomor</p>
                    <p class="text-xl font-bold text-main">{{ number_format($stats['assigned']) }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-4" style="padding: 16px 20px">
                <div>
                    <p class="text-xs font-medium text-muted">Finalis (D)</p>
                    <p class="text-xl font-bold text-main">{{ number_format($stats['finalist']) }} <span class="text-xs font-normal text-muted">· maks {{ sprintf('%03d', $maxD) }}</span></p>
                </div>
            </div>
            <div class="card flex items-center gap-4" style="padding: 16px 20px">
                <div>
                    <p class="text-xs font-medium text-muted">Partisipan (F)</p>
                    <p class="text-xl font-bold text-main">{{ number_format($stats['participant']) }} <span class="text-xs font-normal text-muted">· maks {{ sprintf('%03d', $maxF) }}</span></p>
                </div>
            </div>
        </div>

        {{-- PENGATURAN --}}
        <div class="card p-6 mb-6">
            <h3 class="text-lg font-bold text-main mb-1">Pengaturan Penomoran</h3>
            <p class="text-sm text-muted mb-4">Contoh hasil: <code>055/D/{{ $suffix }}</code></p>

            <form method="POST" action="{{ route('admin.certificates.settings') }}" class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr))">
                @csrf
                @method('PUT')
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div>
                    <label for="cert-suffix" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
                        Suffix Nomor <span style="color:#DC2626">*</span>
                    </label>
                    <input type="text" id="cert-suffix" name="suffix" required class="form-input" value="{{ $suffix }}">
                    <p class="text-xs text-muted mt-1">Sisa nomor setelah segmen angka &amp; D/F.</p>
                </div>

                <div>
                    <label for="cert-start-d" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
                        Angka Awal Finalis (D) <span style="color:#DC2626">*</span>
                    </label>
                    <input type="number" id="cert-start-d" name="start_finalist" required min="1" max="999999" class="form-input" value="{{ $startD }}">
                </div>

                <div>
                    <label for="cert-start-f" style="display:block; font-size:13px; font-weight:500; color:var(--text-muted); margin-bottom:6px;">
                        Angka Awal Partisipan (F) <span style="color:#DC2626">*</span>
                    </label>
                    <input type="number" id="cert-start-f" name="start_participant" required min="1" max="999999" class="form-input" value="{{ $startF }}">
                </div>

                <div style="align-self: end;">
                    <button type="submit" class="btn-primary" style="width:100%;">Simpan Pengaturan</button>
                </div>
            </form>
        </div>

        {{-- AKSI --}}
        <div class="card p-6 mb-6 flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('admin.certificates.generate') }}">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <button type="submit" class="btn-primary">Generate / Update Nomor</button>
            </form>

            <a href="{{ route('admin.certificates.export') }}" class="btn-ghost">
                Export CSV
            </a>

            <form method="POST" action="{{ route('admin.certificates.renumber') }}"
                  onsubmit="return confirm('Urutkan ulang SEMUA nomor finalis (D) dari angka awal? Nomor lama &amp; override manual akan diganti.');">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="type" value="D">
                <button type="submit" class="btn-danger">Nomor Ulang Finalis (D)</button>
            </form>

            <form method="POST" action="{{ route('admin.certificates.renumber') }}"
                  onsubmit="return confirm('Urutkan ulang SEMUA nomor partisipan (F) dari angka awal? Nomor lama &amp; override manual akan diganti.');">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="type" value="F">
                <button type="submit" class="btn-danger">Nomor Ulang Partisipan (F)</button>
            </form>

            <p class="text-xs text-muted">Generate mengisi roster tim VALID (finalis = tim juara, partisipan = sisanya) lalu menomori yang belum punya nomor. Nomor yang sudah ada / hasil override tidak berubah. Tombol <strong>Nomor Ulang</strong> menata ulang seluruh nomor satu tipe mulai dari angka awal saat ini — gunakan setelah testing atau jika ada nomor bolong.</p>
        </div>

        {{-- TABEL --}}
        <div class="card p-0 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b" style="border-color: var(--border)">
                <p class="text-sm font-medium text-muted">
                    Nomor sertifikat <strong class="text-main">{{ $event->name }}</strong>
                </p>

                <form method="GET" class="flex items-center gap-2">
                    <select name="event_id" onchange="this.form.submit()" class="form-input" style="width:auto; padding:6px 10px; font-size:13px;">
                        @foreach($events as $e)
                            <option value="{{ $e->id }}" @selected($e->id === $event->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table style="border: none; width: 100%;">
                    <thead>
                        <tr>
                            <th style="border-right: none; width: 60px;">Tipe</th>
                            <th style="border-right: none;">Nama</th>
                            <th style="border-right: none;">Tim</th>
                            <th style="border-right: none;">Kompetisi</th>
                            <th style="border-right: none;">Nomor Sertifikat</th>
                            <th style="border-right: none; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td style="border-right: none;">
                                    <span class="badge {{ $row->type === 'D' ? 'badge-valid' : 'badge-pending' }}">{{ $row->type }}</span>
                                </td>
                                <td style="border-right: none;">{{ $row->user->name ?? '-' }}</td>
                                <td style="border-right: none;">
                                    {{ $row->team->name ?? '-' }}
                                    @if($row->team?->winner)
                                        <span class="badge badge-valid" style="margin-left:6px;">Juara {{ $row->team->winner->rank }}</span>
                                    @endif
                                </td>
                                <td style="border-right: none;">{{ $row->team->competition->name ?? '-' }}</td>
                                <td style="border-right: none; font-family: monospace;">
                                    @if($row->sequence !== null)
                                        {{ sprintf('%03d/%s/%s', $row->sequence, $row->type, $suffix) }}
                                    @else
                                        <span class="badge badge-invalid">Belum bernomor</span>
                                    @endif
                                </td>
                                <td style="border-right: none; text-align: right;">
                                    <form method="POST" action="{{ route('admin.certificates.update-number', $row->id) }}" class="inline-form flex items-center gap-2 justify-end">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="sequence" min="1" max="999999" required class="form-input"
                                               style="width: 110px; padding: 5px 8px; font-size: 13px;"
                                               value="{{ $row->sequence }}">
                                        <button type="submit" class="btn-primary" style="padding: 5px 12px; font-size: 12px;">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-muted text-sm">
                                    Belum ada data. Klik <strong>Generate / Update Nomor</strong> untuk menyusun roster dari tim berbayar VALID.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-admin-layout>

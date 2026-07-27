<x-admin-layout title="Dashboard" subtitle="Ringkasan data event aktif">

    {{-- ============================================================ --}}
    {{-- STATS CARDS --}}
    {{-- ============================================================ --}}
    <div class="grid gap-5 mb-8" style="grid-template-columns: repeat(auto-fit, minmax(220px,1fr))">

        {{-- Total Tim --}}
        <div class="card flex items-center gap-4">
            <div class="stat-icon stat-icon-blue flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted">Total Tim</p>
                <p class="text-2xl font-bold text-black mt-1">{{ number_format($totalTeams) }}</p>
                <p class="text-xs font-medium text-muted mt-1">Tim terdaftar aktif</p>
            </div>
        </div>

        {{-- Total Anggota --}}
        <div class="card flex items-center gap-4">
            <div class="stat-icon stat-icon-amber flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted">Total Peserta Lomba</p>
                <p class="text-2xl font-bold text-black mt-1">{{ number_format($totalMembers) }}</p>
                <p class="text-xs font-medium text-muted mt-1">Anggota aktif di semua tim</p>
            </div>
        </div>

        {{-- Seminar --}}
        <div class="card flex items-center gap-4">
            <div class="stat-icon stat-icon-green flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted">Peserta Seminar</p>
                <p class="text-2xl font-bold text-black mt-1">{{ number_format($seminarParticipants) }}</p>
                <p class="text-xs font-medium text-muted mt-1">Payment seminar VALID</p>
            </div>
        </div>

        {{-- Pending Payment --}}
        <div class="card flex items-center gap-4">
            <div class="stat-icon stat-icon-rose flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-muted">Payment Menunggu</p>
                <p class="text-2xl font-bold text-black mt-1">{{ number_format($pendingCount) }}</p>
                <p class="text-xs font-medium mt-1">
                    <span style="color:var(--accent)">{{ $validCount }} Valid</span>
                    &nbsp;·&nbsp;
                    <span style="color:#DC2626">{{ $invalidCount }} Ditolak</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- COMPETITION BREAKDOWN + CHART --}}
    {{-- ============================================================ --}}
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: 24px;">

        {{-- Per-kompetisi table --}}
        <div class="card p-0 overflow-hidden">
            <div class="p-6 flex items-center justify-between border-b" style="border-color: var(--border);">
                <h2 class="font-semibold text-main">Pendaftaran per Kompetisi</h2>
                @if($activeEvent)
                    <span class="badge badge-valid">{{ $activeEvent->name }}</span>
                @else
                    <span class="badge badge-invalid">Tidak ada event aktif</span>
                @endif
            </div>

            @if($competitions->isEmpty())
                <p class="text-sm py-8 text-center text-muted">Belum ada kompetisi untuk event aktif.</p>
            @else
                <table style="border: none;">
                    <thead>
                        <tr>
                            <th style="border-right: none;">Kompetisi</th>
                            <th class="text-right" style="border-right: none;">Tim</th>
                            <th class="text-right" style="border-right: none;">Sudah Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($competitions as $comp)
                            <tr>
                                <td style="border-right: none;">
                                    <p class="font-semibold text-main">{{ $comp->name }}</p>
                                    <p class="text-xs font-medium text-muted mt-1">{{ $comp->slug }}</p>
                                </td>
                                <td class="text-right font-bold text-main" style="border-right: none;">{{ $comp->teams_count }}</td>
                                <td class="text-right" style="border-right: none;">
                                    <span class="badge badge-pending">{{ $comp->teams_with_payment_count }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="p-6 bg-gray-50 flex justify-end gap-3" style="background: #F9FAFB;">
                <a href="{{ route('admin.export.teams') }}" class="btn-ghost text-sm">
                    Export CSV
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn-primary text-sm">
                    Lihat Semua Payment
                </a>
            </div>
        </div>

        {{-- Chart --}}
        <div class="card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-main">Pendaftar per Hari</h2>
                <span class="text-sm text-muted">30 hari terakhir</span>
            </div>
            <div style="position: relative; height: 280px; flex: 1;">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        const labels   = @json($labels);
        const data     = @json($chartData);
        const accent   = '#2F2FE4';

        const ctx = document.getElementById('registrationChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(47, 47, 228, 0.15)');
        gradient.addColorStop(1, 'rgba(47, 47, 228, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Tim Daftar',
                    data,
                    borderColor: accent,
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: accent,
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#FFFFFF',
                        borderColor: '#E5E7EB',
                        borderWidth: 1,
                        titleColor: '#6B7280',
                        titleFont: { family: "'Inter', sans-serif", weight: 'normal', size: 13 },
                        bodyColor: '#111827',
                        bodyFont: { family: "'Inter', sans-serif", weight: '600', size: 14 },
                        padding: 12,
                        cornerRadius: 8,
                        boxPadding: 4,
                        callbacks: {
                            title: (items) => items[0].label,
                            label: (item) => ` ${item.raw} tim mendaftar`,
                        }
                    }
                },
                scales: {
                    x: {
                        grid:  { display: false },
                        ticks: { color: '#6B7280', font: { size: 12, family: "'Inter', sans-serif" }, maxTicksLimit: 6 },
                        border: { display: false }
                    },
                    y: {
                        grid:  { color: '#F3F4F6' },
                        ticks: { color: '#6B7280', font: { size: 12, family: "'Inter', sans-serif" }, stepSize: 1, precision: 0 },
                        beginAtZero: true,
                        border: { display: false }
                    }
                }
            }
        });
    })();
    </script>

</x-admin-layout>

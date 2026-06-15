<div>
    <!-- Period Selector -->
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <select wire:model.live="selectedMonth" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
            @endfor
        </select>
        <select wire:model.live="selectedYear" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    <!-- Summary Cards -->
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Income -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-6 text-white shadow-lg shadow-emerald-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-500/30">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-2 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="mb-1 flex items-center gap-2">
                    <svg class="h-5 w-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    <span class="text-sm font-medium text-emerald-100">Pemasukan</span>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($this->summary['income'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Expense -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 p-6 text-white shadow-lg shadow-rose-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-rose-500/30">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-2 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="mb-1 flex items-center gap-2">
                    <svg class="h-5 w-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    <span class="text-sm font-medium text-rose-100">Pengeluaran</span>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($this->summary['expense'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Balance -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-6 text-white shadow-lg shadow-indigo-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/30">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-2 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="mb-1 flex items-center gap-2">
                    <svg class="h-5 w-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    <span class="text-sm font-medium text-indigo-100">Saldo Bersih</span>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($this->summary['balance'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Bills -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-lg shadow-amber-500/20 transition-all duration-300 hover:shadow-xl hover:shadow-amber-500/30">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-2 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
            <div class="relative">
                <div class="mb-1 flex items-center gap-2">
                    <svg class="h-5 w-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium text-amber-100">Tagihan</span>
                </div>
                <p class="text-2xl font-bold">{{ $this->summary['upcoming_bills'] }} <span class="text-base font-normal opacity-80">mendatang</span></p>
                @if ($this->summary['overdue_bills'] > 0)
                    <p class="mt-1 text-sm text-amber-100">⚠️ {{ $this->summary['overdue_bills'] }} jatuh tempo</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="mb-8 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- Bar Chart: Income vs Expense -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm xl:col-span-2">
            <h3 class="mb-4 text-base font-semibold text-slate-800">Pemasukan vs Pengeluaran (6 Bulan)</h3>
            <div id="barChart" wire:ignore style="min-height: 320px;"></div>
        </div>

        <!-- Donut Chart: Expense by Category -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-base font-semibold text-slate-800">Pengeluaran per Kategori</h3>
            @if (count($this->donutChartData['series']) > 0)
                <div id="donutChart" wire:ignore style="min-height: 320px;"></div>
            @else
                <div class="flex h-64 items-center justify-center text-sm text-slate-400">
                    <p>Belum ada data pengeluaran</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <!-- Recent Transactions -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">Lihat semua →</a>
            </div>
            <div class="space-y-3">
                @forelse ($this->recentTransactions as $tx)
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 transition hover:bg-slate-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl" style="background-color: {{ $tx->category->color }}15;">
                            <div class="h-3 w-3 rounded-full" style="background-color: {{ $tx->category->color }};"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-medium text-slate-700">{{ $tx->description ?? $tx->category->name }}</p>
                            <p class="text-xs text-slate-400">{{ $tx->category->name }} · {{ $tx->transaction_date->translatedFormat('d M Y') }}</p>
                        </div>
                        <span class="text-sm font-semibold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}{{ $tx->formatted_amount }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">Belum ada transaksi</div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Bills -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">Tagihan Mendatang</h3>
                <a href="{{ route('bills.index') }}" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-500">Lihat semua →</a>
            </div>
            <div class="space-y-3">
                @forelse ($this->upcomingBills as $bill)
                    <div class="flex items-center gap-3 rounded-xl p-3 transition
                        {{ $bill->is_overdue ? 'bg-red-50 hover:bg-red-100' : 'bg-slate-50 hover:bg-slate-100' }}">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $bill->is_overdue ? 'bg-red-100' : 'bg-amber-100' }}">
                            @if ($bill->is_overdue)
                                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            @else
                                <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-medium text-slate-700">{{ $bill->title }}</p>
                            <p class="text-xs {{ $bill->is_overdue ? 'text-red-500 font-medium' : 'text-slate-400' }}">
                                {{ $bill->is_overdue ? 'Jatuh tempo ' : '' }}{{ $bill->due_date->translatedFormat('d M Y') }}
                            </p>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">{{ $bill->formatted_amount }}</span>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">Tidak ada tagihan mendatang</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Bar Chart
    const barChartData = @json($this->barChartData);
    let barChart = new ApexCharts(document.querySelector("#barChart"), {
        chart: {
            type: 'bar',
            height: 320,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
        },
        plotOptions: {
            bar: { borderRadius: 8, columnWidth: '60%' },
        },
        dataLabels: { enabled: false },
        series: [
            { name: 'Pemasukan', data: barChartData.income },
            { name: 'Pengeluaran', data: barChartData.expense },
        ],
        xaxis: {
            categories: barChartData.categories,
            labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
        },
        yaxis: {
            labels: {
                style: { colors: '#94a3b8', fontSize: '12px' },
                formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
            },
        },
        colors: ['#22c55e', '#ef4444'],
        fill: {
            type: 'gradient',
            gradient: { shade: 'light', type: 'vertical', shadeIntensity: 0.2, opacityFrom: 1, opacityTo: 0.9 },
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: {
            y: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) },
        },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '13px', fontWeight: 500 },
    });
    barChart.render();

    // Donut Chart
    const donutChartData = @json($this->donutChartData);
    let donutChart = null;
    if (donutChartData.series.length > 0) {
        donutChart = new ApexCharts(document.querySelector("#donutChart"), {
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif',
            },
            series: donutChartData.series,
            labels: donutChartData.labels,
            colors: donutChartData.colors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '14px',
                                fontWeight: 600,
                                formatter: (w) => 'Rp ' + new Intl.NumberFormat('id-ID').format(w.globals.seriesTotals.reduce((a, b) => a + b, 0)),
                            },
                        },
                    },
                },
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '12px', fontWeight: 500 },
            tooltip: {
                y: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) },
            },
        });
        donutChart.render();
    }

    // Listen for chart data updates
    $wire.on('chartDataUpdated', (data) => {
        const payload = data[0] || data;
        if (barChart && payload.barChart) {
            barChart.updateOptions({
                xaxis: { categories: payload.barChart.categories },
            });
            barChart.updateSeries([
                { name: 'Pemasukan', data: payload.barChart.income },
                { name: 'Pengeluaran', data: payload.barChart.expense },
            ]);
        }
        if (payload.donutChart && payload.donutChart.series.length > 0) {
            if (donutChart) {
                donutChart.updateOptions({
                    labels: payload.donutChart.labels,
                    colors: payload.donutChart.colors,
                });
                donutChart.updateSeries(payload.donutChart.series);
            }
        }
    });
</script>
@endscript

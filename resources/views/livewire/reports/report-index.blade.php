<div>
    <!-- Filters -->
    <div class="mb-6 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-500">Dari Tanggal</label>
                <input wire:model.live="dateFrom" type="date" class="rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-500">Sampai Tanggal</label>
                <input wire:model.live="dateTo" type="date" class="rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-500">Tipe</label>
                <select wire:model.live="filterType" class="rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="all">Semua</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-500">Kategori</label>
                <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="resetFilters" class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-500 transition hover:bg-slate-100">Reset</button>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('reports.export.excel', ['from' => $dateFrom, 'to' => $dateTo, 'type' => $filterType, 'category' => $filterCategory]) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('reports.export.pdf', ['from' => $dateFrom, 'to' => $dateTo, 'type' => $filterType, 'category' => $filterCategory]) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs font-medium text-emerald-600">Total Pemasukan</p>
            <p class="mt-1 text-lg font-bold text-emerald-700">Rp {{ number_format($this->summary['income'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
            <p class="text-xs font-medium text-rose-600">Total Pengeluaran</p>
            <p class="mt-1 text-lg font-bold text-rose-700">Rp {{ number_format($this->summary['expense'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
            <p class="text-xs font-medium text-indigo-600">Saldo Bersih</p>
            <p class="mt-1 text-lg font-bold {{ $this->summary['balance'] >= 0 ? 'text-indigo-700' : 'text-red-700' }}">Rp {{ number_format($this->summary['balance'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-medium text-slate-600">Jumlah Transaksi</p>
            <p class="mt-1 text-lg font-bold text-slate-700">{{ $this->summary['count'] }}</p>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if ($this->categoryBreakdown->count() > 0)
    <div class="mb-6 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
        <h3 class="mb-4 text-sm font-semibold text-slate-800">Ringkasan per Kategori</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->categoryBreakdown as $cat)
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                    <div class="h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $cat->color }};"></div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate text-sm font-medium text-slate-700">{{ $cat->name }}</p>
                        <p class="text-xs text-slate-400">{{ $cat->count }} transaksi · {{ $cat->type === 'income' ? 'Masuk' : 'Keluar' }}</p>
                    </div>
                    <p class="text-sm font-semibold {{ $cat->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                        Rp {{ number_format($cat->total, 0, ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Transactions Table -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Deskripsi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Tipe</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $tx)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $tx->description ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium" style="background-color: {{ $tx->category->color }}15; color: {{ $tx->category->color }};">
                                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $tx->category->color }};"></span>
                                    {{ $tx->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}{{ $tx->formatted_amount }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400">Tidak ada transaksi pada periode ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>

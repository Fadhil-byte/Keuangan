<div>
    <!-- Header & Filters -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari transaksi..."
                       class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all hover:shadow-xl hover:shadow-indigo-500/30 active:scale-[0.98]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Transaksi
            </button>
        </div>

        <!-- Filter Row -->
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterType" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="all">Semua Tipe</option>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
            <select wire:model.live="filterCategory" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->type === 'income' ? '↑' : '↓' }})</option>
                @endforeach
            </select>
            <input wire:model.live="dateFrom" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Dari">
            <input wire:model.live="dateTo" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Sampai">
            @if ($search || $filterType !== 'all' || $filterCategory || $dateFrom || $dateTo)
                <button wire:click="resetFilters" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-500 transition hover:bg-slate-100">Reset</button>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
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
    </div>

    <!-- Transaction Table -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sort('transaction_date')" class="flex items-center gap-1 font-semibold text-slate-600 hover:text-slate-800">
                                Tanggal
                                @if ($sortBy === 'transaction_date')
                                    <svg class="h-3 w-3 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Deskripsi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Tipe</th>
                        <th class="px-4 py-3 text-right">
                            <button wire:click="sort('amount')" class="flex items-center gap-1 font-semibold text-slate-600 hover:text-slate-800 ml-auto">
                                Jumlah
                                @if ($sortBy === 'amount')
                                    <svg class="h-3 w-3 {{ $sortDir === 'asc' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $tx)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $tx->transaction_date->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $tx->description ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium" style="background-color: {{ $tx->category->color }}15; color: {{ $tx->category->color }};">
                                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $tx->category->color }};"></span>
                                    {{ $tx->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $tx->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $tx->type === 'income' ? '↑ Masuk' : '↓ Keluar' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}{{ $tx->formatted_amount }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $tx->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $tx->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                Belum ada transaksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <h3 class="mb-5 text-lg font-bold text-slate-800">{{ $editingId ? 'Edit Transaksi' : 'Tambah Transaksi' }}</h3>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Tipe</label>
                        <select wire:model.live="type" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Kategori</label>
                        <select wire:model="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">Pilih kategori</option>
                            @foreach ($type === 'income' ? $incomeCategories : $expenseCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Jumlah (Rp)</label>
                        <input wire:model="amount" type="number" step="1" min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="0">
                        @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Tanggal</label>
                        <input wire:model="transaction_date" type="date" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        @error('transaction_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Deskripsi (opsional)</label>
                    <input wire:model="description" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Keterangan transaksi">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:shadow-xl active:scale-[0.98]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="mb-2 text-lg font-bold text-slate-800">Hapus Transaksi?</h3>
            <p class="mb-5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button wire:click="delete" class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.98]">Hapus</button>
            </div>
        </div>
    </div>
    @endif
</div>

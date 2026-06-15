<div>
    <!-- Header with search and add button -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-3">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kategori..."
                       class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <select wire:model.live="filterType" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="all">Semua Tipe</option>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all hover:shadow-xl hover:shadow-indigo-500/30 active:scale-[0.98]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </button>
    </div>

    @php
        $colorOptions = [
            '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
            '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6', '#6366f1',
            '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#64748b',
        ];
    @endphp

    <!-- Categories Grid -->
    <div class="space-y-8">
        @if ($filterType === 'all' || $filterType === 'income')
        <!-- Income Categories -->
        <div>
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-emerald-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                Pemasukan ({{ $incomeCategories->count() }})
            </h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($incomeCategories as $category)
                    <div class="group flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all hover:border-slate-300 hover:shadow-md">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background-color: {{ $category->color }}20;">
                            <div class="h-4 w-4 rounded-full" style="background-color: {{ $category->color }};"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-medium text-slate-700">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">{{ $category->transactions()->count() }} transaksi</p>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 transition group-hover:opacity-100">
                            <button wire:click="openEditModal({{ $category->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="confirmDelete({{ $category->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-sm text-slate-400">Belum ada kategori pemasukan</div>
                @endforelse
            </div>
        </div>
        @endif

        @if ($filterType === 'all' || $filterType === 'expense')
        <!-- Expense Categories -->
        <div>
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-rose-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                Pengeluaran ({{ $expenseCategories->count() }})
            </h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($expenseCategories as $category)
                    <div class="group flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all hover:border-slate-300 hover:shadow-md">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background-color: {{ $category->color }}20;">
                            <div class="h-4 w-4 rounded-full" style="background-color: {{ $category->color }};"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-medium text-slate-700">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">{{ $category->transactions()->count() }} transaksi</p>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 transition group-hover:opacity-100">
                            <button wire:click="openEditModal({{ $category->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="confirmDelete({{ $category->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-sm text-slate-400">Belum ada kategori pengeluaran</div>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$el.querySelector('input[name=name]')?.focus()">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <h3 class="mb-5 text-lg font-bold text-slate-800">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Nama</label>
                    <input wire:model="name" name="name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Nama kategori">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Tipe</label>
                    <select wire:model="type" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Warna</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($colorOptions as $c)
                            <button type="button" wire:click="$set('color', '{{ $c }}')"
                                    class="h-8 w-8 rounded-full border-2 transition hover:scale-110 {{ $color === $c ? 'border-slate-800 ring-2 ring-offset-2' : 'border-transparent' }}"
                                    style="background-color: {{ $c }};">
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:shadow-xl active:scale-[0.98]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 class="mb-2 text-lg font-bold text-slate-800">Hapus Kategori?</h3>
            <p class="mb-5 text-sm text-slate-500">Semua transaksi dan tagihan dalam kategori ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button wire:click="delete" class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.98]">Hapus</button>
            </div>
        </div>
    </div>
    @endif
</div>

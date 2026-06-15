<div>
    <!-- Stats Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
            <p class="text-xs font-medium text-rose-600">Total Belum Lunas</p>
            <p class="mt-1 text-lg font-bold text-rose-700">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-medium text-amber-600">Jatuh Tempo 7 Hari</p>
            <p class="mt-1 text-lg font-bold text-amber-700">{{ $upcomingCount }} tagihan</p>
        </div>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="text-xs font-medium text-red-600">Terlambat</p>
            <p class="mt-1 text-lg font-bold text-red-700">{{ $overdueCount }} tagihan</p>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-3">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari tagihan..."
                       class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option value="all">Semua Status</option>
                <option value="unpaid">Belum Lunas</option>
                <option value="paid">Lunas</option>
                <option value="overdue">Terlambat</option>
            </select>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all hover:shadow-xl hover:shadow-indigo-500/30 active:scale-[0.98]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tagihan
        </button>
    </div>

    <!-- Bills List -->
    <div class="space-y-3">
        @forelse ($bills as $bill)
            @php
                $isOverdue = $bill->is_overdue;
                $isPaid = $bill->status === 'paid';
            @endphp
            <div class="group flex items-center gap-4 rounded-2xl border bg-white p-4 shadow-sm transition-all hover:shadow-md
                {{ $isOverdue ? 'border-red-200 bg-red-50/50' : ($isPaid ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200/80') }}">

                <!-- Status Toggle -->
                <button wire:click="toggleStatus({{ $bill->id }})"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition
                        {{ $isPaid ? 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' : ($isOverdue ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-amber-100 text-amber-600 hover:bg-amber-200') }}">
                    @if ($isPaid)
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @elseif ($isOverdue)
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    @else
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </button>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="truncate text-sm font-semibold {{ $isPaid ? 'text-slate-400 line-through' : 'text-slate-700' }}">{{ $bill->title }}</h4>
                        <!-- Status Badge -->
                        <span class="inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $isPaid ? 'bg-emerald-100 text-emerald-700' : ($isOverdue ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $isPaid ? 'Lunas' : ($isOverdue ? 'Terlambat' : 'Belum Lunas') }}
                        </span>
                        @if ($bill->recurrence !== 'none')
                            <span class="inline-flex shrink-0 items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                {{ $bill->recurrence === 'monthly' ? 'Bulanan' : 'Tahunan' }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-xs {{ $isOverdue ? 'text-red-500 font-medium' : 'text-slate-400' }}">
                        Jatuh tempo: {{ $bill->due_date->translatedFormat('d F Y') }}
                        @if ($isOverdue)
                            ({{ $bill->due_date->diffForHumans() }})
                        @endif
                    </p>
                    @if ($bill->description)
                        <p class="mt-1 truncate text-xs text-slate-400">{{ $bill->description }}</p>
                    @endif
                </div>

                <!-- Amount -->
                <div class="text-right">
                    <p class="text-sm font-bold {{ $isPaid ? 'text-slate-400' : 'text-slate-700' }}">{{ $bill->formatted_amount }}</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 opacity-0 transition group-hover:opacity-100">
                    <button wire:click="openEditModal({{ $bill->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="confirmDelete({{ $bill->id }})" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white py-12 text-center text-sm text-slate-400">
                <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Belum ada tagihan
            </div>
        @endforelse
    </div>

    @if ($bills->hasPages())
        <div class="mt-4">{{ $bills->links() }}</div>
    @endif

    <!-- Create/Edit Modal -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <h3 class="mb-5 text-lg font-bold text-slate-800">{{ $editingId ? 'Edit Tagihan' : 'Tambah Tagihan' }}</h3>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Judul Tagihan</label>
                    <input wire:model="title" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Listrik, Internet, dll">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Jumlah (Rp)</label>
                        <input wire:model="amount" type="number" step="1" min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="0">
                        @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Jatuh Tempo</label>
                        <input wire:model="due_date" type="date" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        @error('due_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="unpaid">Belum Lunas</option>
                            <option value="paid">Lunas</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-600">Pengulangan</label>
                        <select wire:model="recurrence" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="none">Tidak berulang</option>
                            <option value="monthly">Bulanan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Kategori (opsional)</label>
                    <select wire:model="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">Tanpa kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-600">Deskripsi (opsional)</label>
                    <textarea wire:model="description" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Catatan tambahan"></textarea>
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
            <h3 class="mb-2 text-lg font-bold text-slate-800">Hapus Tagihan?</h3>
            <p class="mb-5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button wire:click="delete" class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.98]">Hapus</button>
            </div>
        </div>
    </div>
    @endif
</div>

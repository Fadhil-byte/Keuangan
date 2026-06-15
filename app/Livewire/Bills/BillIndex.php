<?php

namespace App\Livewire\Bills;

use App\Models\Bill;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BillIndex extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterStatus = 'all'; // all, paid, unpaid, overdue

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $title = '';
    public ?int $category_id = null;
    public string $amount = '';
    public string $description = '';
    public string $due_date = '';
    public string $status = 'unpaid';
    public string $recurrence = 'none';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:paid,unpaid'],
            'recurrence' => ['required', 'in:none,monthly,yearly'],
        ];
    }

    public function toggleStatus(int $id): void
    {
        $bill = Bill::forUser(Auth::id())->findOrFail($id);
        $bill->update([
            'status' => $bill->status === 'paid' ? 'unpaid' : 'paid',
        ]);

        $statusLabel = $bill->fresh()->status === 'paid' ? 'Lunas' : 'Belum Lunas';
        session()->flash('success', "Status tagihan diubah menjadi {$statusLabel}!");
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->due_date = now()->addDays(7)->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $bill = Bill::forUser(Auth::id())->findOrFail($id);
        $this->editingId = $bill->id;
        $this->title = $bill->title;
        $this->category_id = $bill->category_id;
        $this->amount = (string) $bill->amount;
        $this->description = $bill->description ?? '';
        $this->due_date = $bill->due_date->format('Y-m-d');
        $this->status = $bill->status;
        $this->recurrence = $bill->recurrence;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        // Verify category belongs to user if provided
        if ($this->category_id) {
            Category::forUser(Auth::id())->findOrFail($this->category_id);
        }

        $data = [
            'title' => $this->title,
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'description' => $this->description ?: null,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'recurrence' => $this->recurrence,
        ];

        if ($this->editingId) {
            $bill = Bill::forUser(Auth::id())->findOrFail($this->editingId);
            $bill->update($data);
            session()->flash('success', 'Tagihan berhasil diperbarui!');
        } else {
            Auth::user()->bills()->create($data);
            session()->flash('success', 'Tagihan berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $bill = Bill::forUser(Auth::id())->findOrFail($this->deletingId);
            $bill->delete();
            session()->flash('success', 'Tagihan berhasil dihapus!');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->category_id = null;
        $this->amount = '';
        $this->description = '';
        $this->due_date = '';
        $this->status = 'unpaid';
        $this->recurrence = 'none';
        $this->resetValidation();
    }

    public function render()
    {
        $userId = Auth::id();
        $query = Bill::forUser($userId);

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus === 'paid') {
            $query->paid();
        } elseif ($this->filterStatus === 'unpaid') {
            $query->unpaid()->where('due_date', '>=', now()->toDateString());
        } elseif ($this->filterStatus === 'overdue') {
            $query->overdue();
        }

        $bills = $query->orderBy('due_date', 'asc')->paginate(15);

        $categories = Category::forUser($userId)->expense()->orderBy('name')->get();

        // Stats
        $totalUnpaid = Bill::forUser($userId)->unpaid()->sum('amount');
        $overdueCount = Bill::forUser($userId)->overdue()->count();
        $upcomingCount = Bill::forUser($userId)->upcoming(7)->count();

        return view('livewire.bills.bill-index', [
            'bills' => $bills,
            'categories' => $categories,
            'totalUnpaid' => (float) $totalUnpaid,
            'overdueCount' => $overdueCount,
            'upcomingCount' => $upcomingCount,
        ])->layout('components.layouts.app', ['header' => 'Pengingat Tagihan', 'title' => 'Tagihan']);
    }
}

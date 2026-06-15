<?php

namespace App\Livewire\Transactions;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionIndex extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterType = 'all';
    public ?int $filterCategory = null;
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortBy = 'transaction_date';
    public string $sortDir = 'desc';

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $type = 'expense';
    public ?int $category_id = null;
    public string $amount = '';
    public string $description = '';
    public string $transaction_date = '';

    protected $queryString = [
        'filterType' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense'],
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->category_id = null;
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'desc';
        }
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $transaction = Transaction::forUser(Auth::id())->findOrFail($id);
        $this->editingId = $transaction->id;
        $this->type = $transaction->type;
        $this->category_id = $transaction->category_id;
        $this->amount = (string) $transaction->amount;
        $this->description = $transaction->description ?? '';
        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        // Verify category belongs to user
        Category::forUser(Auth::id())->findOrFail($this->category_id);

        $data = [
            'type' => $this->type,
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'description' => $this->description ?: null,
            'transaction_date' => $this->transaction_date,
        ];

        if ($this->editingId) {
            $transaction = Transaction::forUser(Auth::id())->findOrFail($this->editingId);
            $transaction->update($data);
            session()->flash('success', 'Transaksi berhasil diperbarui!');
        } else {
            Auth::user()->transactions()->create($data);
            session()->flash('success', 'Transaksi berhasil ditambahkan!');
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
            $transaction = Transaction::forUser(Auth::id())->findOrFail($this->deletingId);
            $transaction->delete();
            session()->flash('success', 'Transaksi berhasil dihapus!');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->type = 'expense';
        $this->category_id = null;
        $this->amount = '';
        $this->description = '';
        $this->transaction_date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterType = 'all';
        $this->filterCategory = null;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    /**
     * Get summary totals for current filter.
     */
    public function getSummaryProperty(): array
    {
        $query = Transaction::forUser(Auth::id());

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }
        if ($this->dateFrom) {
            $query->where('transaction_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('transaction_date', '<=', $this->dateTo);
        }
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        $baseQuery = clone $query;

        $income = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $expense = (clone $baseQuery)->where('type', 'expense')->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
        ];
    }

    public function render()
    {
        $userId = Auth::id();

        $query = Transaction::forUser($userId)->with('category');

        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }
        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }
        if ($this->dateFrom) {
            $query->where('transaction_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('transaction_date', '<=', $this->dateTo);
        }

        $transactions = $query->orderBy($this->sortBy, $this->sortDir)->paginate(15);

        $categories = Category::forUser($userId)->orderBy('name')->get();

        return view('livewire.transactions.transaction-index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'incomeCategories' => $categories->where('type', 'income'),
            'expenseCategories' => $categories->where('type', 'expense'),
        ])->layout('components.layouts.app', ['header' => 'Transaksi', 'title' => 'Transaksi']);
    }
}

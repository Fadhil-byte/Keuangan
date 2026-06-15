<?php

namespace App\Livewire\Reports;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ReportIndex extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo = '';
    public string $filterType = 'all';
    public ?int $filterCategory = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Get summary for current filters.
     */
    public function getSummaryProperty(): array
    {
        $userId = Auth::id();

        $incomeQuery = Transaction::forUser($userId)->income();
        $expenseQuery = Transaction::forUser($userId)->expense();

        if ($this->dateFrom) {
            $incomeQuery->where('transaction_date', '>=', $this->dateFrom);
            $expenseQuery->where('transaction_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $incomeQuery->where('transaction_date', '<=', $this->dateTo);
            $expenseQuery->where('transaction_date', '<=', $this->dateTo);
        }
        if ($this->filterCategory) {
            $incomeQuery->where('category_id', $this->filterCategory);
            $expenseQuery->where('category_id', $this->filterCategory);
        }

        $income = (float) $incomeQuery->sum('amount');
        $expense = (float) $expenseQuery->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'count' => Transaction::forUser($userId)
                ->dateRange($this->dateFrom, $this->dateTo)
                ->when($this->filterCategory, fn ($q) => $q->where('category_id', $this->filterCategory))
                ->when($this->filterType !== 'all', fn ($q) => $q->where('type', $this->filterType))
                ->count(),
        ];
    }

    /**
     * Get category breakdown.
     */
    public function getCategoryBreakdownProperty()
    {
        $userId = Auth::id();

        return Transaction::forUser($userId)
            ->when($this->dateFrom, fn ($q) => $q->where('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('transaction_date', '<=', $this->dateTo))
            ->when($this->filterType !== 'all', fn ($q) => $q->where('transactions.type', $this->filterType))
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                'categories.color',
                'categories.icon',
                'transactions.type',
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon', 'transactions.type')
            ->orderByDesc('total')
            ->get();
    }

    public function resetFilters(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->filterType = 'all';
        $this->filterCategory = null;
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();

        $query = Transaction::forUser($userId)
            ->with('category')
            ->dateRange($this->dateFrom, $this->dateTo);

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }
        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        $transactions = $query->orderByDesc('transaction_date')->paginate(20);
        $categories = Category::forUser($userId)->orderBy('name')->get();

        return view('livewire.reports.report-index', [
            'transactions' => $transactions,
            'categories' => $categories,
        ])->layout('components.layouts.app', ['header' => 'Laporan Keuangan', 'title' => 'Laporan']);
    }
}

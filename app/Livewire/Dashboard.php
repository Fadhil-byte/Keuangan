<?php

namespace App\Livewire;

use App\Models\Bill;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public int $selectedYear;
    public int $selectedMonth;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    /**
     * Get summary cards data.
     */
    public function getSummaryProperty(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $income = Transaction::forUser($userId)
            ->income()
            ->dateRange($startOfMonth->toDateString(), $endOfMonth->toDateString())
            ->sum('amount');

        $expense = Transaction::forUser($userId)
            ->expense()
            ->dateRange($startOfMonth->toDateString(), $endOfMonth->toDateString())
            ->sum('amount');

        $upcomingBills = Bill::forUser($userId)->upcoming(30)->count();
        $overdueBills = Bill::forUser($userId)->overdue()->count();

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
            'upcoming_bills' => $upcomingBills,
            'overdue_bills' => $overdueBills,
        ];
    }

    /**
     * Get monthly bar chart data (6 months).
     */
    public function getBarChartDataProperty(): array
    {
        $userId = Auth::id();
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $income = Transaction::forUser($userId)
                ->income()
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $expense = Transaction::forUser($userId)
                ->expense()
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;
        }

        return [
            'categories' => $months,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
    }

    /**
     * Get expense by category donut chart data.
     */
    public function getDonutChartDataProperty(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $data = Transaction::query()
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->where('transactions.transaction_date', '>=', $startOfMonth->toDateString())
            ->where('transactions.transaction_date', '<=', $endOfMonth->toDateString())
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.color', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'series' => $data->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
            'colors' => $data->pluck('color')->toArray(),
        ];
    }

    /**
     * Get recent transactions.
     */
    public function getRecentTransactionsProperty()
    {
        return Transaction::forUser(Auth::id())
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Get upcoming bills.
     */
    public function getUpcomingBillsProperty()
    {
        return Bill::forUser(Auth::id())
            ->unpaid()
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    public function updatedSelectedMonth(): void
    {
        $this->dispatch('chartDataUpdated', [
            'barChart' => $this->barChartData,
            'donutChart' => $this->donutChartData,
        ]);
    }

    public function updatedSelectedYear(): void
    {
        $this->dispatch('chartDataUpdated', [
            'barChart' => $this->barChartData,
            'donutChart' => $this->donutChartData,
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app', ['header' => 'Dashboard', 'title' => 'Dashboard']);
    }
}

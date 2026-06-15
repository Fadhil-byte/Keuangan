<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    /**
     * Export transactions to CSV (Excel-compatible).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $transactions = $this->getFilteredTransactions($request);

        $filename = 'laporan_keuangan_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            fputcsv($handle, ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'Jumlah (Rp)'], ';');

            $totalIncome = 0;
            $totalExpense = 0;

            foreach ($transactions as $tx) {
                $amount = (float) $tx->amount;
                if ($tx->type === 'income') {
                    $totalIncome += $amount;
                } else {
                    $totalExpense += $amount;
                }

                fputcsv($handle, [
                    $tx->transaction_date->format('d/m/Y'),
                    $tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $tx->category->name,
                    $tx->description ?? '-',
                    number_format($amount, 0, ',', '.'),
                ], ';');
            }

            // Summary rows
            fputcsv($handle, [], ';');
            fputcsv($handle, ['', '', '', 'Total Pemasukan', number_format($totalIncome, 0, ',', '.')], ';');
            fputcsv($handle, ['', '', '', 'Total Pengeluaran', number_format($totalExpense, 0, ',', '.')], ';');
            fputcsv($handle, ['', '', '', 'Saldo Bersih', number_format($totalIncome - $totalExpense, 0, ',', '.')], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export transactions to PDF.
     */
    public function exportPdf(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        $pdf = Pdf::loadView('exports.report-pdf', [
            'transactions' => $transactions,
            'totalIncome' => (float) $totalIncome,
            'totalExpense' => (float) $totalExpense,
            'balance' => (float) ($totalIncome - $totalExpense),
            'dateFrom' => $request->input('from', now()->startOfMonth()->format('Y-m-d')),
            'dateTo' => $request->input('to', now()->endOfMonth()->format('Y-m-d')),
            'user' => Auth::user(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('laporan_keuangan_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Get filtered transactions for the authenticated user.
     */
    private function getFilteredTransactions(Request $request)
    {
        $query = Transaction::forUser(Auth::id())
            ->with('category')
            ->orderByDesc('transaction_date');

        if ($from = $request->input('from')) {
            $query->where('transaction_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->where('transaction_date', '<=', $to);
        }
        if ($type = $request->input('type')) {
            if ($type !== 'all') {
                $query->where('type', $type);
            }
        }
        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        return $query->get();
    }
}

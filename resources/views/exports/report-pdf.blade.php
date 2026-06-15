<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #6366f1;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #64748b;
        }
        .meta-info {
            margin-bottom: 20px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 2px 0;
            font-size: 11px;
        }
        .meta-info .label {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 12px 8px;
            border: 1px solid #e2e8f0;
        }
        .summary-card .amount {
            font-size: 16px;
            font-weight: bold;
            margin-top: 4px;
        }
        .summary-card .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .income { color: #16a34a; }
        .expense { color: #dc2626; }
        .balance { color: #4f46e5; }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data th {
            background: #6366f1;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data th:last-child {
            text-align: right;
        }
        table.data td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }
        table.data td:last-child {
            text-align: right;
            font-weight: bold;
        }
        table.data tr:nth-child(even) {
            background: #f8fafc;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px 0;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-income {
            background: #dcfce7;
            color: #16a34a;
        }
        .badge-expense {
            background: #fef2f2;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>{{ $user->name }} &mdash; {{ $user->email }}</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Periode:</td>
                <td>{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} &mdash; {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Dibuat pada:</td>
                <td>{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Total transaksi:</td>
                <td>{{ $transactions->count() }}</td>
            </tr>
        </table>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="label">Total Pemasukan</div>
            <div class="amount income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Pengeluaran</div>
            <div class="amount expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Saldo Bersih</div>
            <div class="amount balance">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $tx)
                <tr>
                    <td>{{ $tx->transaction_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $tx->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                            {{ $tx->type === 'income' ? 'MASUK' : 'KELUAR' }}
                        </span>
                    </td>
                    <td>{{ $tx->category->name }}</td>
                    <td>{{ $tx->description ?? '-' }}</td>
                    <td class="{{ $tx->type === 'income' ? 'income' : 'expense' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini di-generate otomatis oleh Keuangan App &mdash; {{ now()->format('d F Y, H:i') }}
    </div>
</body>
</html>

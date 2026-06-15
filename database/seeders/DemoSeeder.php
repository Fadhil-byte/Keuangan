<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data for development/testing.
     */
    public function run(): void
    {
        // Create demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@keuangan.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Seed categories for demo user
        CategorySeeder::seedForUser($user);

        // Get categories
        $incomeCategories = Category::forUser($user->id)->income()->get();
        $expenseCategories = Category::forUser($user->id)->expense()->get();

        // Generate 6 months of transaction data
        for ($month = 5; $month >= 0; $month--) {
            $date = Carbon::now()->subMonths($month);

            // Income transactions (2-4 per month)
            $incomeCount = rand(2, 4);
            for ($i = 0; $i < $incomeCount; $i++) {
                $category = $incomeCategories->random();
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'type' => 'income',
                    'amount' => $this->randomAmount('income'),
                    'description' => $this->randomDescription('income', $category->name),
                    'transaction_date' => $date->copy()->day(rand(1, 28)),
                ]);
            }

            // Expense transactions (8-15 per month)
            $expenseCount = rand(8, 15);
            for ($i = 0; $i < $expenseCount; $i++) {
                $category = $expenseCategories->random();
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'type' => 'expense',
                    'amount' => $this->randomAmount('expense', $category->name),
                    'description' => $this->randomDescription('expense', $category->name),
                    'transaction_date' => $date->copy()->day(rand(1, 28)),
                ]);
            }
        }

        // Create some bills
        $billsData = [
            ['title' => 'Listrik PLN', 'amount' => 450000, 'due_date' => Carbon::now()->addDays(5), 'status' => 'unpaid', 'recurrence' => 'monthly'],
            ['title' => 'Internet Indihome', 'amount' => 350000, 'due_date' => Carbon::now()->addDays(10), 'status' => 'unpaid', 'recurrence' => 'monthly'],
            ['title' => 'BPJS Kesehatan', 'amount' => 150000, 'due_date' => Carbon::now()->addDays(15), 'status' => 'unpaid', 'recurrence' => 'monthly'],
            ['title' => 'Cicilan Motor', 'amount' => 850000, 'due_date' => Carbon::now()->subDays(2), 'status' => 'unpaid', 'recurrence' => 'monthly'],
            ['title' => 'Spotify Premium', 'amount' => 55000, 'due_date' => Carbon::now()->subDays(5), 'status' => 'paid', 'recurrence' => 'monthly'],
            ['title' => 'Pajak Kendaraan', 'amount' => 1500000, 'due_date' => Carbon::now()->addMonths(2), 'status' => 'unpaid', 'recurrence' => 'yearly'],
        ];

        $billCategory = Category::forUser($user->id)->expense()->where('name', 'Tagihan & Utilitas')->first();

        foreach ($billsData as $bill) {
            Bill::create(array_merge($bill, [
                'user_id' => $user->id,
                'category_id' => $billCategory?->id,
            ]));
        }
    }

    /**
     * Generate random amount based on type.
     */
    private function randomAmount(string $type, ?string $category = null): float
    {
        if ($type === 'income') {
            return rand(3000000, 15000000); // 3M - 15M IDR
        }

        // Expense amounts vary by category
        return match ($category) {
            'Makanan & Minuman' => rand(25000, 250000),
            'Transportasi'     => rand(20000, 500000),
            'Belanja'          => rand(50000, 1000000),
            'Tagihan & Utilitas' => rand(100000, 800000),
            'Hiburan'          => rand(30000, 300000),
            'Kesehatan'        => rand(50000, 500000),
            'Pendidikan'       => rand(100000, 2000000),
            'Rumah Tangga'     => rand(50000, 500000),
            default            => rand(10000, 500000),
        };
    }

    /**
     * Generate random description based on type and category.
     */
    private function randomDescription(string $type, string $category): string
    {
        $descriptions = [
            'income' => [
                'Gaji'       => ['Gaji bulanan', 'Gaji pokok', 'Gaji + tunjangan'],
                'Freelance'  => ['Proyek website', 'Desain logo', 'Jasa konsultasi', 'Proyek aplikasi'],
                'Investasi'  => ['Dividen saham', 'Bunga deposito', 'Profit reksadana'],
                'Bonus'      => ['Bonus tahunan', 'THR', 'Bonus proyek'],
                'Lainnya'    => ['Pendapatan lain', 'Transfer masuk', 'Cashback'],
            ],
            'expense' => [
                'Makanan & Minuman' => ['Makan siang', 'Kopi', 'Belanja groceries', 'Makan malam', 'Snack'],
                'Transportasi'     => ['Bensin', 'Grab/Gojek', 'Parkir', 'Tol', 'Servis kendaraan'],
                'Belanja'          => ['Beli baju', 'Perlengkapan kantor', 'Aksesoris', 'Beli sepatu'],
                'Tagihan & Utilitas' => ['Listrik', 'Air PDAM', 'Internet', 'Gas', 'Telepon'],
                'Hiburan'          => ['Nonton bioskop', 'Spotify', 'Netflix', 'Game', 'Konser'],
                'Kesehatan'        => ['Obat', 'Cek kesehatan', 'Vitamin', 'Gym membership'],
                'Pendidikan'       => ['Buku', 'Kursus online', 'Seminar', 'Workshop'],
                'Rumah Tangga'     => ['Sabun', 'Alat kebersihan', 'Perabotan', 'Perbaikan rumah'],
                'Lainnya'          => ['Donasi', 'Hadiah', 'Pengeluaran lain'],
            ],
        ];

        $options = $descriptions[$type][$category] ?? ['Transaksi'];
        return $options[array_rand($options)];
    }
}

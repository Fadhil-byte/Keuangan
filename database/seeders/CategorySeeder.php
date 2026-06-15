<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed default categories for a specific user.
     */
    public function run(): void
    {
        // This seeder is designed to be called per-user during registration
        // For initial seed, we create categories for all existing users
        $users = User::all();
        foreach ($users as $user) {
            $this->seedForUser($user);
        }
    }

    /**
     * Create default categories for a given user.
     */
    public static function seedForUser(User $user): void
    {
        $incomeCategories = [
            ['name' => 'Gaji',       'color' => '#22c55e', 'icon' => 'banknotes'],
            ['name' => 'Freelance',   'color' => '#06b6d4', 'icon' => 'computer-desktop'],
            ['name' => 'Investasi',   'color' => '#8b5cf6', 'icon' => 'chart-bar'],
            ['name' => 'Bonus',       'color' => '#f59e0b', 'icon' => 'gift'],
            ['name' => 'Lainnya',     'color' => '#64748b', 'icon' => 'ellipsis-horizontal-circle'],
        ];

        $expenseCategories = [
            ['name' => 'Makanan & Minuman', 'color' => '#ef4444', 'icon' => 'cake'],
            ['name' => 'Transportasi',       'color' => '#f97316', 'icon' => 'truck'],
            ['name' => 'Belanja',            'color' => '#ec4899', 'icon' => 'shopping-bag'],
            ['name' => 'Tagihan & Utilitas', 'color' => '#eab308', 'icon' => 'document-text'],
            ['name' => 'Hiburan',            'color' => '#a855f7', 'icon' => 'musical-note'],
            ['name' => 'Kesehatan',          'color' => '#14b8a6', 'icon' => 'heart'],
            ['name' => 'Pendidikan',         'color' => '#3b82f6', 'icon' => 'academic-cap'],
            ['name' => 'Rumah Tangga',       'color' => '#84cc16', 'icon' => 'home'],
            ['name' => 'Lainnya',            'color' => '#64748b', 'icon' => 'ellipsis-horizontal-circle'],
        ];

        foreach ($incomeCategories as $cat) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name'], 'type' => 'income'],
                ['color' => $cat['color'], 'icon' => $cat['icon']]
            );
        }

        foreach ($expenseCategories as $cat) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name'], 'type' => 'expense'],
                ['color' => $cat['color'], 'icon' => $cat['icon']]
            );
        }
    }
}

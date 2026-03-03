<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Loyer', 'icon' => '🏠', 'color' => '#6366f1'],
            ['name' => 'Électricité', 'icon' => '⚡', 'color' => '#f59e0b'],
            ['name' => 'Eau', 'icon' => '💧', 'color' => '#3b82f6'],
            ['name' => 'Internet', 'icon' => '🌐', 'color' => '#8b5cf6'],
            ['name' => 'Courses', 'icon' => '🛒', 'color' => '#10b981'],
            ['name' => 'Ménage', 'icon' => '🧹', 'color' => '#ec4899'],
            ['name' => 'Autre', 'icon' => '📦', 'color' => '#64748b'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
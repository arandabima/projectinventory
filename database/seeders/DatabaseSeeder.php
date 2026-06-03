<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $atk = Category::firstOrCreate(['name' => 'ATK'], ['description' => 'Alat tulis kantor']);
        $it = Category::firstOrCreate(['name' => 'IT'], ['description' => 'Perangkat teknologi']);

        Item::firstOrCreate(['sku' => 'ATK-001'], [
            'category_id' => $atk->id,
            'name' => 'Kertas A4 80gsm',
            'unit' => 'rim',
            'current_stock' => 12,
            'minimum_stock' => 5,
            'location' => 'Gudang A',
        ]);

        Item::firstOrCreate(['sku' => 'IT-001'], [
            'category_id' => $it->id,
            'name' => 'Keyboard USB',
            'unit' => 'pcs',
            'current_stock' => 3,
            'minimum_stock' => 4,
            'location' => 'Rak IT-2',
        ]);
    }
}

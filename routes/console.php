<?php

use App\Models\Item;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inventory:summary', function () {
    $items = Item::count();
    $lowStock = Item::whereColumn('current_stock', '<=', 'minimum_stock')->count();

    $this->info("Items: {$items}");
    $this->info("Low stock: {$lowStock}");
})->purpose('Menampilkan ringkasan inventory.');

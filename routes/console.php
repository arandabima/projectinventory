<?php

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inventory:summary', function () {
    $items = Item::count();
    $lowStock = Item::whereColumn('current_stock', '<=', 'minimum_stock')->count();

    $this->info("Items: {$items}");
    $this->info("Low stock: {$lowStock}");
})->purpose('Menampilkan ringkasan inventory.');

Artisan::command('inventory:ensure-admin', function () {
    $username = env('ADMIN_USERNAME', 'admin');
    $email = env('ADMIN_EMAIL', 'admin@example.com');
    $password = env('ADMIN_PASSWORD', 'admin12345');

    $user = User::where('username', $username)
        ->orWhere('email', $email)
        ->first() ?? new User();

    $user->fill([
        'name' => env('ADMIN_NAME', 'Administrator'),
        'username' => $username,
        'email' => $email,
        'password' => $password,
    ]);
    $user->save();

    $this->info("Admin user ready: {$username}");
})->purpose('Membuat atau memperbarui user admin inventory.');

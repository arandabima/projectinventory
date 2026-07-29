<?php
namespace App\Services;
use App\Models\Item;
class InventoryService { public function decreaseStock(Item $product, int $quantity): void { if ($product->current_stock < $quantity) { throw new \DomainException('Stok produk tidak mencukupi.'); } $product->decrement('current_stock', $quantity); } public function increaseStock(Item $product, int $quantity): void { $product->increment('current_stock', $quantity); } }

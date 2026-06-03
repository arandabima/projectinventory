<?php

namespace App\Services;

use App\Models\Item;
use App\Models\NotificationMessage;

class InventoryNotificationService
{
    public function notifyLowStock(Item $item): void
    {
        if (! filter_var(env('LOW_STOCK_NOTIFICATION', true), FILTER_VALIDATE_BOOL)) {
            return;
        }

        if ($item->current_stock > $item->minimum_stock) {
            return;
        }

        NotificationMessage::create([
            'channel' => 'system',
            'recipient' => 'gudang',
            'subject' => 'Stok menipis: '.$item->name,
            'message' => "SKU {$item->sku} tersisa {$item->current_stock} {$item->unit}. Minimum stok {$item->minimum_stock}.",
            'status' => 'pending',
        ]);
    }
}

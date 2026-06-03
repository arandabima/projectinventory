<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\NotificationMessage;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'serviceName' => env('INVENTORY_SERVICE', 'gateway'),
            'totalItems' => Item::count(),
            'totalStock' => Item::sum('current_stock'),
            'lowStockCount' => Item::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'pendingNotifications' => NotificationMessage::where('status', 'pending')->count(),
            'lowStockItems' => Item::with('category')->whereColumn('current_stock', '<=', 'minimum_stock')->latest()->take(5)->get(),
            'latestMovements' => StockMovement::with('item')->latest()->take(8)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalItems' => Item::count(),
            'availableItems' => Item::where('status', 'available')->count(),
            'borrowedItems' => Item::where('status', 'borrowed')->count(),
            'totalBorrowings' => Borrowing::count(),
            'pendingBorrowings' => Borrowing::where('status', 'pending')->count(),
            'activeBorrowings' => Borrowing::where('status', 'approved')->count(),
            'completedBorrowings' => Borrowing::where('status', 'returned')->count(),
        ]);
    }
}

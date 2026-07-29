<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'user' => $user,
            'totalTransactions' => Order::where('buyer_id', $user?->id)->count(),
            'pendingOrders' => Order::where('buyer_id', $user?->id)
                ->where('status', 'pending')
                ->count(),
        ]);
    }
}

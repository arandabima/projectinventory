<?php

namespace App\Http\Controllers;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use App\Models\Item;
use App\Notifications\BorrowingApproved;
use App\Notifications\BorrowingReady;
use App\Notifications\BorrowingRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminBorrowingController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage', Borrowing::class);

        return view('admin.borrowings.index', [
            'borrowings' => Borrowing::with('user', 'items.item')->latest()->paginate(12),
        ]);
    }

    public function approve(Request $request, Borrowing $borrowing): RedirectResponse
    {
        Gate::authorize('manage', $borrowing);
        if (! $this->canTransition($borrowing->status, BorrowingStatus::approved)) {
            return back()->withErrors(['status' => 'Hanya borrowing pending yang bisa di-approve.']);
        }
        $borrowing->load('items');
        DB::transaction(function () use ($request, $borrowing) {
            foreach ($borrowing->items as $lineItem) {
                $item = Item::lockForUpdate()->findOrFail($lineItem->item_id);
                if ($item->current_stock < $lineItem->quantity) {
                    abort(422, 'Stok tidak cukup untuk approve borrowing ini.');
                }
                $item->decrement('current_stock', $lineItem->quantity);
                $item->update(['status' => 'borrowed']);
            }

            $borrowing->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);
        });

        $borrowing->user?->notify(new BorrowingApproved($borrowing));
        $borrowing->user?->notify(new BorrowingReady($borrowing));

        return back()->with('status', 'Borrowing disetujui.');
    }

    public function reject(Request $request, Borrowing $borrowing): RedirectResponse
    {
        Gate::authorize('manage', $borrowing);
        if (! $this->canTransition($borrowing->status, BorrowingStatus::rejected)) {
            return back()->withErrors(['status' => 'Hanya borrowing pending yang bisa ditolak.']);
        }
        $borrowing->load('items');
        $borrowing->update([
            'status' => BorrowingStatus::rejected,
            'approved_by' => $request->user()->id,
            'rejected_at' => now(),
        ]);

        $borrowing->user?->notify(new BorrowingRejected($borrowing));

        return back()->with('status', 'Borrowing ditolak.');
    }

    public function returned(Request $request, Borrowing $borrowing): RedirectResponse
    {
        Gate::authorize('manage', $borrowing);
        if (! $this->canTransition($borrowing->status, BorrowingStatus::returned)) {
            return back()->withErrors(['status' => 'Hanya borrowing approved yang bisa di-return.']);
        }
        $borrowing->load('items');
        DB::transaction(function () use ($borrowing, $request) {
            foreach ($borrowing->items as $lineItem) {
                $item = Item::lockForUpdate()->findOrFail($lineItem->item_id);
                $item->increment('current_stock', $lineItem->quantity);
                $item->update(['status' => 'available']);
            }

            $borrowing->update([
                'status' => BorrowingStatus::returned,
                'approved_by' => $request->user()->id,
                'returned_at' => now(),
            ]);
        });

        return back()->with('status', 'Borrowing ditandai returned.');
    }

    private function canTransition(BorrowingStatus $currentStatus, BorrowingStatus $nextStatus): bool
    {
        return match ($currentStatus) {
            BorrowingStatus::pending => in_array($nextStatus, [BorrowingStatus::approved, BorrowingStatus::rejected], true),
            BorrowingStatus::approved => $nextStatus === BorrowingStatus::returned,
            BorrowingStatus::rejected, BorrowingStatus::returned => false,
        };
    }
}

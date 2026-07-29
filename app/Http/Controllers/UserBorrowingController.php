<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Item;
use App\Notifications\NewBorrowingRequested;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserBorrowingController extends Controller
{
    public function index(Request $request): View
    {
        return view('user.borrowings.index', [
            'borrowings' => Borrowing::with('items.item')->where('user_id', $request->user()->id)->latest()->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        $itemIds = collect($request->input('item_ids', []))->filter()->map(fn ($id) => (int) $id)->all();
        $items = $itemIds ? Item::whereIn('id', $itemIds)->where('status', 'available')->get() : collect();

        return view('user.borrowings.create', compact('items', 'itemIds'));
    }

    public function store(StoreBorrowingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $borrowing = DB::transaction(function () use ($request, $data) {
            $items = Item::whereIn('id', $data['item_ids'])
                ->where('status', 'available')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lineItems = collect();

            foreach ($data['item_ids'] as $index => $itemId) {
                $item = $items->get((int) $itemId);
                $quantity = (int) ($data['quantities'][$index] ?? 0);

                if (! $item) {
                    continue;
                }

                Gate::authorize('borrow', $item);

                if ($item->current_stock < $quantity) {
                    abort(422, "Stok {$item->name} tidak cukup.");
                }

                $lineItems->push([
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'unit_price' => $item->price ?? 0,
                    'subtotal' => $quantity * (float) ($item->price ?? 0),
                ]);
            }

            if ($lineItems->isEmpty()) {
                abort(422, 'Pilih minimal 1 barang yang tersedia.');
            }

            $borrowing = Borrowing::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'borrow_date' => $data['borrow_date'],
                'return_date' => $data['return_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lineItems as $lineItem) {
                $borrowing->items()->create($lineItem);
            }

            return $borrowing;
        });

        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewBorrowingRequested($borrowing));
        }

        return redirect()->route('user.borrowings.show', $borrowing)->with('status', 'Pengajuan berhasil dibuat.');
    }

    public function show(Request $request, Borrowing $borrowing): View
    {
        Gate::authorize('view', $borrowing);

        return view('user.borrowings.show', [
            'borrowing' => $borrowing->load('items.item', 'approver'),
        ]);
    }
}

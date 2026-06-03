<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Services\CloudinaryService;
use App\Services\InventoryNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function store(Request $request, InventoryNotificationService $notifications, CloudinaryService $cloudinary): RedirectResponse
    {
        $data = $request->validate([
            'item_id'     => ['required', 'exists:items,id'],
            'type'        => ['required', 'in:in,out,adjustment'],
            'quantity'    => ['required', 'integer'],
            'notes'       => ['nullable', 'string', 'max:500'],
            'recorded_by' => ['nullable', 'string', 'max:120'],
            'document'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,mp4,mov', 'max:20480'],
        ]);

        // Upload dokumen ke Cloudinary jika ada
        $documentUrl      = null;
        $documentPublicId = null;

        if ($request->hasFile('document')) {
            $uploaded         = $cloudinary->upload(
                $request->file('document')->getRealPath(),
                'inventory/documents'
            );
            $documentUrl      = $uploaded['url'];
            $documentPublicId = $uploaded['public_id'];
        }

        DB::transaction(function () use ($data, $notifications, $documentUrl, $documentPublicId) {
            $item        = Item::lockForUpdate()->findOrFail($data['item_id']);
            $stockBefore = $item->current_stock;
            $quantity    = (int) $data['quantity'];

            $stockAfter = match ($data['type']) {
                'in'         => $stockBefore + abs($quantity),
                'out'        => $stockBefore - abs($quantity),
                'adjustment' => $quantity,
            };

            if ($stockAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak boleh menjadi negatif.',
                ]);
            }

            $movementQuantity = $data['type'] === 'adjustment'
                ? $stockAfter - $stockBefore
                : abs($quantity);

            StockMovement::create([
                'item_id'           => $item->id,
                'type'              => $data['type'],
                'quantity'          => $movementQuantity,
                'stock_before'      => $stockBefore,
                'stock_after'       => $stockAfter,
                'notes'             => $data['notes'] ?? null,
                'recorded_by'       => $data['recorded_by'] ?? 'operator',
                'document_url'      => $documentUrl,
                'document_public_id' => $documentPublicId,
            ]);

            $item->update(['current_stock' => $stockAfter]);
            $notifications->notifyLowStock($item->fresh());
        });

        return redirect()->route('items.index')->with('status', 'Mutasi stok berhasil disimpan.');
    }
}
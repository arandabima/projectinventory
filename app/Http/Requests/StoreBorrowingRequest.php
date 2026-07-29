<?php

namespace App\Http\Requests;

use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'borrow_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['required', 'integer', 'distinct', 'exists:items,id'],
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $itemIds = collect($this->input('item_ids', []))->map(fn ($id) => (int) $id)->all();
            $quantities = $this->input('quantities', []);

            $items = Item::whereIn('id', $itemIds)->get()->keyBy('id');

            foreach ($itemIds as $index => $itemId) {
                $item = $items->get($itemId);
                $quantity = (int) ($quantities[$index] ?? 0);

                if (! $item) {
                    continue;
                }

                if ($item->status !== ItemStatus::available) {
                    $validator->errors()->add("item_ids.$index", "Barang {$item->name} sedang tidak tersedia.");
                }

                if ($item->current_stock < $quantity) {
                    $validator->errors()->add("quantities.$index", "Stok {$item->name} tidak cukup.");
                }
            }
        });
    }
}

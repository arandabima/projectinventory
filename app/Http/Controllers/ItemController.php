<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with('category')->latest();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%")
                    ->orWhere('location', 'ilike', "%{$search}%");
            });
        }

        return view('items.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'movementItems' => Item::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Item::create($this->validatedData($request));

        return redirect()->route('items.index')->with('status', 'Barang berhasil dicatat.');
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $item->update($this->validatedData($request, $item->id));

        return redirect()->route('items.index')->with('status', 'Data barang diperbarui.');
    }

    private function validatedData(Request $request, ?int $itemId = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:items,sku,'.$itemId],
            'name' => ['required', 'string', 'max:150'],
            'unit' => ['required', 'string', 'max:30'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

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
            'items'         => $query->paginate(10)->withQueryString(),
            'categories'    => Category::orderBy('name')->get(),
            'movementItems' => Item::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $uploaded = $this->cloudinary->upload(
                $request->file('image')->getRealPath(),
                'inventory/items'
            );
            $data['image_url']       = $uploaded['url'];
            $data['image_public_id'] = $uploaded['public_id'];
        }

        Item::create($data);

        return redirect()->route('items.index')->with('status', 'Barang berhasil dicatat.');
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item'       => $item,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $data = $this->validatedData($request, $item->id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama di Cloudinary
            if ($item->image_public_id) {
                $this->cloudinary->delete($item->image_public_id);
            }

            $uploaded = $this->cloudinary->upload(
                $request->file('image')->getRealPath(),
                'inventory/items'
            );
            $data['image_url']       = $uploaded['url'];
            $data['image_public_id'] = $uploaded['public_id'];
        }

        $item->update($data);

        return redirect()->route('items.index')->with('status', 'Data barang diperbarui.');
    }

    private function validatedData(Request $request, ?int $itemId = null): array
    {
        return $request->validate([
            'category_id'   => ['nullable', 'exists:categories,id'],
            'sku'           => ['required', 'string', 'max:50', Rule::unique('items', 'sku')->ignore($itemId)],
            'name'          => ['required', 'string', 'max:150'],
            'unit'          => ['required', 'string', 'max:30'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location'      => ['nullable', 'string', 'max:120'],
            'description'   => ['nullable', 'string', 'max:500'],
            'image'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov', 'max:20480'],
        ]);
    }
}
<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        $items = Item::where('organization_id', $request->user()->organization_id)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'description', 'unit_price', 'unit', 'is_taxable', 'is_active']);

        return Inertia::render('Owner/Items/Index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('Owner/Items/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit_price'  => ['required', 'numeric', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'is_taxable'  => ['boolean'],
            'is_active'   => ['boolean'],
        ]);

        Item::create([
            ...$data,
            'organization_id' => $request->user()->organization_id,
            'is_taxable'      => $data['is_taxable'] ?? true,
            'is_active'       => $data['is_active'] ?? true,
        ]);

        return redirect('/owner/items')->with('success', 'Catalog item created.');
    }

    public function edit(Request $request, Item $item): Response
    {
        abort_unless($item->organization_id === $request->user()->organization_id, 403);

        return Inertia::render('Owner/Items/Edit', ['item' => $item]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->organization_id === $request->user()->organization_id, 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit_price'  => ['required', 'numeric', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'is_taxable'  => ['boolean'],
            'is_active'   => ['boolean'],
        ]);

        $item->update($data);

        return redirect('/owner/items')->with('success', 'Catalog item updated.');
    }

    public function destroy(Request $request, Item $item): RedirectResponse
    {
        abort_unless($item->organization_id === $request->user()->organization_id, 403);

        $item->update(['is_active' => false]);

        return redirect('/owner/items')->with('success', 'Item deactivated.');
    }
}

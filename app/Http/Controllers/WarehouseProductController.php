<?php

namespace App\Http\Controllers;

use App\Models\WarehouseProduct;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WarehouseProduct::query();

        if ($request->filled('search')) {
            $search = $request->search;
            
            // Smart Barcode Logic:
            // If it looks like a barcode (mostly digits, length > 5) let's check if it exists exactly
            if (preg_match('/^[0-9A-Z]{6,20}$/i', $search)) {
                $exactMatch = WarehouseProduct::where('barcode', $search)->first();
                
                // If we found it, show only that or redirect to it (for now just filter exactly)
                if (!$exactMatch) {
                    // Not found, redirect to create with prefilled barcode
                    return redirect()->route('magazzino.prodotti.create', ['barcode' => $search])
                                     ->with('info', "Barcode $search non trovato. Crea la nuova scheda prodotto.");
                }
            }

            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20);
        $totalProducts = WarehouseProduct::count();

        return view('magazzino.products.index', compact('products', 'totalProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('magazzino.products.form', [
            'prefilled_barcode' => $request->query('barcode')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:255|unique:warehouse_products,code',
            'barcode' => 'nullable|string|max:255|unique:warehouse_products,barcode',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'is_inventariable' => 'boolean',
        ]);

        $product = WarehouseProduct::create($validated);

        return redirect()->route('magazzino.prodotti.index')
                         ->with('success', 'Prodotto creato con successo nel Catalogo.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WarehouseProduct $prodotti)
    {
        return view('magazzino.products.form', ['product' => $prodotti]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WarehouseProduct $prodotti)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:255|unique:warehouse_products,code,' . $prodotti->id,
            'barcode' => 'nullable|string|max:255|unique:warehouse_products,barcode,' . $prodotti->id,
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'is_inventariable' => 'boolean',
        ]);

        $prodotti->update($validated);

        return redirect()->route('magazzino.prodotti.index')
                         ->with('success', 'Prodotto aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WarehouseProduct $prodotti)
    {
        $prodotti->delete();

        return redirect()->route('magazzino.prodotti.index')
                         ->with('success', 'Prodotto eliminato dal Catalogo.');
    }
}

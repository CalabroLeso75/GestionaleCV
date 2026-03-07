<?php

namespace App\Http\Controllers;

use App\Models\WarehouseStock;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;

class WarehouseStockController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = WarehouseStock::with(['product', 'location']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }

            if ($request->boolean('low_stock')) {
                $query->whereRaw('quantity < IFNULL(min_stock, quantity + 1)');
            }

            $stocks    = $query->orderBy('location_id')->paginate(30);
        } catch (\Exception $e) {
            $stocks = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);
        }

        $locations = WarehouseLocation::orderBy('name')->get();

        return view('magazzino.stock.index', compact('stocks', 'locations'));
    }
}

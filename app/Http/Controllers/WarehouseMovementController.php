<?php

namespace App\Http\Controllers;

use App\Models\WarehouseMovement;
use App\Models\WarehouseProduct;
use App\Models\WarehouseLocation;
use App\Models\WarehouseStock;
use App\Models\InternalEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseMovementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = WarehouseMovement::with(['product', 'sourceLocation', 'destinationLocation', 'user']);

            if ($request->filled('tipo')) {
                $query->where('movement_type', $request->tipo);
            }
            if ($request->filled('da')) {
                $query->whereDate('movement_date', '>=', $request->da);
            }
            if ($request->filled('a')) {
                $query->whereDate('movement_date', '<=', $request->a);
            }

            $movements = $query->orderByDesc('movement_date')->paginate(30);
        } catch (\Exception $e) {
            $movements = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);
        }

        return view('magazzino.movimenti.index', compact('movements'));
    }

    public function create(Request $request)
    {
        $products   = WarehouseProduct::orderBy('name')->get();
        $locations  = WarehouseLocation::orderBy('name')->get();
        $employees  = InternalEmployee::orderBy('last_name')->get();
        $prefill    = $request->only(['product_id', 'location_id']);
        return view('magazzino.movimenti.form', compact('products', 'locations', 'employees', 'prefill'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'movement_type'           => 'required|in:CARICO,SCARICO,TRASFERIMENTO,SMISTAMENTO,ASSEGNAZIONE,RITORNO,INVENTARIO',
            'movement_date'           => 'required|date',
            'product_id'              => 'required|exists:warehouse_products,id',
            'quantity'                => 'required|integer|min:1',
            'source_location_id'      => 'nullable|exists:warehouse_locations,id',
            'destination_location_id' => 'nullable|exists:warehouse_locations,id',
            'assigned_to_user_id'     => 'nullable|exists:internal_employees,id',
            'notes'                   => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        DB::transaction(function () use ($validated) {
            // Create the movement log
            WarehouseMovement::create($validated);

            $type   = $validated['movement_type'];
            $pId    = $validated['product_id'];
            $qty    = $validated['quantity'];
            $src    = $validated['source_location_id'] ?? null;
            $dst    = $validated['destination_location_id'] ?? null;

            // Update stock levels based on movement type
            if (in_array($type, ['CARICO', 'RITORNO']) && $dst) {
                // Add to destination
                $stock = WarehouseStock::firstOrCreate(
                    ['product_id' => $pId, 'location_id' => $dst],
                    ['quantity' => 0]
                );
                $stock->increment('quantity', $qty);
            }

            if (in_array($type, ['SCARICO', 'ASSEGNAZIONE']) && $src) {
                // Remove from source
                $stock = WarehouseStock::where(['product_id' => $pId, 'location_id' => $src])->first();
                if ($stock) {
                    $stock->decrement('quantity', $qty);
                }
            }

            if (in_array($type, ['TRASFERIMENTO', 'SMISTAMENTO']) && $src && $dst) {
                // Remove from source, add to destination
                $srcStock = WarehouseStock::where(['product_id' => $pId, 'location_id' => $src])->first();
                if ($srcStock) {
                    $srcStock->decrement('quantity', $qty);
                }
                $dstStock = WarehouseStock::firstOrCreate(
                    ['product_id' => $pId, 'location_id' => $dst],
                    ['quantity' => 0]
                );
                $dstStock->increment('quantity', $qty);
            }

            if ($type === 'INVENTARIO' && $dst) {
                // Set exact quantity (inventory correction)
                $stock = WarehouseStock::firstOrCreate(
                    ['product_id' => $pId, 'location_id' => $dst],
                    ['quantity' => 0]
                );
                $stock->update(['quantity' => $qty]);
            }
        });

        return redirect()->route('magazzino.movimenti.index')
                         ->with('success', 'Movimento registrato con successo. Le giacenze sono state aggiornate automaticamente.');
    }
}

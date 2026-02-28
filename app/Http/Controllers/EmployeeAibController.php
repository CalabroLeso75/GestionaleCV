<?php

namespace App\Http\Controllers;

use App\Models\InternalEmployee;
use Illuminate\Http\Request;

class EmployeeAibController extends Controller
{
    public function index(Request $request)
    {
        $query = InternalEmployee::query();

        // Search by name, surname or tax code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('tax_code', 'like', "%{$search}%");
            });
        }
        
        // Filter by Qualification Status
        if ($request->filled('status')) {
            if ($request->status === 'qualified') {
                $query->where('is_aib_qualified', true);
            } elseif ($request->status === 'unqualified') {
                $query->where('is_aib_qualified', false)->orWhereNull('is_aib_qualified');
            }
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->paginate(50);

        // Append search parameters to pagination links
        $employees->appends($request->all());

        return view('pc.aib.personnel.index', compact('employees'));
    }

    public function toggleAib(Request $request, InternalEmployee $employee)
    {
        // Toggle the AIB qualification status
        $employee->is_aib_qualified = !$employee->is_aib_qualified;
        $employee->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_aib_qualified' => $employee->is_aib_qualified,
                'message' => 'Qualifica AIB aggiornata con successo.'
            ]);
        }

        return redirect()->back()->with('success', 'Qualifica AIB aggiornata per ' . $employee->first_name . ' ' . $employee->last_name . '.');
    }
}

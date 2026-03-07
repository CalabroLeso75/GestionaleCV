<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class DbManagerController extends Controller
{
    /**
     * Show the main DB Manager interface.
     */
    public function index(Request $request)
    {
        $tables = $this->getAllTables();
        $selectedTable = $request->input('table');
        
        $tableData = null;
        $columns = [];
        $queryResult = null;
        $queryError = null;
        $sqlQuery = $request->input('query');
        
        // Handle explicit SQL Query Execution
        if ($request->isMethod('post') && $request->has('action') && $request->action === 'query') {
            try {
                // Determine if it's a SELECT or a write operation
                $isSelect = stripos(trim($sqlQuery), 'select') === 0 || stripos(trim($sqlQuery), 'show') === 0 || stripos(trim($sqlQuery), 'describe') === 0;
                
                if ($isSelect) {
                    $queryResult = DB::select($sqlQuery);
                } else {
                    $queryResult = DB::statement($sqlQuery);
                    $queryResult = "Query eseguita con successo.";
                }
                
                // If it's a SELECT, extract columns from the first object
                if (is_array($queryResult) && count($queryResult) > 0) {
                    $columns = array_keys((array)$queryResult[0]);
                }
                
            } catch (\Exception $e) {
                $queryError = $e->getMessage();
            }
        } 
        // Handle Table Browsing
        elseif ($selectedTable && in_array($selectedTable, $tables)) {
            try {
                $columns = Schema::getColumnListing($selectedTable);
                
                // Simple pagination for table browsing
                $perPage = 50;
                $page = $request->input('page', 1);
                $total = DB::table($selectedTable)->count();
                
                $items = DB::table($selectedTable)
                           ->skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();
                           
                $tableData = new LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $page,
                    ['path' => route('admin.dbmanager.index', ['table' => $selectedTable])]
                );
                
            } catch (\Exception $e) {
                $queryError = "Errore durante il caricamento della tabella: " . $e->getMessage();
            }
        }

        return view('admin.dbmanager.index', compact('tables', 'selectedTable', 'tableData', 'columns', 'sqlQuery', 'queryResult', 'queryError'));
    }

    /**
     * Get all tables in the database.
     */
    private function getAllTables()
    {
        $tables = [];
        // Support for MySQL/MariaDB
        $query = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        $key = "Tables_in_{$dbName}";
        
        foreach ($query as $tableObj) {
            // Fallback if the key case is slightly different
            $objArray = (array)$tableObj;
            $tableName = array_values($objArray)[0];
            $tables[] = $tableName;
        }
        
        sort($tables);
        return $tables;
    }
}

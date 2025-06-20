<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\TableStatus;
class AdminTableController extends Controller
{
    public function index(Request $request)
    {
        $tables = Table::with('status')->get();
        $statuses = TableStatus::all();
        $editTable = null;

        if ($request->has('edit')) {
            $editTable = Table::find($request->edit);
        }

        return view('admin.pages.table', [
            'tables' => $tables,
            'statuses' => $statuses,
            'editTable' => $editTable
        ]);
    }

    public function store(Request $request)
    {
        Table::create($request->only('name', 'qr_table', 'table_status_id'));
        return redirect()->route('table.index');
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->update($request->only('name', 'qr_table', 'table_status_id'));
        return redirect()->route('table.index');
    }

    public function destroy($id)
    {
        Table::destroy($id);
        return redirect()->route('table.index');
    }
}

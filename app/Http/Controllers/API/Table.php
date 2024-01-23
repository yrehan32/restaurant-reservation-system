<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Table\CreateRequest;
use App\Http\Requests\Table\UpdateRequest;
use App\Models\Table as ModelsTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Table extends Controller
{
    public function get() : JsonResponse
    {
        $tables = ModelsTable::all();

        return response()->json([
            'error' => false,
            'message' => 'Table retrieved successfully.',
            'data' => [
                'tables' => $tables,
            ],
        ]);
    }

    public function getById($id) : JsonResponse
    {
        $table = ModelsTable::findOrFail($id);

        return response()->json([
            'error' => false,
            'message' => 'Table retrieved successfully.',
            'data' => [
                'table' => $table,
            ],
        ]);
    }

    public function create(CreateRequest $request) : JsonResponse
    {
        $table = ModelsTable::create($request->validated());

        return response()->json([
            'error' => false,
            'message' => 'Table created successfully.',
            'data' => [
                'table' => $table,
            ],
        ]);
    }

    public function update(UpdateRequest $request, $id) : JsonResponse
    {
        $table = ModelsTable::findOrFail($id);

        $table->update($request->validated());

        return response()->json([
            'error' => false,
            'message' => 'Table updated successfully.',
            'data' => [
                'table' => $table,
            ],
        ]);
    }

    public function delete($id) : JsonResponse
    {
        $table = ModelsTable::findOrFail($id);

        $table->delete();

        return response()->json([
            'error' => false,
            'message' => 'Table deleted successfully.',
            'data' => null,
        ]);
    }
}

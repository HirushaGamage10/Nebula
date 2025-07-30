<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ModuleCreationController extends Controller
{
    public function create()
    {
        $modules = Module::orderBy('module_name', 'asc')->get();
        return view('module_creation', compact('modules'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'module_name' => 'required|string|max:255',
                'module_code' => 'required|string|max:100|unique:modules,module_code',
                'credits' => 'required|integer|min:0',
                'module_type' => ['required', Rule::in(['core', 'elective', 'special_unit_compulsory'])],
            ]);

            $module = Module::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Module created successfully.',
                'module' => $module
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing module data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the module.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $module = Module::find($id);
        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found.'
            ], 404);
        }

        $validatedData = $request->validate([
            'module_name' => 'sometimes|required|string|max:255',
            'module_code' => 'sometimes|required|string|max:100|unique:modules,module_code,' . $id . ',module_id',
            'credits' => 'sometimes|required|integer|min:0',
            'module_type' => ['sometimes', 'required', Rule::in(['core', 'elective', 'special_unit_compulsory'])],
        ]);

        $module->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Module updated successfully.',
            'module' => $module
        ]);
    }
}

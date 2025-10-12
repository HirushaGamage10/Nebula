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

            $validatedData['module_name'] = collect(explode(' ', $validatedData['module_name']))
                ->map(function ($word) {
                    // If the word is ALL CAPS (e.g., CCP), keep it as is
                    if (preg_match('/^[A-Z0-9]+$/', $word)) {
                        return $word;
                    }
                    // Otherwise, make only first letter uppercase
                    return ucfirst(strtolower($word));
                })
                ->implode(' ');

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
            'module_code' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                'unique:modules,module_code,' . $id . ',module_id',
                'regex:/^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/'
            ],
            'credits' => 'sometimes|required|integer|min:0',
            'module_type' => ['sometimes', 'required', Rule::in(['core', 'elective', 'special_unit_compulsory'])],
        ], [
            'module_code.regex' => 'Module code must follow the pattern: program_name_specification_unit_code (e.g., CS101_Programming_001)'
        ]);

        if (isset($validatedData['module_name'])) {
            $validatedData['module_name'] = collect(explode(' ', $validatedData['module_name']))
                ->map(function ($word) {
                    if (preg_match('/^[A-Z0-9]+$/', $word)) {
                        return $word;
                    }
                    return ucfirst(strtolower($word));
                })
                ->implode(' ');
        }

        $module->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Module updated successfully.',
            'module' => $module
        ]);
    }

    public function destroy($id)
    {
        try {
            $module = Module::find($id);
            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module not found.'
                ], 404);
            }

            $module->delete();

            return response()->json([
                'success' => true,
                'message' => 'Module deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting module: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the module.'
            ], 500);
        }
    }


}

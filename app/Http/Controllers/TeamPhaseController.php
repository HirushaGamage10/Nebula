<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamPhaseController extends Controller
{
    public function index()
    {
        $phases = Phase::with(['teams.roles'])->orderBy('id', 'asc')->get();
        return view('team_phase.index', compact('phases'));
    }

    public function createPhase(Request $request)
    {
        $data = $request->validate([
            'phase_id' => 'required|unique:phases,phase_id',
            'phase_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'supervisors' => 'nullable|array',
        ]);

        if (isset($data['supervisors'])) {
            $data['supervisors'] = json_encode($data['supervisors']);
        }

        Phase::create($data);
        return back()->with('success', '✅ Phase created successfully!');
    }

    public function assignMember(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'p_no' => 'nullable|string|max:20',
            'link1' => 'nullable|string|max:255',
            'link2' => 'nullable|string|max:255',
            'phase_id' => 'required|exists:phases,id',
            'roles' => 'required|array|min:1',
        ]);

        // ✅ If same person already exists in this phase, reuse that record
        $team = Team::where('name', $data['name'])
                    ->where('phase_id', $data['phase_id'])
                    ->first();

        // ✅ Upload image if provided
        if ($request->hasFile('profile_pic')) {
            $data['profile_pic'] = $request->file('profile_pic')->store('team_profiles', 'public');
        }

        // ✅ If not exists, create new team record for this phase
        if (!$team) {
            $team = Team::create([
                'name' => $data['name'],
                'p_no' => $data['p_no'] ?? null,
                'link1' => $data['link1'] ?? null,
                'link2' => $data['link2'] ?? null,
                'phase_id' => $data['phase_id'],
                'profile_pic' => $data['profile_pic'] ?? null,
            ]);
        }

        // ✅ Remove old roles (for this phase only)
        $team->roles()->delete();

        // ✅ Add new roles
        $team->roles()->createMany(
            collect($data['roles'])->map(fn($r) => ['role' => $r])->toArray()
        );

        return back()->with('success', '✅ Member assigned successfully!');
    }
}

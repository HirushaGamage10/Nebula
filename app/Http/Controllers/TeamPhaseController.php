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
        'phases' => 'required|array|min:1',
        'roles' => 'required|array',
    ]);

    if ($request->hasFile('profile_pic')) {
        $profilePic = $request->file('profile_pic')->store('team_profiles', 'public');
    }

    foreach ($data['phases'] as $phaseId) {

        // Create or find team member for this phase
        $team = Team::firstOrCreate(
            [
                'name' => $data['name'],
                'phase_id' => $phaseId,
            ],
            [
                'p_no' => $data['p_no'] ?? null,
                'link1' => $data['link1'] ?? null,
                'link2' => $data['link2'] ?? null,
                'profile_pic' => $profilePic ?? null,
            ]
        );

        // Remove old roles for this phase
        $team->roles()->delete();

        // If roles were selected for this phase
        if (isset($data['roles'][$phaseId])) {
            $team->roles()->createMany(
                collect($data['roles'][$phaseId])->map(fn($r) => ['role' => $r])->toArray()
            );
        }
    }

    return back()->with('success', 'Member assigned successfully!');
}

}

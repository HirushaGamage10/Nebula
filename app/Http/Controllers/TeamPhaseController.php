<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeamPhaseController extends Controller
{
    public function __construct()
    {
        // Check if user is authenticated and has Developer role
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            
            // Allow all GET requests for authenticated users
            if ($request->isMethod('GET')) {
                return $next($request);
            }
            
            // Check if user has Developer role in users table
            if ($user && strtolower($user->user_role) === 'developer') {
                return $next($request);
            }
            
            // Deny POST/PUT/DELETE for non-Developers
            return back()->with('error', 'Access denied. Developer role required for modifications.');
        });
    }

    public function index()
    {
        $phases = Phase::with(['teams.roles'])->orderBy('id', 'asc')->get();
        $isDeveloper = Auth::check() && strtolower(Auth::user()->user_role) === 'developer';
        
        return view('team_phase.index', compact('phases', 'isDeveloper'));
    }

    public function createPhase(Request $request)
    {
        $data = $request->validate([
            'phase_id' => 'required|unique:phases,phase_id',
            'phase_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'supervisors' => 'nullable|array',
            'supervisors.*.name' => 'required|string|max:255',
            'supervisors.*.designation' => 'required|string|max:255',
        ]);

        // Process supervisors JSON
        if (isset($data['supervisors'])) {
            $data['supervisors'] = $this->processSupervisors($data['supervisors']);
        }

        Phase::create($data);
        return back()->with('success', '✅ Phase created successfully!');
    }

    public function updatePhase(Request $request, Phase $phase)
    {
        $data = $request->validate([
            'phase_id' => 'required|unique:phases,phase_id,' . $phase->id,
            'phase_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'supervisors' => 'nullable|array',
            'supervisors.*.name' => 'required|string|max:255',
            'supervisors.*.designation' => 'required|string|max:255',
        ]);

        // Process supervisors JSON
        if (isset($data['supervisors'])) {
            $data['supervisors'] = $this->processSupervisors($data['supervisors']);
        } else {
            // Keep existing supervisors if not provided
            $data['supervisors'] = $phase->supervisors;
        }

        $phase->update($data);
        return back()->with('success', '✅ Phase updated successfully!');
    }

    public function deletePhase(Phase $phase)
    {
        // Delete all team members and their roles for this phase first
        foreach ($phase->teams as $team) {
            $team->roles()->delete();
        }
        $phase->teams()->delete();
        $phase->delete();
        
        return back()->with('success', '✅ Phase deleted successfully!');
    }

    public function assignMember(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'p_no' => 'nullable|string|max:20',
            'link1' => 'nullable|url|max:255',
            'link2' => 'nullable|url|max:255',
            'phases' => 'required|array|min:1',
            'roles' => 'required|array',
        ]);

        // Handle profile picture upload
        $profilePic = null;
        if ($request->hasFile('profile_pic')) {
            $profilePic = $request->file('profile_pic')->store('team_profiles', 'public');
        }

        foreach ($data['phases'] as $phaseId) {
            // Create team member for each phase
            $team = Team::create([
                'name' => $data['name'],
                'phase_id' => $phaseId,
                'p_no' => $data['p_no'] ?? null,
                'link1' => $data['link1'] ?? null,
                'link2' => $data['link2'] ?? null,
                'profile_pic' => $profilePic,
            ]);

            // Add roles for this phase
            if (isset($data['roles'][$phaseId])) {
                $team->roles()->createMany(
                    collect($data['roles'][$phaseId])->map(fn($r) => ['role' => $r])->toArray()
                );
            }
        }

        return back()->with('success', 'Team member assigned to phases successfully!');
    }

    public function updateMember(Request $request, Team $team)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'p_no' => 'nullable|string|max:20',
            'link1' => 'nullable|url|max:255',
            'link2' => 'nullable|url|max:255',
            'roles' => 'required|array',
        ]);

        // Update profile picture if provided
        if ($request->hasFile('profile_pic')) {
            // Delete old picture if exists
            if ($team->profile_pic) {
                Storage::disk('public')->delete($team->profile_pic);
            }
            $data['profile_pic'] = $request->file('profile_pic')->store('team_profiles', 'public');
        }

        $team->update($data);

        // Update roles
        $team->roles()->delete();
        if (isset($data['roles'])) {
            $team->roles()->createMany(
                collect($data['roles'])->map(fn($r) => ['role' => $r])->toArray()
            );
        }

        return back()->with('success', 'Team member updated successfully!');
    }

    public function deleteMember(Team $team)
    {
        // Delete profile picture if exists
        if ($team->profile_pic) {
            Storage::disk('public')->delete($team->profile_pic);
        }
        
        // Delete roles
        $team->roles()->delete();
        $team->delete();
        
        return back()->with('success', 'Team member deleted successfully!');
    }

    public function addMemberToPhase(Request $request, Team $team)
    {
        $data = $request->validate([
            'phase_id' => 'required|exists:phases,id',
            'roles' => 'required|array',
        ]);

        // Check if member already exists in this phase
        $existingTeam = Team::where('name', $team->name)
            ->where('phase_id', $data['phase_id'])
            ->first();
            
        if ($existingTeam) {
            return back()->with('error', 'This team member already exists in the selected phase.');
        }

        // Clone member to new phase
        $newTeam = Team::create([
            'name' => $team->name,
            'phase_id' => $data['phase_id'],
            'p_no' => $team->p_no,
            'link1' => $team->link1,
            'link2' => $team->link2,
            'profile_pic' => $team->profile_pic,
        ]);

        // Add roles for new phase
        $newTeam->roles()->createMany(
            collect($data['roles'])->map(fn($r) => ['role' => $r])->toArray()
        );

        return back()->with('success', 'Team member added to phase successfully!');
    }

    public function removeMemberFromPhase(Team $team, Phase $phase)
    {
        // Find and delete the team member for this specific phase
        $teamMember = Team::where('name', $team->name)
            ->where('phase_id', $phase->id)
            ->first();
            
        if ($teamMember) {
            $teamMember->roles()->delete();
            $teamMember->delete();
        }

        return back()->with('success', 'Team member removed from phase successfully!');
    }

    /**
     * Process supervisors array to remove empty entries
     */
    private function processSupervisors($supervisors)
    {
        if (!is_array($supervisors)) {
            return null;
        }

        // Filter out empty entries
        $filtered = array_filter($supervisors, function($supervisor) {
            return !empty(trim($supervisor['name'])) && !empty(trim($supervisor['designation']));
        });

        // Reset array keys
        return array_values($filtered);
    }
}
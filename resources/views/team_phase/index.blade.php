@extends('inc.app')

@section('title', 'Team & Phase Management | Nebula')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary">Team and Phase Management</h3>
        <small class="text-muted">Manage project phases and team members</small>
      </div>

      <!-- Add New Phase -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
          <h5 class="fw-semibold text-dark mb-0">Add New Phase</h5>
        </div>
        <div class="card-body bg-white">
          <form method="POST" action="{{ route('phase.create') }}" class="row g-3">
            @csrf
            <div class="col-md-3">
              <label class="form-label fw-semibold">Phase ID</label>
              <input type="text" name="phase_id" class="form-control" placeholder="PH001" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Phase Name</label>
              <input type="text" name="phase_name" class="form-control" placeholder="Design Phase" required>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Start Date</label>
              <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">End Date</label>
              <input type="date" name="end_date" class="form-control" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Assign Team Member -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
          <h5 class="fw-semibold text-dark mb-0">Assign Team Member to Multiple Phases</h5>
        </div>
        <div class="card-body bg-white">
          <form method="POST" action="{{ route('team.assign') }}" enctype="multipart/form-data" id="assignForm">
            @csrf
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label class="form-label fw-semibold">Member Name</label>
                <input type="text" name="name" class="form-control" placeholder="Savindu Fernando" required>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Phone No</label>
                <input type="text" name="p_no" class="form-control" placeholder="07XXXXXXXX">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Links</label>
                <input type="url" name="link1" class="form-control mb-2" placeholder="LinkedIn">
                <input type="url" name="link2" class="form-control" placeholder="Other link">
              </div>
            </div>

            <!-- Phase selection -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Select Phases</label>
              <div class="d-flex flex-wrap gap-3">
                @foreach($phases as $phase)
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input phase-checkbox" id="phase_{{ $phase->id }}" name="phases[]" value="{{ $phase->id }}">
                    <label class="form-check-label" for="phase_{{ $phase->id }}">{{ $phase->phase_name }}</label>
                  </div>
                @endforeach
              </div>
            </div>

            <!-- Dynamic roles container -->
            <div id="roleContainer" class="mt-3"></div>

            <div class="text-end mt-3">
              <button type="submit" class="btn btn-success px-4">Assign to Phases</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Display Phases & Team Members -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <h5 class="fw-semibold text-dark mb-0">All Phases & Team Members</h5>
        </div>
        <div class="card-body bg-white">
          @foreach($phases as $phase)
            <div class="mb-4">
              <h5 class="fw-semibold text-primary">{{ $phase->phase_name }}</h5>
              <p class="text-muted small mb-3">
                <i class="ti ti-calendar"></i> {{ $phase->start_date }} → {{ $phase->end_date }}
              </p>
              <div class="row g-3">
                @forelse($phase->teams as $team)
                  <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 team-member-card" 
                         data-bs-toggle="modal" data-bs-target="#memberModal_{{ $team->id }}">
                      <div class="card-body d-flex align-items-center gap-3">
                        <img src="{{ $team->profile_pic ? asset('storage/'.$team->profile_pic) : asset('images/default-user.png') }}"
                             alt="Profile" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                        <div class="flex-grow-1">
                          <h6 class="fw-semibold mb-0">{{ $team->name }}</h6>
                          <small class="text-muted">{{ $team->p_no ?? 'No contact' }}</small>
                          <div class="mt-2">
                            @foreach($team->roles as $role)
                              @php
                                $badgeColor = match(strtolower($role->role)) {
                                  'leader' => 'primary',
                                  'developer' => 'success',
                                  'ba' => 'info',
                                  'qa' => 'warning',
                                  'devops' => 'dark',
                                  default => 'secondary'
                                };
                              @endphp
                              <span class="badge bg-{{ $badgeColor }} me-1 text-uppercase">{{ $role->role }}</span>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Modal for each member -->
                    <div class="modal fade" id="memberModal_{{ $team->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">{{ $team->name }} - Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">

                            <!-- Profile picture + roles -->
                            <div class="text-center mb-3">
                              <img src="{{ $team->profile_pic ? asset('storage/'.$team->profile_pic) : asset('images/default-user.png') }}"
                                   class="rounded-circle mb-2" width="120" height="120">
                              <h4>{{ $team->name }}</h4>
                              @foreach ($team->roles as $r)
                                <span class="badge bg-primary">{{ $r->role }}</span>
                              @endforeach
                            </div>

                            <!-- LinkedIn visual preview -->
                            @if($team->link1)
                              <div class="card shadow-sm border-0 p-3">
                                <div class="d-flex align-items-center mb-2">
                                  <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/LinkedIn_logo_initials.png" 
                                       width="40" class="me-3">
                                  <div>
                                    <h5 class="mb-0">LinkedIn Profile</h5>
                                    <small class="text-muted">Connected account</small>
                                  </div>
                                </div>
                                <hr>
                                <p class="mb-1"><strong>{{ $team->name }}</strong></p>
                                <p class="text-muted">Click the button below to view full LinkedIn details.</p>
                                <a href="{{ $team->link1 }}" target="_blank" class="btn btn-primary w-100">View LinkedIn Profile</a>
                              </div>
                            @endif

                            @if($team->link2)
                              <div class="mt-3">
                                <a href="{{ $team->link2 }}" target="_blank" class="btn btn-outline-secondary w-100">Visit Other Link</a>
                              </div>
                            @endif

                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                @empty
                  <div class="col-12"><p class="text-muted">No members yet.</p></div>
                @endforelse
              </div>
            </div>
            <hr>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>

<!-- JS for dynamic role checkboxes -->
<script>
document.querySelectorAll('.phase-checkbox').forEach(cb => {
  cb.addEventListener('change', function() {
    const container = document.getElementById('roleContainer');
    container.innerHTML = '';
    document.querySelectorAll('.phase-checkbox:checked').forEach(sel => {
      const phaseId = sel.value;
      const phaseName = sel.nextElementSibling.innerText;
      const html = `
        <div class="card mt-3 border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-semibold mb-2 text-primary">${phaseName} Roles</h6>
            <div class="d-flex flex-wrap gap-3">
              ${['Leader','Developer','BA','QA','DevOps'].map(r => `
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="roles[${phaseId}][]" value="${r}" id="${r}_${phaseId}">
                  <label class="form-check-label" for="${r}_${phaseId}">${r.toUpperCase()}</label>
                </div>`).join('')}
            </div>
          </div>
        </div>`;
      container.insertAdjacentHTML('beforeend', html);
    });
  });
});
</script>
@endsection

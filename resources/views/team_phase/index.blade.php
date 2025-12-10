@extends('inc.app')

@section('title', 'Team & Phase Management | Nebula')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary">Team and Phase Management</h3>
        <div>
          <small class="text-muted">Manage project phases and team members</small>
          @if($isDeveloper)
            <span class="badge bg-success ms-2">Developer Mode</span>
          @endif
        </div>
      </div>

      @if($isDeveloper)
      <!-- Add New Phase -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
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
      @endif

      <!-- Display Phases & Team Members -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h5 class="fw-semibold text-dark mb-0">All Phases & Team Members</h5>
          @if(!$isDeveloper)
            <small class="text-muted">View Only Mode</small>
          @endif
        </div>
        <div class="card-body bg-white">
          @foreach($phases as $phase)
            <div class="mb-4 phase-container" data-phase-id="{{ $phase->id }}">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-semibold text-primary">{{ $phase->phase_name }}
                  <small class="text-muted">(ID: {{ $phase->phase_id }})</small>
                </h5>
                @if($isDeveloper)
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPhaseModal_{{ $phase->id }}">
                    <i class="ti ti-edit"></i> Edit
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('phase.delete', $phase) }}', 'phase')">
                    <i class="ti ti-trash"></i> Delete
                  </button>
                </div>
                @endif
              </div>
              <p class="text-muted small mb-3">
                <i class="ti ti-calendar"></i> {{ $phase->start_date }} → {{ $phase->end_date }}
              </p>
              
              <div class="row g-3">
                @forelse($phase->teams as $team)
                  <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 team-member-card">
                      <div class="card-body d-flex align-items-center gap-3">
                        <img src="{{ $team->profile_pic ? asset('storage/'.$team->profile_pic) : asset('images/default-user.png') }}"
                             alt="Profile" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                        <div class="flex-grow-1">
                          <div class="d-flex justify-content-between align-items-start">
                            <div>
                              <h6 class="fw-semibold mb-0">{{ $team->name }}</h6>
                              <small class="text-muted">{{ $team->p_no ?? 'No contact' }}</small>
                            </div>
                            @if($isDeveloper)
                            <div class="dropdown">
                              <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                              </button>
                              <ul class="dropdown-menu">
                                <li>
                                  <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editMemberModal_{{ $team->id }}">
                                    <i class="ti ti-edit me-2"></i> Edit
                                  </button>
                                </li>
                                <li>
                                  <button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('team.delete', $team) }}', 'member')">
                                    <i class="ti ti-trash me-2"></i> Delete
                                  </button>
                                </li>
                                <li>
                                  <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addToPhaseModal_{{ $team->id }}">
                                    <i class="ti ti-plus me-2"></i> Add to Another Phase
                                  </button>
                                </li>
                                <li>
                                  <button class="dropdown-item text-warning" onclick="confirmRemovePhase('{{ route('team.remove-phase', ['team' => $team, 'phase' => $phase]) }}')">
                                    <i class="ti ti-x me-2"></i> Remove from this Phase
                                  </button>
                                </li>
                              </ul>
                            </div>
                            @endif
                          </div>
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
                      <!-- View Details Button (for all roles) -->
                      <div class="card-footer bg-transparent border-0 pt-0">
                        <button class="btn btn-sm btn-outline-info w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#memberModal_{{ $team->id }}">
                          <i class="ti ti-eye"></i> View Details
                        </button>
                      </div>
                    </div>

                    <!-- View Details Modal -->
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

                    <!-- Edit Member Modal (Developer only) -->
                    @if($isDeveloper)
                    <div class="modal fade" id="editMemberModal_{{ $team->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit {{ $team->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST" action="{{ route('team.update', $team) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Phone No</label>
                                <input type="text" name="p_no" class="form-control" value="{{ $team->p_no }}">
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_pic" class="form-control">
                                @if($team->profile_pic)
                                  <small class="text-muted">Current: {{ basename($team->profile_pic) }}</small>
                                @endif
                              </div>
                              <div class="mb-3">
                                <label class="form-label">LinkedIn URL</label>
                                <input type="url" name="link1" class="form-control" value="{{ $team->link1 }}">
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Other Link</label>
                                <input type="url" name="link2" class="form-control" value="{{ $team->link2 }}">
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Roles in {{ $phase->phase_name }}</label>
                                <div class="d-flex flex-wrap gap-3">
                                  @foreach(['Leader','Developer','BA','QA','DevOps'] as $role)
                                    <div class="form-check">
                                      <input class="form-check-input" type="checkbox" 
                                             name="roles[]" 
                                             value="{{ $role }}"
                                             id="edit_role_{{ $role }}_{{ $team->id }}"
                                             {{ $team->roles->contains('role', $role) ? 'checked' : '' }}>
                                      <label class="form-check-label" for="edit_role_{{ $role }}_{{ $team->id }}">
                                        {{ $role }}
                                      </label>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Add to Another Phase Modal -->
                    <div class="modal fade" id="addToPhaseModal_{{ $team->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Add {{ $team->name }} to Another Phase</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST" action="{{ route('team.add-phase', $team) }}">
                            @csrf
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label">Select Phase</label>
                                <select name="phase_id" class="form-select" required>
                                  <option value="">Choose a phase...</option>
                                  @foreach($phases->where('id', '!=', $phase->id) as $otherPhase)
                                    <option value="{{ $otherPhase->id }}">{{ $otherPhase->phase_name }}</option>
                                  @endforeach
                                </select>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Roles in New Phase</label>
                                <div class="d-flex flex-wrap gap-3">
                                  @foreach(['Leader','Developer','BA','QA','DevOps'] as $role)
                                    <div class="form-check">
                                      <input class="form-check-input" type="checkbox" 
                                             name="roles[]" 
                                             value="{{ $role }}"
                                             id="new_role_{{ $role }}_{{ $team->id }}">
                                      <label class="form-check-label" for="new_role_{{ $role }}_{{ $team->id }}">
                                        {{ $role }}
                                      </label>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-success">Add to Phase</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    @endif
                  </div>
                @empty
                  <div class="col-12">
                    <p class="text-muted">No members assigned to this phase.</p>
                  </div>
                @endforelse
              </div>
            </div>

            <!-- Edit Phase Modal (Developer only) -->
            @if($isDeveloper)
            <div class="modal fade" id="editPhaseModal_{{ $phase->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit {{ $phase->phase_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form method="POST" action="{{ route('phase.update', $phase) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Phase ID</label>
                        <input type="text" name="phase_id" class="form-control" value="{{ $phase->phase_id }}" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Phase Name</label>
                        <input type="text" name="phase_name" class="form-control" value="{{ $phase->phase_name }}" required>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Start Date</label>
                          <input type="date" name="start_date" class="form-control" value="{{ $phase->start_date }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">End Date</label>
                          <input type="date" name="end_date" class="form-control" value="{{ $phase->end_date }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            @endif
            
            @if(!$loop->last)<hr>@endif
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="deleteMessage">Are you sure you want to delete this?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST" action="">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
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

function confirmDelete(url, type) {
  const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
  const message = type === 'phase' 
    ? 'Are you sure you want to delete this phase? This will also delete all team members and their roles in this phase.'
    : 'Are you sure you want to delete this team member?';
  
  document.getElementById('deleteMessage').textContent = message;
  document.getElementById('deleteForm').action = url;
  modal.show();
}

function confirmRemovePhase(url) {
  if (confirm('Are you sure you want to remove this member from this phase? This will not delete the member from other phases.')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);
    
    document.body.appendChild(form);
    form.submit();
  }
}
</script>

<style>
.team-member-card:hover {
  transform: translateY(-2px);
  transition: transform 0.2s;
  cursor: pointer;
}

.dropdown-menu {
  min-width: 200px;
}

.badge {
  font-size: 0.7rem;
  padding: 0.35em 0.65em;
}
</style>
@endsection
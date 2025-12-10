

<?php $__env->startSection('title', 'Team & Phase Management | Nebula'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-12">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary">Nebula IT Team</h3>
        <div>
          <small class="text-muted">Manage project phases and team members</small>
          <?php if($isDeveloper): ?>
            <span class="badge bg-success ms-2">Developer Mode</span>
          <?php endif; ?>
        </div>
      </div>

      <?php if($isDeveloper): ?>
      <!-- Add New Phase -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h5 class="fw-semibold text-dark mb-0">Add New Phase</h5>
        </div>
        <div class="card-body bg-white">
          <form method="POST" action="<?php echo e(route('phase.create')); ?>" class="row g-3" id="addPhaseForm">
            <?php echo csrf_field(); ?>
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
            
            <!-- Supervisors Section -->
            <div class="col-12">
              <label class="form-label fw-semibold">Supervisors</label>
              <div id="supervisorsContainer">
                <div class="row supervisor-row mb-2">
                  <div class="col-md-6">
                    <input type="text" name="supervisors[0][name]" class="form-control" placeholder="Supervisor Name">
                  </div>
                  <div class="col-md-5">
                    <input type="text" name="supervisors[0][designation]" class="form-control" placeholder="Designation">
                  </div>
                  <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-supervisor" style="display: none;">
                      <i class="ti ti-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addSupervisorBtn">
                <i class="ti ti-plus"></i> Add Supervisor
              </button>
            </div>
            
            <div class="col-12 d-flex align-items-end justify-content-end">
              <button type="submit" class="btn btn-primary px-4">Add Phase</button>
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
          <form method="POST" action="<?php echo e(route('team.assign')); ?>" enctype="multipart/form-data" id="assignForm">
            <?php echo csrf_field(); ?>
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
                <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input phase-checkbox" id="phase_<?php echo e($phase->id); ?>" name="phases[]" value="<?php echo e($phase->id); ?>">
                    <label class="form-check-label" for="phase_<?php echo e($phase->id); ?>"><?php echo e($phase->phase_name); ?></label>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
      <?php endif; ?>

      <!-- Display Phases & Team Members -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h5 class="fw-semibold text-dark mb-0">All Phases & Team Members</h5>
          
          <!-- Phase Filter Buttons -->
          <div class="d-flex align-items-center">
            <div class="btn-group btn-group-sm me-3" role="group" aria-label="Phase filter">
              <input type="radio" class="btn-check" name="phaseFilter" id="filterAll" checked onclick="filterPhases('all')">
              <label class="btn btn-outline-primary" for="filterAll">All</label>
              <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="radio" class="btn-check" name="phaseFilter" id="filter<?php echo e($phase->id); ?>" onclick="filterPhases('<?php echo e($phase->id); ?>')">
                <label class="btn btn-outline-primary" for="filter<?php echo e($phase->id); ?>"><?php echo e($phase->phase_name); ?></label>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <?php if(!$isDeveloper): ?>
              <small class="text-muted">View Only Mode</small>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- Hardcoded Image Slider -->
        <div id="teamImageSlider" class="carousel slide mb-4" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="<?php echo e(asset('images/team/1.jpg')); ?>" class="d-block w-100" alt="Image 1">
            </div>
            <div class="carousel-item">
              <img src="<?php echo e(asset('images/team/3.jpg')); ?>" class="d-block w-100" alt="Image 2">
            </div>
            <div class="carousel-item">
              <img src="<?php echo e(asset('images/team/4.jpg')); ?>" class="d-block w-100" alt="Image 5">
            </div>
            <div class="carousel-item">
              <img src="<?php echo e(asset('images/team/5.jpg')); ?>" class="d-block w-100" alt="Image 5">
            </div>
            <div class="carousel-item">
              <img src="<?php echo e(asset('images/team/7.jpg')); ?>" class="d-block w-100" alt="Image 5">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#teamImageSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#teamImageSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>

        <div class="card-body bg-white">
          <!-- Two columns layout for phases -->
          <div class="row g-2" id="phasesContainer">
            <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="col-lg-6 mb-4 phase-container" data-phase-id="<?php echo e($phase->id); ?>">
                <div class="border rounded-3 p-3 bg-light h-100">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-semibold text-primary mb-0"><?php echo e($phase->phase_name); ?>

                      <small class="text-muted">(ID: <?php echo e($phase->phase_id); ?>)</small>
                    </h5>
                    <?php if($isDeveloper): ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPhaseModal_<?php echo e($phase->id); ?>">
                        <i class="ti ti-edit"></i> Edit
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('<?php echo e(route('phase.delete', $phase)); ?>', 'phase')">
                        <i class="ti ti-trash"></i> Delete
                      </button>
                    </div>
                    <?php endif; ?>
                  </div>
                  <p class="text-muted small mb-3">
                    <i class="ti ti-calendar"></i> <?php echo e($phase->start_date); ?> → <?php echo e($phase->end_date); ?>

                  </p>
                  
                  <!-- Supervisors Display - 3 per row -->
                  <?php if($phase->supervisors && count($phase->supervisors) > 0): ?>
                  <div class="mb-3">
                    <h6 class="fw-semibold text-secondary mb-2">
                      <i class="ti ti-user-check"></i> Supervisors
                    </h6>
                    <div class="row g-2">
                      <?php $__currentLoopData = $phase->supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                        <div class="card border-0 shadow-sm bg-white h-100">
                          <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-start">
                              <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold text-dark" style="font-size: 0.9rem;"><?php echo e($supervisor['name']); ?></h6>
                                <small class="text-muted" style="font-size: 0.8rem;"><?php echo e($supervisor['designation']); ?></small>
                              </div>
                              <?php if($isDeveloper): ?>
                              <button class="btn btn-sm btn-outline-secondary ms-2" 
                                      onclick="removeSupervisor('<?php echo e($phase->id); ?>', <?php echo e($index); ?>)"
                                      style="padding: 0.15rem 0.3rem; font-size: 0.7rem;">
                                <i class="ti ti-trash"></i>
                              </button>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                  </div>
                  <?php endif; ?>
                  
                  <h6 class="fw-semibold text-secondary mb-2">Team Members</h6>
                  <div class="row g-2">
                    <?php $__empty_1 = true; $__currentLoopData = $phase->teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                      <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 team-member-card mb-2">
                          <div class="card-body p-2 d-flex flex-column">
                            <div class="d-flex flex-column align-items-center text-center">
                                <!-- Profile Image -->
                                <img src="<?php echo e($team->profile_pic ? asset('storage/'.$team->profile_pic) : asset('images/default-user.png')); ?>"
                                    alt="Profile" class="rounded-circle mb-2" width="60" height="60" style="object-fit: cover;">
                                
                                <!-- Member Info -->
                                <h6 class="fw-semibold mb-0 text-dark"><?php echo e($team->name); ?></h6>
                                <small class="text-muted mb-2"><?php echo e($team->p_no ?? 'No contact'); ?></small>
                                
                                <!-- Roles Badges -->
                                <div class="mb-2">
                                    <?php $__currentLoopData = $team->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $badgeColor = match(strtolower($role->role)) {
                                                'leader' => 'primary',
                                                'developer' => 'success',
                                                'ba' => 'info',
                                                'qa' => 'warning',
                                                'devops' => 'dark',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($badgeColor); ?> me-1 mb-1 text-uppercase" style="font-size: 0.6rem; padding: 0.2em 0.4em;"><?php echo e($role->role); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-1 w-100 mt-auto">
                                <!-- View Details Button -->
                                <button class="btn btn-sm btn-outline-info flex-grow-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#memberModal_<?php echo e($team->id); ?>">
                                    <i class="ti ti-eye"></i>
                                </button>
                                
                                <?php if($isDeveloper): ?>
                                <!-- Dropdown for actions -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editMemberModal_<?php echo e($team->id); ?>">
                                                <i class="ti ti-edit me-2"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-danger" onclick="confirmDelete('<?php echo e(route('team.delete', $team)); ?>', 'member')">
                                                <i class="ti ti-trash me-2"></i> Delete
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addToPhaseModal_<?php echo e($team->id); ?>">
                                                <i class="ti ti-plus me-2"></i> Add to Another Phase
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-warning" onclick="confirmRemovePhase('<?php echo e(route('team.remove-phase', ['team' => $team, 'phase' => $phase])); ?>')">
                                                <i class="ti ti-x me-2"></i> Remove from this Phase
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        </div>
                      </div>

                      <!-- View Details Modal -->
                      <div class="modal fade" id="memberModal_<?php echo e($team->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title"><?php echo e($team->name); ?> - Details</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <!-- Profile picture + roles -->
                              <div class="text-center mb-3">
                                <img src="<?php echo e($team->profile_pic ? asset('storage/'.$team->profile_pic) : asset('images/default-user.png')); ?>"
                                     class="rounded-circle mb-2" width="120" height="120">
                                <h4><?php echo e($team->name); ?></h4>
                                <?php $__currentLoopData = $team->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <span class="badge bg-primary"><?php echo e($r->role); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </div>

                              <!-- LinkedIn visual preview -->
                              <?php if($team->link1): ?>
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
                                  <p class="mb-1"><strong><?php echo e($team->name); ?></strong></p>
                                  <p class="text-muted">Click the button below to view full LinkedIn details.</p>
                                  <a href="<?php echo e($team->link1); ?>" target="_blank" class="btn btn-primary w-100">View LinkedIn Profile</a>
                                </div>
                              <?php endif; ?>

                              <?php if($team->link2): ?>
                                <div class="mt-3">
                                  <a href="<?php echo e($team->link2); ?>" target="_blank" class="btn btn-outline-secondary w-100">Visit Other Link</a>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Edit Member Modal (Developer only) -->
                      <?php if($isDeveloper): ?>
                      <div class="modal fade" id="editMemberModal_<?php echo e($team->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit <?php echo e($team->name); ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="<?php echo e(route('team.update', $team)); ?>" enctype="multipart/form-data">
                              <?php echo csrf_field(); ?>
                              <?php echo method_field('PUT'); ?>
                              <div class="modal-body">
                                <div class="mb-3">
                                  <label class="form-label">Name</label>
                                  <input type="text" name="name" class="form-control" value="<?php echo e($team->name); ?>" required>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Phone No</label>
                                  <input type="text" name="p_no" class="form-control" value="<?php echo e($team->p_no); ?>">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Profile Picture</label>
                                  <input type="file" name="profile_pic" class="form-control">
                                  <?php if($team->profile_pic): ?>
                                    <small class="text-muted">Current: <?php echo e(basename($team->profile_pic)); ?></small>
                                  <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">LinkedIn URL</label>
                                  <input type="url" name="link1" class="form-control" value="<?php echo e($team->link1); ?>">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Other Link</label>
                                  <input type="url" name="link2" class="form-control" value="<?php echo e($team->link2); ?>">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Roles in <?php echo e($phase->phase_name); ?></label>
                                  <div class="d-flex flex-wrap gap-3">
                                    <?php $__currentLoopData = ['Leader','Developer','BA','QA','DevOps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="roles[]" 
                                               value="<?php echo e($role); ?>"
                                               id="edit_role_<?php echo e($role); ?>_<?php echo e($team->id); ?>"
                                               <?php echo e($team->roles->contains('role', $role) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="edit_role_<?php echo e($role); ?>_<?php echo e($team->id); ?>">
                                          <?php echo e($role); ?>

                                        </label>
                                      </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                      <div class="modal fade" id="addToPhaseModal_<?php echo e($team->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Add <?php echo e($team->name); ?> to Another Phase</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="<?php echo e(route('team.add-phase', $team)); ?>">
                              <?php echo csrf_field(); ?>
                              <div class="modal-body">
                                <div class="mb-3">
                                  <label class="form-label">Select Phase</label>
                                  <select name="phase_id" class="form-select" required>
                                    <option value="">Choose a phase...</option>
                                    <?php $__currentLoopData = $phases->where('id', '!=', $phase->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $otherPhase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <option value="<?php echo e($otherPhase->id); ?>"><?php echo e($otherPhase->phase_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Roles in New Phase</label>
                                  <div class="d-flex flex-wrap gap-3">
                                    <?php $__currentLoopData = ['Leader','Developer','BA','QA','DevOps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="roles[]" 
                                               value="<?php echo e($role); ?>"
                                               id="new_role_<?php echo e($role); ?>_<?php echo e($team->id); ?>">
                                        <label class="form-check-label" for="new_role_<?php echo e($role); ?>_<?php echo e($team->id); ?>">
                                          <?php echo e($role); ?>

                                        </label>
                                      </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                      <div class="col-12">
                        <p class="text-muted">No members assigned to this phase.</p>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Edit Phase Modal (Developer only) -->
                <?php if($isDeveloper): ?>
                <div class="modal fade" id="editPhaseModal_<?php echo e($phase->id); ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit <?php echo e($phase->phase_name); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <form method="POST" action="<?php echo e(route('phase.update', $phase)); ?>" id="editPhaseForm_<?php echo e($phase->id); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Phase ID</label>
                            <input type="text" name="phase_id" class="form-control" value="<?php echo e($phase->phase_id); ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Phase Name</label>
                            <input type="text" name="phase_name" class="form-control" value="<?php echo e($phase->phase_name); ?>" required>
                          </div>
                          <div class="row mb-3">
                            <div class="col-md-6">
                              <label class="form-label">Start Date</label>
                              <input type="date" name="start_date" class="form-control" value="<?php echo e(\Carbon\Carbon::parse($phase->start_date)->format('Y-m-d')); ?>" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">End Date</label>
                              <input type="date" name="end_date" class="form-control" value="<?php echo e(\Carbon\Carbon::parse($phase->end_date)->format('Y-m-d')); ?>" required>
                            </div>
                          </div>
                          
                          <!-- Supervisors Edit Section -->
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Supervisors</label>
                            <div id="editSupervisorsContainer_<?php echo e($phase->id); ?>">
                              <?php if($phase->supervisors && count($phase->supervisors) > 0): ?>
                                <?php $__currentLoopData = $phase->supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row supervisor-row mb-2">
                                  <div class="col-md-5">
                                    <input type="text" name="supervisors[<?php echo e($index); ?>][name]" 
                                           class="form-control" 
                                           value="<?php echo e($supervisor['name']); ?>"
                                           placeholder="Supervisor Name">
                                  </div>
                                  <div class="col-md-5">
                                    <input type="text" name="supervisors[<?php echo e($index); ?>][designation]" 
                                           class="form-control" 
                                           value="<?php echo e($supervisor['designation']); ?>"
                                           placeholder="Designation">
                                  </div>
                                  <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-supervisor-btn" 
                                            onclick="removeSupervisorRow(this)">
                                      <i class="ti ti-trash"></i>
                                    </button>
                                  </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php else: ?>
                                <div class="row supervisor-row mb-2">
                                  <div class="col-md-5">
                                    <input type="text" name="supervisors[0][name]" class="form-control" placeholder="Supervisor Name">
                                  </div>
                                  <div class="col-md-5">
                                    <input type="text" name="supervisors[0][designation]" class="form-control" placeholder="Designation">
                                  </div>
                                  <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-supervisor-btn" 
                                            onclick="removeSupervisorRow(this)">
                                      <i class="ti ti-trash"></i>
                                    </button>
                                  </div>
                                </div>
                              <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" 
                                    onclick="addSupervisorRow(<?php echo e($phase->id); ?>)">
                              <i class="ti ti-plus"></i> Add Supervisor
                            </button>
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
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
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
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Supervisor Removal Confirmation Modal -->
<div class="modal fade" id="removeSupervisorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-warning">Remove Supervisor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to remove this supervisor?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="removeSupervisorForm" method="POST" action="">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <input type="hidden" name="supervisor_index" id="supervisorIndex">
          <button type="submit" class="btn btn-warning">Remove</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Phase Filter Function
function filterPhases(phaseId) {
    const allPhases = document.querySelectorAll('.phase-container');
    
    if (phaseId === 'all') {
        allPhases.forEach(phase => {
            phase.style.display = 'block';
        });
    } else {
        allPhases.forEach(phase => {
            if (phase.getAttribute('data-phase-id') === phaseId) {
                phase.style.display = 'block';
            } else {
                phase.style.display = 'none';
            }
        });
    }
}

// Supervisor management functions
document.getElementById('addSupervisorBtn')?.addEventListener('click', function() {
    const container = document.getElementById('supervisorsContainer');
    const rows = container.querySelectorAll('.supervisor-row');
    const index = rows.length;
    
    const newRow = document.createElement('div');
    newRow.className = 'row supervisor-row mb-2';
    newRow.innerHTML = `
        <div class="col-md-6">
            <input type="text" name="supervisors[${index}][name]" class="form-control" placeholder="Supervisor Name">
        </div>
        <div class="col-md-5">
            <input type="text" name="supervisors[${index}][designation]" class="form-control" placeholder="Designation">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm remove-supervisor" onclick="removeSupervisorRow(this)">
                <i class="ti ti-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
});

function addSupervisorRow(phaseId) {
    const container = document.getElementById(`editSupervisorsContainer_${phaseId}`);
    const rows = container.querySelectorAll('.supervisor-row');
    const index = rows.length;
    
    const newRow = document.createElement('div');
    newRow.className = 'row supervisor-row mb-2';
    newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="supervisors[${index}][name]" class="form-control" placeholder="Supervisor Name">
        </div>
        <div class="col-md-5">
            <input type="text" name="supervisors[${index}][designation]" class="form-control" placeholder="Designation">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger btn-sm remove-supervisor-btn" onclick="removeSupervisorRow(this)">
                <i class="ti ti-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeSupervisorRow(button) {
    const row = button.closest('.supervisor-row');
    row.remove();
    
    // Re-index remaining rows
    const container = row.parentElement;
    const rows = container.querySelectorAll('.supervisor-row');
    rows.forEach((row, index) => {
        const nameInput = row.querySelector('input[name^="supervisors"][name$="[name]"]');
        const designationInput = row.querySelector('input[name^="supervisors"][name$="[designation]"]');
        
        if (nameInput) nameInput.name = `supervisors[${index}][name]`;
        if (designationInput) designationInput.name = `supervisors[${index}][designation]`;
    });
}

function removeSupervisor(phaseId, supervisorIndex) {
    if (confirm('Are you sure you want to remove this supervisor?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/phases/${phaseId}/remove-supervisor`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrf);
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);
        
        const indexInput = document.createElement('input');
        indexInput.type = 'hidden';
        indexInput.name = 'supervisor_index';
        indexInput.value = supervisorIndex;
        form.appendChild(indexInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Phase role checkboxes
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
        csrf.value = '<?php echo e(csrf_token()); ?>';
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

// Initialize date pickers with correct format
document.addEventListener('DOMContentLoaded', function() {
    // Set min date for end date based on start date
    document.querySelectorAll('input[name="start_date"]').forEach(startDate => {
        startDate.addEventListener('change', function() {
            const endDate = this.closest('form').querySelector('input[name="end_date"]');
            if (endDate) {
                endDate.min = this.value;
            }
        });
    });
});
</script>

<style>
.team-member-card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.dropdown-menu {
    min-width: 200px;
}

.badge {
    font-size: 0.6rem;
    padding: 0.2em 0.4em;
}

.supervisor-row {
    align-items: center;
}

#teamImageSlider img {
    height: 350px;
    object-fit: cover;
}

.phase-container {
    transition: all 0.3s ease;
}

/* Compact member cards for 4 per row */
.team-member-card .card-body {
    padding: 0.75rem !important;
}

.team-member-card h6 {
    font-size: 0.85rem;
    margin-bottom: 0.15rem !important;
    line-height: 1.2;
}

.team-member-card small {
    font-size: 0.7rem;
}

/* Supervisor cards styling - 3 per row */
.supervisor-card {
    min-height: 80px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .phases-container .col-lg-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .team-member-card .col-lg-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    /* Supervisors - 2 per row on tablets */
    .supervisor-card .col-lg-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 768px) {
    .team-member-card .col-lg-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    .btn-group {
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    
    /* Supervisors - 1 per row on mobile */
    .supervisor-card .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (max-width: 576px) {
    .team-member-card .col-lg-3 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Center align team member cards */
.team-member-card .d-flex.flex-column {
    align-items: center;
}

/* Action buttons styling */
.team-member-card .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.team-member-card .dropdown-toggle::after {
    display: none;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/team_phase/index.blade.php ENDPATH**/ ?>
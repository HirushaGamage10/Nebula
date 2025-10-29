

<?php $__env->startSection('title', 'Team & Phase Management | Nebula'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <!-- HEADER -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary">Team & Phase Management</h3>
        <small class="text-muted">Manage project phases and team members</small>
      </div>

      <!-- ADD PHASE -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
          <h5 class="fw-semibold text-dark mb-0">➕ Add New Phase</h5>
        </div>
        <div class="card-body bg-white">
          <form method="POST" action="<?php echo e(route('phase.create')); ?>" class="row g-3">
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
            <div class="col-md-1 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ASSIGN MEMBER -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
          <h5 class="fw-semibold text-dark mb-0">👥 Assign Team Member to Multiple Phases</h5>
        </div>
        <div class="card-body bg-white">
          <form method="POST" action="<?php echo e(route('team.assign')); ?>" enctype="multipart/form-data" id="assignForm">
            <?php echo csrf_field(); ?>
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label class="form-label fw-semibold">Member Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Savindu Fernando" required>
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
                <input type="url" name="link1" class="form-control mb-2" placeholder="LinkedIn / GitHub">
                <input type="url" name="link2" class="form-control" placeholder="Other link">
              </div>
            </div>

            <!-- MULTI-PHASE SELECT -->
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

            <!-- ROLE SELECTION CONTAINER -->
            <div id="roleContainer" class="mt-3"></div>

            <div class="text-end mt-3">
              <button type="submit" class="btn btn-success px-4">Assign to Phases</button>
            </div>
          </form>
        </div>
      </div>

      <!-- DISPLAY ALL -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <h5 class="fw-semibold text-dark mb-0">📋 All Phases & Team Members</h5>
        </div>
        <div class="card-body bg-white">
          <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-4">
              <h5 class="fw-semibold text-primary"><?php echo e($phase->phase_name); ?></h5>
              <p class="text-muted small mb-3">
                <i class="ti ti-calendar"></i> <?php echo e($phase->start_date); ?> → <?php echo e($phase->end_date); ?>

              </p>
              <div class="row g-3">
                <?php $__empty_1 = true; $__currentLoopData = $phase->teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                      <div class="card-body d-flex align-items-center gap-3">
                        <img src="<?php echo e($team->profile_pic ? asset('storage/'.$team->profile_pic) : asset('images/default-user.png')); ?>"
                             alt="Profile" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                        <div class="flex-grow-1">
                          <h6 class="fw-semibold mb-0"><?php echo e($team->name); ?></h6>
                          <small class="text-muted"><?php echo e($team->p_no ?? 'No contact'); ?></small>
                          <div class="mt-2">
                            <?php $__currentLoopData = $team->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php
                                $badgeColor = match(strtolower($role->role)) {
                                  'leader' => 'primary',
                                  'developer' => 'success',
                                  'ba' => 'info',
                                  'qa' => 'warning',
                                  default => 'secondary'
                                };
                              ?>
                              <span class="badge bg-<?php echo e($badgeColor); ?> me-1 text-uppercase"><?php echo e($role->role); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <div class="col-12"><p class="text-muted">No members yet.</p></div>
                <?php endif; ?>
              </div>
            </div>
            <hr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- JS to dynamically show roles for each selected phase -->
<script>
document.querySelectorAll('.phase-checkbox').forEach(cb => {
  cb.addEventListener('change', function() {
    const container = document.getElementById('roleContainer');
    container.innerHTML = ''; // reset
    document.querySelectorAll('.phase-checkbox:checked').forEach(sel => {
      const phaseId = sel.value;
      const phaseName = sel.nextElementSibling.innerText;
      const html = `
        <div class="card mt-3 border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-semibold mb-2 text-primary">${phaseName} Roles</h6>
            <div class="d-flex flex-wrap gap-3">
              ${['leader','developer','BA','QA'].map(r => `
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/team_phase/index.blade.php ENDPATH**/ ?>
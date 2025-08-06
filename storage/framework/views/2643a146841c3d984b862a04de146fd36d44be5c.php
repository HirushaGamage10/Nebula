<?php $__env->startSection('title', 'NEBULA | Create User'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $currentUserRole = auth()->user()->user_role ?? '';
?>

<?php if($currentUserRole == 'Program Administrator (level 01)' || $currentUserRole == 'Developer'): ?>
<div class="container mt-5">
  <div class="p-4 rounded shadow w-100 bg-white mt-4">
    <h3 class="text-center mb-4">Create a User</h3>
    
    <!-- Display validation errors -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form id="createUserForm" method="POST" action="<?php echo e(route('user.create')); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3 row align-items-center mx-3">
        <label for="name" class="col-sm-2 col-form-label fw-bold">Name<span style="color: red;">*</span></label>
        <div class="col-sm-10">
          <input type="text" 
                 class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                 id="name" 
                 name="name" 
                 placeholder="User name" 
                 value="<?php echo e(old('name')); ?>"
                 required>
          <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      
      <div class="mb-3 row mx-3">
        <label for="email" class="col-sm-2 col-form-label fw-bold">Email<span style="color: red;">*</span></label>
        <div class="col-sm-10">
          <input type="email" 
                 class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                 id="email" 
                 name="email" 
                 placeholder="User email" 
                 value="<?php echo e(old('email')); ?>"
                 required>
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          <div class="form-text mt-1">
            <small class="text-muted">
              <strong>Email Requirements:</strong><br>
              • Must be a valid email format (e.g., user@domain.com)<br>
              • Cannot contain spaces<br>
              • Can only contain one @ symbol<br>
              • Must be unique (not already registered)
            </small>
          </div>
        </div>
      </div>
      
      <div class="mb-3 row align-items-center mx-3">
        <label for="employee_id" class="col-sm-2 col-form-label fw-bold">Employee ID<span style="color: red;">*</span></label>
        <div class="col-sm-10">
          <input type="text" 
                 class="form-control <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                 id="employee_id" 
                 name="employee_id" 
                 placeholder="Employee ID" 
                 value="<?php echo e(old('employee_id')); ?>"
                 required>
          <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      
      <div class="mb-3 row align-items-center mx-3">
        <label for="role" class="col-sm-2 col-form-label fw-bold">Role<span style="color: red;">*</span></label>
        <div class="col-sm-10">
          <select class="form-control <?php $__errorArgs = ['user_role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                  id="role" 
                  name="user_role" 
                  required>
            <option value="">Select Role</option>
            <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($role); ?>" <?php echo e(old('user_role') == $role ? 'selected' : ''); ?>><?php echo e($role); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['user_role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      
      <div class="mb-3 row align-items-center mx-3">
        <label for="user_location" class="col-sm-2 col-form-label fw-bold">Location <span class="required">*</span></label>
        <div class="col-sm-10">
          <select class="form-control <?php $__errorArgs = ['user_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                  id="user_location" 
                  name="user_location" 
                  required>
            <option value="">Select Location</option>
            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($location); ?>" <?php echo e(old('user_location') == $location ? 'selected' : ''); ?>><?php echo e($location); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['user_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>
      
      <div class="mb-3 row mx-3">
        <label for="setPassword" class="col-sm-2 col-form-label fw-bold">Password<span style="color: red;">*</span></label>
        <div class="col-sm-8">
          <div class="input-group">
            <input type="password" 
                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   id="setPassword" 
                   name="password" 
                   placeholder="Set Password" 
                   required 
                   pattern=".{6,}">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
              <i class="ti ti-eye" id="eyeIcon"></i>
            </button>
          </div>
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          <div class="form-text mt-1">
            <small class="text-muted">
              <strong>Password Requirements:</strong><br>
              • Minimum 6 characters long<br>
              • Must contain at least one uppercase letter (A-Z)<br>
              • Must contain at least one lowercase letter (a-z)<br>
              • Must contain at least one number (0-9)
            </small>
          </div>
        </div>
        <div class="col-sm-2">
          <button type="button" id="generatePassword" class="btn btn-primary w-100">Generate</button>
        </div>
      </div>
      
      <div class="mb-3 row align-items-center mx-3 mt-5">
        <div class="col-sm-12">
          <button type="submit" id="createUserBtn" class="btn btn-primary w-100">Create User</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('createUserForm');
  const createUserBtn = document.getElementById('createUserBtn');

  // Password generator
  document.getElementById('generatePassword').addEventListener('click', function() {
    var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var password = "";
    for (var i = 0; i < 8; i++) {
      var randomIndex = Math.floor(Math.random() * charset.length);
      password += charset[randomIndex];
    }
    document.getElementById('setPassword').value = password;
  });

  // Password visibility toggle
  document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('setPassword');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      eyeIcon.classList.remove('ti-eye');
      eyeIcon.classList.add('ti-eye-off');
    } else {
      passwordInput.type = 'password';
      eyeIcon.classList.remove('ti-eye-off');
      eyeIcon.classList.add('ti-eye');
    }
  });

  function showToast(message, type) {
    const toastHtml = `
      <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>`;
    document.querySelector('.toast-container').insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.querySelector('.toast-container .toast:last-child');
    const toast = new bootstrap.Toast(toastEl, { delay: 1500 });
    toast.show();
  }

  // Real-time email validation
  const emailInput = document.getElementById('email');
  emailInput.addEventListener('blur', function() {
    const email = this.value;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (email && !emailRegex.test(email)) {
      this.classList.add('is-invalid');
      if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'Please enter a valid email address format.';
        this.parentNode.appendChild(errorDiv);
      }
    } else {
      this.classList.remove('is-invalid');
      const errorDiv = this.parentNode.querySelector('.invalid-feedback');
      if (errorDiv) {
        errorDiv.remove();
      }
    }
  });

  // Real-time name validation
  const nameInput = document.getElementById('name');
  nameInput.addEventListener('blur', function() {
    const name = this.value;
    const nameRegex = /^[a-zA-Z\s]+$/;
    
    if (name && !nameRegex.test(name)) {
      this.classList.add('is-invalid');
      if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'Name can only contain letters and spaces.';
        this.parentNode.appendChild(errorDiv);
      }
    } else {
      this.classList.remove('is-invalid');
      const errorDiv = this.parentNode.querySelector('.invalid-feedback');
      if (errorDiv) {
        errorDiv.remove();
      }
    }
  });

  // Real-time password validation
  const passwordInput = document.getElementById('setPassword');
  passwordInput.addEventListener('input', function() {
    const password = this.value;
    
    if (password.length < 6) {
      this.classList.add('is-invalid');
      if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'Password must be at least 6 characters long.';
        this.parentNode.appendChild(errorDiv);
      }
    } else {
      this.classList.remove('is-invalid');
      const errorDiv = this.parentNode.querySelector('.invalid-feedback');
      if (errorDiv) {
        errorDiv.remove();
      }
    }
  });

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showToast('User created: ' + data.message, 'success');
        setTimeout(function() {
          location.reload();
        }, 1500);
      } else {
        showToast('Error: ' + (data.message || 'Unknown error'), 'danger');
      }
    })
    .catch(error => {
      showToast('Error creating user: ' + error.message, 'danger');
    });
  });
});
</script>
<?php else: ?>
<div class="alert alert-warning mt-5 mx-5">
    <h4 class="alert-heading">Access Restricted</h4>
    <p>Only Program Administrator (level 01) and Developer can create new users. You do not have permission to access this feature.</p>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/create_user.blade.php ENDPATH**/ ?>
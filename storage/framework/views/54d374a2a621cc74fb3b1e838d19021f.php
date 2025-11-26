

<?php $__env->startSection('title', 'NEBULA | Add External Institute Student ID'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Success Message Styles */
.success-message{
  position:fixed;top:20px;right:20px;z-index:9999;background:linear-gradient(135deg,#28a745,#20c997);
  color:#fff;padding:15px 20px;border-radius:10px;box-shadow:0 4px 15px rgba(40,167,69,.3);
  font-weight:500;font-size:14px;max-width:400px;transform:translateX(100%);transition:transform .3s ease-in-out;border-left:4px solid #fff
}
.success-message.show{transform:translateX(0)}
.success-message .success-icon{margin-right:10px;font-size:18px}

/* Error Message Styles */
.error-message{
  position:fixed;top:20px;right:20px;z-index:9999;background:linear-gradient(135deg,#dc3545,#e74c3c);
  color:#fff;padding:15px 20px;border-radius:10px;box-shadow:0 4px 15px rgba(220,53,69,.3);
  font-weight:500;font-size:14px;max-width:400px;transform:translateX(100%);transition:transform .3s ease-in-out;border-left:4px solid #fff
}
.error-message.show{transform:translateX(0)}
.error-message .error-icon{margin-right:10px;font-size:18px}
</style>

<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <h2 class="text-center mb-4">Add External Institute Student ID</h2>
      <hr>

      <form id="uh-index-form">
        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-bold">Location</label>
          <div class="col-sm-9">
            <select class="form-select" id="locationSelect" name="location" required>
              <option value="">Select Location</option>
            </select>
          </div>
        </div>

        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-bold">Course</label>
          <div class="col-sm-9">
            <select class="form-select" id="courseSelect" name="course" required disabled>
              <option value="">Select Course</option>
            </select>
          </div>
        </div>

        <div class="row mb-3 align-items-center">
          <label class="col-sm-3 col-form-label fw-bold">Intake</label>
          <div class="col-sm-9">
            <select class="form-select" id="intakeSelect" name="intake" required disabled>
              <option value="">Select Intake</option>
            </select>
          </div>
        </div>
      </form>

      <div id="studentsSection" style="display:none;">
        <h4 class="mt-4">Students – External Institute ID</h4>
        <form id="uh-index-save-form">
          <table class="table table-bordered mt-3">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Student ID</th>
                <th style="min-width:320px">External Institute Student ID</th>
                <th style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody id="studentsTableBody"></tbody>
          </table>
          <button type="submit" class="btn btn-primary mt-3">Save External Institute IDs</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
// toast helpers
function showSuccessMessage(msg){document.querySelectorAll('.success-message,.error-message').forEach(n=>n.remove());const d=document.createElement('div');d.className='success-message';d.innerHTML=`<i class="ti ti-check-circle success-icon"></i>${msg}`;document.body.appendChild(d);setTimeout(()=>d.classList.add('show'),100);setTimeout(()=>{d.classList.remove('show');setTimeout(()=>d.remove(),300)},4000)}
function showErrorMessage(msg){document.querySelectorAll('.success-message,.error-message').forEach(n=>n.remove());const d=document.createElement('div');d.className='error-message';d.innerHTML=`<i class="ti ti-alert-circle error-icon"></i>${msg}`;document.body.appendChild(d);setTimeout(()=>d.classList.add('show'),100);setTimeout(()=>{d.classList.remove('show');setTimeout(()=>d.remove(),300)},5000)}

$(function () {
  // 1) locations (static)
  const locations=[
    {id:'Welisara',name:'Nebula Institute of Technology - Welisara'},
    {id:'Moratuwa',name:'Nebula Institute of Technology - Moratuwa'},
    {id:'Peradeniya',name:'Nebula Institute of Technology - Peradeniya'}
  ];
  locations.forEach(loc=>$('#locationSelect').append(`<option value="${loc.id}">${loc.name}</option>`));

  // 2) courses for location
  $('#locationSelect').on('change', function(){
    const location=$(this).val();
    $('#courseSelect').prop('disabled',true).html('<option value="">Select Course</option>');
    $('#intakeSelect').prop('disabled',true).html('<option value="">Select Intake</option>');
    $('#studentsSection').hide();
    if(!location) return;

    $.post("<?php echo e(route('uh.index.courses')); ?>",{location,_token:'<?php echo e(csrf_token()); ?>'},function(res){
      (res.courses||[]).forEach(c=>$('#courseSelect').append(`<option value="${c.course_id}">${c.course_name}</option>`));
      $('#courseSelect').prop('disabled',false);
    });
  });

  // 3) intakes for course
  $('#courseSelect').on('change', function(){
    const course_id=$(this).val();
    $('#intakeSelect').prop('disabled',true).html('<option value="">Select Intake</option>');
    $('#studentsSection').hide();
    if(!course_id) return;

    $.post("<?php echo e(route('uh.index.intakes')); ?>",{course_id,_token:'<?php echo e(csrf_token()); ?>'},function(res){
      (res.intakes||[]).forEach(i=>$('#intakeSelect').append(`<option value="${i.intake_id}">${i.batch}</option>`));
      $('#intakeSelect').prop('disabled',false);
    });
  });

  // 4) students for intake (ONLY Registered) + add Terminate button
  $('#intakeSelect').on('change', function(){
    const intake_id=$(this).val();
    $('#studentsSection').hide();
    if(!intake_id) return;

    $.post("<?php echo e(route('uh.index.students')); ?>",{intake_id,_token:'<?php echo e(csrf_token()); ?>'},function(res){
      const $tb=$('#studentsTableBody').empty();
      if(res.students && res.students.length){
        res.students.forEach(st=>{
          $tb.append(`
            <tr data-student-id="${st.student_id}" data-intake-id="${st.intake_id}">
              <td>${st.name}</td>
              <td>${st.student_id}</td>
              <td>
                <input type="text" class="form-control" name="external_institute_id[${st.student_id}]"
                  value="${st.uh_index_number || ''}" placeholder="Enter Pearson/UH/Other Institute ID">
              </td>
              <td>
                <button type="button" class="btn btn-outline-danger btn-sm btn-terminate">Terminate</button>
              </td>
            </tr>`);
        });
      }else{
        $tb.append('<tr><td colspan="4" class="text-center text-muted">No registered students found for this intake.</td></tr>');
      }
      $('#studentsSection').show();
    }).fail(()=>showErrorMessage('Failed to load students.'));
  });

  // 4a) click Terminate (AJAX)
  $('#studentsTableBody').on('click','.btn-terminate', function(){
    const $tr=$(this).closest('tr');
    const student_id=$tr.data('student-id');
    const intake_id=$tr.data('intake-id');
    if(!confirm('Terminate this student from the intake?')) return;

    $.post("<?php echo e(route('uh.index.terminate')); ?>",
      {student_id,intake_id,_token:'<?php echo e(csrf_token()); ?>'},
      function(res){
        if(res.success){
          showSuccessMessage(res.message || 'Student terminated.');
          $tr.remove(); // vanish row
          if($('#studentsTableBody tr').length===0){
            $('#studentsTableBody').append('<tr><td colspan="4" class="text-center text-muted">No registered students found for this intake.</td></tr>');
          }
        }else{
          showErrorMessage(res.message || 'Termination failed.');
        }
      }
    ).fail((xhr)=>{
      const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Termination failed.';
      showErrorMessage(msg);
    });
  });

  // 5) save IDs
  $('#uh-index-save-form').on('submit', function(e){
    e.preventDefault();
    const students=[];
    $('#studentsTableBody tr').each(function(){
      const student_id=$(this).data('student-id');
      const uh_index_number=$(this).find('input').val();
      if(student_id) students.push({student_id, uh_index_number});
    });

    $.post("<?php echo e(route('uh.index.save')); ?>",{students,_token:'<?php echo e(csrf_token()); ?>'},function(res){
      if(res.success) showSuccessMessage(res.message || 'Saved.');
      else showErrorMessage(res.message || 'Failed to save.');
    }).fail((xhr)=>{
      const msg=(xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error saving.';
      showErrorMessage(msg);
    });
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\danid\Desktop\Project\New Nebula\resources\views/uh_index_numbers.blade.php ENDPATH**/ ?>
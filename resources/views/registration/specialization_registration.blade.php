@extends('inc.app')

@section('title', 'NEBULA | Specialization Registration')

@section('content')
<div class="container-fluid"><div class="card"><div class="card-body">
  <h2 class="text-center mb-4">Degree &amp; Diploma Specialization Registration</h2>
  <p class="text-muted text-center">Only students already eligible and course-registered can be assigned to a specialization.</p><hr>
  <div class="row g-3 mx-2">
    <div class="col-md-6"><label class="form-label">Location</label><select id="location" class="form-select"><option value="">Select location</option>@foreach($locations as $location)<option value="{{ $location }}">{{ $location }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Course</label><select id="course" class="form-select" disabled><option value="">Select course</option></select></div>
    <div class="col-md-6"><label class="form-label">Intake</label><select id="intake" class="form-select" disabled><option value="">Select intake</option></select></div>
    <div class="col-md-6"><label class="form-label">Specialization</label><select id="specialization" class="form-select" disabled><option value="">Select specialization</option></select></div>
  </div>
  <div id="studentArea" class="mt-4 d-none"><div class="d-flex justify-content-between mb-2"><strong id="count"></strong><button id="save" class="btn btn-primary">Register Selected Students</button></div>
    <div class="table-responsive"><table class="table table-bordered"><thead><tr><th><input id="selectAll" type="checkbox"></th><th>Student ID</th><th>Name</th><th>Email</th><th>Current Specialization</th></tr></thead><tbody id="students"></tbody></table></div>
  </div>
</div></div></div>
@push('scripts')<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', () => {
 const location=document.querySelector('#location'), course=document.querySelector('#course'), intake=document.querySelector('#intake'), spec=document.querySelector('#specialization'), body=document.querySelector('#students'), area=document.querySelector('#studentArea'); let courseOptions=[];
 const reset=(el,text)=>{el.innerHTML=`<option value="">${text}</option>`;el.disabled=true};
 const post=(url,data)=>fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify(data)}).then(r=>r.json());
 location.addEventListener('change',async()=>{ reset(course,'Select course');reset(intake,'Select intake');reset(spec,'Select specialization');area.classList.add('d-none'); if(!location.value)return; const d=await post('/specialization-registration/courses',{location:location.value}); courseOptions=d.courses||[]; course.disabled=false; courseOptions.forEach(x=>course.add(new Option(x.course_name,x.course_id))); });
 course.addEventListener('change',async()=>{reset(intake,'Select intake');reset(spec,'Select specialization');area.classList.add('d-none');if(!course.value)return;const d=await post('/specialization-registration/intakes',{location:location.value,course_id:course.value});intake.disabled=false;d.intakes.forEach(x=>intake.add(new Option(x.batch,x.intake_id)));let specs=courseOptions.find(x=>String(x.course_id)===course.value)?.specializations||[];for(let i=0;i<2&&typeof specs==='string';i++){try{specs=JSON.parse(specs)}catch{specs=[]}}if(Array.isArray(specs)&&specs.length){spec.disabled=false;specs.forEach(x=>spec.add(new Option(x,x)));}});
 async function load(){area.classList.add('d-none');if(!location.value||!course.value||!intake.value||!spec.value)return;const d=await post('/specialization-registration/students',{location:location.value,course_id:course.value,intake_id:intake.value});body.innerHTML=d.students.map(s=>`<tr><td><input class="student" type="checkbox" value="${s.student_id}" ${s.specialization===spec.value?'checked':''}></td><td>${s.student_id}</td><td>${s.name||''}</td><td>${s.email||''}</td><td>${s.specialization||'-'}</td></tr>`).join('');document.querySelector('#count').textContent=`${d.students.length} eligible students`;area.classList.remove('d-none');}
 intake.addEventListener('change',load);spec.addEventListener('change',load);document.querySelector('#selectAll').addEventListener('change',e=>document.querySelectorAll('.student').forEach(x=>x.checked=e.target.checked));
 document.querySelector('#save').addEventListener('click',async()=>{const student_ids=[...document.querySelectorAll('.student:checked')].map(x=>x.value);if(!student_ids.length)return alert('Select at least one student.');const d=await post('/specialization-registration/store',{location:location.value,course_id:course.value,intake_id:intake.value,specialization:spec.value,student_ids});alert(d.message||'Saved');if(d.success)load();});
});</script>@endpush
@endsection

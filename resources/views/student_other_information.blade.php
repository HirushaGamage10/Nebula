@extends('inc.app')

@section('title', 'NEBULA | Student Other Information')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Student Other Information</h2>
                        <hr class="mb-4">

                        {{-- Search --}}
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="nicSearchForm">
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label for="nicInput" class="col-sm-2 col-form-label">Student NIC<span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control bg-white" id="nicInput" name="nic"
                                                placeholder="Enter Student ID (NIC)" required>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-primary w-100">Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Toasts + Spinner + Generic Message Modal --}}
                        <div id="toastContainer" aria-live="polite" aria-atomic="true"
                            style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>
                        <div id="spinner-overlay" style="display:none;">
                            <div class="lds-ring">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>

                        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="messageModalLabel">Message</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" id="messageModalBody"></div>
                                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button></div>
                                </div>
                            </div>
                        </div>

                        {{-- Termination Modal --}}
                        <div class="modal fade" id="terminationModal" tabindex="-1" aria-labelledby="terminationModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="terminationModalLabel">Terminate Student</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="terminationReason" class="form-label">Reason <span
                                                    class="text-danger">*</span></label>
                                            <textarea id="terminationReason" class="form-control" rows="4"
                                                placeholder="Explain the reason"></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label for="termination_document" class="form-label">Attach document
                                                (optional)</label>
                                            <input type="file" id="termination_document" class="form-control"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        </div>
                                        <small class="text-muted">This will set the student's academic status to
                                            <b>terminated</b>.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" id="confirmTerminateBtn" class="btn btn-danger">Confirm
                                            Termination</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Re‑Register Modal --}}
                        <div class="modal fade" id="reRegisterModal" tabindex="-1" aria-labelledby="reRegisterModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reRegisterModalLabel">Re‑Register Student</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="reRegisterReason" class="form-label">Reason <span
                                                    class="text-danger">*</span></label>
                                            <textarea id="reRegisterReason" class="form-control" rows="4"
                                                placeholder="Why reinstate?"></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label for="reRegister_document" class="form-label">Attach document
                                                (optional)</label>
                                            <input type="file" id="reRegister_document" class="form-control"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        </div>
                                        <small class="text-muted">This will set the student's academic status to
                                            <b>active</b>.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" id="confirmReRegisterBtn"
                                            class="btn btn-success">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-4">

                        {{-- Main Form --}}
                        <div id="studentOtherInformationForm" style="display:none;">
                            <div class="alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mb-3"
                                id="statusBanner" style="display:none;">
                                <i class="ti ti-alert-triangle"></i>
                                <div><b>Terminated</b> — fields are locked. Click <b>Re‑Register Student</b> to activate
                                    again.</div>
                            </div>

                            <form id="otherInformationForm" class="p-4 rounded w-100 bg-white mt-2"
                                enctype="multipart/form-data">
                                {{-- Student Details --}}
                                <div class="mb-4">
                                    <h5 class="mb-3">Student Details</h5>
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label for="studentNameInput" class="col-sm-3 col-form-label">Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control bg-white" id="studentNameInput"
                                                name="studentName" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label for="studentIDInput" class="col-sm-3 col-form-label">Student ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control bg-white" id="studentIDInput"
                                                name="studentID" readonly>
                                        </div>
                                    </div>
                                </div>

                                {{-- Disciplinary --}}
                                <div class="mb-4">
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label for="disciplinaryIssues" class="col-sm-3 col-form-label">Disciplinary
                                            Issues</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="disciplinaryIssues" name="disciplinaryIssues"
                                                placeholder="Enter disciplinary issues" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label for="disciplinary_issue_document"
                                            class="col-sm-3 col-form-label">Disciplinary Issue Document</label>
                                        <div class="col-sm-9">
                                            <input type="file" class="form-control" id="disciplinary_issue_document"
                                                name="disciplinary_issue_document" accept=".pdf,.doc,.docx,.jpg,.png">
                                        </div>
                                    </div>

                                    {{-- Terminate + Re‑register actions --}}
                                    <div class="mb-2 row mx-3">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 d-flex gap-2">
                                            <button type="button" id="openTerminateModalBtn" class="btn btn-outline-danger">
                                                <i class="ti ti-user-x me-1"></i> Terminate Student
                                            </button>
                                            <button type="button" id="reRegisterBtn" class="btn btn-success"
                                                style="display:none;">
                                                <i class="ti ti-user-check me-1"></i> Re‑Register Student
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Higher Studies --}}
                                <div class="mb-4">
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label class="col-sm-3 col-form-label">Continue to Higher Studies?</label>
                                        <div class="col-sm-9">
                                            <div class="form-check form-check-inline">
                                                <input value="true" class="form-check-input" type="radio" id="continueYes"
                                                    name="continueStudies">
                                                <label class="form-check-label" for="continueYes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input value="false" class="form-check-input" type="radio" id="continueNo"
                                                    name="continueStudies" checked>
                                                <label class="form-check-label" for="continueNo">No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="higherStudiesContainer" class="mb-3 mx-5 bg-light-primary p-3 rounded"
                                        style="display:none;">
                                        <div class="mb-3 row align-items-center">
                                            <label for="institute" class="col-sm-2 col-form-label">Institute<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control bg-white" id="institute"
                                                    name="institute" placeholder="Enter institute">
                                            </div>
                                        </div>
                                        <div class="mb-1 row align-items-center">
                                            <label for="fieldOfStudy" class="col-sm-2 col-form-label">Field of Study<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control bg-white" id="fieldOfStudy"
                                                    name="fieldOfStudy" placeholder="Enter field of study">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Employment --}}
                                <div class="mb-4">
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label class="col-sm-3 col-form-label">Currently an Employee?</label>
                                        <div class="col-sm-9">
                                            <div class="form-check form-check-inline">
                                                <input value="true" class="form-check-input" type="radio" id="employeeYes"
                                                    name="currentlyEmployee">
                                                <label class="form-check-label" for="employeeYes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input value="false" class="form-check-input" type="radio" id="employeeNo"
                                                    name="currentlyEmployee" checked>
                                                <label class="form-check-label" for="employeeNo">No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="employmentContainer" class="mb-3 mx-5 bg-light-primary p-3 rounded"
                                        style="display:none;">
                                        <div class="mb-3 row align-items-center">
                                            <label for="jobTitle" class="col-sm-2 col-form-label">Job Title<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control bg-white" id="jobTitle"
                                                    name="jobTitle" placeholder="Enter job title">
                                            </div>
                                        </div>
                                        <div class="mb-1 row align-items-center">
                                            <label for="workplace" class="col-sm-2 col-form-label">Workplace<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control bg-white" id="workplace"
                                                    name="workplace" placeholder="Enter workplace">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Other Information --}}
                                <div class="mb-4">
                                    <div class="mb-3 row mx-3 align-items-center">
                                        <label for="otherInformation" class="col-sm-3 col-form-label">Other
                                            Information</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="otherInformation" name="otherInformation"
                                                placeholder="Enter other information" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden flag toggled by JS on termination --}}
                                <input type="hidden" name="terminateStudent" id="terminateStudent" value="false">

                                {{-- Actions --}}
                                <div class="text-center mt-4">
                                    <button id="dataSubmit" type="button" class="btn btn-primary w-100 mt-3">SAVE</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStatus = 'active'; // default; updated after search

        // Show/hide containers
        document.querySelectorAll('input[name="continueStudies"]').forEach(r => {
            r.addEventListener('change', () => {
                document.getElementById('higherStudiesContainer').style.display =
                    (document.getElementById('continueYes').checked) ? 'block' : 'none';
            });
        });
        document.querySelectorAll('input[name="currentlyEmployee"]').forEach(r => {
            r.addEventListener('change', () => {
                const show = document.getElementById('employeeYes').checked;
                document.getElementById('employmentContainer').style.display = show ? 'block' : 'none';
            });
        });

        // NIC search
        document.getElementById('nicSearchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const nic = document.getElementById('nicInput').value.trim();
            const wrapper = document.getElementById('studentOtherInformationForm');
            if (!nic) { showMessage('Warning', 'Please enter a NIC.'); return; }

            document.getElementById('spinner-overlay').style.display = 'flex';
            $.ajax({
                type: 'POST',
                url: '{{ route("retrieve.student.details") }}',
                data: { _token: '{{ csrf_token() }}', identificationType: 'nic', idValue: nic },
                success: function (res) {
                    document.getElementById('spinner-overlay').style.display = 'none';
                    if (res.success) {
                        document.getElementById('studentNameInput').value = res.data.student_name;
                        document.getElementById('studentIDInput').value = res.data.student_id;
                        currentStatus = res.data.academic_status ?? 'active'; // requires controller to send this
                        wrapper.style.display = 'block';
                        toggleFormLock(); // lock if terminated
                    } else {
                        wrapper.style.display = 'none';
                        showMessage('Warning', res.message);
                    }
                },
                error: function () {
                    document.getElementById('spinner-overlay').style.display = 'none';
                    showMessage('Error', 'An error occurred while searching for the student.');
                }
            });
        });

        // Lock/unlock when status is terminated/active
        function toggleFormLock() {
            const form = document.getElementById('otherInformationForm');
            const saveBtn = document.getElementById('dataSubmit');
            const termBtn = document.getElementById('openTerminateModalBtn');
            const reBtn = document.getElementById('reRegisterBtn');
            const banner = document.getElementById('statusBanner');

            // normalize once
            const status = (currentStatus || '').toString().trim().toLowerCase();

            const controls = form.querySelectorAll('input, textarea, select, button');
            const toDisable = Array.from(controls).filter(el =>
                !['dataSubmit', 'openTerminateModalBtn', 'reRegisterBtn'].includes(el.id)
            );

            if (status === 'terminated') {
                toDisable.forEach(el => el.disabled = true);

                saveBtn.style.display = 'none';
                termBtn.style.display = 'none';
                reBtn.style.display = 'inline-block';

                // force show banner
                banner.style.display = 'flex';
                banner.classList.remove('d-none');
            } else {
                toDisable.forEach(el => el.disabled = false);

                // keep these readonly
                document.getElementById('studentNameInput').readOnly = true;
                document.getElementById('studentIDInput').readOnly = true;

                saveBtn.style.display = 'inline-block';
                termBtn.style.display = 'inline-block';
                reBtn.style.display = 'none';

                // force hide banner (both ways)
                banner.style.display = 'none';
                banner.classList.add('d-none');
            }
        }


        // Termination flow
        document.getElementById('openTerminateModalBtn')?.addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('terminationModal')).show();
        });

        document.getElementById('confirmTerminateBtn')?.addEventListener('click', () => {
            const reasonEl = document.getElementById('terminationReason');
            const docEl = document.getElementById('termination_document');
            if (!reasonEl.value.trim()) { reasonEl.focus(); return; }

            document.getElementById('terminateStudent').value = 'true';
            bootstrap.Modal.getInstance(document.getElementById('terminationModal')).hide();
            actuallySubmitOtherInfo(reasonEl, docEl, /*isTerminate*/true);
        });

        // Re‑register flow
        document.getElementById('reRegisterBtn')?.addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('reRegisterModal')).show();
        });

        document.getElementById('confirmReRegisterBtn')?.addEventListener('click', () => {
            const reason = document.getElementById('reRegisterReason').value.trim();
            const doc = document.getElementById('reRegister_document').files[0];
            if (!reason) { document.getElementById('reRegisterReason').focus(); return; }

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('studentID', document.getElementById('studentIDInput').value);
            fd.append('reason', reason);
            if (doc) fd.append('document', doc);

            document.getElementById('spinner-overlay').style.display = 'flex';
            $.ajax({
                type: 'POST',
                url: '{{ route("reinstate.student") }}', // add this route on backend
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    document.getElementById('spinner-overlay').style.display = 'none';
                    if (res.success) {
                        showToast('Success', res.message, '#ccffcc');

                        // Use the status the server just saved
                        currentStatus = (res.academic_status || 'active');

                        // Close modal + clear fields
                        document.getElementById('reRegisterReason').value = '';
                        document.getElementById('reRegister_document').value = '';
                        bootstrap.Modal.getInstance(document.getElementById('reRegisterModal')).hide();

                        // Hard unlock everything in case they were disabled earlier
                        const form = document.getElementById('otherInformationForm');
                        form.querySelectorAll('input, textarea, select, button').forEach(el => el.disabled = false);

                        // Name/ID remain readonly as before
                        document.getElementById('studentNameInput').readOnly = true;
                        document.getElementById('studentIDInput').readOnly = true;

                        // Hide banner and swap buttons
                        document.getElementById('statusBanner').style.display = 'none';
                        document.getElementById('reRegisterBtn').style.display = 'none';
                        document.getElementById('openTerminateModalBtn').style.display = 'inline-block';
                        document.getElementById('dataSubmit').style.display = 'inline-block';

                        // Or simply call your helper (kept for consistency)
                        toggleFormLock();
                    } else {
                        showMessage('Error', res.message ?? 'Failed to reinstate.');
                    }
                },
                error: function () {
                    document.getElementById('spinner-overlay').style.display = 'none';
                    showMessage('Error', 'Error while reinstating student.');
                }
            });
        });

        // Normal Save (no termination)
        document.getElementById('dataSubmit').addEventListener('click', () => {
            actuallySubmitOtherInfo();
        });

        // Core AJAX submit for save/terminate
        function actuallySubmitOtherInfo(reasonEl = null, docEl = null, isTerminate = false) {
            const form = document.getElementById('otherInformationForm');
            const fd = new FormData(form);

            if (isTerminate) {
                fd.append('terminationReason', reasonEl.value.trim());
                if (docEl && docEl.files.length) fd.append('termination_document', docEl.files[0]);
            }

            document.getElementById('spinner-overlay').style.display = 'flex';
            $.ajax({
                type: 'POST',
                url: '{{ route("store.other.informations") }}',
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    document.getElementById('spinner-overlay').style.display = 'none';
                    if (res.success) {
                        showToast('Success', res.message, '#ccffcc');
                        // If terminated, lock immediately
                        if (isTerminate) {
                            currentStatus = 'terminated';
                            toggleFormLock();
                        } else {
                            // on normal save, keep current status
                        }
                        // clear termination fields/flag
                        document.getElementById('terminateStudent').value = 'false';
                        document.getElementById('terminationReason').value = '';
                        const tdoc = document.getElementById('termination_document');
                        if (tdoc) tdoc.value = '';
                    } else {
                        showMessage('Error', res.message);
                    }
                },
                error: function () {
                    document.getElementById('spinner-overlay').style.display = 'none';
                    showMessage('Error', 'An error occurred while saving the data.');
                }
            });
        }

        // helpers
        function showMessage(title, message) {
            document.getElementById('messageModalLabel').textContent = title;
            document.getElementById('messageModalBody').textContent = message;
            new bootstrap.Modal(document.getElementById('messageModal')).show();
        }
        function showToast(title, message, backgroundColor) {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast'; toast.style.backgroundColor = backgroundColor;
            toast.innerHTML = `
            <div class="toast-header">
              <strong class="me-auto">${title}</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>`;
            toastContainer.appendChild(toast);
            new bootstrap.Toast(toast).show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
    </script>

    <style>
        .lds-ring {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px
        }

        .lds-ring div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 8px solid #fff;
            border-radius: 50%;
            animation: lds-ring 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            border-color: #fff transparent transparent transparent
        }

        .lds-ring div:nth-child(1) {
            animation-delay: -.45s
        }

        .lds-ring div:nth-child(2) {
            animation-delay: -.3s
        }

        .lds-ring div:nth-child(3) {
            animation-delay: -.15s
        }

        @keyframes lds-ring {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        #spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999
        }

        .bg-light-primary {
            background-color: #f1f5f9 !important
        }
    </style>
@endsection
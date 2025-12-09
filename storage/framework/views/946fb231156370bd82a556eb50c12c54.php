<?php $__env->startSection('title', 'NEBULA | Developer Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

    .tab-btn {
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        border: 1px solid #cfcfcf;
        background: var(--tab-bg, white);
        color: var(--tab-text, black);
        margin-right: 8px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tab-btn.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    body.dark-mode .tab-btn {
        --tab-bg: #2c2f36;
        --tab-text: #e4e4e4;
        border-color: #555;
    }

    .tab-content {
        display: none;
        position: relative;
    }
    .tab-content.active {
        display: block;
    }

    .spinner {
        width: 60px;
        height: 60px;
        border: 6px solid #cfd0d1;
        border-top-color: #0d6efd;
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
        margin: 40px auto;
        display: none;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .fullscreen-btn {
        position: absolute;
        top: 8px;
        right: 15px;
        z-index: 20;
        background: #0d6efd;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
    }

    .fullscreen-mode iframe {
        height: calc(100vh - 100px) !important;
    }

    iframe {
        transition: height 0.2s ease-in-out;
    }

</style>

<div class="container-fluid">

    <div class="card shadow-sm p-4 mb-4 bg-white">
        <h3 class="fw-bold m-0">Developer Dashboard</h3>
        <small class="text-muted">Access all role dashboards in a single interface</small>
    </div>

    <!-- TAB BUTTONS WITH ICONS -->
    <div class="d-flex flex-wrap mb-3">
        <button class="tab-btn active" onclick="showTab('dgm')"><i class="fa-solid fa-user-tie"></i>DGM</button>
        <button class="tab-btn" onclick="showTab('pa1')"><i class="fa-solid fa-user-gear"></i>PA L1</button>
        <button class="tab-btn" onclick="showTab('pa2')"><i class="fa-solid fa-users-gear"></i>PA L2</button>
        <button class="tab-btn" onclick="showTab('counselor')"><i class="fa-solid fa-user-graduate"></i>Counselor</button>
        <button class="tab-btn" onclick="showTab('marketing')"><i class="fa-solid fa-bullhorn"></i>Marketing</button>
        <button class="tab-btn" onclick="showTab('librarian')"><i class="fa-solid fa-book"></i>Librarian</button>
        <button class="tab-btn" onclick="showTab('hostel')"><i class="fa-solid fa-house"></i>Hostel</button>
        <button class="tab-btn" onclick="showTab('project')"><i class="fa-solid fa-file-code"></i>Project Tutor</button>
        <button class="tab-btn" onclick="showTab('bursar')"><i class="fa-solid fa-money-bill"></i>Bursar</button>
        <button class="tab-btn" onclick="showTab('devtools')"><i class="fa-solid fa-code"></i>Developer Tools</button>
    </div>


    <!-- TAB CONTENT AREAS (with spinner + fullscreen) -->
    <?php
        $tabs = [
            'dgm' => route('dgmdashboard'),
            'pa1' => route('admin.l1.dashboard'),
            'pa2' => route('program.admin.l2.dashboard'),
            'counselor' => route('student.counselor.dashboard'),
            'marketing' => route('marketing.manager.dashboard'),
            'librarian' => route('librarian.dashboard'),
            'hostel' => route('hostel.manager.dashboard'),
            'project' => route('project.tutor.dashboard'),
            'bursar' => route('bursar.dashboard'),
        ];
    ?>

    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div id="<?php echo e($id); ?>" class="tab-content <?php echo e($id === 'dgm' ? 'active' : ''); ?>">
            <button class="fullscreen-btn" onclick="toggleFullscreen('<?php echo e($id); ?>')">
                <i class="fa-solid fa-expand"></i> Fullscreen
            </button>

            <div id="spinner-<?php echo e($id); ?>" class="spinner"></div>

            <iframe id="frame-<?php echo e($id); ?>" 
                    src="<?php echo e($url); ?>"
                    class="w-100"
                    style="height:1500px; border:none;"
                    onload="hideSpinner('<?php echo e($id); ?>')">
            </iframe>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Developer tools section -->
    <div id="devtools" class="tab-content">
        <div class="alert alert-info mt-3">
            Developer Tools Section Coming Soon
        </div>
    </div>

</div>

<script>

function showTab(id) {
    document.querySelectorAll('.tab-content').forEach(e => e.classList.remove('active'));
    document.getElementById(id).classList.add('active');

    document.querySelectorAll('.tab-btn').forEach(e => e.classList.remove('active'));
    event.target.classList.add('active');

    autoResizeIframe(id);
}

function hideSpinner(id) {
    document.getElementById('spinner-' + id).style.display = 'none';
}

function toggleFullscreen(id) {
    let block = document.getElementById(id);
    block.classList.toggle('fullscreen-mode');
}

function autoResizeIframe(id) {
    let iframe = document.getElementById('frame-' + id);

    // Reset height
    iframe.style.height = '1500px';

    setTimeout(() => {
        try {
            let body = iframe.contentWindow.document.body;
            let html = iframe.contentWindow.document.documentElement;
            let height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);

            iframe.style.height = height + 50 + 'px';
        } catch (err) {
            // If blocked by cross-site policy, leave default height
        }
    }, 1200);
}

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/dashboards/developer_dashboard.blade.php ENDPATH**/ ?>
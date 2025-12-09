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
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    
    .tab-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
    
    body.dark-mode .tab-btn.active {
        background: #0d6efd;
        color: white;
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
        transition: background 0.2s;
    }
    
    .fullscreen-btn:hover {
        background: #0b5ed7;
    }

    .fullscreen-mode iframe {
        height: calc(100vh - 100px) !important;
    }

    iframe {
        transition: height 0.2s ease-in-out;
    }

    /* Multi split */
    .multi-container {
        display: flex;
        width: 100%;
        height: 90vh;
        border: 2px solid #aaa;
        gap: 5px;
        margin-top: 20px;
        padding: 5px;
        background: #f8f9fa;
    }
    
    body.dark-mode .multi-container {
        background: #1a1d23;
        border-color: #555;
    }
    
    .multi-box {
        flex: 1;
        border: 1px solid #ccc;
        position: relative;
        overflow: hidden;
        background: white;
    }
    
    body.dark-mode .multi-box {
        background: #2c2f36;
        border-color: #555;
    }
    
    .multi-box iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .selector {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 50;
        width: 180px;
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }
</style>

<div class="container-fluid">

    <div class="card shadow-sm p-4 mb-4 bg-white">
        <h3 class="fw-bold m-0">Developer Dashboard</h3>
        <small class="text-muted">Access all role dashboards from a single interface</small>
    </div>

    <!-- TAB BUTTONS -->
    <div class="d-flex flex-wrap mb-3">
        <button class="tab-btn active" data-tab="dgm"><i class="fa-solid fa-user-tie"></i> DGM</button>
        <button class="tab-btn" data-tab="pa1"><i class="fa-solid fa-user-gear"></i> PA L1</button>
        <button class="tab-btn" data-tab="pa2"><i class="fa-solid fa-users-gear"></i> PA L2</button>
        <button class="tab-btn" data-tab="counselor"><i class="fa-solid fa-user-graduate"></i> Counselor</button>
        <button class="tab-btn" data-tab="marketing"><i class="fa-solid fa-bullhorn"></i> Marketing</button>
        <button class="tab-btn" data-tab="librarian"><i class="fa-solid fa-book"></i> Librarian</button>
        <button class="tab-btn" data-tab="hostel"><i class="fa-solid fa-house"></i> Hostel</button>
        <button class="tab-btn" data-tab="project"><i class="fa-solid fa-file-code"></i> Project Tutor</button>
        <button class="tab-btn" data-tab="bursar"><i class="fa-solid fa-money-bill"></i> Bursar</button>
        <button class="tab-btn" data-tab="devtools"><i class="fa-solid fa-code"></i> Developer</button>
    </div>

    <!-- MULTI VIEW BUTTONS -->
    <div class="mb-3">
        <button class="btn btn-dark" onclick="twoView()">Two View</button>
        <button class="btn btn-secondary" onclick="threeView()">Three View</button>
        <button class="btn btn-primary" onclick="fourView()">Four View</button>
    </div>

    <!-- TAB CONTENT LOADER -->
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
        <div id="tab-<?php echo e($id); ?>" class="tab-content <?php echo e($id === 'dgm' ? 'active' : ''); ?>">
            <button class="fullscreen-btn" onclick="toggleFullscreen('<?php echo e($id); ?>')">
                <i class="fa-solid fa-expand"></i> Fullscreen
            </button>

            <div id="spinner-<?php echo e($id); ?>" class="spinner"></div>

            <iframe id="frame-<?php echo e($id); ?>" 
                    src="<?php echo e($url); ?>"
                    class="w-100"
                    style="height:1500px; border:none;"
                    data-loaded="false">
            </iframe>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div id="tab-devtools" class="tab-content">
        <div class="alert alert-info mt-3">
            <i class="fa-solid fa-wrench"></i> Developer Tools Coming Soon
        </div>
    </div>

    <!-- MULTI SPLIT AREA -->
    <div id="multi-area"></div>

</div>

<script>
// Dashboard routes mapping
const dashboardRoutes = {
    'dgm': '<?php echo e(route('dgmdashboard')); ?>',
    'pa1': '<?php echo e(route('admin.l1.dashboard')); ?>',
    'pa2': '<?php echo e(route('program.admin.l2.dashboard')); ?>',
    'counselor': '<?php echo e(route('student.counselor.dashboard')); ?>',
    'marketing': '<?php echo e(route('marketing.manager.dashboard')); ?>',
    'librarian': '<?php echo e(route('librarian.dashboard')); ?>',
    'hostel': '<?php echo e(route('hostel.manager.dashboard')); ?>',
    'project': '<?php echo e(route('project.tutor.dashboard')); ?>',
    'bursar': '<?php echo e(route('bursar.dashboard')); ?>'
};

// Initialize tab buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            showTab(tabId);
        });
    });
    
    // Add load handlers to iframes
    document.querySelectorAll('iframe[id^="frame-"]').forEach(iframe => {
        iframe.addEventListener('load', function() {
            const id = this.id.replace('frame-', '');
            hideSpinner(id);
            this.setAttribute('data-loaded', 'true');
        });
    });
});

// Keyboard shortcuts
const dashboardKeys = {
    '1': 'dgm', '2': 'pa1', '3': 'pa2', '4': 'counselor',
    '5': 'marketing', '6': 'librarian', '7': 'hostel',
    '8': 'project', '9': 'bursar', '0': 'devtools'
};

document.addEventListener('keydown', function(e) {
    // Only trigger if no input is focused
    if (document.activeElement.tagName === 'INPUT' || 
        document.activeElement.tagName === 'TEXTAREA' ||
        document.activeElement.tagName === 'SELECT') {
        return;
    }
    
    if (dashboardKeys[e.key]) {
        showTab(dashboardKeys[e.key]);
    }
});

// Tab switch function
function showTab(id) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.remove('active');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById('tab-' + id);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Update button states
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    const activeBtn = document.querySelector(`.tab-btn[data-tab="${id}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }

    // Auto resize iframe
    autoResizeIframe(id);
}

// Spinner hide
function hideSpinner(id) {
    const spinner = document.getElementById('spinner-' + id);
    if (spinner) {
        spinner.style.display = 'none';
    }
}

// Fullscreen toggle
function toggleFullscreen(id) {
    const block = document.getElementById('tab-' + id);
    if (block) {
        block.classList.toggle('fullscreen-mode');
        
        const btn = block.querySelector('.fullscreen-btn i');
        if (btn) {
            if (block.classList.contains('fullscreen-mode')) {
                btn.className = 'fa-solid fa-compress';
            } else {
                btn.className = 'fa-solid fa-expand';
            }
        }
    }
}

// Auto resize iframe
function autoResizeIframe(id) {
    const iframe = document.getElementById('frame-' + id);
    if (iframe && iframe.getAttribute('data-loaded') === 'false') {
        iframe.style.height = '1500px';
    }
}

// Dashboard options for selectors
function getDashboardOptions() {
    return `
        <option value="${dashboardRoutes.dgm}">DGM</option>
        <option value="${dashboardRoutes.pa1}">PA L1</option>
        <option value="${dashboardRoutes.pa2}">PA L2</option>
        <option value="${dashboardRoutes.counselor}">Counselor</option>
        <option value="${dashboardRoutes.marketing}">Marketing</option>
        <option value="${dashboardRoutes.librarian}">Librarian</option>
        <option value="${dashboardRoutes.hostel}">Hostel</option>
        <option value="${dashboardRoutes.project}">Project Tutor</option>
        <option value="${dashboardRoutes.bursar}">Bursar</option>
    `;
}

// Multi split: two pane
function twoView() {
    createMultiView(2);
}

// Multi split: three pane
function threeView() {
    createMultiView(3);
}

// Multi split: four pane
function fourView() {
    createMultiView(4);
}

// Create multi split view
function createMultiView(count) {
    const area = document.getElementById('multi-area');
    area.innerHTML = '';

    const container = document.createElement('div');
    container.className = 'multi-container';

    for (let i = 1; i <= count; i++) {
        const box = document.createElement('div');
        box.className = 'multi-box';
        
        const select = document.createElement('select');
        select.className = 'selector';
        select.innerHTML = getDashboardOptions();
        select.onchange = function() {
            changeMultiFrame(this);
        };
        
        const iframe = document.createElement('iframe');
        iframe.src = dashboardRoutes.pa1;
        
        box.appendChild(select);
        box.appendChild(iframe);
        container.appendChild(box);
    }
    
    area.appendChild(container);
}

// Change multi-view iframe source
function changeMultiFrame(selectObj) {
    const iframe = selectObj.parentElement.querySelector('iframe');
    if (iframe) {
        iframe.src = selectObj.value;
    }
}

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/dashboards/developer_dashboard.blade.php ENDPATH**/ ?>
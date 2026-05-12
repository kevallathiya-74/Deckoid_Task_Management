<div class="top-bar">
    <button class="sidebar-toggler me-3" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <h5 class="fw-bold mb-0 text-neutral-800"><?= $title ?? 'Taskly' ?></h5>
    
    <div class="ms-auto d-flex align-items-center">
        <div class="me-3 text-end d-none d-md-block">
            <div class="text-xs fw-bold text-neutral-400 text-uppercase">Today</div>
            <div class="text-sm fw-bold text-neutral-700"><?= date('D, d M Y') ?></div>
        </div>
        <div class="bg-neutral-100 p-2 rounded-circle">
            <i class="fas fa-calendar-alt text-neutral-500"></i>
        </div>
    </div>
</div>

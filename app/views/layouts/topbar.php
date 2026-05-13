<div class="top-bar">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggler me-3 btn btn-light d-lg-none rounded-3 border-0" id="mobileSidebarToggle">
                <i class="fas fa-bars-staggered"></i>
            </button>
            
            <div class="page-title d-none d-md-block">
                <h5 class="fw-bold mb-0 text-neutral-900"><?= $title ?? 'Dashboard' ?></h5>
                <p class="mb-0 text-xs text-neutral-400">Welcome back, <?= explode(' ', $_SESSION['user_name'] ?? 'User')[0] ?>!</p>
            </div>
            
            <div class="d-md-none">
                <h6 class="fw-bold mb-0 text-neutral-900"><?= $title ?? 'Dashboard' ?></h6>
            </div>
        </div>

        <div class="topbar-actions d-flex align-items-center gap-3">
            <!-- User Profile -->
            <div class="dropdown">
                <div class="user-pill d-flex align-items-center p-1 pe-3 gap-2 rounded-pill bg-neutral-50 border cursor-pointer" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'User') ?>&background=8b5cf6&color=fff" class="rounded-circle" width="32" height="32">
                    <span class="d-none d-lg-block text-sm fw-bold text-neutral-800"><?= explode(' ', $_SESSION['user_name'] ?? 'User')[0] ?></span>
                    <i class="fas fa-chevron-down text-xs text-neutral-400"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2">
                    <li><a class="dropdown-item py-2 px-3 d-flex align-items-center gap-3" href="<?= url('/' . ($_SESSION['user_role'] ?? 'staff') . '/profile') ?>"><i class="fas fa-user-circle text-primary"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li><a class="dropdown-item py-2 px-3 d-flex align-items-center gap-3 text-danger" href="<?= url('/logout') ?>"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.action-btn {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    background: white;
    color: var(--neutral-500);
    position: relative;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: var(--neutral-50);
    color: var(--primary-600);
    border-color: var(--primary-200);
}

.badge-dot {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    background: var(--primary-500);
    border: 2px solid white;
    border-radius: 50%;
}

.user-pill {
    transition: all 0.3s ease;
}

.user-pill:hover {
    border-color: var(--primary-300);
    background: white;
}
</style>

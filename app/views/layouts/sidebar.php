<aside class="sidebar d-flex flex-column p-3 bg-white" id="sidebar">
    <div class="sidebar-header mb-4 px-3 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-primary mb-0 logo-text"><i class="fas fa-tasks me-2"></i>Taskly</h4>
        <button class="btn btn-sm btn-light d-md-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="<?= url('/dashboard') ?>" class="nav-link <?= $active_page == 'dashboard' ? 'active' : 'text-neutral-700' ?>" title="Dashboard">
                <i class="fas fa-chart-line me-2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= url('/projects') ?>" class="nav-link <?= $active_page == 'projects' ? 'active' : 'text-neutral-700' ?>" title="Projects">
                <i class="fas fa-project-diagram me-2"></i>
                <span>Projects</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= url('/tasks') ?>" class="nav-link <?= $active_page == 'tasks' ? 'active' : 'text-neutral-700' ?>" title="Tasks">
                <i class="fas fa-list-check me-2"></i>
                <span>Tasks</span>
            </a>
        </li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
        <li class="nav-item mb-2">
            <a href="<?= url('/staff') ?>" class="nav-link <?= $active_page == 'staff' ? 'active' : 'text-neutral-700' ?>" title="Staff Management">
                <i class="fas fa-users me-2"></i>
                <span>Staff Management</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    
    <hr>
    
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-neutral-900 text-decoration-none dropdown-toggle px-3" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= isset($_SESSION['user_avatar']) ? $_SESSION['user_avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User') ?>" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong class="logo-text"><?= isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User' ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-light text-small shadow" aria-labelledby="dropdownUser">
            <li><a class="dropdown-item" href="<?= url('/settings') ?>"><i class="fas fa-cog me-2"></i>Settings</a></li>
            <li><a class="dropdown-item" href="<?= url('/profile') ?>"><i class="fas fa-user me-2"></i>Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= url('/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Sign out</a></li>
        </ul>
    </div>
</aside>

<style>
.sidebar .nav-link {
    color: var(--neutral-600);
    border-radius: var(--radius-lg);
    padding: 0.75rem 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    white-space: nowrap;
}

.sidebar .nav-link:hover {
    background-color: var(--neutral-100);
    color: var(--primary-600);
}

.sidebar .nav-link.active {
    background-color: var(--primary-500);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(53, 109, 255, 0.3);
}

.text-neutral-700 {
    color: var(--neutral-700);
}
</style>

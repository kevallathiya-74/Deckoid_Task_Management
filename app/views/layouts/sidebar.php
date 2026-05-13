<aside class="sidebar" id="sidebar">
    <div class="sidebar-header p-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="logo-box-clean">
                <img src="<?= url('assets/image/logo.png') ?>" alt="Logo" width="35" height="35">
            </div>
            <h4 class="mb-0 brand-text" style="letter-spacing: -0.5px; font-size: 1.25rem;"><b>Deckoid</b><span style="color: #6366f1;">Tasks</span></h4>
        </div>
        <button class="btn btn-link p-0 text-neutral-400 sidebar-toggle-btn d-none d-md-block" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="btn btn-sm btn-light d-md-none border-0" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-content px-2 flex-grow-1">
        <?php $prefix = (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') ? 'admin' : 'staff'; ?>
        
        <div class="sidebar-label px-4 mb-2 mt-2">Workspace</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a href="<?= url("/$prefix/dashboard") ?>" class="nav-link <?= $active_page == 'dashboard' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i class="fas fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url("/$prefix/projects") ?>" class="nav-link <?= $active_page == 'projects' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Projects">
                    <i class="fas fa-folder-closed"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url("/$prefix/tasks") ?>" class="nav-link <?= $active_page == 'tasks' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Tasks">
                    <i class="fas fa-tasks"></i>
                    <span>Tasks</span>
                </a>
            </li>
        </ul>

        <?php if ($prefix == 'admin'): ?>
        <div class="sidebar-label px-4 mb-2 mt-4">Management</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a href="<?= url('/admin/staff') ?>" class="nav-link <?= $active_page == 'staff' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Team Members">
                    <i class="fas fa-users"></i>
                    <span>Team Members</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url('/admin/kpi') ?>" class="nav-link <?= $active_page == 'kpi' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="KPI Management">
                    <i class="fas fa-chart-line"></i>
                    <span>KPI Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url('/admin/leaves') ?>" class="nav-link <?= $active_page == 'leaves_admin' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Leave Requests">
                    <i class="fas fa-calendar"></i>
                    <span>Leave Requests</span>
                </a>
            </li>
        </ul>
        <?php endif; ?>

        <div class="sidebar-label px-4 mb-2 mt-4">Personal</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a href="<?= url("/$prefix/profile") ?>" class="nav-link <?= $active_page == 'profile' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Account Settings">
                    <i class="fas fa-circle-user"></i>
                    <span>Account Settings</span>
                </a>
            </li>
            <?php if ($prefix == 'staff'): ?>
            <li class="nav-item">
                <a href="<?= url("/$prefix/leaves") ?>" class="nav-link <?= $active_page == 'leaves' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="My Leaves">
                    <i class="fas fa-calendar-day"></i>
                    <span>My Leaves</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="sidebar-footer p-3 border-top border-light">
        <div class="user-profile-card-combined d-flex align-items-center gap-3 p-2 rounded-4">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'User') ?>&background=8b5cf6&color=fff" class="rounded-circle shadow-sm" width="38" height="38">
            <div class="flex-grow-1 overflow-hidden brand-name-container">
                <h6 class="mb-0 text-sm fw-bold text-neutral-900 text-truncate"><?= $_SESSION['user_name'] ?? 'User' ?></h6>
                <p class="mb-0 text-xs text-neutral-500 text-capitalize"><?= $_SESSION['user_role'] ?? 'Member' ?></p>
            </div>
            <a href="<?= url('/logout') ?>" class="logout-icon-btn text-neutral-400 hover-text-danger transition-all" data-bs-toggle="tooltip" data-bs-placement="top" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>

<style>
.sidebar {
    background: white !important;
    border-right: 1px solid var(--neutral-100);
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    z-index: 1050;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    box-shadow: 10px 0 30px rgba(0,0,0,0.02);
}

.sidebar-header {
    padding: 2rem 1.5rem;
}

.logo-box-clean img {
    width: 35px;
    height: 35px;
    object-fit: contain;
}

.sidebar-content {
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
    flex-grow: 1;
}

.sidebar-content::-webkit-scrollbar {
    display: none;
}

.sidebar-label {
    padding: 1.5rem 1.5rem 0.5rem;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--neutral-400);
}

.sidebar .nav-link {
    padding: 0.85rem 1.25rem;
    margin: 0.25rem 0.75rem;
    color: var(--neutral-600);
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.2s ease;
    border-radius: 12px;
}

.sidebar .nav-link i {
    width: 24px;
    font-size: 1.1rem;
    margin-right: 12px;
    text-align: center;
}

.sidebar .nav-link:hover {
    background: #f8fafc;
    color: var(--primary-600);
}

.sidebar .nav-link.active {
    background: var(--grad-primary) !important;
    color: white !important;
    box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.4);
    position: relative;
}

.sidebar .nav-link.active i {
    color: white !important;
}

.sidebar .nav-link.active::after {
    content: '';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    background: white;
    border-radius: 50%;
}

.user-profile-card-combined {
    background: #f8fafc;
    transition: all 0.3s ease;
}

.user-profile-card-combined:hover {
    background: #f1f5f9;
}

.logout-icon-btn {
    padding: 8px;
    border-radius: 8px;
}

.logout-icon-btn:hover {
    background: #fee2e2;
    color: #ef4444 !important;
}

.sidebar-toggle-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar-toggle-btn:hover {
    background: #f1f5f9;
}

/* Specific Icon Colors - When NOT active */
.nav-link:not(.active)[href*="dashboard"] i { color: #8b5cf6; }
.nav-link:not(.active)[href*="projects"] i { color: #64748b; }
.nav-link:not(.active)[href*="tasks"] i { color: #3b82f6; }
.nav-link:not(.active)[href*="staff"] i { color: #0ea5e9; }
.nav-link:not(.active)[href*="kpi"] i { color: #10b981; }
.nav-link:not(.active)[href*="leaves"] i { color: #f59e0b; }
.nav-link:not(.active)[href*="profile"] i { color: #475569; }

/* Collapsed State Overrides */
.sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
}

.sidebar.collapsed .sidebar-label,
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .brand-text,
.sidebar.collapsed .brand-name-container,
.sidebar.collapsed .nav-link.active::after {
    display: none;
}

.sidebar.collapsed .sidebar-header {
    justify-content: space-between;
    padding: 1.5rem 1rem !important;
}

.sidebar.collapsed .logo-box-clean {
    display: flex;
    justify-content: center;
}

.sidebar.collapsed .sidebar-toggle-btn {
    display: flex !important;
    justify-content: center;
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.85rem;
    margin: 0.25rem 0.5rem;
}

.sidebar.collapsed .nav-link i {
    margin-right: 0 !important;
}

.sidebar.collapsed .user-profile-card-combined {
    padding: 0.5rem !important;
    justify-content: center;
}

.sidebar.collapsed .logout-icon-btn {
    display: none;
}

.sidebar.collapsed .sidebar-toggle-btn i {
    transform: rotate(180deg);
}

.brand-text b { font-weight: 800; }
</style>

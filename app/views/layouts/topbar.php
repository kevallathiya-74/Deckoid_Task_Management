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
            <!-- Notifications -->
            <div class="dropdown">
                <button class="action-btn position-relative" data-bs-toggle="dropdown" id="notificationDropdownBtn">
                    <i class="far fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white d-none" id="notificationBadge" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">
                        0
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 animate-fade-in" style="width: 320px; right: 0;">
                    <div class="p-3 border-bottom border-light bg-neutral-50 rounded-top-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-neutral-900 mb-0 font-outfit">Notifications</h6>
                    </div>
                    <div class="list-group list-group-flush" id="notificationList" style="max-height: 350px; overflow-y: auto;">
                        <div class="text-center py-4 text-neutral-400 text-xs fw-medium">Loading notifications...</div>
                    </div>
                    <div class="p-2 border-top border-light text-center bg-neutral-50 rounded-bottom-4">
                        <a href="<?= url('/' . (isAdminOrSubAdmin() ? 'admin' : 'staff') . '/tasks') ?>" class="text-xs fw-bold text-primary text-decoration-none">View All Tasks</a>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="dropdown">
                <div class="user-pill d-flex align-items-center p-1 pe-3 gap-2 rounded-pill bg-neutral-50 border cursor-pointer" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? 'User') ?>&background=8b5cf6&color=fff" class="rounded-circle" width="32" height="32">
                    <span class="d-none d-lg-block text-sm fw-bold text-neutral-800"><?= explode(' ', $_SESSION['user_name'] ?? 'User')[0] ?></span>
                    <i class="fas fa-chevron-down text-xs text-neutral-400"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2">
                    <li><a class="dropdown-item py-2 px-3 d-flex align-items-center gap-3" href="<?= url('/' . (isAdminOrSubAdmin() ? 'admin' : 'staff') . '/profile') ?>"><i class="fas fa-user-circle text-primary"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li><a class="dropdown-item py-2 px-3 d-flex align-items-center gap-3 text-danger" href="<?= url('/logout') ?>"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Login Overdue Alert Modal -->
<div class="modal fade" id="overdueLoginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-danger border-opacity-50 p-2">
            <div class="modal-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="fw-bold text-neutral-900 mb-2">Attention Required</h3>
                <p class="text-neutral-500 mb-4 pb-2">You have tasks that are past their deadline. Please review your Overdue Tasks immediately.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-secondary-soft rounded-pill px-4 fw-bold" data-bs-dismiss="modal">I understand</button>
                    <a href="<?= url('/staff/tasks') ?>" class="btn btn-danger rounded-pill px-4 fw-bold shadow-danger">View Tasks</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function fetchNotifications() {
        $.get('<?= url('/api/dashboard/notifications') ?>', function(res) {
            if (res.status === 'success' && res.data) {
                const badge = $('#notificationBadge');
                const list = $('#notificationList');
                
                let count = res.data.count;
                if (count > 0) {
                    badge.text(count > 99 ? '99+' : count).removeClass('d-none');
                } else {
                    badge.addClass('d-none');
                }
                
                if (res.data.items && res.data.items.length > 0) {
                    let html = '';
                    res.data.items.forEach(item => {
                        html += `
                        <a href="${item.link}" class="list-group-item list-group-item-action p-3 border-bottom-0 border-light border-bottom">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-${item.type}-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                                    <i class="fas ${item.icon} text-${item.type} text-xs"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="text-sm fw-bold text-neutral-900 mb-0">${item.title}</h6>
                                    </div>
                                    <p class="text-xs text-neutral-500 mb-1 lh-sm">${item.message}</p>
                                    <span class="text-xs text-neutral-400 fw-bold" style="font-size: 0.65rem;">${item.time}</span>
                                </div>
                            </div>
                        </a>
                        `;
                    });
                    list.html(html);
                } else {
                    list.html('<div class="text-center py-4 text-neutral-400 text-xs fw-medium">No new notifications</div>');
                }
            }
        }).fail(function() {
            $('#notificationList').html('<div class="text-center py-4 text-danger text-xs fw-medium">Failed to load notifications</div>');
        });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    function checkOverdueStatus() {
        $.when(
            $.get('<?= url('/api/tasks/overdue') ?>'),
            $.get('<?= url('/api/todos/overdue') ?>')
        ).done(function(tasksRes, todosRes) {
            let hasOverdue = false;
            if (tasksRes[0] && tasksRes[0].status === 'success' && tasksRes[0].data && tasksRes[0].data.length > 0) hasOverdue = true;
            if (todosRes[0] && todosRes[0].status === 'success' && todosRes[0].data && todosRes[0].data.length > 0) hasOverdue = true;

            if (hasOverdue) {
                $('.overdue-sidebar-icon').addClass('text-danger');
                $('.overdue-sidebar-text').addClass('text-danger fw-bold');
                
                const lastShown = localStorage.getItem('lastOverdueReminderTime');
                const now = new Date().getTime();
                const twoHours = 2 * 60 * 60 * 1000;
                
                if (!lastShown || (now - parseInt(lastShown) > twoHours)) {
                    $('#overdueLoginModal').modal('show');
                    localStorage.setItem('lastOverdueReminderTime', now.toString());
                }
            } else {
                $('.overdue-sidebar-icon').removeClass('text-danger');
                $('.overdue-sidebar-text').removeClass('text-danger fw-bold');
            }
        });
    }
    
    checkOverdueStatus();
});
</script>

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

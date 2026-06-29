<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        <!-- Dashboard Header -->
        <!-- <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-neutral-900 mb-0">Project Dashboard</h5>
                <p class="text-neutral-500 mb-0 text-xs">Real-time overview of your workspace performance</p>
            </div>
        </div> -->

        <!-- Statistics Grid -->
        <div class="row g-3 mb-4">
            <div class="col-xl col-md-4">
                <div class="dashboard-card bg-primary text-white glass-card">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <div>
                            <p class="mb-1 fw-bold" style="font-size: 18px;">Total Projects</p>
                            <h4 class="mb-0 fw-bold     fw-bold text-white text-xs" style="font-size: 32px; "><?= $stats['total_projects'] ?></h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-project-diagram fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4">
                <div class="dashboard-card bg-warning text-white glass-card">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <div>
                            <p class="mb-1 fw-bold" style="font-size: 18px;">Due Today</p>
                            <h4 class="mb-0 fw-bold fw-bold text-white text-xs" style="font-size: 32px;"><?= $stats['due_today'] ?></h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-calendar-day fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4">
                <div class="dashboard-card bg-danger text-white glass-card">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <div>
                            <p class="mb-1 fw-bold" style="font-size: 18px;">Overdue Tasks</p>
                            <h4 class="mb-0 fw-bold fw-bold text-white text-xs" style="font-size: 32px;"><?= $stats['overdue_tasks'] ?></h4>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-exclamation-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once ROOT_PATH . '/app/views/tasks/_shared_list.php'; ?>
        <!-- Growth Graph -->
        <!-- <div class="row mb-5">
            <div class="col-12">
                <div class="glass-card p-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div>
                            <h5 class="fw-bold text-neutral-900 mb-1">Growth Analysis</h5>
                            <p class="text-xs text-neutral-400 mb-0">Visualizing team productivity and task completion velocity</p>
                        </div>
                        <select class="form-select border border-neutral-200 bg-white rounded-pill px-3" style="width: auto; height: 45px; min-height: 45px;">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                        </select>
                    </div>
                    <div style="height: 380px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</main>


<?php $is_dashboard = true; ?>
<script>
$(document).ready(function() {
    pollAlerts();
    setInterval(pollAlerts, 30000);
});

function pollAlerts() {
    <?php if (isAdminOrSubAdmin()): ?>
    $.get('<?= url('/api/dashboard/alerts') ?>', function(res) {
        if ((res.status === 'success' || res.success) && res.data && res.data.length > 0) {
            res.data.forEach(alert => {
                if ($(`#alert-${alert.id}`).length === 0) {
                    showAdminPopup(alert);
                }
            });
        }
    }).fail(function() {
        console.error("Failed to load alerts");
    });
    <?php endif; ?>
}

function showAdminPopup(alert) {
    const escapeHtml = (unsafe) => {
        return (unsafe || '').toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    };
    
    const html = `
        <div id="alert-${alert.id}" class="glass-card mb-3 p-4 animate-slide-in-right shadow-deep border-start border-danger border-4" style="min-width: 380px;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="badge bg-danger-soft text-danger px-3 py-1 rounded-pill text-xs fw-bold">CRITICAL TASK ALERT</div>
                <button onclick="dismissAlert('${alert.id}')" class="btn-close text-xs"></button>
            </div>
            <p class="text-sm text-neutral-800 mb-4 leading-relaxed">
                Task <span class="fw-bold text-primary">"${escapeHtml(alert.task_title)}"</span> assigned to 
                <span class="fw-bold">${escapeHtml(alert.staff_name)}</span> has been marked as <span class="text-danger fw-bold">incomplete</span>.
            </p>
            <div class="d-flex gap-3">
                <button onclick="reassignTask('${alert.task_id}', '${alert.id}')" class="btn btn-primary py-2 text-xs flex-grow-1">Reassign Now</button>
                <button onclick="dismissAlert('${alert.id}')" class="btn btn-secondary py-2 text-xs flex-grow-1">Dismiss</button>
            </div>
        </div>
    `;
    $('#admin-alert-container').prepend(html);
}

function dismissAlert(id) {
    $.post('<?= url('/api/dashboard/alerts/read') ?>', { id: id }, function(res) {
        if (res.status === 'success' || res.success) {
            $(`#alert-${id}`).addClass('animate-fade-out');
            setTimeout(() => $(`#alert-${id}`).remove(), 300);
        }
    });
}

function reassignTask(taskId, alertId) {
    window.location.href = `<?= url('/admin/tasks') ?>?edit=${taskId}`;
}
</script>

<style>
.icon-shape {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.stat-icon-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
}

.dot { width: 10px; height: 10px; border-radius: 50%; }
.shadow-danger { box-shadow: 0 0 10px rgba(244, 63, 94, 0.4); }
.shadow-warning { box-shadow: 0 0 10px rgba(245, 158, 11, 0.4); }
.shadow-primary { box-shadow: 0 0 10px rgba(139, 92, 246, 0.4); }

.ls-1 { letter-spacing: 1px; }

.priority-card:hover {
    border-color: var(--primary-300) !important;
    background: var(--primary-50) !important;
    transform: translateX(5px);
}

.hover-translate-y:hover {
    transform: translateY(-3px);
    border-color: var(--primary-200) !important;
}

.custom-checkbox .form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 6px;
    cursor: pointer;
}

.custom-checkbox .form-check-input:checked {
    background-color: var(--primary-500);
    border-color: var(--primary-500);
}

.animate-slide-in-right {
    animation: slideInRight 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

@keyframes slideInRight {
    from { transform: translateX(50px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.leading-relaxed { line-height: 1.6; }
</style>


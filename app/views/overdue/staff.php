<?php
$title = "My Overdue Tasks";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content">
    <?php require_once __DIR__ . '/../layouts/topbar.php'; ?>

    <main class="container-fluid px-4 py-4">

        <!-- Compact SaaS KPI Cards -->
        <div class="row g-4 mb-4 overdue-kpi-row">

            <!-- Card 1: My Overdue Tasks -->
            <div class="col-12 col-md-6">
                <div class="overdue-kpi-card overdue-kpi-danger">
                    <div class="overdue-kpi-icon" style="background:#FEE2E2;">
                        <i class="fas fa-exclamation-triangle" style="color:#EF4444;"></i>
                    </div>
                    <div class="overdue-kpi-body">
                        <div class="overdue-kpi-label">MY OVERDUE TASKS</div>
                        <div class="overdue-kpi-value"><?= $summary['total_overdue'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Max Overdue Days -->
            <div class="col-12 col-md-6">
                <div class="overdue-kpi-card">
                    <div class="overdue-kpi-icon" style="background:#FEF3C7;">
                        <i class="fas fa-history" style="color:#F59E0B;"></i>
                    </div>
                    <div class="overdue-kpi-body">
                        <div class="overdue-kpi-label">MAX OVERDUE DAYS</div>
                        <div class="overdue-kpi-value">
                            <?= $summary['max_overdue_days'] ?>
                            <span class="overdue-kpi-unit">days</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Filter & List -->
        <div class="glass-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-neutral-900 mb-0 font-outfit">My Overdue Tasks</h5>
                <div class="d-flex gap-3">
                    <select class="form-select glass-input fw-bold border-0 bg-neutral-50 rounded-pill px-4" id="taskTypeFilter" style="width: 200px;">
                        <option value="All">All</option>
                        <option value="Tasks">Tasks</option>
                        <option value="Todo Tasks">Todo Tasks</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="overdueTasksTable" style="min-width: 900px;">
                    <thead>
                        <tr>
                            <th class="ps-4 text-xs fw-bold text-uppercase text-neutral-400">Task Title</th>
                            <th class="text-xs fw-bold text-uppercase text-neutral-400">Task Type</th>
                            <th class="text-xs fw-bold text-uppercase text-neutral-400">Assigned To</th>
                            <th class="text-xs fw-bold text-uppercase text-neutral-400">Due Date</th>
                            <th class="text-xs fw-bold text-uppercase text-neutral-400">Status</th>
                            <th class="text-xs fw-bold text-uppercase text-neutral-400">Days Overdue</th>
                            <th class="text-xs fw-bold text-uppercase text-neutral-400">Project</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
$(document).ready(function() {
    const table = $('#overdueTasksTable').DataTable({
        serverSide: true,
        ajax: {
            url: '<?= url('/api/tasks/overdue') ?>',
            dataSrc: 'data',
            data: function(d) {
                d.task_type = $('#taskTypeFilter').val();
            }
        },
        columns: [
            { 
                data: 'title',
                render: function(data) {
                    return `<div class="fw-bold text-neutral-900 font-outfit px-3 py-2">${data}</div>`;
                }
            },
            {
                data: 'task_type',
                render: function(data) {
                    const badgeClass = data === 'Task' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info';
                    return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">${data}</span>`;
                }
            },
            {
                data: 'assigned_to_name',
                render: function(data) {
                    return `<div class="fw-bold text-neutral-700 py-2">${data || 'Unassigned'}</div>`;
                }
            },
            {
                data: 'due_date',
                render: function(data) {
                    return `<div class="text-danger fw-bold py-2"><i class="far fa-calendar-times me-2"></i>${moment(data).format('DD MMM YYYY')}</div>`;
                }
            },
            {
                data: 'status',
                render: function(data) {
                    if (!data) data = 'pending';
                    let cls = 'bg-primary-soft text-primary';
                    if (data === 'in_progress') cls = 'bg-warning-soft text-warning';
                    if (data === 'review') cls = 'bg-info-soft text-info';
                    if (data === 'completed') cls = 'bg-success-soft text-success';
                    return `<div><span class="badge ${cls} rounded-pill py-1 px-3 text-capitalize border-0" style="font-size: 0.7rem; font-weight: 700;">${data.replace('_', ' ')}</span></div>`;
                }
            },
            {
                data: 'days_overdue',
                render: function(data) {
                    return `<span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-bold">${data} Days</span>`;
                }
            },
            {
                data: 'project_name',
                render: function(data) {
                    return `<div class="text-neutral-500 fw-bold text-xs text-uppercase py-2">${data || '-'}</div>`;
                }
            }
        ],
        order: [[4, 'desc']],
        dom: 't<"d-flex justify-content-between align-items-center p-4 border-top border-light"ip>',
        language: {
            search: "",
            searchPlaceholder: "Search overdue tasks...",
            lengthMenu: "_MENU_ per page",
            emptyTable: "No overdue tasks found."
        }
    });

    $('.dataTables_filter input').addClass('form-control border-0 bg-neutral-50 rounded-pill px-4').attr('placeholder', 'Search tasks...').css({'height': '45px'});

    $('#taskTypeFilter').on('change', function() {
        table.ajax.reload();
    });
});
</script>

<style>
/* ─── Compact SaaS KPI Cards ──────────────────────────────────────── */
.overdue-kpi-card {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 20px;
    min-height: 100px;
    max-height: 130px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.overdue-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}

.overdue-kpi-icon {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
}

.overdue-kpi-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.overdue-kpi-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #6B7280;
    text-transform: uppercase;
    white-space: nowrap;
}

.overdue-kpi-value {
    font-size: 40px;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    font-family: 'Outfit', sans-serif;
    display: flex;
    align-items: baseline;
    gap: 6px;
}

.overdue-kpi-unit {
    font-size: 18px;
    font-weight: 600;
    color: #6B7280;
}

/* DataTables filter */
.dataTables_length, .dataTables_filter {
    padding: 0 !important;
}
.dataTables_filter input {
    background: rgba(248, 250, 252, 0.8) !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 0.6rem 1.25rem !important;
    font-size: 0.85rem !important;
    font-weight: 500;
    transition: all 0.3s ease;
    width: 250px !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

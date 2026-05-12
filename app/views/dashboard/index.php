<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col">
                <h4 class="fw-bold">Welcome back, <?= explode(' ', $_SESSION['user_name'])[0] ?></h4>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Total Projects</div>
                            <div class="stats-value"><?= $stats['total_projects'] ?></div>
                        </div>
                        <div class="stats-icon bg-primary-100 text-primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Active Tasks</div>
                            <div class="stats-value text-warning-700"><?= $stats['active_tasks'] ?></div>
                        </div>
                        <div class="stats-icon bg-warning-100 text-warning-700">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Total Staff</div>
                            <div class="stats-value"><?= $stats['total_staff'] ?></div>
                        </div>
                        <div class="stats-icon bg-success-100 text-success">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Completed</div>
                            <div class="stats-value text-success"><?= $stats['completed_projects'] ?></div>
                        </div>
                        <div class="stats-icon bg-success-100 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Charts Section -->
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Project Performance</h5>
                        <div class="badge bg-neutral-100 text-neutral-600 px-3">Live Data</div>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="projectChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Task Priorities</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="taskPriorityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Activity -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Recently Created Tasks</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-neutral-50 text-neutral-500 text-uppercase text-xs fw-bold">
                                    <tr>
                                        <th class="ps-4">Task</th>
                                        <th>Project</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_tasks as $task): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?= $task['title'] ?></td>
                                        <td><span class="badge bg-primary-50 text-primary-700"><?= $task['project_name'] ?></span></td>
                                        <td><?= $task['assigned_to_name'] ?></td>
                                        <td>
                                            <span class="badge bg-neutral-100 text-neutral-600 rounded-pill px-3">
                                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 text-neutral-500"><?= date('d M, Y', strtotime($task['due_date'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recent_tasks)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-neutral-400">No recent tasks found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    // Fetch Chart Data
    $.getJSON('<?= url('/api/dashboard/charts') ?>', function(data) {
        // Project Chart (Status Distribution)
        const projectCtx = document.getElementById('projectChart').getContext('2d');
        new Chart(projectCtx, {
            type: 'bar',
            data: {
                labels: data.projects.map(p => p.status.toUpperCase()),
                datasets: [{
                    label: 'Projects',
                    data: data.projects.map(p => p.count),
                    backgroundColor: '#4F46E5',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Task Priority Chart (Pie)
        const taskCtx = document.getElementById('taskPriorityChart').getContext('2d');
        new Chart(taskCtx, {
            type: 'doughnut',
            data: {
                labels: data.tasks.map(t => t.priority.toUpperCase()),
                datasets: [{
                    data: data.tasks.map(t => t.count),
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#6366F1'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
    });
});
</script>

<style>
.stats-card {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid var(--neutral-100);
}
.stats-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--neutral-400);
    margin-bottom: 0.5rem;
}
.stats-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--neutral-800);
}
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-primary-100 { background-color: #EEF2FF; }
.text-primary { color: #4F46E5; }
.bg-warning-100 { background-color: #FFFBEB; }
.text-warning-700 { color: #B45309; }
.bg-success-100 { background-color: #ECFDF5; }
.text-success { color: #10B981; }
.bg-neutral-100 { background-color: #F3F4F6; }
.text-neutral-600 { color: #4B5563; }
.text-neutral-500 { color: #6B7280; }
.text-neutral-400 { color: #9CA3AF; }

.card { border-radius: 1rem; border: 1px solid var(--neutral-100); box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
</style>

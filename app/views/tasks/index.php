<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">Task Management</h2>
                <p class="text-neutral-500">Track and manage project tasks and assignments.</p>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="fas fa-plus me-2"></i>New Task
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Project</label>
                        <select class="form-select" name="project_id" id="filter_project">
                            <option value="">All Projects</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($project_id) && $project_id == $p['id']) ? 'selected' : '' ?>>
                                    <?= $p['project_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Assigned To</label>
                        <select class="form-select" name="assigned_to" id="filter_assignee">
                            <option value="">All Staff</option>
                            <?php foreach ($staff as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Status</label>
                        <select class="form-select" name="status" id="filter_status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">Review</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="resetFilters" class="btn btn-light w-100">Reset Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tasks Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tasksTable">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="ps-4">Task Details</th>
                                <th>Project</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTaskForm" action="<?= url('/api/tasks') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Task Title</label>
                        <input type="text" class="form-control" name="title" required placeholder="e.g., Design homepage hero section">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Provide task details..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Project</label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Select Project</option>
                                <?php foreach ($projects as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= (isset($project_id) && $project_id == $p['id']) ? 'selected' : '' ?>>
                                        <?= $p['project_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Assign To</label>
                            <select class="form-select" name="assigned_to" required>
                                <option value="">Select Staff</option>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['role_name'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Department Role</label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Priority</label>
                            <select class="form-select" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" required value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Update Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTaskForm" action="<?= url('/api/tasks/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Task Title</label>
                        <input type="text" class="form-control" name="title" id="edit_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Assign To</label>
                            <select class="form-select" name="assigned_to" id="edit_assigned_to" required>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Department Role</label>
                            <select class="form-select" name="role_id" id="edit_role_id" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Priority</label>
                            <select class="form-select" name="priority" id="edit_priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" id="edit_due_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Progress (%)</label>
                            <input type="number" class="form-control" name="progress_percentage" id="edit_progress" min="0" max="100">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Status Notes</label>
                            <input type="text" class="form-control" name="status_notes" id="edit_notes" placeholder="Reason for status change...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const table = $('#tasksTable').DataTable({
        ajax: {
            url: '<?= url('/api/tasks') ?>',
            dataSrc: function(json) {
                if (json.success === false) {
                    toastr.error(json.message || 'Failed to load tasks');
                    return [];
                }
                return json.data;
            },
            data: function(d) {
                d.project_id = $('#filter_project').val();
                d.assigned_to = $('#filter_assignee').val();
                d.status = $('#filter_status').val();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                toastr.error('Ajax error loading tasks');
            }
        },
        columns: [
            { 
                data: 'title',
                render: function(data, type, row) {
                    return `
                        <div class="ps-2">
                            <div class="fw-bold text-neutral-900">${data}</div>
                            <div class="text-xs text-neutral-500 text-truncate" style="max-width: 250px;">${row.description || 'No description'}</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'project_name',
                render: function(data) {
                    return `<span class="text-xs fw-medium text-primary-600">${data}</span>`;
                }
            },
            { 
                data: 'assigned_to_name',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-neutral-100 text-neutral-600 rounded-circle me-2 d-flex align-items-center justify-content-center text-xs fw-bold" style="width: 24px; height: 24px;">
                                ${data.charAt(0)}
                            </div>
                            <div class="text-xs">
                                <div class="fw-bold">${data}</div>
                                <div class="text-neutral-400" style="font-size: 10px;">${row.role_name}</div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'priority',
                render: function(data) {
                    let cls = 'bg-neutral-100 text-neutral-600';
                    if (data === 'high') cls = 'bg-danger-100 text-danger';
                    if (data === 'medium') cls = 'bg-warning-100 text-warning-700';
                    return `<span class="badge ${cls} text-capitalize px-2 py-1 rounded text-xs">${data}</span>`;
                }
            },
            { 
                data: 'progress_percentage',
                render: function(data) {
                    return `
                        <div style="width: 100px;">
                            <div class="d-flex justify-content-between text-xs mb-1">
                                <span>${data}%</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: ${data}%" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let cls = 'bg-neutral-100 text-neutral-600';
                    if (data === 'in_progress') cls = 'bg-primary-100 text-primary-600';
                    if (data === 'review') cls = 'bg-warning-100 text-warning-700';
                    if (data === 'completed') cls = 'bg-success-100 text-success';
                    return `<span class="badge ${cls} text-capitalize px-3 py-2 rounded-pill">${data.replace('_', ' ')}</span>`;
                }
            },
            { 
                data: 'due_date',
                render: function(data) {
                    const date = new Date(data);
                    const today = new Date();
                    const isOverdue = date < today && data !== 'completed';
                    return `<span class="text-xs ${isOverdue ? 'text-danger fw-bold' : 'text-neutral-600'}">
                        <i class="far fa-calendar-alt me-1"></i>${date.toLocaleDateString()}
                    </span>`;
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-light btn-sm edit-task">
                                <i class="fas fa-edit text-primary"></i>
                            </button>
                            <button class="btn btn-light btn-sm delete-task" data-id="${data.id}" data-title="${data.title}">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[6, 'asc']],
        dom: '<"d-flex justify-content-between align-items-center p-3"f<"d-flex"l>>t<"d-flex justify-content-between align-items-center p-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search tasks..."
        }
    });

    $('#filter_project, #filter_assignee, #filter_status').on('change', () => table.ajax.reload());
    $('#resetFilters').on('click', () => { $('#filterForm')[0].reset(); table.ajax.reload(); });

    handleFormSubmit('#addTaskForm', () => { $('#addTaskModal').modal('hide'); $('#addTaskForm')[0].reset(); table.ajax.reload(); });
    handleFormSubmit('#editTaskForm', () => { $('#editTaskModal').modal('hide'); table.ajax.reload(); });

    $(document).on('click', '.edit-task', function() {
        const data = table.row($(this).closest('tr')).data();
        $('#edit_id').val(data.id);
        $('#edit_title').val(data.title);
        $('#edit_description').val(data.description);
        $('#edit_assigned_to').val(data.assigned_to);
        $('#edit_role_id').val(data.role_id);
        $('#edit_priority').val(data.priority);
        $('#edit_status').val(data.status);
        $('#edit_due_date').val(data.due_date);
        $('#edit_progress').val(data.progress_percentage);
        $('#edit_notes').val(data.status_notes);
        $('#editTaskModal').modal('show');
    });

    $(document).on('click', '.delete-task', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        Swal.fire({
            title: 'Delete Task?',
            text: `Are you sure you want to delete "${title}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/tasks/delete') ?>', { id: id }, (res) => {
                    if (res.success) { toastr.success(res.message); table.ajax.reload(); }
                    else { toastr.error(res.message); }
                }, 'json');
            }
        });
    });
});
</script>

<style>
.bg-primary-100 { background-color: var(--primary-50); }
.text-primary-600 { color: var(--primary-600); }
.bg-success-100 { background-color: #f0fdf4; }
.bg-danger-100 { background-color: #fef2f2; }
.bg-warning-100 { background-color: #fffbeb; }
.text-warning-700 { color: #b45309; }
.bg-neutral-100 { background-color: var(--neutral-100); }
.text-neutral-900 { color: var(--neutral-900); }
.text-neutral-600 { color: var(--neutral-600); }
.text-neutral-500 { color: var(--neutral-500); }
.text-neutral-400 { color: var(--neutral-400); }

.progress { background-color: var(--neutral-100); border-radius: 10px; }
.progress-bar { background-color: var(--primary-500); border-radius: 10px; }

.table thead th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-weight: 600;
    color: var(--neutral-500);
    border-bottom: 1px solid var(--neutral-200);
}

.badge { font-weight: 500; }
.avatar-sm { font-size: 10px; }
</style>

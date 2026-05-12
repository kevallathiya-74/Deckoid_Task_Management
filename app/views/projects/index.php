<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">Project Management</h2>
                <p class="text-neutral-500">Create and monitor projects across departments.</p>
            </div>
            <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class="fas fa-plus me-2"></i>New Project
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Department</label>
                        <select class="form-select" name="role_id" id="filter_role">
                            <option value="">All Departments</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Status</label>
                        <select class="form-select" name="status" id="filter_status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="resetFilters" class="btn btn-light w-100">Reset Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Projects Grid/Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="projectsTable">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="ps-4">Project & Client</th>
                                <th>Department</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Timeline</th>
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

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProjectForm" action="<?= url('/api/projects') ?>" method="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="project_name" required placeholder="Enter project title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" name="client_name" required placeholder="Client name">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Briefly describe the project goals..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Select Department</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col">
                            <label class="form-label">Deadline</label>
                            <input type="date" class="form-control" name="deadline" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProjectForm" action="<?= url('/api/projects/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" name="project_name" id="edit_project_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" name="client_name" id="edit_client_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="role_id" id="edit_role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="edit_start_date">
                        </div>
                        <div class="col">
                            <label class="form-label">Deadline</label>
                            <input type="date" class="form-control" name="deadline" id="edit_deadline">
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
    const table = $('#projectsTable').DataTable({
        ajax: {
            url: '<?= url('/api/projects') ?>',
            dataSrc: function(json) {
                if (json.success === false) {
                    toastr.error(json.message || 'Failed to load projects');
                    return [];
                }
                return json.data;
            },
            data: function(d) {
                d.role_id = $('#filter_role').val();
                d.status = $('#filter_status').val();
            },
            error: function(xhr, error, thrown) {
                console.error(xhr.responseText);
                toastr.error('Ajax error: See console for details');
            }
        },
        columns: [
            { 
                data: 'project_name',
                render: function(data, type, row) {
                    return `
                        <div class="ps-2">
                            <div class="fw-bold text-neutral-900">${data}</div>
                            <div class="text-xs text-primary-600 fw-medium mb-1"><i class="fas fa-user me-1"></i>${row.client_name}</div>
                            <div class="text-xs text-neutral-500 text-truncate" style="max-width: 250px;">${row.description || 'No description'}</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'department_name',
                render: function(data) {
                    return `<span class="badge bg-primary-50 text-primary-700">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data) {
                    const total = parseInt(data.total_tasks) || 0;
                    const completed = parseInt(data.completed_tasks) || 0;
                    const progress = total > 0 ? Math.round((completed / total) * 100) : 0;
                    return `
                        <div style="width: 150px;">
                            <div class="d-flex justify-content-between text-xs mb-1">
                                <span>${progress}%</span>
                                <span class="text-neutral-400">${completed}/${total} Tasks</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: ${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let cls = 'bg-neutral-100 text-neutral-600';
                    if (data === 'active') cls = 'bg-primary-100 text-primary-600';
                    if (data === 'completed') cls = 'bg-success-100 text-success';
                    if (data === 'cancelled') cls = 'bg-danger-100 text-danger';
                    return `<span class="badge ${cls} text-capitalize px-3 py-2 rounded-pill">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data) {
                    const start = data.start_date ? new Date(data.start_date).toLocaleDateString() : 'N/A';
                    const deadline = data.deadline ? new Date(data.deadline).toLocaleDateString() : 'N/A';
                    return `<div class="text-xs">
                        <span class="text-neutral-500">Start: ${start}</span><br>
                        <span class="text-neutral-900 fw-medium">Due: ${deadline}</span>
                    </div>`;
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                render: function(data) {
                    const isAdmin = '<?= $_SESSION['user_role'] ?>' === 'admin';
                    let html = `<div class="btn-group">`;
                    html += `<a href="<?= url('/tasks?project_id=') ?>${data.id}" class="btn btn-light btn-sm" title="View Tasks"><i class="fas fa-tasks text-neutral-600"></i></a>`;
                    if (isAdmin) {
                        html += `
                            <button class="btn btn-light btn-sm edit-project" title="Edit Project">
                                <i class="fas fa-edit text-primary"></i>
                            </button>
                            <button class="btn btn-light btn-sm delete-project" data-id="${data.id}" data-name="${data.project_name}" title="Delete Project">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        `;
                    }
                    html += `</div>`;
                    return html;
                }
            }
        ],
        order: [[4, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center p-3"f<"d-flex"l>>t<"d-flex justify-content-between align-items-center p-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search projects..."
        }
    });

    $('#filter_role, #filter_status').on('change', () => table.ajax.reload());
    $('#resetFilters').on('click', () => { $('#filterForm')[0].reset(); table.ajax.reload(); });

    handleFormSubmit('#addProjectForm', () => { $('#addProjectModal').modal('hide'); $('#addProjectForm')[0].reset(); table.ajax.reload(); });
    handleFormSubmit('#editProjectForm', () => { $('#editProjectModal').modal('hide'); table.ajax.reload(); });

    $(document).on('click', '.edit-project', function() {
        const data = table.row($(this).closest('tr')).data();
        $('#edit_id').val(data.id);
        $('#edit_project_name').val(data.project_name);
        $('#edit_client_name').val(data.client_name);
        $('#edit_description').val(data.description);
        $('#edit_role_id').val(data.role_id);
        $('#edit_status').val(data.status);
        $('#edit_start_date').val(data.start_date);
        $('#edit_deadline').val(data.deadline);
        $('#editProjectModal').modal('show');
    });

    $(document).on('click', '.delete-project', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Delete Project?',
            text: `Are you sure you want to delete "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/projects/delete') ?>', { id: id }, (res) => {
                    if (res.success) { toastr.success(res.message); table.ajax.reload(); }
                    else { toastr.error(res.message); }
                }, 'json');
            }
        });
    });
});
</script>

<style>
.bg-primary-50 { background-color: var(--primary-50); }
.text-primary-700 { color: var(--primary-700); }
.bg-primary-100 { background-color: var(--primary-50); }
.text-primary-600 { color: var(--primary-600); }
.bg-success-100 { background-color: #f0fdf4; }
.bg-danger-100 { background-color: #fef2f2; }
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
</style>

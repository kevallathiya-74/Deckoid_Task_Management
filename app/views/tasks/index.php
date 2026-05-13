<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 mb-5">
            <div>
                <h3 class="fw-bold text-neutral-900 mb-1">Task Management</h3>
                <p class="text-neutral-500 mb-0">Track and manage project tasks and assignments</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fas fa-plus me-2"></i> Create Task
            </button>
        </div>

        <!-- Dynamic Filters -->
        <div class="glass-card mb-5 p-4">
            <form id="filterForm" class="row g-4 align-items-end">
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 mb-3 ms-1">Active Project</label>
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-50 border-0 rounded-start-pill ps-3">
                            <i class="fas fa-layer-group text-neutral-300"></i>
                        </span>
                        <select class="form-select border-0 bg-neutral-50 rounded-end-pill py-2 text-sm fw-bold" name="project_id" id="filter_project">
                            <option value="">All Active Projects</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($project_id) && $project_id == $p['id']) ? 'selected' : '' ?>>
                                    <?= $p['project_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 mb-3 ms-1">Assigned To</label>
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-50 border-0 rounded-start-pill ps-3">
                            <i class="fas fa-user-check text-neutral-300"></i>
                        </span>
                        <select class="form-select border-0 bg-neutral-50 rounded-end-pill py-2 text-sm fw-bold" name="assigned_to" id="filter_assignee">
                            <option value="">All Team Members</option>
                            <?php foreach ($staff as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 mb-3 ms-1">Status</label>
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-50 border-0 rounded-start-pill ps-3">
                            <i class="fas fa-check-circle text-neutral-300"></i>
                        </span>
                        <select class="form-select border-0 bg-neutral-50 rounded-end-pill py-2 text-sm fw-bold" name="status" id="filter_status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">Review</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <button type="button" id="resetFilters" class="btn btn-secondary border-0 bg-neutral-50 w-100 rounded-pill py-2 text-xs fw-bold text-neutral-600">
                        <i class="fas fa-filter me-1"></i> Reset Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="glass-card overflow-hidden">
            <table class="table table-hover align-middle mb-0" id="tasksTable">
                <thead>
                    <tr>
                        <th class="ps-4">Task Details</th>
                        <th>Project</th>
                        <th>Assignee To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-4">
            <div class="modal-header border-0 pb-4">
                <div>
                    <h4 class="fw-bold text-neutral-900 mb-1">Define New Task</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTaskForm" action="<?= url('/api/tasks') ?>" method="POST">
                <div class="modal-body py-0">
                    <div class="mb-4">
                        <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Task Title</label>
                        <input type="text" class="form-control rounded-4 py-3" name="title" required placeholder="e.g. Implement User Authentication flow">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Detailed Description</label>
                        <textarea class="form-control rounded-4 py-3" name="description" rows="3" placeholder="Enter task description"></textarea>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Select Project</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="project_id" required>
                                <option value="">Select Project</option>
                                <?php foreach ($projects as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= (isset($project_id) && $project_id == $p['id']) ? 'selected' : '' ?>>
                                        <?= $p['project_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Assign Team Lead</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="assigned_to" required>
                                <option value="">Select Member</option>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['role_name'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Department </label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="role_id" required>
                                <option value="">Select Department</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Priority</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Status</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="status">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Expected Delivery Date</label>
                            <input type="date" class="form-control rounded-4 py-3" name="due_date" required value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Expected Delivery Time</label>
                            <input type="time" class="form-control rounded-4 py-3" name="due_time" required value="09:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-5 gap-3">
                    <button type="button" class="btn btn-secondary flex-grow-1 py-3" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-4">
            <div class="modal-header border-0 pb-4">
                <div>
                    <h4 class="fw-bold text-neutral-900 mb-1">Update Execution Details</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTaskForm" action="<?= url('/api/tasks/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body py-0">
                    <div class="mb-4">
                        <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Task Title</label>
                        <input type="text" class="form-control rounded-4 py-3" name="title" id="edit_title" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Description</label>
                        <textarea class="form-control rounded-4 py-3" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Assigned To</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="assigned_to" id="edit_assigned_to" required>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Department</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="role_id" id="edit_role_id" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Priority</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="priority" id="edit_priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Status</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 py-3 text-sm fw-bold" name="status" id="edit_status">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Expected Delivery </label>
                            <div class="row g-2">
                                <div class="col-7"><input type="date" class="form-control rounded-4 py-3" name="due_date" id="edit_due_date" required></div>
                                <div class="col-5"><input type="time" class="form-control rounded-4 py-3" name="due_time" id="edit_due_time" required></div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mb-2">
                        <div class="col-md-5">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Progress (%)</label>
                            <div class="d-flex align-items-center bg-neutral-50 p-3 rounded-4">
                                <input type="range" class="form-range me-4" id="edit_progress_range" min="0" max="100" step="5">
                                <span class="fw-bold text-primary font-outfit" id="progress_val">0%</span>
                                <input type="hidden" name="progress_percentage" id="edit_progress">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Progress Notes</label>
                            <input type="text" class="form-control rounded-4 py-3" name="status_notes" id="edit_notes" placeholder="Detailed update on current blockages or progress...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-5 gap-3">
                    <button type="button" class="btn btn-secondary flex-grow-1 py-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3">Save Changes</button>
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
            dataSrc: 'data',
            data: function(d) {
                d.project_id = $('#filter_project').val();
                d.assigned_to = $('#filter_assignee').val();
                d.status = $('#filter_status').val();
            }
        },
        scrollX: true,
        autoWidth: false,
        columns: [
            { 
                data: 'title',
                render: function(data, type, row) {
                    return `
                        <div class="py-2">
                            <div class="fw-bold text-neutral-900 mb-1 font-outfit fs-6">${data}</div>
                            <div class="text-xs text-neutral-400 text-truncate font-medium" style="max-width: 300px;">${row.description || 'Instructional breakdown pending specification...'}</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'project_name',
                render: function(data) {
                    return `<span class="badge bg-neutral-50 text-neutral-600 border px-3 py-2 font-outfit fw-bold">${data}</span>`;
                }
            },
            { 
                data: 'assigned_to_name',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center py-1">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(data)}&background=8b5cf6&color=fff" width="40" height="40" class="rounded-circle shadow-sm border border-2 border-white me-3">
                            <div>
                                <div class="fw-bold text-neutral-800 font-outfit text-sm">${data}</div>
                                <div class="text-xs text-neutral-400 fw-bold text-uppercase" style="font-size: 0.65rem;">${row.role_name}</div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'priority',
                render: function(data) {
                    let cls = 'bg-primary-soft text-primary';
                    let icon = 'fa-circle-arrow-down';
                    if (data === 'high') { cls = 'bg-danger-soft text-danger'; icon = 'fa-fire-flame-curved'; }
                    if (data === 'medium') { cls = 'bg-warning-soft text-warning'; icon = 'fa-circle-minus'; }
                    return `<span class="badge ${cls} text-capitalize px-3 py-2 font-outfit fw-bold"><i class="fas ${icon} me-2"></i>${data}</span>`;
                }
            },
            { 
                data: 'status',
                render: function(data, type, row) {
                    const progress = row.progress_percentage || 0;
                    let cls = 'bg-primary-soft text-primary';
                    if (data === 'in_progress') cls = 'bg-warning-soft text-warning';
                    if (data === 'review') cls = 'bg-info-soft text-info';
                    if (data === 'completed') cls = 'bg-success-soft text-success';
                    
                    return `
                        <div style="width: 150px;">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <span class="badge ${cls} py-1 px-2 text-capitalize">${data.replace('_', ' ')}</span>
                                <span class="text-xs fw-bold text-neutral-800 font-outfit">${progress}%</span>
                            </div>
                            <div class="progress rounded-pill overflow-visible" style="height: 5px; background: var(--neutral-100);">
                                <div class="progress-bar bg-primary rounded-pill position-relative" style="width: ${progress}%">
                                    <div class="progress-glow"></div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'due_date',
                render: function(data, type, row) {
                    const target = moment(data + ' ' + (row.due_time || '00:00'));
                    const isOverdue = target.isBefore(moment()) && row.status !== 'completed';
                    return `
                        <div class="d-flex align-items-center gap-3">
                            <div class="timeline-dot ${isOverdue ? 'bg-danger' : 'bg-primary'}"></div>
                            <div class="text-xs">
                                <div class="text-neutral-400 fw-bold text-uppercase" style="font-size: 0.6rem;">Target Time</div>
                                <div class="${isOverdue ? 'text-danger' : 'text-neutral-900'} fw-bold font-outfit">
                                    ${target.format('DD MMM')} <span class="text-neutral-400 fw-medium ms-1">${target.format('hh:mm A')}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="dropdown">
                            <button class="action-btn-sm" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-deep border-0 rounded-4 p-2">
                                <li><a class="dropdown-item rounded-3 py-2 edit-task" href="javascript:void(0)"><i class="fas fa-pen-nib me-2 text-primary"></i>Refine Task</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li><a class="dropdown-item rounded-3 py-2 text-danger delete-task" href="javascript:void(0)" data-id="${data.id}" data-title="${data.title}"><i class="fas fa-trash-can me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ],
        order: [[5, 'asc']],
        dom: '<"d-flex justify-content-between align-items-center p-4"f<"d-flex gap-3"l>>t<"d-flex justify-content-between align-items-center p-4 border-top border-light"ip>',
        language: {
            search: "",
            searchPlaceholder: "Search task specifying keywords...",
            lengthMenu: "_MENU_ per page",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        }
    });

    $('.dataTables_filter input').addClass('form-control border-0 bg-neutral-50 rounded-pill px-4').attr('placeholder', 'Search tasks...').css({'height': '45px'});

    $('#edit_progress_range').on('input', function() {
        const val = $(this).val();
        $('#progress_val').text(val + '%');
        $('#edit_progress').val(val);
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
        if (data.due_date) {
            const dt = data.due_date.split(' ');
            $('#edit_due_date').val(dt[0]);
            $('#edit_due_time').val(data.due_time ? data.due_time.substring(0, 5) : '09:00');
        }
        const prog = data.progress_percentage || 0;
        $('#edit_progress').val(prog);
        $('#edit_progress_range').val(prog);
        $('#progress_val').text(prog + '%');
        $('#edit_notes').val(data.status_notes);
        $('#editTaskModal').modal('show');
    });

    $(document).on('click', '.delete-task', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Task?',
            text: "This operation cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/tasks/delete') ?>', { id: id }, (res) => {
                    if (res.success) { toastr.success(res.message); table.ajax.reload(null, false); }
                    else { toastr.error(res.message); }
                });
            }
        });
    });
});
</script>

<style>
.progress-bar { transition: width 1.2s cubic-bezier(0.34, 1.56, 0.64, 1); overflow: visible; }
.progress-glow {
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: progress-glow 2s infinite;
}
@keyframes progress-glow { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

.timeline-dot { width: 8px; height: 8px; border-radius: 50%; }

.action-btn-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: var(--neutral-400);
    transition: all 0.3s ease;
}
.action-btn-sm:hover { background: var(--neutral-100); color: var(--primary-600); }

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 10px !important;
    padding: 0.5rem 0.9rem !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--grad-primary) !important;
    color: white !important;
}

.form-range::-webkit-slider-runnable-track { background: var(--neutral-200); height: 6px; border-radius: 10px; }
.form-range::-webkit-slider-thumb { margin-top: -6px; background: var(--primary-500); width: 18px; height: 18px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.2s ease; }
.form-range::-webkit-slider-thumb:active { transform: scale(1.2); }

.font-outfit { font-family: 'Outfit', sans-serif; }
</style>

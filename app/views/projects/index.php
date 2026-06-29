<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    border-radius: 10px !important;
    min-height: 45px !important;
    padding: 4px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #8b5cf6 !important;
    border: none !important;
    color: white !important;
    border-radius: 20px !important;
    padding: 2px 10px !important;
    font-size: 0.8rem !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white !important;
    margin-right: 5px !important;
    border: none !important;
    background: transparent !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: rgba(255,255,255,0.8) !important;
}
.select2-container--default .select2-dropdown {
    border-radius: 10px !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
}
</style>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        <!-- Page Header Removed -->
        <!-- Smart Filters -->
        <!-- <div class="glass-card mb-5 p-4">
            <form id="filterForm" class="row g-4 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 mb-3 ms-1">Department</label>
                    <div class="input-group bg-neutral-50 rounded-pill">
                        <span class="input-group-text ps-3">
                            <i class="fas fa-building-user text-neutral-300"></i>
                        </span>
                        <select class="form-select text-sm fw-bold h-100" name="role_id" id="filter_role">
                            <option value="">All Departments</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <button type="button" id="resetFilters" class="btn btn-secondary border-0 bg-neutral-50 w-100 rounded-pill h-100 text-xs fw-bold text-neutral-600">
                        <i class="fas fa-filter me-2"></i> Reset Filters
                    </button>
                </div>
            </form>
        </div> -->

        <div class="glass-card overflow-hidden p-0 mt-4">
            <?php if (isAdminOrSubAdmin()): ?>
            <div class="p-3 d-flex justify-content-end border-bottom border-light">
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class="fas fa-plus me-2"></i> New Project
                </button>
            </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="projectsTable" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4 text-xs fw-bold text-uppercase text-neutral-400" style="width: 20%;">Project Name</th>
                        <th class="text-xs fw-bold text-uppercase text-neutral-400" style="width: 20%;">Client Name</th>
                        <th class="text-xs fw-bold text-uppercase text-neutral-400" style="width: 45%;">Description</th>
                        <th class="text-end pe-4 text-xs fw-bold text-uppercase text-neutral-400" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-4">
            <div class="modal-header border-0 pb-4">
                <div>
                    <h4 class="fw-bold text-neutral-900 mb-1">New Project</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addProjectForm" action="<?= url('/api/projects') ?>" method="POST">
                <div class="modal-body py-0">
                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Project Name</label>
                            <input type="text" class="form-control rounded-4" name="project_name" required placeholder="e.g. Phoenix E-commerce Revamp">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Client Name</label>
                            <input type="text" class="form-control rounded-4" name="client_name" required placeholder="Client Name">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Description</label>
                        <textarea class="form-control rounded-4" name="description" rows="3" placeholder="Detail the core objectives and deliverables..."></textarea>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Department Role</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 text-sm fw-bold select2-multi" name="role_ids[]" multiple required data-placeholder="Select Departments...">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Assign Team</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 text-sm fw-bold select2-multi" name="assigned_users[]" multiple data-placeholder="Select Members...">
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['role_name'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                </div>
                </div> <!-- Close modal-body -->
                <div class="modal-footer border-0 pt-5 gap-3">
                    <button type="button" class="btn btn-secondary flex-grow-1 py-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-4">
            <div class="modal-header border-0 pb-4">
                <div>
                    <h4 class="fw-bold text-neutral-900 mb-1">Edit Project Details</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProjectForm" action="<?= url('/api/projects/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body py-0">
                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Project Name</label>
                            <input type="text" class="form-control rounded-4" name="project_name" id="edit_project_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Client Name</label>
                            <input type="text" class="form-control rounded-4" name="client_name" id="edit_client_name" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Description</label>
                        <textarea class="form-control rounded-4" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Department</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 text-sm fw-bold select2-multi" name="role_ids[]" id="edit_role_ids" multiple required data-placeholder="Select Departments...">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase ms-1 mb-2">Assign Team</label>
                            <select class="form-select border-0 bg-neutral-50 rounded-4 text-sm fw-bold select2-multi" name="assigned_users[]" id="edit_assigned_users" multiple data-placeholder="Select Members...">
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['role_name'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                </div>
                </div> <!-- Close modal-body -->
                <div class="modal-footer border-0 pt-5 gap-3">
                    <button type="button" class="btn btn-secondary flex-grow-1 py-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3">Update Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Project Modal -->
<div class="modal fade" id="viewProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-0 p-4">
            <div class="modal-header border-0 pb-4">
                <div>
                    <h4 class="fw-bold text-neutral-900 mb-1" id="view_project_name">Project Name</h4>
                    <p class="text-neutral-500 mb-0"><i class="fas fa-building-circle-check me-2"></i><span id="view_client_name">Client Name</span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-0">
                <div class="mb-4">
                    <h6 class="text-xs fw-bold text-neutral-400 text-uppercase mb-2">Description</h6>
                    <p class="text-neutral-700 bg-neutral-50 p-3 rounded-4" id="view_description"></p>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <h6 class="text-xs fw-bold text-neutral-400 text-uppercase mb-2">Departments</h6>
                        <div id="view_departments" class="d-flex flex-wrap"></div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-4 gap-3">
                <button type="button" class="btn btn-secondary flex-grow-1 py-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#addProjectModal .select2-multi').select2({
        placeholder: 'Select Members...',
        allowClear: true,
        dropdownParent: $('#addProjectModal')
    });

    $('#editProjectModal .select2-multi').select2({
        placeholder: 'Select Members...',
        allowClear: true,
        dropdownParent: $('#editProjectModal')
    });

    const table = $('#projectsTable').DataTable({
        serverSide: true,
        ajax: {
            url: '<?= url('/api/projects') ?>',
            dataSrc: 'data',
            data: function(d) {
                var role = $('#filter_role');
                if (role.length) d.role_id = role.val();
            }
        },
        scrollX: false,
        autoWidth: false,
        columns: [
            { 
                data: 'project_name',
                className: 'ps-4',
                render: function(data, type, row) {
                    return `
                        <div class="py-2">
                            <div class="fw-bold text-neutral-900 font-outfit fs-6">${data}</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'client_name',
                render: function(data, type, row) {
                    return `
                        <div class="py-2 text-neutral-800 fw-bold">
                            <i class="fas fa-building-circle-check me-2 opacity-50 text-primary"></i>${data}
                        </div>
                    `;
                }
            },
            { 
                data: 'description',
                render: function(data, type, row) {
                    return `
                        <div class="py-2 text-xs text-neutral-500 font-medium" style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.5;">
                            ${data || 'No description provided.'}
                        </div>
                    `;
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                visible: <?= isAdminOrSubAdmin() ? 'true' : 'false' ?>,
                render: function(data) {
                    const isAdmin = <?= isAdminOrSubAdmin() ? 'true' : 'false' ?>;
                    if (!isAdmin) return '';
                    
                    return `
                        <div class="d-flex gap-2 justify-content-end flex-wrap">
                            <button class="btn btn-sm btn-light rounded-circle edit-project shadow-sm" style="width: 32px; height: 32px; padding: 0;" title="Edit Project">
                                <i class="fas fa-pen-to-square text-primary"></i>
                            </button>
                            <button class="btn btn-sm btn-light rounded-circle delete-project shadow-sm" data-id="${data.id}" data-name="${data.project_name}" style="width: 32px; height: 32px; padding: 0;" title="Delete Project">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        dom: 't<"d-flex justify-content-between align-items-center px-3 py-2 border-top border-light"ip>',
        language: {
            search: "",
            searchPlaceholder: "Search projects by name, client...",
            lengthMenu: "_MENU_ per page",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        }
    });


    if ($('#filter_role').length) {
        $('#filter_role').on('change', () => table.ajax.reload());
    }
    if ($('#resetFilters').length) {
        $('#resetFilters').on('click', () => { $('#filterForm')[0].reset(); table.ajax.reload(); });
    }

    handleFormSubmit('#addProjectForm', () => { $('#addProjectModal').modal('hide'); $('#addProjectForm')[0].reset(); table.ajax.reload(); });
    handleFormSubmit('#editProjectForm', () => { $('#editProjectModal').modal('hide'); table.ajax.reload(); });

    $(document).off('click', '.edit-project').on('click', '.edit-project', function() {
        let $tr = $(this).closest('tr');
        if ($tr.hasClass('child')) {
            $tr = $tr.prev('.parent');
        }
        const data = table.row($tr).data();
        if (!data) return;
        
        $('#edit_id').val(data.id);
        $('#edit_project_name').val(data.project_name);
        $('#edit_client_name').val(data.client_name);
        $('#edit_description').val(data.description);
        const roleIds = data.role_ids_csv ? data.role_ids_csv.toString().split(',') : [];
        $('#edit_role_ids').val(roleIds).trigger('change');
        
        const assignedUsers = data.assigned_users_csv ? data.assigned_users_csv.toString().split(',') : [];
        $('#edit_assigned_users').val(assignedUsers).trigger('change');
        
        $('#editProjectModal').modal('show');
    });

    $(document).off('click', '.view-project').on('click', '.view-project', function() {
        const $tr = $(this).closest('tr');
        const data = table.row($tr).data();
        $('#view_project_name').text(data.project_name);
        $('#view_client_name').text(data.client_name);
        $('#view_description').text(data.description || 'No description provided.');
        
        const roles = data.role_name ? data.role_name.split(',') : [];
        $('#view_departments').html(roles.map(r => `<span class="badge bg-neutral-50 text-neutral-600 border px-2 py-1 m-1">${r.trim()}</span>`).join(''));
        
        $('#viewProjectModal').modal('show');
    });

    $(document).off('click', '.delete-project').on('click', '.delete-project', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Terminate Project?',
            text: "This action will archive the project and all associated tasks!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Yes, terminate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/projects/delete') ?>', { id: id }, (res) => {
                    if (res.status === 'success' || res.success) { 
                        toastr.success(res.message); 
                        table.ajax.reload(null, false); 
                    } else { 
                        toastr.error(res.message); 
                    }
                }).fail((xhr) => {
                    toastr.error(xhr.responseJSON?.message || 'Failed to terminate project');
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

.font-outfit { font-family: 'Outfit', sans-serif; }
</style>

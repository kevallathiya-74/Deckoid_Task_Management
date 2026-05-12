<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">Staff Management</h2>
                <p class="text-neutral-500">Manage your team members and their roles.</p>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="fas fa-plus me-2"></i>Add Staff
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Department Role</label>
                        <select class="form-select" name="filter_role" id="filter_role">
                            <option value="">All Departments</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-500">Status</label>
                        <select class="form-select" name="filter_status" id="filter_status">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="resetFilters" class="btn btn-light w-100">Reset Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Staff Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="staffTable">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="ps-4">Staff Member</th>
                                <th>Username</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add New Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStaffForm" action="<?= url('/api/staff') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Role</label>
                        <select class="form-select" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStaffForm" action="<?= url('/api/staff/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Password (Leave blank to keep current)</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="edit_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Role</label>
                        <select class="form-select" name="role_id" id="edit_role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#staffTable').DataTable({
        ajax: {
            url: '<?= url('/api/staff') ?>',
            dataSrc: 'data',
            data: function(d) {
                d.role_id = $('#filter_role').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { 
                data: null,
                render: function(data) {
                    return `
                        <div class="d-flex align-items-center ps-2">
                            <div class="avatar-sm bg-primary-100 text-primary-600 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                ${data.full_name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="fw-semibold text-neutral-900">${data.full_name}</div>
                                <div class="text-xs text-neutral-500">${data.email}</div>
                            </div>
                        </div>
                    `;
                }
            },
            { data: 'username' },
            { 
                data: 'role_name',
                render: function(data) {
                    return `<span class="badge bg-light text-neutral-700 fw-medium">${data}</span>`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const cls = data === 'active' ? 'bg-success-100 text-success' : 'bg-neutral-100 text-neutral-500';
                    return `<span class="badge ${cls} text-capitalize px-3 py-2 rounded-pill">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-light btn-sm edit-staff">
                                <i class="fas fa-edit text-primary"></i>
                            </button>
                            <button class="btn btn-light btn-sm delete-staff" data-id="${data.id}" data-name="${data.full_name}">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[4, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center p-3"f<"d-flex"l>>t<"d-flex justify-content-between align-items-center p-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search staff..."
        }
    });

    // Filter Handling
    $('#filter_role, #filter_status').on('change', function() {
        table.ajax.reload();
    });

    $('#resetFilters').on('click', function() {
        $('#filterForm')[0].reset();
        table.ajax.reload();
    });

    // Form Submissions
    handleFormSubmit('#addStaffForm', function() {
        $('#addStaffModal').modal('hide');
        $('#addStaffForm')[0].reset();
        table.ajax.reload();
    });

    handleFormSubmit('#editStaffForm', function() {
        $('#editStaffModal').modal('hide');
        table.ajax.reload();
    });

    // Edit Button Handler
    $(document).on('click', '.edit-staff', function() {
        const staff = table.row($(this).closest('tr')).data();
        $('#edit_id').val(staff.id);
        $('#edit_full_name').val(staff.full_name);
        $('#edit_username').val(staff.username);
        $('#edit_email').val(staff.email);
        $('#edit_role_id').val(staff.role_id);
        $('#edit_status').val(staff.status);
        $('#editStaffModal').modal('show');
    });

    // Delete Button Handler
    $(document).on('click', '.delete-staff', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${name}. This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/staff/delete') ?>', { id: id }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
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
.bg-neutral-100 { background-color: var(--neutral-100); }
.text-neutral-700 { color: var(--neutral-700); }
.text-neutral-900 { color: var(--neutral-900); }
.text-neutral-500 { color: var(--neutral-500); }

.badge {
    font-weight: 500;
}

.table thead th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-weight: 600;
    color: var(--neutral-500);
}

.dataTables_filter input {
    border-radius: var(--radius-lg);
    border: 1px solid var(--neutral-200);
    padding: 0.5rem 1rem;
    font-size: var(--text-sm);
    min-width: 250px;
}

.dataTables_filter input:focus {
    outline: none;
    border-color: var(--primary-400);
    box-shadow: 0 0 0 4px var(--primary-100);
}
</style>

<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        
        

         <!-- SECTION 2 & 3: Create / Edit Form -->
            <section class="mb-5">
            <div class="col-lg-5 w-100">
                <div class="glass-card h-100">
                    <div class="p-4 border-bottom border-light">
                        <h5 class="fw-bold text-neutral-900 mb-1" id="formTitle">Create SOP</h5>
                        <p class="text-xs text-neutral-400 mb-0">Assign new operational guidelines</p>
                    </div>
                    
                    <div class="p-4">
                        <form id="sopForm">
                            <input type="hidden" id="sop_id" name="id">
                            
                            <!-- SECTION 3: Assign Member -->
                            <div class="mb-4">
                                <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 ms-1 mb-2">Assign To</label>
                                <select class="form-select border-0 bg-neutral-50 rounded-3 text-sm fw-bold select2-staff" name="staff_id" id="assign_staff" required>
                                    <option value="">Select Staff</option>
                                    <?php foreach ($staff as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= htmlspecialchars($user['full_name']) ?> - <?= htmlspecialchars($user['role_name'] ?? 'Staff') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- SECTION 2: SOP Description -->
                            <div class="mb-4">
                                <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 ms-1 mb-2">SOP Details</label>
                                <textarea class="form-control rounded-4 bg-neutral-50 border-1 p-3" 
                                          name="description" id="sop_description" 
                                          rows="10" style="min-height: 250px; resize: vertical;" 
                                          placeholder="Enter SOP instructions, process documentation, workflow steps, responsibilities, or operational guidelines..." required></textarea>
                            </div>

                            <!-- ACTION BUTTONS -->
                            <div class="d-flex gap-3 pt-2">
    <button type="submit" class="btn btn-primary flex-fill py-3 fw-bold shadow-primary">
        <i class="fas fa-save me-2"></i> Save SOP
    </button>
    <button type="button" id="resetBtn" class="btn btn-secondary-soft flex-fill py-3 fw-bold">
        Reset
    </button>
</div>
                        </form>
                    </div>
                </div>
            </div>
            </section>

            <!-- SECTION 1: Staff Filter & List -->
            <section class="mb-5">
            <div class="col-lg-7 w-100">
                <div class="glass-card h-100 d-flex flex-column">
                    <div class="p-4 border-bottom border-light d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-neutral-900 mb-1">SOP Directory</h5>
                            <p class="text-xs text-neutral-400 mb-0">View and manage assigned SOPs</p>
                        </div>
                    </div>
                    
                    <div class="p-4 border-bottom border-light bg-neutral-50 bg-opacity-50">
                        <label class="form-label text-xs fw-bold text-uppercase text-neutral-400 mb-2 ms-1">Select Staff Member (Filter)</label>
                        <select class="form-select border-0 rounded-3 text-sm fw-bold shadow-sm select2-staff" id="filter_staff">
                            <option value="">All Staff Members</option>
                            <?php foreach ($staff as $user): ?>
                                <option value="<?= $user['id'] ?>">
                                    <?= htmlspecialchars($user['full_name']) ?> - <?= htmlspecialchars($user['role_name'] ?? 'Staff') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="p-0 flex-grow-1">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="sopTable">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Staff Member</th>
                                        <th>Description Snippet</th>
                                        <th>Date</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </section>


    </div>
</main>

<script>
$(document).ready(function() {
    // Initialize Select2 for searchable dropdowns
    $('.select2-staff').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('body')
    });

    const table = $('#sopTable').DataTable({
        serverSide: true,
        ajax: {
            url: '<?= url('/api/sops') ?>',
            dataSrc: 'data',
            data: function(d) {
                d.staff_id = $('#filter_staff').val();
            }
        },
        columns: [
            { 
                data: null,
                render: function(data) {
                    return `
                        <div class="d-flex align-items-center py-2 ps-2">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(data.staff_name)}&background=8b5cf6&color=fff" width="36" height="36" class="rounded-circle shadow-sm border border-2 border-white me-3">
                            <div>
                                <div class="fw-bold text-neutral-900 mb-0 font-outfit text-sm">${data.staff_name}</div>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'description',
                render: function(data) {
                    let snippet = data.length > 40 ? data.substring(0, 40) + '...' : data;
                    return `<span class="text-xs text-neutral-500">${snippet}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return `<span class="text-neutral-500 text-xs fw-bold">${moment(data).format('DD MMM, YYYY')}</span>`;
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light text-primary edit-sop" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger delete-sop" data-id="${data.id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[2, 'desc']],
        dom: 't<"d-flex justify-content-between align-items-center p-4 border-top border-light"ip>',
        language: {
            search: "",
            searchPlaceholder: "Search SOP...",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        }
    });

    // Custom Styling for DataTable search
    $('.dataTables_filter input').addClass('form-control border-0 bg-neutral-50 rounded-pill px-4').attr('placeholder', 'Search SOP...').css({'height': '45px'});

    $('#filter_staff').on('change', function() {
        table.ajax.reload();
    });

    $('#resetBtn').on('click', function() {
        $('#sopForm')[0].reset();
        $('#sop_id').val('');
        $('#assign_staff').val('').trigger('change');
        $('#formTitle').text('Create SOP');
    });

    $('#sopForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: '<?= url('/api/sops/save') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.html(originalText).prop('disabled', false);
                if (res.status === 'success') {
                    toastr.success(res.message);
                    $('#resetBtn').click();
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr) {
                btn.html(originalText).prop('disabled', false);
                toastr.error(xhr.responseJSON?.message || 'Failed to save SOP');
            }
        });
    });

    $(document).on('click', '.edit-sop', function() {
        const $tr = $(this).closest('tr');
        const sop = table.row($tr).data();
        $('#sop_id').val(sop.id);
        $('#assign_staff').val(sop.staff_id).trigger('change');
        $('#sop_description').val(sop.description);
        $('#formTitle').text('Edit SOP');
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $("#sopForm").offset().top - 100
        }, 500);
    });

    $(document).on('click', '.delete-sop', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This SOP will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Yes, delete it!',
            borderRadius: '1.25rem'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/sops/delete') ?>', { id: id }, function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(res.message);
                    }
                });
            }
        });
    });
});
</script>

<style>
.select2-container--bootstrap-5 .select2-selection {
    min-height: 48px;
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    border: 1px solid var(--neutral-200);
}
</style>

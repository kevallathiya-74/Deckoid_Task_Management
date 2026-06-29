<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>
<main class="main-content">
    <div class="container-fluid animate-fade-up">

        <?php if (isAdminOrSubAdmin()): ?>
        <!-- Quick Create Row (Admin Only) -->
        <div class="card glass-card mb-3 border-0">
            <div class="card-body p-2">
                <form id="createTodoForm" class="row g-2 align-items-center">
                    <div class="col-md-3 col-12">
                        <input type="text" class="form-control glass-input" name="title" placeholder="Enter todo task..." required>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <select class="form-select glass-input" name="assigned_to" required>
                            <option value="" selected disabled>Assign Staff...</option>
                            <?php foreach ($staff as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <select class="form-select glass-input" name="is_pinned">
                            <option value="0" selected>Normal Task</option>
                            <option value="1">Pin Task</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <input type="date" class="form-control glass-input" name="deadline_date" placeholder="Select Date">
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <input type="time" class="form-control glass-input" name="deadline_time" placeholder="Select Time">
                    </div>
                    <div class="col-md-1 col-12">
                        <button type="submit" class="btn btn-primary w-100 btn-glow px-0 text-center">
                            <i class="fas fa-plus"></i><span class="d-md-none ms-1">Add</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter Row -->
        <div class="mb-3 d-flex align-items-center gap-2 justify-content-end">
            <label class="text-xs fw-bold text-neutral-500 text-uppercase mb-0">Select Staff:</label>
            <select id="staffFilter" class="form-select form-select-sm glass-input" style="width: 200px;">
                <option value="" selected>Select Staff Member</option>
                <?php foreach ($staff as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
        <!-- Quick Create Row (Staff Only) -->
        <div class="card glass-card mb-3 border-0">
            <div class="card-body p-2">
                <form id="createStaffTodoForm" class="row g-2 align-items-center">
                    <div class="col-md-4 col-12">
                        <input type="text" class="form-control glass-input" name="title" placeholder="Task" required maxlength="255">
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <select class="form-select glass-input" name="todo_type" required>
                            <option value="Normal Task" selected>Normal Task</option>
                            <option value="Pinned Task">Pinned Task</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <input type="date" class="form-control glass-input" name="deadline_date" placeholder="Select Date">
                    </div>
                    <div class="col-md-2 col-sm-6 col-12">
                        <input type="time" class="form-control glass-input" name="deadline_time" placeholder="Select Time">
                    </div>
                    <div class="col-md-2 col-12">
                        <button type="submit" class="btn btn-primary w-100 btn-glow">
                            <i class="fas fa-plus me-1"></i>Add Todo
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pinned Tasks Section -->
        <div id="pinnedTasksSection" class="mb-4 d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-thumbtack text-primary me-2"></i>
                    <h6 class="text-xs fw-bold text-neutral-500 text-uppercase mb-0">Pinned Tasks</h6>
                </div>
                <?php if (isAdminOrSubAdmin()): ?>
                    <button id="resetPinnedTasks" class="btn btn-xs btn-light-subtle btn-glow">
                        <i class="fas fa-sync-alt me-1"></i>Reset
                    </button>
                <?php endif; ?>
            </div>
            <div class="row g-3" id="pinnedTasksContainer">
                <!-- Loaded via AJAX -->
            </div>
        </div>

        <!-- Normal Tasks Section -->
        <div class="d-flex align-items-center mb-3">
            <i class="fas fa-list text-primary me-2"></i>
            <h6 class="text-xs fw-bold text-neutral-500 text-uppercase mb-0">Normal Tasks</h6>
        </div>
        <!-- Todo Table Card -->
        <div class="card glass-card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="todoTable">
                        <thead class="table-light text-xs text-uppercase text-neutral-500">
                            <tr>
                                <th class="px-4 py-3">Task</th>
                                <th class="py-3">Todo Type</th>
                                <?php if (isAdminOrSubAdmin()): ?>
                                    <th class="py-3">Created By</th>
                                    <th class="py-3">Assigned To</th>
                                <?php endif; ?>
                                <th class="py-3 text-start" style="width: 220px; min-width:180px;">Remark</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Created Date</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="noTodosMessage" class="text-center py-5 d-none">
                    <div class="text-neutral-400 mb-2">
                        <i class="fas fa-clipboard-list fa-3x"></i>
                    </div>
                    <p class="text-neutral-500 mb-0">No todos assigned yet</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Todo Modal -->
<div class="modal fade" id="editTodoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal border-0">
            <div class="modal-header border-bottom-0 p-4">
                <h5 class="modal-title fw-bold text-neutral-800">Edit Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTodoForm">
                <div class="modal-body p-4 pt-0">
                    <input type="hidden" name="id" id="edit_todo_id">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Task</label>
                        <input type="text" class="form-control glass-input text-sm" name="title" id="edit_todo_title" required>
                    </div>
                    <?php if (isAdminOrSubAdmin()): ?>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Assign Staff</label>
                        <select class="form-select glass-input text-sm" name="assigned_to" id="edit_todo_assigned_to" required>
                            <?php foreach ($staff as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Task Type</label>
                        <select class="form-select glass-input text-sm" name="is_pinned" id="edit_todo_is_pinned">
                            <option value="0">Normal Task</option>
                            <option value="1">Pin Task</option>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Todo Type</label>
                        <select class="form-select glass-input text-sm" name="todo_type" id="edit_todo_type" required>
                            <option value="Normal Task">Normal Task</option>
                            <option value="Pinned Task">Pinned Task</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Deadline Date</label>
                            <input type="date" class="form-control glass-input text-sm" name="deadline_date" id="edit_todo_deadline_date">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Deadline Time</label>
                            <input type="time" class="form-control glass-input text-sm" name="deadline_time" id="edit_todo_deadline_time">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-neutral-500 text-uppercase mb-2">Status</label>
                        <select class="form-select glass-input text-sm" name="status" id="edit_todo_status">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0 justify-content-between">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-glow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->

<script>
$(document).ready(function() {
    loadTodos();

    function loadTodos(staffId = '') {
        let fetchUrl = '';
        <?php if (isAdminOrSubAdmin()): ?>
            fetchUrl = staffId ? '<?= url('/admin/todos') ?>?staff_id=' + staffId : '<?= url('/admin/todos') ?>';
        <?php else: ?>
            fetchUrl = '<?= url('/staff/todos') ?>';
        <?php endif; ?>

        $.ajax({
            url: fetchUrl,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    const todos = response.data;
                    const tbody = $('#todoTable tbody');
                    const pinnedContainer = $('#pinnedTasksContainer');
                    
                    tbody.empty();
                    pinnedContainer.empty();

                    const pinnedTodos = todos.filter(t => t.todo_type === 'Pinned Task' || t.is_pinned == 1);
                    const normalTodos = todos.filter(t => t.todo_type !== 'Pinned Task' && t.is_pinned != 1);

                    // Handle Pinned Tasks
                    if (pinnedTodos.length > 0) {
                        $('#pinnedTasksSection').removeClass('d-none');
                        
                        let itemsHtml = '';
                        pinnedTodos.forEach(function(todo) {
                            const date = new Date(todo.created_at);
                            const formattedDate = date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

                            let assignedToHtml = '';
                            <?php if (isAdminOrSubAdmin()): ?>
                                if (todo.assigned_to_name === todo.assigned_by_name) {
                                    assignedToHtml = `
                                        <div class="text-xs text-neutral-500 mt-1">Created By: ${todo.assigned_by_name} (Personal Task)</div>
                                        ${todo.notes ? `<div class="text-xs text-neutral-600 mt-1 bg-neutral-50 p-1 px-2 rounded d-inline-block"><i class="fas fa-comment-dots me-1 text-primary"></i>${todo.notes}</div>` : ''}
                                    `;
                                } else {
                                    assignedToHtml = `
                                        <div class="text-xs text-neutral-500 mt-1">Assigned: ${todo.assigned_to_name}</div>
                                        <div class="text-xs text-neutral-500 mt-1">Created By: ${todo.assigned_by_name}</div>
                                        ${todo.notes ? `<div class="text-xs text-neutral-600 mt-1 bg-neutral-50 p-1 px-2 rounded d-inline-block"><i class="fas fa-comment-dots me-1 text-primary"></i>${todo.notes}</div>` : ''}
                                    `;
                                }
                            <?php endif; ?>

                            let checkboxHtml = '';
                            <?php if (!isAdminOrSubAdmin()): ?>
                                checkboxHtml = `
                                    <input type="text" class="form-control form-control-sm text-xs todo-remark me-3" data-id="${todo.id}" placeholder="Add remark..." value="${todo.notes || ''}" style="width: 180px; background: rgba(255,255,255,0.7);">
                                    <div class="form-check m-0 d-flex align-items-center">
                                        <input class="form-check-input toggle-status m-0" type="checkbox" style="width: 1.25rem; height: 1.25rem; cursor: pointer; border: 2px solid #000 !important;" data-id="${todo.id}" ${todo.status === 'completed' ? 'checked' : ''}>
                                    </div>
                                `;
                            <?php endif; ?>

                            let deadlineHtml = '';
                            let titleClass = todo.status === 'completed' ? 'text-decoration-line-through text-neutral-400' : 'text-neutral-800';
                            
                            if (todo.deadline_date) {
                                let deadlineDate = new Date(todo.deadline_date + (todo.deadline_time ? 'T' + todo.deadline_time : 'T23:59:59'));
                                let formattedDeadlineDate = new Date(todo.deadline_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                                let formattedDeadlineTime = todo.deadline_time ? new Date('1970-01-01T' + todo.deadline_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '';
                                
                                let now = new Date();
                                let isOverdue = now > deadlineDate && todo.status !== 'completed';
                                let isDueToday = now.toDateString() === deadlineDate.toDateString() && todo.status !== 'completed';
                                let isUpcoming = !isOverdue && !isDueToday && (deadlineDate - now) <= 86400000 && todo.status !== 'completed';
                                
                                let badgeHtml = '';
                                let textClass = 'text-neutral-500';
                                
                                if (todo.status === 'completed') {
                                    badgeHtml = '<span class="badge bg-success-subtle text-success">COMPLETED</span>';
                                } else if (isOverdue) {
                                    badgeHtml = '<span class="badge bg-danger-subtle text-danger">OVERDUE</span>';
                                    textClass = 'text-danger fw-bold';
                                } else if (isDueToday) {
                                    badgeHtml = '<span class="badge bg-warning-subtle text-warning">DUE TODAY</span>';
                                } else if (isUpcoming) {
                                    badgeHtml = '<span class="badge bg-info-subtle text-info">UPCOMING</span>';
                                }
                                
                                deadlineHtml = `
                                    <div class="text-xs ${textClass} mt-2 d-flex flex-wrap align-items-center" style="gap: 8px;">
                                        <span class="d-inline-flex align-items-center text-nowrap">
                                            <i class="far fa-calendar-alt me-1"></i> ${formattedDeadlineDate}
                                        </span>
                                        ${formattedDeadlineTime ? `
                                        <span class="d-inline-flex align-items-center text-nowrap border-start ps-2 border-secondary-subtle">
                                            <i class="far fa-clock me-1"></i> ${formattedDeadlineTime}
                                        </span>` : ''}
                                        ${badgeHtml ? `<span class="d-inline-flex align-items-center">${badgeHtml}</span>` : ''}
                                    </div>
                                `;
                            }

                            itemsHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center p-2" style="background: transparent; border-color: rgba(0,0,0,0.05);">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fw-bold ${titleClass}" style="font-size: 0.9rem;">${todo.title}</span>
                                            <span class="text-xs text-neutral-500 d-none d-md-inline"><i class="far fa-clock me-1"></i>${formattedDate}</span>
                                        </div>
                                        ${deadlineHtml}
                                        ${assignedToHtml}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        ${checkboxHtml}
                                        <button class="btn btn-sm btn-icon btn-light-subtle edit-todo" 
                                            data-id="${todo.id}" 
                                            data-title="${todo.title}" 
                                            data-assigned_to="${todo.assigned_to}" 
                                            data-status="${todo.status}"
                                            data-todo_type="${todo.todo_type}"
                                            data-is_pinned="${todo.is_pinned}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-danger-subtle delete-todo m-0" data-id="${todo.id}"><i class="fas fa-trash"></i></button>
                                    </div>
                                </li>
                            `;
                        });

                        const boxHtml = `
                            <div class="col-12">
                                <div class="card glass-card border-0 shadow-sm" style="border-left: 4px solid #8b5cf6 !important; background: rgba(255, 255, 255, 0.8);">
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            ${itemsHtml}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                        pinnedContainer.html(boxHtml);
                    } else {
                        $('#pinnedTasksSection').addClass('d-none');
                    }

                    // Handle Normal Tasks
                    if (normalTodos.length === 0) {
                        $('#todoTable').addClass('d-none');
                        <?php if (isAdminOrSubAdmin()): ?>
                        if (staffId === '') {
                            $('#noTodosMessage').html('<div class="text-neutral-400 mb-2"><i class="fas fa-hand-pointer fa-3x"></i></div><p class="text-neutral-500 mb-0">Select a staff member to view todos.</p>');
                        } else {
                            $('#noTodosMessage').html('<div class="text-neutral-400 mb-2"><i class="fas fa-clipboard-list fa-3x"></i></div><p class="text-neutral-500 mb-0">No todos assigned yet</p>');
                        }
                        <?php else: ?>
                        $('#noTodosMessage').html('<div class="text-neutral-400 mb-2"><i class="fas fa-clipboard-list fa-3x"></i></div><p class="text-neutral-500 mb-0">No todos assigned yet</p>');
                        <?php endif; ?>
                        $('#noTodosMessage').removeClass('d-none');
                    } else {
                        $('#todoTable').removeClass('d-none');
                        $('#noTodosMessage').addClass('d-none');

                        normalTodos.forEach(function(todo) {
                            const statusBadge = todo.status === 'completed' 
                                ? '<span class="badge bg-success-subtle text-success">Completed</span>'
                                : '<span class="badge bg-warning-subtle text-warning">Pending</span>';
                            
                            let actions = '';
                            actions = `
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <?php if (!isAdminOrSubAdmin()): ?>
                                    <div class="form-check m-0 me-2">
                                        <input class="form-check-input toggle-status m-0" type="checkbox" style="width: 1.25rem; height: 1.25rem; cursor: pointer; border: 2px solid #000 !important;" data-id="${todo.id}" ${todo.status === 'completed' ? 'checked' : ''}>
                                    </div>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-icon btn-light-subtle edit-todo" 
                                        data-id="${todo.id}" 
                                        data-title="${todo.title}" 
                                        data-assigned_to="${todo.assigned_to}" 
                                        data-status="${todo.status}"
                                        data-todo_type="${todo.todo_type}"
                                        data-is_pinned="${todo.is_pinned}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-danger-subtle delete-todo" data-id="${todo.id}"><i class="fas fa-trash"></i></button>
                                </div>
                            `;

                            let adminCols = '';
                            <?php if (isAdminOrSubAdmin()): ?>
                                if (todo.assigned_to_name === todo.assigned_by_name) {
                                    adminCols = `
                                        <td>${todo.assigned_by_name} <span class="text-neutral-500">(Personal Task)</span></td>
                                        <td><span class="text-neutral-400">-</span></td>
                                    `;
                                } else {
                                    adminCols = `
                                        <td>${todo.assigned_by_name}</td>
                                        <td>${todo.assigned_to_name}</td>
                                    `;
                                }
                            <?php endif; ?>

                            let remarkCol = '';
                            <?php if (isAdminOrSubAdmin()): ?>
                                remarkCol = `
                                    <td class="text-start text-xs text-neutral-600 align-middle">
                                        ${todo.notes ? `<div class="bg-neutral-50 p-2 rounded text-start" style="max-width: 260px; word-break: break-word;">${todo.notes}</div>` : '<span class="text-neutral-300 fst-italic">No remark</span>'}
                                    </td>
                                `;
                            <?php else: ?>
                                remarkCol = `
                                    <td class="text-start align-middle">
                                        <input type="text" class="form-control form-control-sm text-xs todo-remark" data-id="${todo.id}" placeholder="Enter remark..." value="${todo.notes || ''}" style="width: 220px; max-width:100%;">
                                    </td>
                                `;
                            <?php endif; ?>

                            let deadlineHtml = '';
                            let titleClass = todo.status === 'completed' ? 'text-decoration-line-through text-neutral-400' : 'text-neutral-800';
                            
                            if (todo.deadline_date) {
                                let deadlineDate = new Date(todo.deadline_date + (todo.deadline_time ? 'T' + todo.deadline_time : 'T23:59:59'));
                                let formattedDeadlineDate = new Date(todo.deadline_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                                let formattedDeadlineTime = todo.deadline_time ? new Date('1970-01-01T' + todo.deadline_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '';
                                
                                let now = new Date();
                                let isOverdue = now > deadlineDate && todo.status !== 'completed';
                                let isDueToday = now.toDateString() === deadlineDate.toDateString() && todo.status !== 'completed';
                                let isUpcoming = !isOverdue && !isDueToday && (deadlineDate - now) <= 86400000 && todo.status !== 'completed';
                                
                                let badgeHtml = '';
                                let textClass = 'text-neutral-500';
                                
                                if (todo.status === 'completed') {
                                    badgeHtml = '<span class="badge bg-success-subtle text-success">COMPLETED</span>';
                                } else if (isOverdue) {
                                    badgeHtml = '<span class="badge bg-danger-subtle text-danger">OVERDUE</span>';
                                    textClass = 'text-danger fw-bold';
                                } else if (isDueToday) {
                                    badgeHtml = '<span class="badge bg-warning-subtle text-warning">DUE TODAY</span>';
                                } else if (isUpcoming) {
                                    badgeHtml = '<span class="badge bg-info-subtle text-info">UPCOMING</span>';
                                }
                                
                                deadlineHtml = `
                                    <div class="text-xs ${textClass} mt-2 d-flex flex-wrap align-items-center" style="gap: 8px;">
                                        <span class="d-inline-flex align-items-center text-nowrap">
                                            <i class="far fa-calendar-alt me-1"></i> ${formattedDeadlineDate}
                                        </span>
                                        ${formattedDeadlineTime ? `
                                        <span class="d-inline-flex align-items-center text-nowrap border-start ps-2 border-secondary-subtle">
                                            <i class="far fa-clock me-1"></i> ${formattedDeadlineTime}
                                        </span>` : ''}
                                        ${badgeHtml ? `<span class="d-inline-flex align-items-center">${badgeHtml}</span>` : ''}
                                    </div>
                                `;
                            }

                            const row = `
                                <tr>
                                    <td class="px-4 py-3" style="min-width: 250px;">
                                        <div class="fw-bold ${titleClass} mb-1" style="word-break: break-word; white-space: normal;">${todo.title}</div>
                                        ${deadlineHtml}
                                    </td>
                                    <td>
                                        <div class="badge bg-light text-dark border">${todo.todo_type}</div>
                                    </td>
                                    ${adminCols}
                                    ${remarkCol}
                                    <td>${statusBadge}</td>
                                    <td>${new Date(todo.created_at).toLocaleDateString('en-GB')}</td>
                                    <td class="px-4 text-end">${actions}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    }
                }
            }
        });
    }

    // Staff Filter Binding
    $('#staffFilter').on('change', function() {
        loadTodos($(this).val());
    });

    function reloadCurrentTasks() {
        loadTodos($('#staffFilter').length ? $('#staffFilter').val() : '');
    }

    // Create Admin Todo
    $('#createTodoForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= url('/api/todos/create') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#createTodoForm')[0].reset();
                    reloadCurrentTasks();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Create Staff Todo
    $('#createStaffTodoForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= url('/staff/todos/create') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#createStaffTodoForm')[0].reset();
                    reloadCurrentTasks();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.toggle-status', function() {
        const id = $(this).data('id');
        const status = this.checked ? 'completed' : 'pending';
        
        let dataPayload = { id: id, status: status };
        
        // Check for remark if completing
        const remarkInput = $('.todo-remark[data-id="' + id + '"]');
        if (remarkInput.length) {
            const notes = remarkInput.val().trim();
            if (status === 'completed' && notes === '') {
                toastr.error('Please write a remark before completing the task.');
                this.checked = false;
                return;
            }
            dataPayload.notes = notes;
            remarkInput.data('last-saved', notes); // Update last-saved so blur doesn't fire duplicate
        }

        $.ajax({
            url: '<?= url('/api/todos/update') ?>',
            type: 'POST',
            data: dataPayload,
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Todo status updated');
                    reloadCurrentTasks();
                } else {
                    toastr.error(response.message);
                    this.checked = !this.checked;
                }
            }.bind(this)
        });
    });

    // Save Remark on blur or change
    $(document).on('change blur', '.todo-remark', function() {
        const id = $(this).data('id');
        const notes = $(this).val();
        
        // Prevent multiple fires if blur and change happen together
        if ($(this).data('last-saved') === notes) return;
        $(this).data('last-saved', notes);
        
        $.ajax({
            url: '<?= url('/api/todos/update') ?>',
            type: 'POST',
            data: { id: id, notes: notes },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success('Remark saved successfully');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Reset Pinned Tasks
    $(document).on('click', '#resetPinnedTasks', function() {
        if (confirm('Are you sure you want to reset all pinned tasks?')) {
            $.ajax({
                url: '<?= url('/api/todos/reset_pinned') ?>',
                type: 'POST',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        reloadCurrentTasks();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Delete Todo
    $(document).on('click', '.delete-todo', function() {
        if (confirm('Are you sure you want to delete this todo?')) {
            const id = $(this).data('id');
            $.ajax({
                url: '<?= url('/api/todos/delete') ?>',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        reloadCurrentTasks();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Edit Todo
    $(document).on('click', '.edit-todo', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const assigned_to = $(this).data('assigned_to');
        const status = $(this).data('status');
        const is_pinned = $(this).data('is_pinned');
        const todo_type = $(this).data('todo_type');

        $('#edit_todo_id').val(id);
        $('#edit_todo_title').val(title);
        <?php if (isAdminOrSubAdmin()): ?>
        $('#edit_todo_assigned_to').val(assigned_to);
        $('#edit_todo_is_pinned').val(is_pinned);
        <?php else: ?>
        $('#edit_todo_type').val(todo_type || 'Normal Task');
        <?php endif; ?>
        $('#edit_todo_status').val(status);

        $('#editTodoModal').modal('show');
    });

    $('#editTodoForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= url('/api/todos/update') ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#editTodoModal').modal('hide');
                    reloadCurrentTasks();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });
});
</script>

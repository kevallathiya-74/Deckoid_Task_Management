<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up" style="padding-top: 10px;">
        <!-- Page Header -->
        <div class="pub-topbar">
            <div class="pub-topbar-title">
                <h3 class="pub-page-title" id="report-title-display">Publishing Report</h3>
                <p class="pub-page-subtitle">Track content production and publishing status</p>
            </div>
            <div class="pub-topbar-actions">
                <div class="pub-filter-pill">
                    <label for="select-month" class="pub-filter-label">Select month</label>
                    <select id="select-month" class="form-select form-select-sm pub-filter-select">
                        <?php
                        $months = [
                            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                        ];
                        $currentMonth = date('n');
                        foreach ($months as $val => $name):
                        ?>
                            <option value="<?= $val ?>" <?= $val == $currentMonth ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pub-filter-pill">
                    <label for="select-year" class="pub-filter-label">Select year</label>
                    <select id="select-year" class="form-select form-select-sm pub-filter-select">
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear - 1; $y <= $currentYear + 2; $y++):
                        ?>
                            <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="button" class="pub-button-filter" id="btn-load-report" aria-label="Load report">
                    <i class="fas fa-sync"></i>
                </button>
            </div>
        </div>

        <!-- CATEGORY FILTERS -->
        <div class="pub-category-filters" id="category-filters-container">
            <button class="pub-cat-pill active" data-cat="posts">Posts</button>
            <button class="pub-cat-pill" data-cat="reels">Reels</button>
            <button class="pub-cat-pill" data-cat="facebook_ads">Facebook Ads</button>
        </div>

        <!-- Report Content -->
        <div class="pub-report-shell">
            <div class="pub-report-panel">
                <div id="report-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-neutral-500">Loading report data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* ==========================================
   PUBLISHING REPORT - UI REFINEMENT
   ========================================== */
.pub-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 22px;
}

.pub-topbar-title {
    min-width: 240px;
}

.pub-page-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 6px;
    color: #111827;
}

.pub-page-subtitle {
    margin: 0;
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.5;
}

.pub-topbar-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.pub-filter-pill {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    border-radius: 999px;
    padding: 6px 10px;
}

.pub-filter-select {
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
    min-width: 136px;
    height: 44px;
    font-size: 0.95rem;
    color: #111827;
    padding-left: 14px;
    padding-right: 14px;
}

.pub-filter-select:focus {
    outline: none !important;
    box-shadow: inset 0 0 0 1px rgba(124, 58, 237, 0.22) !important;
}

.pub-button-filter {
    width: 44px;
    height: 44px;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.pub-button-filter:hover {
    background: #eef2ff;
    color: #3730a3;
}

.pub-category-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 22px;
}

.pub-cat-pill {
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    color: #334155;
    padding: 12px 24px;
    font-size: 0.95rem;
    font-weight: 700;
    transition: all 0.25s ease;
    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
}

.pub-cat-pill:hover {
    background: #f8f5ff;
    border-color: #ddd6fe;
    color: #5b21b6;
    transform: translateY(-1px);
}

.pub-cat-pill.active {
    background: linear-gradient(90deg, #7c3aed, #8b5cf6);
    border-color: transparent;
    color: #ffffff;
    box-shadow: 0 18px 40px rgba(124, 58, 237, 0.16);
}

.pub-report-shell {
    display: flex;
    justify-content: center;
}

.pub-report-panel {
    width: 100%;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 28px;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.06);
    padding: 22px;
}

.pub-empty-state {
    max-width: 560px;
    margin: 0 auto;
    padding: 44px 32px;
    border: 1px dashed rgba(124, 58, 237, 0.3);
    border-radius: 28px;
    background: rgba(243, 229, 255, 0.45);
    color: #334155;
    text-align: center;
}

.pub-empty-state .empty-icon {
    width: 62px;
    height: 62px;
    margin: 0 auto 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: rgba(124, 58, 237, 0.12);
    color: #7c3aed;
    font-size: 1.4rem;
}

.pub-empty-state h3 {
    margin: 0 0 10px;
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
}

.pub-empty-state p {
    margin: 0 0 22px;
    color: #475569;
    font-size: 0.99rem;
    line-height: 1.75;
}

.pub-btn-create {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 26px;
    border-radius: 999px;
    border: none;
    background: linear-gradient(90deg, #7c3aed, #8b5cf6);
    color: #ffffff;
    font-weight: 700;
    box-shadow: 0 16px 36px rgba(124, 58, 237, 0.18);
    transition: all 0.2s ease;
}

.pub-btn-create:hover {
    transform: translateY(-1px);
    opacity: 0.95;
}

.pub-report-container {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    margin-bottom: 26px;
    overflow: hidden;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.05);
}

.pub-week-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    padding: 24px 24px 18px;
    border-bottom: 1px solid #f3f4f6;
}

.pub-week-box {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border-radius: 999px;
    padding: 12px 18px;
    background: rgba(124, 58, 237, 0.1);
    color: #7c3aed;
    font-weight: 800;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.pub-table-wrapper {
    width: 100%;
    border-radius: 0 0 24px 24px;
    overflow-x: auto;
    overflow-y: hidden;
    background: #ffffff;
    border-top: 1px solid #f3f4f6;
}

.pub-table {
    width: 100%;
    min-width: 960px;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
}

.pub-table thead th {
    padding: 14px 14px;
    text-align: center;
    font-size: 0.78rem;
    font-weight: 800;
    color: #111827;
    background: #f8f5ff;
    border-bottom: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
    white-space: nowrap;
}

.pub-table thead th:first-child {
    text-align: left;
    padding-left: 20px;
}

.pub-table thead th:last-child {
    border-right: none;
}

.pub-table tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
    background: #ffffff;
    vertical-align: middle;
}

.pub-table tbody td:last-child {
    border-right: none;
}

.pub-table tbody tr:last-child td {
    border-bottom: none;
}

.pub-company-input,
.pub-task-textarea {
    width: 100%;
    border: none;
    background: transparent;
    padding: 8px 10px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: #111827;
    box-sizing: border-box;
}

.pub-company-input {
    min-height: 42px;
    font-size: 0.95rem;
    font-weight: 700;
}

.pub-task-textarea {
    min-height: 42px;
    max-height: 220px;
    padding: 8px 10px;
    font-size: 0.92rem;
    line-height: 1.45;
    resize: vertical;
    overflow: auto;
}

.pub-company-input::placeholder,
.pub-task-textarea::placeholder {
    color: #94a3b8;
}

.pub-company-input:focus,
.pub-task-textarea:focus {
    background: #f8f5ff;
    outline: none;
}

.pub-assignment-wrapper {
    padding: 8px 10px;
    min-height: 42px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    background: #f8f8ff;
}

.pub-assignment-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: flex-start;
}

.pub-assignment-chip {
    background: rgba(124, 58, 237, 0.12);
    border: 1px solid rgba(167, 139, 250, 0.45);
    color: #6d28d9;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
}

.pub-assignment-chip-empty {
    color: #94a3b8;
    font-size: 0.88rem;
}

.select2-container--default .select2-selection--multiple {
    border: 1px solid #e5e7eb !important;
    border-radius: 14px !important;
    min-height: 48px;
    padding: 6px 8px;
    background: #ffffff;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #7c3aed !important;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12) !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(124, 58, 237, 0.12) !important;
    border: 1px solid rgba(167, 139, 250, 0.6) !important;
    color: #5b21b6 !important;
    border-radius: 999px !important;
    padding: 4px 10px !important;
    font-size: 0.8rem !important;
    font-weight: 700 !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #5b21b6 !important;
}

.pub-actions-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 48px;
    gap: 8px;
}

.pub-btn-action {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid transparent;
    background: #f8f5ff;
    color: #7c3aed;
    transition: all 0.15s ease;
}

.pub-btn-action:hover {
    background: #efe2ff;
}

.pub-btn-delete {
    color: #dc2626;
}

.pub-table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 0 0;
    flex-wrap: wrap;
}

/* Tighter empty state when used inside table */
.pub-empty-state {
    padding: 24px 18px;
    max-width: 520px;
}

.pub-btn-save {
    background: linear-gradient(90deg, #7c3aed, #8b5cf6);
    color: #ffffff;
    border: none;
    padding: 12px 26px;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 700;
    box-shadow: 0 16px 34px rgba(124, 58, 237, 0.18);
}

.pub-btn-save:hover,
.pub-btn-save:focus {
    transform: translateY(-1px);
    opacity: 0.97;
}

.pub-btn-save:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.pub-btn-add {
    background: transparent;
    color: #3730a3;
    border: 1px solid #e5e7eb;
    padding: 12px 24px;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 700;
}

.pub-btn-add:hover {
    background: #f8f5ff;
    transform: translateY(-1px);
}

.pub-generate-footer {
    display: flex;
    justify-content: center;
    margin-top: 18px;
    margin-bottom: 8px;
}

@media (max-width: 992px) {
    .pub-table { min-width: 820px; }
}

@media (max-width: 820px) {
    .pub-table { min-width: 680px; }
    .pub-week-header-bar { padding: 20px 18px 14px; }
}

@media (max-width: 720px) {
    .pub-table { min-width: 560px; }
    .pub-filter-select { min-width: 120px; }
    .pub-category-filters { gap: 8px; }
    .pub-btn-add, .pub-btn-save { width: 100%; }
}

@media (max-width: 560px) {
    .pub-report-panel { padding: 18px; }
    .pub-week-header-bar { gap: 12px; }
    .pub-topbar-actions { width: 100%; justify-content: flex-start; }
    .pub-filter-pill { width: 100%; }
    .pub-topbar { align-items: flex-start; }
}

/* Small UX & responsiveness improvements */
.pub-table thead th {
    position: sticky;
    top: 0;
    z-index: 3;
}

.pub-col-company { width: 260px; min-width: 180px; }
.pub-col-task { width: auto; min-width: 110px; }
.pub-col-assignment { width: 220px; min-width: 160px; }
.pub-col-actions { width: 90px; min-width: 70px; }

.pub-table-wrapper {
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}

.pub-assignment-wrapper { justify-content: flex-start; }
.pub-assignment-chips { justify-content: flex-start; }

.pub-company-input, .pub-task-textarea { word-break: break-word; }

/* Ensure table header remains visually separated when sticky */
.pub-table thead th { box-shadow: 0 6px 12px rgba(15,23,42,0.04); }

</style>

<script>
$(document).ready(function() {
    const isAdmin = '<?= strtolower($_SESSION['user_role']) ?>' === 'admin';
    const userId = '<?= $_SESSION['user_id'] ?>';
    
    let reportState = {
        tables: [],
        rows: [],
        assignments: {},
        users: []
    };
    
    let currentCategory = 'posts';
    let isSaving = false;

    // Tab Filters
    $('.pub-cat-pill').on('click', function() {
        $('.pub-cat-pill').removeClass('active');
        $(this).addClass('active');
        currentCategory = $(this).data('cat');
        renderReport();
    });

    function fetchReport() {
        const month = $('#select-month').val();
        const year = $('#select-year').val();
        
        $('#report-container').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-neutral-500">Loading report data...</p>
            </div>
        `);
        
        $.get('<?= url('/api/publishing/fetch-report') ?>', { month: month, year: year }, function(res) {
            if (res.status === 'success') {
                reportState = res.data;
                renderReport();
            } else {
                toastr.error(res.message || 'Failed to fetch report');
            }
        }).fail(function() {
            toastr.error('Server error while fetching report');
        });
    }

    function renderReport() {
        const $container = $('#report-container');
        $container.empty();

        // Filter tables matching the current category
        const catTables = reportState.tables.filter(t => t.category === currentCategory);

        if (catTables.length === 0) {
            let html = `<div class="pub-empty-state">`;
            html += `<div class="empty-icon"><i class="fas fa-file-invoice"></i></div>`;
            html += `<h3>No Weekly Tables Yet</h3>`;
            html += `<p>Create your first weekly publishing report table for this category.</p>`;
            if (isAdmin) {
                html += `<button class="pub-btn-create" id="btn-create-first-table"><i class="fas fa-plus"></i>+ Create Week 1 Table</button>`;
            }
            html += `</div>`;
            $container.html(html);

            $('#btn-create-first-table').on('click', function() {
                createTable(1);
            });
            return;
        }

        // Render each table (which represents a week)
        catTables.forEach(table => {
            const tableRows = reportState.rows.filter(r => r.table_id === table.id);

            const $reportContainer = $('<div>').addClass('pub-report-container').attr('data-table-id', table.id);

            // Week Header & Controls
            const $headerBar = $('<div>').addClass('pub-week-header-bar');
            
            const $weekBox = $('<div>').addClass('pub-week-box');
            $weekBox.html(`<span>Week ${table.week_number}</span>`);
            $headerBar.append($weekBox);

            // Admin Actions on Table
            if (isAdmin) {
                const $tblDeleteBtn = $('<button>').addClass('btn btn-sm btn-outline-danger rounded-pill px-3')
                    .html('<i class="fas fa-trash-alt me-2"></i>Delete Table')
                    .on('click', function() {
                        deleteTable(table.id);
                    });
                $headerBar.append($tblDeleteBtn);
            }

            $reportContainer.append($headerBar);

            // Table Wrapper
            const $tableWrapper = $('<div>').addClass('pub-table-wrapper');
            const $table = $('<table>').addClass('pub-table');

            // Colgroup
            const $colgroup = $('<colgroup>');
            $colgroup.append('<col class="pub-col-company">');
            for (let i = 0; i < 7; i++) {
                $colgroup.append('<col class="pub-col-task">');
            }
            $colgroup.append('<col class="pub-col-assignment">');
            if (isAdmin) {
                $colgroup.append('<col class="pub-col-actions">');
            }
            $table.append($colgroup);

            // Thead
            const $thead = $('<thead>');
            const $headerRow = $('<tr>');
            $headerRow.append('<th class="pub-col-company">COMPANY NAME</th>');
            for (let i = 1; i <= 7; i++) {
                $headerRow.append(`<th class="pub-col-task">TASK BOX ${i}</th>`);
            }
            $headerRow.append('<th class="pub-col-assignment">ASSIGNMENT</th>');
            if (isAdmin) {
                $headerRow.append('<th class="pub-col-actions">ACTIONS</th>');
            }
            $thead.append($headerRow);
            $table.append($thead);

            // Tbody
            const $tbody = $('<tbody>');
            if (tableRows.length === 0) {
                // If somehow no rows exist, add an empty placeholder or add empty rows
                for (let r = 0; r < 5; r++) {
                    const tempId = 'temp-' + Math.random().toString(36).substr(2, 9);
                    const newRow = {
                        id: tempId,
                        table_id: table.id,
                        company_name: '',
                        task_box_1: '', task_box_2: '', task_box_3: '', task_box_4: '', task_box_5: '', task_box_6: '', task_box_7: '',
                        row_order: r
                    };
                    reportState.rows.push(newRow);
                    tableRows.push(newRow);
                }
            }

            tableRows.forEach(row => {
                $tbody.append(buildTableRow(row, table.id));
            });

            $table.append($tbody);
            $tableWrapper.append($table);
            $reportContainer.append($tableWrapper);

            // Bottom Buttons inside each table
            const $tableFooter = $('<div>').addClass('pub-table-footer');
            
            if (isAdmin) {
                const $saveBtn = $('<button>').addClass('pub-btn-save').text('SAVE TABLE');
                $saveBtn.on('click', function() {
                    saveReport($(this));
                });
                $tableFooter.append($saveBtn);

                const $addRowBtn = $('<button>').addClass('pub-btn-add').html('<i class="fas fa-plus me-2"></i>Add Row');
                $addRowBtn.on('click', function() {
                    const tempId = 'temp-' + Math.random().toString(36).substr(2, 9);
                    const newRow = {
                        id: tempId,
                        table_id: table.id,
                        company_name: '',
                        task_box_1: '', task_box_2: '', task_box_3: '', task_box_4: '', task_box_5: '', task_box_6: '', task_box_7: '',
                        row_order: tableRows.length
                    };
                    reportState.rows.push(newRow);
                    renderReport();
                });
                $tableFooter.append($addRowBtn);
            }

            $reportContainer.append($tableFooter);
            $container.append($reportContainer);
        });

        // Add Category-level "CREATE MORE TABLE +" at the very bottom
        if (isAdmin) {
            const nextWeekNum = catTables.length + 1;
            const $genFooter = $('<div>').addClass('pub-generate-footer');
            const $createTblBtn = $('<button>').addClass('pub-btn-add').css('background', '#f5f3ff').css('border-color', '#7e22ce').css('color', '#7e22ce')
                .html(`<i class="fas fa-folder-plus me-2"></i>CREATE MORE TABLE (WEEK ${nextWeekNum})`);
            $createTblBtn.on('click', function() {
                createTable(nextWeekNum);
            });
            $genFooter.append($createTblBtn);
            $container.append($genFooter);
        }

        // Initialize Select2 dropdowns
        initSelect2Dropdowns();
        
        // Auto resize task textareas
        autoResizeTextareas();
    }

    function buildTableRow(row, tableId) {
        const $tr = $('<tr>').attr('data-row-id', row.id);

        // 1. Company Name
        const $companyTd = $('<td>');
        const $companyInput = $('<input>').addClass('pub-company-input')
            .attr('type', 'text')
            .attr('placeholder', 'Company name...')
            .val(row.company_name || '');

        if (!isAdmin) {
            $companyInput.attr('disabled', true);
        }

        $companyInput.on('input change', function() {
            row.company_name = $(this).val();
        });
        $companyTd.append($companyInput);
        $tr.append($companyTd);

        // 2. Task Box 1 to 7
        for (let i = 1; i <= 7; i++) {
            const field = `task_box_${i}`;
            const $td = $('<td>');
            const $textarea = $('<textarea>').addClass('pub-task-textarea')
                .val(row[field] || '');

            if (!isAdmin) {
                $textarea.attr('disabled', true);
            }

            $textarea.on('input', function() {
                autoResize(this);
                row[field] = $(this).val();
            });

            $td.append($textarea);
            $tr.append($td);
        }

        // 3. Assignment
        const $assignTd = $('<td>');
        const $assignWrapper = $('<div>').addClass('pub-assignment-wrapper');

        if (isAdmin) {
            const $select = $('<select>').addClass('form-select form-select-sm pub-select-users')
                .attr('multiple', 'multiple')
                .css('width', '100%');

            const currentAssigns = reportState.assignments[tableId] || [];
            const assignedIds = currentAssigns.map(a => a.user_id);

            reportState.users.forEach(user => {
                const $opt = $('<option>')
                    .val(user.id)
                    .text(user.full_name)
                    .attr('selected', assignedIds.includes(user.id));
                $select.append($opt);
            });

            $select.on('change', function() {
                const selectedIds = $(this).val() || [];
                const updatedAssigns = selectedIds.map(uid => {
                    const u = reportState.users.find(usr => usr.id === uid);
                    return {
                        user_id: uid,
                        full_name: u ? u.full_name : '',
                        username: u ? u.username : ''
                    };
                });
                reportState.assignments[tableId] = updatedAssigns;
            });

            $assignWrapper.append($select);
        } else {
            const $chips = $('<div>').addClass('pub-assignment-chips');
            const currentAssigns = reportState.assignments[tableId] || [];

            if (currentAssigns.length > 0) {
                currentAssigns.forEach(assign => {
                    $chips.append($('<span>').addClass('pub-assignment-chip').text(assign.full_name));
                });
            } else {
                $chips.append($('<span>').addClass('pub-assignment-chip-empty').text('No assignments'));
            }
            $assignWrapper.append($chips);
        }

        $assignTd.append($assignWrapper);
        $tr.append($assignTd);

        if (isAdmin) {
            const $actionsTd = $('<td>');
            const $actionsWrapper = $('<div>').addClass('pub-actions-wrapper');

            const $deleteRowBtn = $('<button>').addClass('pub-btn-action pub-btn-delete')
                .attr('title', 'Delete row')
                .html('<i class="fas fa-times"></i>');

            $deleteRowBtn.on('click', function() {
                Swal.fire({
                    title: 'Delete row?',
                    text: 'Are you sure you want to remove this row from the table?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        reportState.rows = reportState.rows.filter(r => r.id !== row.id);
                        renderReport();
                    }
                });
            });

            $actionsWrapper.append($deleteRowBtn);
            $actionsTd.append($actionsWrapper);
            $tr.append($actionsTd);
        }

        return $tr;
    }

    function initSelect2Dropdowns() {
        $('.pub-select-users').select2({
            placeholder: 'Assign members...',
            allowClear: true,
            closeOnSelect: false
        });
    }

    function autoResize(textarea) {
        textarea.style.height = '56px';
        if (textarea.scrollHeight > 56) {
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    }
    
    function autoResizeTextareas() {
        $('.pub-task-textarea').each(function() {
            autoResize(this);
        });
    }

    function createTable(weekNum) {
        const month = $('#select-month').val();
        const year = $('#select-year').val();

        $.post('<?= url('/api/publishing/create-table') ?>', {
            category: currentCategory,
            week_number: weekNum,
            month: month,
            year: year
        }, function(res) {
            if (res.status === 'success') {
                toastr.success(res.message);
                fetchReport();
            } else {
                toastr.error(res.message);
            }
        }).fail(function() {
            toastr.error('Failed to create table');
        });
    }

    function deleteTable(tableId) {
        Swal.fire({
            title: 'Are you sure you want to delete this table?',
            text: "This will permanently delete the table, all rows, and related assignments! No orphan data will remain.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= url('/api/publishing/delete-table') ?>', { id: tableId }, function(res) {
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        fetchReport();
                    } else {
                        toastr.error(res.message);
                    }
                }).fail(function() {
                    toastr.error('Failed to delete table');
                });
            }
        });
    }

    function saveReport($btn) {
        if (isSaving) return;
        
        isSaving = true;
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Saving...');
        
        const dataToSend = JSON.parse(JSON.stringify(reportState));
        
        $.ajax({
            url: '<?= url('/api/publishing/save-report') ?>',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    fetchReport(); 
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                toastr.error('Failed to save table data');
            },
            complete: function() {
                isSaving = false;
                $btn.prop('disabled', false).text(originalText);
            }
        });
    }

    $('#btn-load-report').on('click', function() {
        fetchReport();
    });

    // Initial load
    fetchReport();
});
</script>

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
                <div class="pub-filters-group">
                    <div class="pub-filter-pill">
                        <label for="select-month" class="pub-filter-label">Select month</label>
                        <div class="pub-filter-control">
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
                    </div>

                    <div class="pub-filter-pill">
                        <label for="select-year" class="pub-filter-label">Select year</label>
                        <div class="pub-filter-control">
                            <select id="select-year" class="form-select form-select-sm pub-filter-select">
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear - 1; $y <= $currentYear + 2; $y++):
                        ?>
                            <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                            </select>
                        </div>
                    </div>
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

.pub-filters-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.pub-filter-pill {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.02);
    border-radius: 18px;
    padding: 4px 8px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
    min-width: 100px;
    max-width: 160px;
}

.pub-filter-control {
    width: auto;
    flex: 1 1 auto;
}

.pub-filter-label {
    font-size: 0.68rem;
    color: #9aa7b7;
    margin: 0 6px 0 0;
    white-space: nowrap;
}

.pub-filter-select {
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
    min-width: 80px;
    height: 30px;
    font-size: 0.92rem;
    color: #0f172a;
    padding-left: 6px;
    padding-right: 6px;
    -webkit-appearance: none;
    appearance: none;
}

.pub-filter-select:focus {
    outline: none !important;
    box-shadow: inset 0 0 0 1px rgba(124, 58, 237, 0.22) !important;
}

.pub-filter-pill:hover {
    transform: translateY(-3px);
    transition: transform 0.18s ease;
}

/* Reduce visual weight for desktop */
@media (min-width: 900px) {
    .pub-filter-label { display: none; }
    .pub-filter-pill { padding: 5px 10px; }
    .pub-filter-select { min-width: 95px; height: 32px; }
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

.pub-button-filter { margin-left: 6px; }

@media (max-width: 900px) {
    .pub-topbar {
        align-items: flex-start;
    }
    .pub-topbar-title {
        width: 100%;
    }
    .pub-topbar-actions {
        width: 100%;
        justify-content: flex-start;
        gap: 8px;
    }
    .pub-filters-group {
        order: 2;
    }
    .pub-button-filter {
        order: 3;
    }
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
    margin: 0;
    padding: 0;
    background: transparent !important;
    border: none !important;
}

/* When task textarea is inside a status cell, remove direct padding/border */
.pub-task-cell .pub-task-textarea {
    padding: 0;
    margin: 0;
    background: transparent !important;
    border: none !important;
}

.pub-company-input::placeholder,
.pub-task-textarea::placeholder {
    color: #94a3b8;
}

.pub-company-input:focus,
.pub-task-textarea:focus {
    background: transparent !important;
    outline: none !important;
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

/* ========================================
   TASK STATUS COLOR SYSTEM
   ======================================== */

/* Task Cell Base Styling */
.pub-task-cell {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 6px;
    padding: 8px 10px;
    display: flex;
    align-items: stretch;
    min-height: 42px;
}

.pub-task-cell:hover {
    transform: scale(1.01);
}

.pub-task-cell .pub-task-textarea {
    flex: 1;
    padding: 0;
    margin: 0;
}

/* Status: Production (Yellow) */
.pub-task-cell[data-status="production"] {
    background-color: #FFF3BF;
    border: 1px solid #F4D03F;
    color: #333333;
}

.pub-task-cell[data-status="production"]:hover {
    background-color: #FEE680;
    box-shadow: 0 4px 12px rgba(244, 208, 63, 0.25);
}

/* Status: Approval (Orange) */
.pub-task-cell[data-status="approval"] {
    background-color: #FFE0B2;
    border: 1px solid #F5B041;
    color: #333333;
}

.pub-task-cell[data-status="approval"]:hover {
    background-color: #FFD699;
    box-shadow: 0 4px 12px rgba(245, 176, 65, 0.25);
}

/* Status: Publishing (Green) */
.pub-task-cell[data-status="publishing"] {
    background-color: #D5F5E3;
    border: 1px solid #58D68D;
    color: #155724;
    font-weight: 600;
}

.pub-task-cell[data-status="publishing"]:hover {
    background-color: #AEF0C5;
    box-shadow: 0 4px 12px rgba(88, 214, 141, 0.25);
}

/* Status: Empty/Default (White) */
.pub-task-cell[data-status=""],
.pub-task-cell:not([data-status]),
.pub-task-cell[data-status="null"] {
    background-color: #FFFFFF;
    border: 1px solid #E5E7EB;
    color: #333333;
}

.pub-task-cell[data-status=""]:hover,
.pub-task-cell:not([data-status]):hover,
.pub-task-cell[data-status="null"]:hover {
    background-color: #F9FAFB;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}

/* Legend Styling */
.pub-status-legend {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0;
    margin-left: auto;
    flex-wrap: wrap;
}

.pub-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    white-space: nowrap;
}

.pub-legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.pub-legend-dot.production {
    background-color: #F4D03F;
    box-shadow: 0 0 0 1px #D4AF2F;
}

.pub-legend-dot.approval {
    background-color: #F5B041;
    box-shadow: 0 0 0 1px #D89A35;
}

.pub-legend-dot.publishing {
    background-color: #58D68D;
    box-shadow: 0 0 0 1px #3CAF6D;
}

/* Responsive Legend */
@media (max-width: 720px) {
    .pub-status-legend {
        gap: 10px;
        margin-left: 0;
        margin-top: 12px;
        justify-content: flex-start;
    }
    
    .pub-legend-item {
        font-size: 0.78rem;
    }
}

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
    let autoSyncInterval = null;
    const AUTO_SYNC_INTERVAL = 2000; // Sync every 2 seconds (per prompt requirement)
    let lastSyncTime = null;

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
                // Initialize lastSyncTime from server to avoid missing prior updates
                if (res.sync_timestamp) {
                    lastSyncTime = res.sync_timestamp;
                } else {
                    lastSyncTime = new Date().toISOString();
                }
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

            // Add Status Legend
            const $legend = $('<div>').addClass('pub-status-legend');
            $legend.append(
                $('<div>').addClass('pub-legend-item').html(
                    '<span class="pub-legend-dot production"></span> Production'
                )
            );
            $legend.append(
                $('<div>').addClass('pub-legend-item').html(
                    '<span class="pub-legend-dot approval"></span> Approval'
                )
            );
            $legend.append(
                $('<div>').addClass('pub-legend-item').html(
                    '<span class="pub-legend-dot publishing"></span> Publishing'
                )
            );
            $headerBar.append($legend);

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
                $headerRow.append(`<th class="pub-col-task">DAY ${i}</th>`);
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
                        task_status_1: null, task_status_2: null, task_status_3: null, task_status_4: null, task_status_5: null, task_status_6: null, task_status_7: null,
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
                        task_status_1: null, task_status_2: null, task_status_3: null, task_status_4: null, task_status_5: null, task_status_6: null, task_status_7: null,
                        row_order: tableRows.length
                    };
                    reportState.rows.push(newRow);
                    tableRows.push(newRow); // update local tableRows scope
                    const $tbody = $reportContainer.find('tbody');
                    $tbody.append(buildTableRow(newRow, table.id));
                    initSelect2Dropdowns(); // Re-initialize select2 for new row
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
            // Removed reactive binding to preserve last-known database state
        });
        $companyTd.append($companyInput);
        $tr.append($companyTd);

        // 2. Task Box 1 to 7
        for (let i = 1; i <= 7; i++) {
            const field = `task_box_${i}`;
            const statusField = `task_status_${i}`;
            const $td = $('<td>');
            
            // Create a wrapper div for the task cell with status capability
            const $cellWrapper = $('<div>')
                .addClass('pub-task-cell')
                .attr('data-task-index', i)
                .attr('data-row-id', row.id)
                .attr('data-status', row[statusField] || '');
            
            const $textarea = $('<textarea>')
                .addClass('pub-task-textarea')
                .val(row[field] || '')
                .attr('placeholder', 'Task...')
                .css('width', '100%')
                .css('height', 'auto')
                .css('border', 'none')
                .css('background', 'transparent')
                .css('padding', '0')
                .css('margin', '0')
                .css('resize', 'vertical')
                .css('font-size', '0.92rem')
                .css('line-height', '1.45');

            if (!isAdmin) {
                $textarea.attr('disabled', true);
            }

            $textarea.on('input', function() {
                autoResize(this);
                // Removed reactive binding to preserve last-known database state
            });

            // Add double-click handler for status cycling
            $cellWrapper.on('dblclick', function(e) {
                e.preventDefault();
                cycleTaskStatus($cellWrapper, row, statusField);
            });

            $cellWrapper.append($textarea);
            $td.append($cellWrapper);
            $tr.append($td);
        }

        // 3. Assignment
        const $assignTd = $('<td>');
        const $assignWrapper = $('<div>').addClass('pub-assignment-wrapper');

        if (isAdmin) {
            const $select = $('<select>').addClass('form-select form-select-sm pub-select-users')
                .attr('multiple', 'multiple')
                .css('width', '100%');

            const currentAssigns = reportState.assignments[row.id] || [];
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
                
                // Only save if it actually changed to prevent loops
                const currentStr = JSON.stringify(reportState.assignments[row.id] || []);
                const newStr = JSON.stringify(updatedAssigns);
                
                if (currentStr !== newStr) {
                    reportState.assignments[row.id] = updatedAssigns;
                    
                    // Optimistic update: Sync silently to backend immediately
                    if (!row.id.startsWith('temp-')) {
                        $.ajax({
                            url: '<?= url('/api/publishing/update-assignment') ?>',
                            type: 'POST',
                            data: JSON.stringify({
                                row_id: row.id,
                                assigned_user_ids: selectedIds
                            }),
                            contentType: 'application/json',
                            success: function(res) {
                                if (res && res.status === 'success') {
                                    lastSyncTime = res.updated_at || lastSyncTime;
                                }
                            }
                        });
                    }
                }
            });

            $assignWrapper.append($select);
        } else {
            const $chips = $('<div>').addClass('pub-assignment-chips');
            const currentAssigns = reportState.assignments[row.id] || [];

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

    /**
     * Cycle task status through: empty → production → approval → publishing → empty
     * This function is called when a task cell is double-clicked
     */
    function cycleTaskStatus($cellWrapper, row, statusField) {
        // Only allow status changes if user is admin or authorized
        // Current state is stored in data-status attribute
        const currentStatus = $cellWrapper.attr('data-status') || '';
        
        // Status cycle order: '' → 'production' → 'approval' → 'publishing' → ''
        let nextStatus = '';
        
        if (currentStatus === '' || currentStatus === 'null' || !currentStatus) {
            nextStatus = 'production';
        } else if (currentStatus === 'production') {
            nextStatus = 'approval';
        } else if (currentStatus === 'approval') {
            nextStatus = 'publishing';
        } else if (currentStatus === 'publishing') {
            nextStatus = '';
        } else {
            nextStatus = 'production'; // Default fallback
        }
        
        // Update the DOM element
        $cellWrapper.attr('data-status', nextStatus);
        
        // Update the row data model
        row[statusField] = nextStatus || null;
        
        // Immediately save this specific status change to database (auto-save)
        saveTaskStatusChange(row.id, statusField, nextStatus || null);
    }

    /**
     * Save a single task status change immediately
     */
    function saveTaskStatusChange(rowId, statusField, statusValue) {
        // Send a lightweight single-cell update to the cell-update API
        // statusField format: task_status_N -> extract N
        const match = statusField.match(/task_status_(\d+)/);
        const taskIndex = match ? parseInt(match[1], 10) : 1;
        const payload = {
            row_id: rowId,
            table_id: $('[data-table-id]').first().attr('data-table-id') || '',
            task_index: taskIndex,
            status: statusValue || null,
            updated_by: userId
        };

        console.log('Saving color', payload);

        $.ajax({
            url: '<?= url('/api/publishing/cell-update') ?>',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            timeout: 5000,
            success: function(res) {
                console.log('Save response', res);
                if (res && res.status === 'success') {
                    // Optionally update lastSyncTime to keep polls efficient
                    lastSyncTime = res.updated_at || lastSyncTime;
                }
            },
            error: function(xhr, status, err) {
                console.warn('Auto-save status change failed:', err);
            }
        });
    }

    /**
     * Auto-sync: Fetch updated report data and merge changes without losing local edits
     */
    function autoSyncReportData() {
        // Use incremental sync endpoint to fetch only changed cells since lastSyncTime
        const month = $('#select-month').val();
        const year = $('#select-year').val();

        // Determine visible table ids to scope polling
        const visibleTables = reportState.tables.filter(t => t.category === currentCategory).map(t => t.id);
        if (visibleTables.length === 0) return;

        // Use a single table scope per poll to keep queries small; poll each table sequentially
        visibleTables.forEach(function(tblId) {
            const params = {
                last_sync: lastSyncTime || new Date().toISOString(),
                table_id: tblId
            };

            $.get('<?= url('/api/publishing/sync-changes') ?>', params, function(res) {
                console.log('Polling changes', res);
                if (res.status === 'success' && res.data && res.data.changes) {
                    mergeRemoteChanges({ rows: res.data.changes });
                    // Update lastSyncTime from server to avoid missing entries
                    if (res.data.sync_timestamp) {
                        lastSyncTime = res.data.sync_timestamp;
                    } else {
                        lastSyncTime = new Date().toISOString();
                    }
                }
            }).fail(function() {
                console.warn('Auto-sync failed for table', tblId);
            });
        });
    }

    /**
     * Intelligently merge remote changes with local state
     * Only updates cells that have changed on the remote side
     */
    function mergeRemoteChanges(remoteData) {
        if (!remoteData || !remoteData.rows) return;
        
        // Create a map of remote rows by ID
        const remoteRowMap = {};
        remoteData.rows.forEach(row => {
            remoteRowMap[row.id] = row;
        });
        
        // Update local rows with remote status changes, text changes, and assignments
        reportState.rows.forEach(localRow => {
            const remoteRow = remoteRowMap[localRow.id];
            if (remoteRow) {
                // Remove from map to track which ones are new
                delete remoteRowMap[localRow.id];

                // 1. Sync Text Fields and Statuses (task_box_1 through 7 and task_status)
                for (let i = 1; i <= 7; i++) {
                    const statusField = `task_status_${i}`;
                    const textField = `task_box_${i}`;
                    
                    const remoteStatus = remoteRow[statusField];
                    const localStatus = localRow[statusField];
                    
                    const remoteText = remoteRow[textField];
                    const localText = localRow[textField];
                    
                    const $cellWrapper = $(`.pub-task-cell[data-row-id="${localRow.id}"][data-task-index="${i}"]`);
                    
                    if ($cellWrapper.length > 0) {
                        // Status Sync
                        if (remoteStatus !== localStatus) {
                            const currentDOMStatus = $cellWrapper.attr('data-status') || '';
                            const newStatus = remoteStatus || '';
                            if (currentDOMStatus !== newStatus) {
                                $cellWrapper.attr('data-status', newStatus);
                            }
                            localRow[statusField] = remoteStatus;
                        }
                        
                        // Text Sync
                        const safeRemoteText = remoteText || '';
                        const safeLocalText = localText || '';
                        const $textarea = $cellWrapper.find('.pub-task-textarea');
                        const domValue = $textarea.val() || '';

                        if (safeRemoteText !== safeLocalText) {
                            // Only overwrite DOM if DOM hasn't been locally modified away from localText (unsaved edits)
                            if (!$textarea.is(':focus') && domValue === safeLocalText) {
                                $textarea.val(safeRemoteText);
                                localRow[textField] = safeRemoteText;
                            } else if (domValue === safeRemoteText) {
                                // If the user manually typed exactly the remote value, just update the state
                                localRow[textField] = safeRemoteText;
                            }
                        }
                    }
                }
                
                // 2. Sync Company Name
                const safeRemoteCompany = remoteRow.company_name || '';
                const safeLocalCompany = localRow.company_name || '';
                
                if (safeRemoteCompany !== safeLocalCompany) {
                    const $companyInput = $(`tr[data-row-id="${localRow.id}"] .pub-company-input`);
                    const domCompany = $companyInput.val() || '';
                    if (!$companyInput.is(':focus') && domCompany === safeLocalCompany) {
                        $companyInput.val(safeRemoteCompany);
                        localRow.company_name = safeRemoteCompany;
                    } else if (domCompany === safeRemoteCompany) {
                        localRow.company_name = safeRemoteCompany;
                    }
                }

                // 3. Sync Assignments
                if (remoteRow.assignments) {
                    const remoteAssignStr = JSON.stringify(remoteRow.assignments);
                    const localAssignStr = JSON.stringify(reportState.assignments[localRow.id] || []);
                    if (remoteAssignStr !== localAssignStr) {
                        const $assignTd = $(`tr[data-row-id="${localRow.id}"] .pub-assignment-wrapper`).parent();
                        let isOpen = false;
                        
                        if (isAdmin) {
                            const $select = $assignTd.find('.pub-select-users');
                            if ($select.length) {
                                // Do not update assignment if the admin is actively modifying it
                                isOpen = $select.hasClass('select2-hidden-accessible') && $select.select2('isOpen');
                            }
                        }
                        
                        if (!isOpen) {
                            reportState.assignments[localRow.id] = remoteRow.assignments;
                            if ($assignTd.length > 0) {
                                if (isAdmin) {
                                    const $select = $assignTd.find('.pub-select-users');
                                    if ($select.length) {
                                        const assignedIds = remoteRow.assignments.map(a => a.user_id);
                                        $select.val(assignedIds).trigger('change.select2'); // use change.select2 to not trigger our change event loop
                                    }
                                } else {
                                    const $chips = $assignTd.find('.pub-assignment-chips');
                                    if ($chips.length) {
                                        $chips.empty();
                                        if (remoteRow.assignments.length > 0) {
                                            remoteRow.assignments.forEach(assign => {
                                                $chips.append($('<span>').addClass('pub-assignment-chip').text(assign.full_name));
                                            });
                                        } else {
                                            $chips.append($('<span>').addClass('pub-assignment-chip-empty').text('No assignments'));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        // 4. Handle completely new rows that were added remotely
        Object.values(remoteRowMap).forEach(newRemoteRow => {
            // It's a new row, add it to our local state
            reportState.rows.push(newRemoteRow);
            if (newRemoteRow.assignments) {
                reportState.assignments[newRemoteRow.id] = newRemoteRow.assignments;
            }
            
            // Append it to the corresponding table DOM
            const $container = $(`.pub-report-container[data-table-id="${newRemoteRow.table_id}"]`);
            if ($container.length > 0) {
                const $tbody = $container.find('tbody');
                const $newRowHtml = buildTableRow(newRemoteRow, newRemoteRow.table_id);
                $tbody.append($newRowHtml);
                initSelect2Dropdowns(); // Re-initialize to cover the newly added row
            }
        });
    }

    /**
     * Start auto-sync if not already running
     */
    function startAutoSync() {
        if (autoSyncInterval) return; // Already running
        
        autoSyncInterval = setInterval(function() {
            autoSyncReportData();
        }, AUTO_SYNC_INTERVAL);
        
        console.log('Auto-sync started (interval:', AUTO_SYNC_INTERVAL, 'ms)');
    }

    /**
     * Stop auto-sync
     */
    function stopAutoSync() {
        if (autoSyncInterval) {
            clearInterval(autoSyncInterval);
            autoSyncInterval = null;
            console.log('Auto-sync stopped');
        }
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
        
        // Before saving, we MUST pull the latest values from the DOM into reportState.
        // We removed reactive bindings earlier to prevent polling from breaking active drafts,
        // so the DOM is now the only source of truth for the Admin's typed input.
        $('.pub-report-container').each(function() {
            $(this).find('tbody tr').each(function() {
                const rowId = $(this).attr('data-row-id');
                const localRow = reportState.rows.find(r => r.id === rowId);
                if (localRow) {
                    const companyVal = $(this).find('.pub-company-input').val();
                    if (companyVal !== undefined) localRow.company_name = companyVal;
                    
                    for (let i = 1; i <= 7; i++) {
                        const taskVal = $(this).find(`.pub-task-cell[data-task-index="${i}"] .pub-task-textarea`).val();
                        if (taskVal !== undefined) localRow[`task_box_${i}`] = taskVal;
                    }
                }
            });
        });
        
        const dataToSend = JSON.parse(JSON.stringify(reportState));
        
        $.ajax({
            url: '<?= url('/api/publishing/save-report') ?>',
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    
                    // Replace temp IDs with real database IDs to prevent duplicate inserts on next save
                    if (res.id_mapping && Object.keys(res.id_mapping).length > 0) {
                        Object.keys(res.id_mapping).forEach(oldId => {
                            const newId = res.id_mapping[oldId];
                            
                            // 1. Update state
                            const row = reportState.rows.find(r => r.id === oldId);
                            if (row) row.id = newId;
                            
                            // 2. Update assignments state
                            if (reportState.assignments[oldId]) {
                                reportState.assignments[newId] = reportState.assignments[oldId];
                                delete reportState.assignments[oldId];
                            }
                            
                            // 3. Update DOM
                            $(`tr[data-row-id="${oldId}"]`).attr('data-row-id', newId);
                            $(`.pub-task-cell[data-row-id="${oldId}"]`).attr('data-row-id', newId);
                        });
                    }
                    
                    // Hydrate reportState with the exact data we just saved
                    dataToSend.rows.forEach(savedRow => {
                        const targetId = (res.id_mapping && res.id_mapping[savedRow.id]) ? res.id_mapping[savedRow.id] : savedRow.id;
                        let localR = reportState.rows.find(r => r.id === targetId || r.id === savedRow.id);
                        if (localR) {
                            Object.assign(localR, savedRow);
                            localR.id = targetId; // ensure ID is mapped
                        }
                    });
                    
                    // Force a lightweight sync to pull in any updated timestamps or changes
                    autoSyncReportData();
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

    // Initial load and start auto-sync
    fetchReport();
    startAutoSync();
    
    // Stop auto-sync when leaving the page
    $(window).on('beforeunload', function() {
        stopAutoSync();
    });
});
</script>

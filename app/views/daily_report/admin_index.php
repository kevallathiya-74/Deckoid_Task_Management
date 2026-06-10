<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up" style="padding-top: 16px;">
        <div class="daily-report-card" style="max-width:1240px;margin:0 auto;">
            <!-- <div class="daily-report-header d-flex justify-content-between align-items-center" style="padding:20px 24px;border-bottom:1px solid #eef2ff;">
                <div>
                    <h4 class="fw-bold mb-1">Daily Report Summary</h4>
                    <p class="text-muted mb-0">View staff daily reports by user and date.</p>
                </div>
            </div> -->

            <div class="daily-report-body" style="padding:18px;">
                <div class="admin-filter-panel" style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:18px;align-items:flex-end;padding:18px 16px;border:1px solid #eef2ff;border-radius:18px;background:#fff;">
                    <div style="flex:1;min-width:220px;">
                        <label class="form-label" for="admin-user-select" style="font-weight:700;font-size:12px;color:#374151;">Staff Member</label>
                        <select id="admin-user-select" class="form-select" style="border-radius:12px;padding:10px 12px;">
                            <option value="">Select staff member</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name'] ?? $user['name'] ?? $user['email']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="min-width:210px;">
                        <label class="form-label" for="admin-date-input" style="font-weight:700;font-size:12px;color:#374151;">Report Date</label>
                        <input id="admin-date-input" type="date" class="form-control" style="border-radius:12px;padding:10px 12px;" />
                    </div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <button id="btn-load-report" class="btn btn-primary" style="border-radius:14px;padding:10px 18px;background:linear-gradient(90deg,#7e22ce,#a78bfa);border:none;">Load Report</button>
                        <button id="btn-clear-filters" class="btn btn-light" style="border-radius:14px;padding:10px 18px;border:1px solid #e5e7eb;">Clear</button>
                    </div>
                </div>

                <div id="admin-report-empty" class="alert alert-info" style="border-radius:16px;display:none;">Select a staff member and date, then click <strong>Load Report</strong> to view report details.</div>

                <div id="admin-report-summary" style="display:none;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="summary-card" style="background:#fff;border-radius:16px;padding:18px;box-shadow:0 6px 18px rgba(15,23,42,0.06);min-height:110px;">
                                <div style="font-size:12px;color:#6b7280;">Report Date</div>
                                <div id="admin-summary-date" style="font-weight:800;font-size:18px;">-</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card" style="background:#fff;border-radius:16px;padding:18px;box-shadow:0 6px 18px rgba(15,23,42,0.06);min-height:110px;">
                                <div style="font-size:12px;color:#6b7280;">Total Tasks</div>
                                <div id="admin-summary-total-tasks" style="font-weight:800;font-size:18px;">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card" style="background:#fff;border-radius:16px;padding:18px;box-shadow:0 6px 18px rgba(15,23,42,0.06);min-height:110px;">
                                <div style="font-size:12px;color:#6b7280;">Total Number</div>
                                <div id="admin-summary-total-number" style="font-weight:800;font-size:18px;">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card" style="background:#fff;border-radius:16px;padding:18px;box-shadow:0 6px 18px rgba(15,23,42,0.06);min-height:110px;">
                                <div style="font-size:12px;color:#6b7280;">Last Updated</div>
                                <div id="admin-summary-updated" style="font-weight:800;font-size:16px;">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="pub-table-wrapper" style="border-radius:12px;padding:12px;border:1px solid #eef2ff;background:#fff;overflow-x:auto;">
                        <table class="pub-table daily-table" id="admin-daily-report-table" style="width:100%;min-width:720px;">
                            <colgroup>
                                <col style="width:70%;">
                                <col style="width:30%;">
                            </colgroup>
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:12px 10px;text-align:left;font-weight:800;font-size:12px;color:#111827;">Daily Task</th>
                                    <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Number</th>
                                </tr>
                            </thead>
                            <tbody id="admin-report-rows">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="admin-history-panel" style="margin-top:24px;display:none;">
                    <h5 style="margin-bottom:14px;font-weight:700;color:#111827;">Report History</h5>
                    <div class="pub-table-wrapper" style="border-radius:12px;padding:12px;border:1px solid #eef2ff;background:#fff;overflow-x:auto;">
                        <table class="pub-table" id="admin-history-table" style="width:100%;min-width:720px;">
                            <colgroup>
                                <col style="width:20%;">
                                <col style="width:20%;">
                                <col style="width:20%;">
                                <col style="width:20%;">
                                <col style="width:20%;">
                            </colgroup>
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:12px 10px;text-align:left;font-weight:800;font-size:12px;color:#111827;">Date</th>
                                    <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Tasks</th>
                                    <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Total Value</th>
                                    <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Created</th>
                                    <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Updated</th>
                                </tr>
                            </thead>
                            <tbody id="admin-history-rows"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.daily-report-card { background:#ffffff;border-radius:24px;border:1px solid rgba(15,23,42,0.04);box-shadow:0 6px 18px rgba(15,23,42,0.03); }
.summary-card { transition:transform 0.2s ease; }
.summary-card:hover { transform:translateY(-1px); }
.pub-table thead th { text-transform: uppercase; letter-spacing:0.02em; }
.admin-history-row:hover { background:#f8f6ff; cursor:pointer; }
@media (max-width: 720px) {
    .daily-report-card { border-radius:16px; }
    .pub-table-wrapper { overflow-x:auto; }
    .daily-table { min-width: 600px; }
}
@media (max-width: 520px) {
    .daily-table, .daily-table thead { display:block; }
    .daily-table tbody tr { display:block; margin-bottom:12px; border:1px solid #f1f5f9; border-radius:12px; padding:10px; }
    .daily-table tbody td { display:block; width:100%; padding:6px 0 !important; }
    .daily-table tbody td:last-child { text-align:right; }
}
</style>

<script>
$(function() {
    const $userSelect = $('#admin-user-select');
    const $dateInput = $('#admin-date-input');
    const $loadButton = $('#btn-load-report');
    const $clearButton = $('#btn-clear-filters');
    const $summaryPanel = $('#admin-report-summary');
    const $historyPanel = $('#admin-history-panel');
    const $emptyPanel = $('#admin-report-empty');
    const $rowsBody = $('#admin-report-rows');
    const $historyRows = $('#admin-history-rows');

    function formatDateLabel(dateStr) {
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return new Date(dateStr).toLocaleDateString(undefined, options);
    }

    function formatTimestamp(dateTime) {
        if (!dateTime) return '-';
        const dt = new Date(dateTime);
        return dt.toLocaleString(undefined, { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short', year: 'numeric' });
    }

    function setDefaultDate() {
        const today = new Date().toISOString().slice(0, 10);
        $dateInput.val(today);
    }

    function showEmpty(message) {
        $emptyPanel.text(message).show();
        $summaryPanel.hide();
        $historyPanel.hide();
    }

    function showSummary(data) {
        $('#admin-summary-date').text(formatDateLabel(data.report.report_date));
        $('#admin-summary-total-tasks').text(data.report.total_tasks || 0);
        let totalVal = 0;
        if (data.report.total_value !== null && data.report.total_value !== undefined && data.report.total_value !== '') {
            const parsed = parseFloat(data.report.total_value);
            if (!isNaN(parsed)) {
                totalVal = Math.round(parsed);
            }
        }
        $('#admin-summary-total-number').text(totalVal);
        $('#admin-summary-updated').text(formatTimestamp(data.report.updated_at));
        $summaryPanel.show();
    }

    function renderReportRows(rows) {
        $rowsBody.empty();
        if (!rows || !rows.length) {
            $rowsBody.append('<tr><td colspan="2" style="padding:14px;text-align:center;color:#6b7280;">No report rows available for this date.</td></tr>');
            return;
        }
        rows.forEach(function(row) {
            let formattedNumber = '-';
            if (row.number_value !== null && row.number_value !== undefined && row.number_value !== '') {
                const parsed = parseFloat(row.number_value);
                if (!isNaN(parsed)) {
                    formattedNumber = Math.round(parsed);
                }
            }
            $rowsBody.append(`
                <tr>
                    <td style="padding:12px 10px;vertical-align:top;">${$('<div>').text(row.task_text || '').html()}</td>
                    <td style="padding:12px 10px;text-align:center;vertical-align:middle;">${formattedNumber}</td>
                </tr>
            `);
        });
    }

    function renderHistory(reports) {
        $historyRows.empty();
        if (!reports || !reports.length) {
            $historyRows.append('<tr><td colspan="5" style="padding:14px;text-align:center;color:#6b7280;">No historical reports found for this staff member.</td></tr>');
            return;
        }
        reports.forEach(function(report) {
            const created = formatTimestamp(report.created_at);
            const updated = formatTimestamp(report.updated_at);
            let formattedTotal = 0;
            if (report.total_value !== null && report.total_value !== undefined && report.total_value !== '') {
                const parsed = parseFloat(report.total_value);
                if (!isNaN(parsed)) {
                    formattedTotal = Math.round(parsed);
                }
            }
            $historyRows.append(`
                <tr class="admin-history-row" data-report-date="${report.report_date}">
                    <td style="padding:12px 10px;">${formatDateLabel(report.report_date)}</td>
                    <td style="padding:12px 10px;text-align:center;">${report.total_tasks}</td>
                    <td style="padding:12px 10px;text-align:center;">${formattedTotal}</td>
                    <td style="padding:12px 10px;text-align:center;">${created}</td>
                    <td style="padding:12px 10px;text-align:center;">${updated}</td>
                </tr>
            `);
        });
    }

    function loadReport(userId, date) {
        if (!userId || !date) {
            showEmpty('Please select a staff member and a date.');
            return;
        }

        const endpoint = '<?= url('/admin/api/daily-report/user') ?>/' + encodeURIComponent(userId) + '/' + encodeURIComponent(date);
        $.get(endpoint, function(res) {
            if (res.status === 'success' && res.data && res.data.report) {
                showSummary(res.data);
                renderReportRows(res.data.rows);
                loadHistory(userId);
                $emptyPanel.hide();
                $historyPanel.show();
            } else if (res.status === 'success') {
                $rowsBody.empty();
                renderReportRows([]);
                $summaryPanel.hide();
                showEmpty('No report found for the selected staff member and date.');
                loadHistory(userId);
            } else {
                toastr.error(res.message || 'Unable to load report.');
            }
        }).fail(function() {
            toastr.error('Server error while fetching report.');
        });
    }

    function loadHistory(userId) {
        if (!userId) {
            $historyPanel.hide();
            return;
        }
        const endpoint = '<?= url('/admin/api/daily-report/user') ?>/' + encodeURIComponent(userId);
        $.get(endpoint, function(res) {
            if (res.status === 'success') {
                renderHistory(res.data || []);
                $historyPanel.show();
            } else {
                $historyPanel.hide();
            }
        }).fail(function() {
            $historyPanel.hide();
        });
    }

    $loadButton.on('click', function() {
        const userId = $userSelect.val();
        const date = $dateInput.val();
        loadReport(userId, date);
    });

    $clearButton.on('click', function() {
        $userSelect.val('');
        setDefaultDate();
        showEmpty('Select a staff member and date, then click Load Report to view report details.');
    });

    $(document).on('click', '.admin-history-row', function() {
        const date = $(this).data('report-date');
        $dateInput.val(date);
        const userId = $userSelect.val();
        if (userId) {
            loadReport(userId, date);
        }
    });

    setDefaultDate();
    showEmpty('Select a staff member and date, then click Load Report to view report details.');
});
</script>

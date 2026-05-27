<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up" style="padding-top: 16px;">
        <div class="daily-report-card" style="max-width:1200px;margin:0 auto;">

            <div class="daily-report-header d-flex justify-content-between align-items-center flex-wrap" style="padding:20px 24px;border-bottom:1px solid #eef2ff;gap:12px;">
                <div>
                    <h4 class="fw-bold mb-1">Daily Report</h4>
                    <p class="text-muted mb-0">Track and submit your daily work summary.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-2" style="background:#fff;border:1px solid rgba(126,34,206,0.12);border-radius:20px;padding:8px 14px;">
                        <span class="text-purple" style="font-weight:700;color:#6b21a8;">Date</span>
                        <input id="daily-report-date" type="date" class="form-control" style="border:none;box-shadow:none;min-width:150px;padding:6px 10px;border-radius:12px;" />
                    </div>
                    <button class="btn btn-outline-secondary" id="btn-load-report" style="border-radius:14px;padding:8px 16px;border:1px solid #e5e7eb;background:#fff;color:#111827;">Load</button>
                    <button class="btn btn-primary btn-save-report" id="btn-save-report" style="border-radius:14px;padding:8px 16px;background:linear-gradient(90deg,#7e22ce,#a78bfa);border:none;"> <i class="fas fa-save me-2"></i><span id="save-label">Save Report</span></button>
                </div>
            </div>

            <div class="daily-report-body" style="padding:18px;">
                <div class="pub-table-wrapper" style="border-radius:12px;padding:12px;border:1px solid #eef2ff;background:#fff;">
                    <table class="pub-table daily-table" id="daily-report-table" style="width:100%;min-width:720px;">
                        <colgroup>
                            <col style="width:68%;">
                            <col style="width:22%;">
                            <col style="width:10%;">
                        </colgroup>
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:12px 10px;text-align:left;font-weight:800;font-size:12px;color:#111827;">Daily Task</th>
                                <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Number</th>
                                <th style="padding:12px 10px;text-align:center;font-weight:800;font-size:12px;color:#111827;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr class="daily-row" data-row-index="<?= $i ?>">
                                <td style="padding:10px;vertical-align:top;">
                                    <textarea aria-label="Daily Task" class="pub-task-textarea daily-task" data-row-index="<?= $i ?>" placeholder="Describe the task..."></textarea>
                                </td>
                                <td style="padding:10px;vertical-align:middle;text-align:center;">
                                    <input aria-label="Number value" type="number" step="any" class="form-control daily-number" data-row-index="<?= $i ?>" placeholder="0" style="max-width:120px;margin:0 auto;border-radius:10px;text-align:center;" />
                                </td>
                                <td style="padding:10px;vertical-align:middle;text-align:center;">
                                    <button class="btn btn-sm btn-outline-danger btn-delete-row" title="Delete row" style="border-radius:8px;padding:6px 8px;"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <button class="pub-btn-add btn-add-row" id="btn-add-row" style="border-radius:12px;padding:8px 12px;border:1px solid #e6e6f0;background:#fff;"> <i class="fas fa-plus me-2"></i> Add Row</button>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="summary-card" style="background:#fff;border-radius:14px;padding:10px 14px;box-shadow:0 2px 6px rgba(15,23,42,0.03);text-align:center;min-width:120px;">
                            <div style="font-size:12px;color:#6b7280;">Total Tasks</div>
                            <div id="total-tasks" style="font-weight:800;font-size:18px;">0</div>
                        </div>
                        <div class="summary-card" style="background:#fff;border-radius:14px;padding:10px 14px;box-shadow:0 2px 6px rgba(15,23,42,0.03);text-align:center;min-width:120px;">
                            <div style="font-size:12px;color:#6b7280;">Total Number</div>
                            <div id="total-number" style="font-weight:800;font-size:18px;">0</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<style>
/* Daily Report specific refinements */
.daily-report-card { background:#ffffff;border-radius:24px;border:1px solid rgba(15,23,42,0.04);box-shadow:0 6px 18px rgba(15,23,42,0.03); }
.pub-task-textarea { width:100%; min-height:48px; max-height:360px; resize:none; border-radius:12px; padding:10px 12px; border:1px solid #eef2ff; font-size:13px; box-sizing:border-box; }
.pub-task-textarea:focus { outline:none; box-shadow:0 0 0 4px rgba(126,34,206,0.06); }
.btn-add-row { background:#fff; border:1px solid #e6e6f0; color:#111827; }
.btn-save-report[disabled] { opacity:0.7; }
.daily-table tbody tr:hover { background: #fbfbff; }
.daily-row .btn-delete-row { color:#ef4444; border-color: rgba(239,68,68,0.06); }

@media (max-width: 720px) {
    .daily-report-card { border-radius:16px; }
    .pub-table-wrapper { overflow-x:auto; }
    .daily-table { min-width: 600px; }
}

@media (max-width: 520px) {
    /* Mobile: stack rows into cards */
    .daily-table, .daily-table thead { display:block; }
    .daily-table tbody tr { display:block; margin-bottom:12px; border:1px solid #f1f5f9; border-radius:12px; padding:10px; }
    .daily-table tbody td { display:block; width:100%; padding:6px 0 !important; }
    .daily-table tbody td:last-child { text-align:right; }
}
</style>

<script>
$(function() {
    const $dateInput = $('#daily-report-date');
    const $tbody = $('#daily-report-table tbody');
    let debounceTimer = null;

    function formatDateBadge(dateStr) {
        const d = new Date(dateStr);
        const opts = { day: '2-digit', month: 'short', year: 'numeric' };
        return d.toLocaleDateString(undefined, opts);
    }

    function getSelectedDate() {
        return $dateInput.val() || (new Date()).toISOString().slice(0,10);
    }

    function setDefaultDate() {
        const today = (new Date()).toISOString().slice(0,10);
        $dateInput.val(today);
    }

    function autoResize(el) {
        el.style.height = '48px';
        if (el.scrollHeight > 48) el.style.height = el.scrollHeight + 'px';
    }

    function recalcTotals() {
        let totalTasks = 0;
        let totalNumber = 0;
        $tbody.find('tr').each(function() {
            const task = $(this).find('.daily-task').val().trim();
            const raw = $(this).find('.daily-number').val();
            const num = parseFloat(raw);
            if (task) totalTasks++;
            if (!isNaN(num)) totalNumber += num;
        });
        $('#total-tasks').text(totalTasks);
        $('#total-number').text(totalNumber);
    }

    function addRow(task = '', number = '') {
        const idx = $tbody.find('tr').length;
        const safeTask = task ? $('<div>').text(task).html() : '';
        const safeNumber = number !== null && number !== undefined ? $('<div>').text(number).html() : '';
        const $tr = $(
            `<tr class="daily-row" data-row-index="${idx}">`+
                `<td style="padding:10px;"><textarea aria-label="Daily Task" class="pub-task-textarea daily-task" data-row-index="${idx}" placeholder="Describe the task...">${safeTask}</textarea></td>`+
                `<td style="padding:10px;text-align:center;vertical-align:middle;"><input aria-label="Number value" type="number" step="any" class="form-control daily-number" data-row-index="${idx}" value="${safeNumber}" style="max-width:120px;margin:0 auto;border-radius:10px;text-align:center;" /></td>`+
                `<td style="padding:10px;text-align:center;vertical-align:middle;"><button class="btn btn-sm btn-outline-danger btn-delete-row" title="Delete row" style="border-radius:8px;padding:6px 8px;"><i class="fas fa-times"></i></button></td>`+
            `</tr>`
        );
        $tbody.append($tr);
        $tr.find('.pub-task-textarea').each(function() { autoResize(this); });
    }

    function buildPayload() {
        const rows = [];
        $tbody.find('tr').each(function(i) {
            rows.push({
                task_text: $(this).find('.daily-task').val().trim(),
                number_value: $(this).find('.daily-number').val().trim(),
                row_order: i
            });
        });
        return {
            date: getSelectedDate(),
            rows: rows
        };
    }

    function validatePayload(payload) {
        const cleanRows = [];
        payload.rows.forEach(function(row) {
            if (!row.task_text) {
                return;
            }
            if (row.number_value !== '' && isNaN(Number(row.number_value))) {
                throw new Error('All number values must be numeric.');
            }
            cleanRows.push(row);
        });
        if (!cleanRows.length) {
            throw new Error('Please add at least one task before saving.');
        }
        return cleanRows;
    }

    function displayError(message) {
        toastr.error(message || 'Unable to save report.');
    }

    // Remove row
    $(document).on('click', '.btn-delete-row', function() {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    // Inputs: auto-resize and debounced totals
    $(document).on('input', '.daily-task', function() {
        autoResize(this);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(recalcTotals, 150);
    });

    $(document).on('input', '.daily-number', function() {
        const v = $(this).val();
        if (v !== '' && isNaN(Number(v))) {
            $(this).val('');
        }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(recalcTotals, 150);
    });

    $('#btn-add-row').on('click', function() {
        addRow();
    });

    $('#btn-load-report').on('click', function() {
        loadReport(getSelectedDate());
    });

    $('#btn-save-report').on('click', function() {
        saveReport($(this));
    });

    function saveReport($btn) {
        const payload = buildPayload();

        try {
            validatePayload(payload);
        } catch (err) {
            displayError(err.message);
            return;
        }

        $btn.prop('disabled', true);
        $('#save-label').html('<i class="fas fa-spinner fa-spin"></i> Saving');

        $.ajax({
            url: '<?= url('/api/daily-report/save') ?>',
            method: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            dataType: 'json',
            beforeSend: function(xhr) {
                // Attach CSRF token header if present in meta tag
                const token = $('meta[name="csrf-token"]').attr('content');
                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
                // Optional: attach session-based auth if needed
            },
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message || 'Daily report saved successfully.');
                    loadReport(payload.date);
                } else {
                    console.error('Save API responded with error:', res);
                    displayError(res.message || 'Unable to save report.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let msg = 'Server error while saving report.';
                try {
                    if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        msg = jqXHR.responseJSON.message;
                    } else if (jqXHR && jqXHR.responseText) {
                        // attempt to parse JSON from responseText
                        const parsed = JSON.parse(jqXHR.responseText || '{}');
                        if (parsed && parsed.message) msg = parsed.message;
                    }
                } catch (e) {
                    console.error('Error parsing error response', e, jqXHR.responseText);
                }
                console.error('AJAX save error', textStatus, errorThrown, jqXHR);
                displayError(msg);
            },
            complete: function() {
                $btn.prop('disabled', false);
                $('#save-label').text('Save Report');
            }
        });
    }

    function loadReport(date) {
        const selectedDate = date || getSelectedDate();
        $dateInput.val(selectedDate);

        $.get('<?= url('/api/daily-report/fetch') ?>', { date: selectedDate }, function(res) {
            if (res.status === 'success') {
                const data = res.data;
                $tbody.empty();
                if (data && data.rows && data.rows.length) {
                    data.rows.forEach(function(r) {
                        addRow(r.task_text, r.number_value);
                    });
                } else {
                    for (let i = 0; i < 5; i++) {
                        addRow();
                    }
                }
                recalcTotals();
            } else {
                displayError(res.message || 'Unable to load report.');
            }
        }).fail(function() {
            displayError('Server error while loading report.');
        });
    }

    setDefaultDate();
    loadReport(getSelectedDate());
});
</script>

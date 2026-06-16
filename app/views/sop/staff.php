<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="glass-card p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-neutral-900 mb-1">My SOPs</h4>
                        <p class="text-xs text-neutral-500 mb-0">Standard Operating Procedures assigned to you</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4" id="sopContainer">
            <div class="col-12 text-center py-5" id="loadingSops">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
$(document).ready(function() {
    function loadSops() {
        $.get('<?= url('/api/sops') ?>', { start: 0, length: 100 }, function(res) {
            $('#loadingSops').hide();
            const container = $('#sopContainer');
            
            if (res.status === 'success' && res.data && res.data.length > 0) {
                let html = '';
                res.data.forEach((sop, index) => {
                    const formattedDate = moment(sop.created_at).format('DD MMM, YYYY');
                    const updatedDate = moment(sop.updated_at).format('DD MMM, YYYY');
                    // Format description with line breaks
                    const descriptionHtml = sop.description.replace(/\n/g, '<br>');
                    
                    html += `
                    <div class="col-md-6 col-xl-10 w-100">
                        <div class="glass-card h-100 transition-hover hover-lift">
                            <div class="p-4 border-bottom border-light bg-neutral-50 bg-opacity-50">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold text-xs">SOP #${index + 1}</span>
                                    <span class="text-xs text-neutral-400 fw-bold"><i class="fas fa-calendar-alt me-1"></i> ${formattedDate}</span>
                                </div>
                                <h5 class="fw-bold text-neutral-800 mt-3 mb-0">Assigned by ${sop.creator_name}</h5>
                            </div>
                            <div class="p-4 flex-grow-1" style="max-height: 300px; overflow-y: auto;">
                                <div class="text-sm text-neutral-600 lh-lg font-inter">
                                    ${descriptionHtml}
                                </div>
                            </div>
                            <div class="p-3 border-top border-light text-center bg-neutral-50">
                                <span class="text-xs text-neutral-400 fw-bold">Last Updated: ${updatedDate}</span>
                            </div>
                        </div>
                    </div>
                    `;
                });
                container.html(html);
            } else {
                container.html(`
                <div class="col-12">
                    <div class="glass-card p-5 text-center">
                        <div class="bg-neutral-100 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-file-text text-neutral-400 fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-neutral-800">No SOPs Assigned</h5>
                        <p class="text-neutral-500 text-sm">You currently don't have any standard operating procedures assigned to you.</p>
                    </div>
                </div>
                `);
            }
        }).fail(function() {
            $('#loadingSops').hide();
            $('#sopContainer').html('<div class="col-12"><div class="alert alert-danger">Failed to load SOPs.</div></div>');
        });
    }

    loadSops();
});
</script>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg) !important;
}
</style>

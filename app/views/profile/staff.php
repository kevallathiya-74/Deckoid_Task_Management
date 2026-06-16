<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        <div class="row g-4">
            <!-- Profile Overview Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="glass-card text-center p-0 overflow-hidden" style="top: 100px;">
                    <div class="bg-primary-50 py-5 position-relative">
                        <!-- Abstract shape background -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25" style="background: radial-gradient(circle at top right, var(--bs-primary), transparent 50%);"></div>
                        <div class="position-relative d-inline-block">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=8b5cf6&color=fff&size=128" 
                                 class="rounded-circle border border-4 border-white shadow-sm position-relative z-1" width="120">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle shadow-sm z-2" style="width: 20px; height: 20px; transform: translate(-10px, -10px);"></span>
                        </div>
                    </div>
                    <div class="p-4 pt-4 mt-n3">
                        <h4 class="fw-bold text-neutral-900 mb-1"><?= $user['full_name'] ?></h4>
                        <p class="text-primary fw-bold text-sm mb-3">@<?= $user['username'] ?></p>
                        
                        <div class="d-flex justify-content-center mb-4">
                            <span class="badge bg-primary-50 text-primary px-3 py-2 rounded-pill fw-semibold">
                                <i class="fas fa-id-badge me-2"></i>Staff Member
                            </span>
                        </div>

                        <div class="text-start bg-neutral-50 rounded-4 p-3 border border-neutral-100">
                            <label class="text-xs text-neutral-400 text-uppercase fw-bold d-block mb-1">Email Address</label>
                            <div class="d-flex align-items-center text-neutral-800 fw-medium">
                                <i class="fas fa-envelope text-neutral-400 me-2"></i>
                                <?= $user['email'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics & Projects -->
            <div class="col-xl-8 col-lg-7">
                <!-- Floating Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="glass-card p-4 d-flex align-items-center">
                            <div class="p-3 rounded-4 bg-success-soft me-4">
                                <i class="fas fa-check-double text-success fs-4"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold text-neutral-900 mb-0"><?= $staffStats['completed_tasks'] ?></h3>
                                <p class="text-neutral-400 text-xs fw-bold text-uppercase mb-0">Tasks Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 d-flex align-items-center">
                            <div class="p-3 rounded-4 bg-primary-50 me-4">
                                <i class="fas fa-folder-open text-primary fs-4"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold text-neutral-900 mb-0"><?= count($staffStats['active_projects']) ?></h3>
                                <p class="text-neutral-400 text-xs fw-bold text-uppercase mb-0">Active Projects</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Projects Grid -->
                <div class="glass-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-neutral-900 mb-0">Ongoing Projects</h5>
                        <a href="<?= url('/projects') ?>" class="text-xs text-primary fw-bold text-decoration-none">View All</a>
                    </div>
                    <div class="row g-3">
                        <?php if (empty($staffStats['active_projects'])): ?>
                            <div class="col-12 text-center py-4">
                                <p class="text-neutral-400 text-sm mb-0">No active project assignments.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($staffStats['active_projects'] as $p): ?>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 border border-light bg-neutral-50 hover-shadow transition-all">
                                        <div class="fw-bold text-neutral-900 text-sm mb-1"><?= $p['project_name'] ?></div>
                                        <div class="text-xs text-neutral-400 mb-2"><?= $p['client_name'] ?></div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-soft-warning text-xs px-2 py-1"><?= $p['status'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="glass-card p-4 mb-4">
                    <h5 class="fw-bold text-neutral-900 mb-4">Recent Activity</h5>
                    <div class="timeline-ui">
                        <?php if (empty($staffStats['recent_activity'])): ?>
                            <p class="text-neutral-400 text-sm mb-0">No recent task updates.</p>
                        <?php else: ?>
                            <?php foreach ($staffStats['recent_activity'] as $activity): ?>
                                <div class="timeline-item d-flex pb-4">
                                    <div class="timeline-marker position-relative pe-4">
                                        <div class="bg-primary rounded-circle shadow-primary" style="width: 12px; height: 12px;"></div>
                                        <div class="timeline-line bg-neutral-100 position-absolute start-50 top-0 h-100" style="width: 2px; margin-left: -1px; z-index: -1;"></div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="text-xs text-neutral-400 fw-bold text-uppercase mb-1">
                                            <?= date('d M, Y \a\t H:i', strtotime($activity['updated_at'])) ?>
                                        </div>
                                        <p class="text-sm text-neutral-800 mb-0">
                                            Updated task <span class="fw-bold text-primary">"<?= $activity['title'] ?>"</span> 
                                            in <span class="fw-semibold"><?= $activity['project_name'] ?></span>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="glass-card border-0 mt-4">
                    <div class="p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-neutral-100">
                            <div class="bg-primary-50 p-2 rounded-3 me-3 text-primary">
                                <i class="fas fa-user-shield fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-neutral-900 mb-0">Account Security</h5>
                        </div>
                        <form id="staffSecurityForm">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-neutral-50 border-neutral-200 text-neutral-400 border-end-0 pe-0">
                                            <i class="fas fa-at"></i>
                                        </span>
                                        <input type="text" class="form-control bg-neutral-50 border-neutral-200 border-start-0 py-2 ps-2" name="username" value="<?= $user['username'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-neutral-200 text-neutral-400">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control border-neutral-200 py-2" name="new_password" placeholder="At least 8 characters">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-neutral-200 text-neutral-400">
                                            <i class="fas fa-check-double"></i>
                                        </span>
                                        <input type="password" class="form-control border-neutral-200 py-2" name="confirm_password" placeholder="Confirm your password">
                                    </div>
                                </div>
                                <div class="col-12 text-end pt-4 mt-4 border-top border-neutral-100">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-primary rounded-pill">
                                        <i class="fas fa-lock me-2"></i>Secure Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    $('#staffSecurityForm').on('submit', function(e) {
        e.preventDefault();
        const pass = $('[name="new_password"]').val();
        const confirm = $('[name="confirm_password"]').val();

        if (pass && pass.length < 8) {
            toastr.error('Password must be at least 8 characters');
            return;
        }

        if (pass !== confirm) {
            toastr.error('Passwords do not match');
            return;
        }

        $.post('<?= url('/api/profile/update') ?>', $(this).serialize(), function(res) {
            if (res.status === 'success' || res.success) {
                toastr.success(res.message);
                $('#staffSecurityForm')[0].reset();
            } else {
                toastr.error(res.message);
            }
        }, 'json').fail(function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to update profile');
        });
    });
});
</script>

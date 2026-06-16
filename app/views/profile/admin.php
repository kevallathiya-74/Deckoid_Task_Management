<?php require_once ROOT_PATH . '/app/views/layouts/topbar.php'; ?>

<main class="main-content">
    <div class="container-fluid animate-fade-up">
        <div class="row g-4">
            <!-- Profile Overview Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="glass-card text-center p-0  overflow-hidden" style="top: 100px;">
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
                                <i class="fas fa-shield-alt me-2"></i>System Administrator
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

            <!-- Admin Analytics & Security -->
            <div class="col-xl-8 col-lg-7">
                <!-- Analytics Section -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="glass-card p-4 hover-translate-y h-100 position-relative overflow-hidden">
                           
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-primary-50 rounded-3 p-2 text-primary">
                                    <i class="fas fa-layer-group fs-5"></i>
                                </div>
                                <h6 class="mb-0 text-xs fw-bold text-neutral-500 text-uppercase">Total Projects</h6>
                            </div>
                            <h2 class="mb-0 fw-bold text-neutral-900" style="font-size: 2.5rem;"><?= $stats['total_projects'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4 hover-translate-y h-100 position-relative overflow-hidden">
                            
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-success-soft rounded-3 p-2 text-success">
                                    <i class="fas fa-tasks fs-5"></i>
                                </div>
                                <h6 class="mb-0 text-xs fw-bold text-neutral-500 text-uppercase">Active Tasks</h6>
                            </div>
                            <h2 class="mb-0 fw-bold text-neutral-900" style="font-size: 2.5rem;"><?= $stats['total_tasks'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4 hover-translate-y h-100 position-relative overflow-hidden">
                            
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-info-soft rounded-3 p-2 text-info">
                                    <i class="fas fa-users fs-5"></i>
                                </div>
                                <h6 class="mb-0 text-xs fw-bold text-neutral-500 text-uppercase">Team Members</h6>
                            </div>
                            <h2 class="mb-0 fw-bold text-neutral-900" style="font-size: 2.5rem;"><?= $stats['total_staff'] ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="glass-card border-0">
                    <div class="p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-neutral-100">
                            <div class="bg-primary-50 p-2 rounded-3 me-3 text-primary">
                                <i class="fas fa-user-shield fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-neutral-900 mb-0">Security & Settings</h5>
                        </div>
                        
                        <form id="securityForm">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-neutral-200 text-neutral-400 border-end-0 pe-0">
                                            <i class="fas fa-at"></i>
                                        </span>
                                        <input type="text" class="form-control bg-white border-neutral-200 border-start-0 py-2 ps-2" name="username" value="<?= $user['username'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">Role Access</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-neutral-50 border-neutral-200 text-neutral-400 border-end-0 pe-0">
                                            <i class="fas fa-id-badge"></i>
                                        </span>
                                        <input type="text" class="form-control bg-neutral-50 border-neutral-200 border-start-0 py-2 ps-2 text-neutral-600 fw-bold" value="Administrator" readonly disabled>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-neutral-100">
                                        <div class="bg-danger-soft p-2 rounded-3 me-3 text-danger">
                                            <i class="fas fa-lock fs-5"></i>
                                        </div>
                                        <h5 class="fw-bold text-neutral-900 mb-0">Change Password</h5>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-neutral-200 text-neutral-400">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control border-neutral-200 py-2" name="new_password" placeholder="Min. 8 characters">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-bold text-neutral-400 text-uppercase mb-2">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-neutral-200 text-neutral-400">
                                            <i class="fas fa-check-double"></i>
                                        </span>
                                        <input type="password" class="form-control border-neutral-200 py-2" name="confirm_password" placeholder="Confirm new password">
                                    </div>
                                </div>
                                <div class="col-12 text-end pt-4 mt-4 border-top border-neutral-100">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-primary rounded-pill">
                                        <i class="fas fa-save me-2"></i>Save Changes
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
    $('#securityForm').on('submit', function(e) {
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
                $('#securityForm')[0].reset();
            } else {
                toastr.error(res.message);
            }
        }, 'json').fail(function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to update profile');
        });
    });
});
</script>

<?php require_once VIEWS_PATH . '/components/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">My Account</h1>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!$userData): ?>
        <div class="alert alert-danger">
            <p>Unable to retrieve your account information. Please try again later or contact support.</p>
        </div>
    <?php else: ?>
    <div class="row">
        <!-- Account Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <div class="mt-3">
                            <h4><?= Helpers::escape($userData['username'] ?? 'User') ?></h4>
                            <p class="text-muted font-size-sm"><?= Helpers::escape($userData['email'] ?? '') ?></p>
                            <p class="text-muted">Member since: <?= isset($userData['created_at']) ? date('F j, Y', strtotime($userData['created_at'])) : 'Unknown' ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="list-group mt-4">
                <a href="#" class="list-group-item list-group-item-action active" data-bs-toggle="list" data-bs-target="#account-settings">
                    <i class="bi bi-gear"></i> Account Settings
                </a>
                <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="list" data-bs-target="#personal-info">
                    <i class="bi bi-person-lines-fill"></i> Personal Information
                </a>
                <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="list" data-bs-target="#password-change">
                    <i class="bi bi-key"></i> Change Password
                </a>
                <a href="<?= APP_URL ?>/user/logout.php" class="list-group-item list-group-item-action text-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Account Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Account Settings -->
                        <div class="tab-pane fade show active" id="account-settings">
                            <h3 class="mb-4">Account Settings</h3>
                            <form method="post" action="<?= APP_URL ?>/account.php">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= Helpers::escape($userData['username'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= Helpers::escape($userData['email'] ?? '') ?>" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                        
                        <!-- Personal Information -->
                        <div class="tab-pane fade" id="personal-info">
                            <h3 class="mb-4">Personal Information</h3>
                            <form method="post" action="<?= APP_URL ?>/account.php">
                                <div class="mb-3">
                                    <label for="description" class="form-label">About Me</label>
                                    <textarea class="form-control" id="description" name="description" rows="6" 
                                        placeholder="Share a bit about yourself, including any relevant health information, allergies, preferences, or special instructions for your orders."
                                    ><?= Helpers::escape($userData['description'] ?? '') ?></textarea>
                                    <div class="form-text">This information is optional and will help us serve you better.</div>
                                </div>
                                <button type="submit" name="update_description" class="btn btn-primary">Save Information</button>
                            </form>
                        </div>
                        
                        <!-- Change Password -->
                        <div class="tab-pane fade" id="password-change">
                            <h3 class="mb-4">Change Password</h3>
                            <form method="post" action="<?= APP_URL ?>/account.php">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div class="form-text">Password must be at least 6 characters long.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/components/footer.php'; ?>

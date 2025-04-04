<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? Helpers::escape($page_title) . ' - ' : '' ?><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= APP_URL ?>/index.php"><?= APP_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= isset($current_page) && $current_page === 'home' ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isset($current_page) && $current_page === 'products' ? 'active' : '' ?>" href="<?= APP_URL ?>/products.php">Products</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if (method_exists('Helpers', 'isLoggedIn') && Helpers::isLoggedIn()): ?>
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> <?= Helpers::escape(Helpers::getCurrentUsername()) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <li><a class="dropdown-item <?= isset($current_page) && $current_page === 'account' ? 'active' : '' ?>" href="<?= APP_URL ?>/account.php">
                                    <i class="bi bi-person"></i> My Account
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/user/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-light me-2" id="loginButton">
                            <i class="bi bi-person"></i> Login
                        </button>
                    <?php endif; ?>
                    <a href="<?= APP_URL ?>/cart.php" class="btn btn-outline-light <?= isset($current_page) && $current_page === 'cart' ? 'active' : '' ?>">
                        <i class="bi bi-cart"></i> Cart
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="badge bg-danger"><?= array_sum($_SESSION['cart']) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login/Register Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login or Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false">Register</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-3" id="authTabsContent">
                        <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
                            <form id="loginForm" action="<?= APP_URL ?>/user/login.php" method="post">
                                <div class="mb-3">
                                    <label for="loginIdentifier" class="form-label">Username or Email</label>
                                    <input type="text" class="form-control" id="loginIdentifier" name="identifier" required>
                                </div>
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                        <label class="form-check-label" for="rememberMe">Remember me</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                                <div id="loginMessage" class="mt-3"></div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
                            <form id="registerForm" action="<?= APP_URL ?>/user/register.php" method="post">
                                <div class="mb-3">
                                    <label for="registerUsername" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="registerUsername" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerEmail" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="registerEmail" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="registerPassword" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                                <div id="registerMessage" class="mt-3"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

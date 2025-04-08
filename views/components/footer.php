</main>
    <!-- Footer -->
    <footer class="text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mb-4 mb-md-0">
                    <h5 class="mb-3 fw-bold"><?= APP_NAME ?></h5>
                    <p class="mb-3 text-light opacity-75">Your trusted pharmacy for health and wellness products.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-3 col-lg-4 mb-4 mb-md-0">
                    <h6 class="mb-3 text-light opacity-75 text-uppercase fw-bold">Shop</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= APP_URL ?>/products.php" class="text-white text-decoration-none">All Products</a></li>
                        <li class="mb-2"><a href="<?= APP_URL ?>/products.php?category=Supplements" class="text-white text-decoration-none">Supplements</a></li>
                        <li class="mb-2"><a href="<?= APP_URL ?>/products.php?category=Skincare" class="text-white text-decoration-none">Skincare</a></li>
                        <li class="mb-2"><a href="<?= APP_URL ?>/products.php?category=Medical+Supplies" class="text-white text-decoration-none">Medical Supplies</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-lg-3 mb-4 mb-md-0">
                    <h6 class="mb-3 text-light opacity-75 text-uppercase fw-bold">Account</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= APP_URL ?>/account.php" class="text-white text-decoration-none">My Account</a></li>
                        <li class="mb-2"><a href="<?= APP_URL ?>/cart.php" class="text-white text-decoration-none">My Cart</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="mb-2 mb-md-0 text-light opacity-75">&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
                <div>
                    <a href="#" class="text-white text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-white text-decoration-none me-3">Terms of Service</a>
                    <a href="#" class="text-white text-decoration-none">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>

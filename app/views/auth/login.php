<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="form-container">
    <div class="container form-card">
        <div class="card">
            <div class="card-header text-center">
                <h2 class="card-title">
                    <i class="fas fa-sign-in-alt"></i> Login
                </h2>
                <p>Sign in to your JanataConnect account</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo Config::APP_URL; ?>/login">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <p>Don't have an account? 
                    <a href="<?php echo Config::APP_URL; ?>/register" style="color: var(--primary-color);">
                        Register here
                    </a>
                </p>
            </div>

            <div class="mt-4" style="border-top: 1px solid #eee; padding-top: 1rem;">
                <h4 style="font-size: 1rem; margin-bottom: 1rem; color: var(--dark-gray);">Demo Accounts:</h4>
                <div style="font-size: 0.9rem; color: var(--dark-gray);">
                    <p><strong>Admin:</strong> admin@janataconnect.com / admin123</p>
                    <p><strong>Official:</strong> official@janataconnect.com / official123</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

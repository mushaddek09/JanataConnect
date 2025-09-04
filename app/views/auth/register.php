<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="form-container">
    <div class="container form-card">
        <div class="card">
            <div class="card-header text-center">
                <h2 class="card-title">
                    <i class="fas fa-user-plus"></i> Register
                </h2>
                <p>Create your JanataConnect account</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1rem;">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo $err; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo Config::APP_URL; ?>/register">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

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
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <p>Already have an account? 
                    <a href="<?php echo Config::APP_URL; ?>/login" style="color: var(--primary-color);">
                        Login here
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

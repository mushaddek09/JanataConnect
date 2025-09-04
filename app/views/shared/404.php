<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="error-page">
    <div class="container">
        <div class="card">
            <h1>404</h1>
            <h2>Page Not Found</h2>
            <p>The page you are looking for doesn't exist.</p>
            <a href="<?php echo Config::APP_URL; ?>/" class="btn btn-primary">
                <i class="fas fa-home"></i> Go Home
            </a>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>



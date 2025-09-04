<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <section class="hero">
        <div class="container">
            <h1>Welcome to JanataConnect</h1>
            <p>Digital Bridge for Citizen-Government Engagement in Bangladesh</p>
            <?php if (!Config::isLoggedIn()): ?>
                <a href="<?php echo Config::APP_URL; ?>/register" class="btn btn-secondary btn-lg">
                    <i class="fas fa-user-plus"></i> Get Started
                </a>
            <?php else: ?>
                <a href="<?php echo Config::APP_URL; ?>/dashboard" class="btn btn-secondary btn-lg">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2>How It Works</h2>
            
            <div class="row">
                <div class="col-4">
                    <div class="card text-center">
                        <i class="fas fa-user-plus feature-icon"></i>
                        <h3>1. Register</h3>
                        <p>Create your account as a citizen to start engaging with government services.</p>
                    </div>
                </div>
                
                <div class="col-4">
                    <div class="card text-center">
                        <i class="fas fa-edit feature-icon"></i>
                        <h3>2. Submit</h3>
                        <p>Submit your suggestions, report issues, or provide feedback to relevant departments.</p>
                    </div>
                </div>
                
                <div class="col-4">
                    <div class="card text-center">
                        <i class="fas fa-check-circle feature-icon"></i>
                        <h3>3. Track</h3>
                        <p>Monitor the progress of your submissions and receive updates from government officials.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="departments">
        <div class="container">
            <h2>Government Departments</h2>
            
            <div class="row">
                <?php foreach (array_slice($departments, 0, 6) as $key => $department): ?>
                    <div class="col-4">
                        <div class="card">
                            <h4><?php echo $department; ?></h4>
                            <p>Submit your suggestions and reports to this department for better service delivery.</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?php echo Config::APP_URL; ?>/register" class="btn btn-primary">
                    View All Departments
                </a>
            </div>
        </div>
    </section>

    <?php if (!empty($recent_submissions)): ?>
    <section class="recent-submissions">
        <div class="container">
            <h2>Recent Submissions</h2>
            
            <div class="row">
                <?php foreach ($recent_submissions as $submission): ?>
                    <div class="col-6">
                        <div class="card">
                            <h4><?php echo htmlspecialchars($submission['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($submission['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-between align-center">
                                <span class="badge badge-<?php echo str_replace('_', '-', $submission['status']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                </span>
                                <small style="color: var(--dark-gray);">
                                    <?php echo date('M d, Y', strtotime($submission['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="cta">
        <div class="container">
            <h2>Ready to Make a Difference?</h2>
            <p>Join thousands of citizens who are actively participating in Bangladesh's digital governance.</p>
            <?php if (!Config::isLoggedIn()): ?>
                <a href="<?php echo Config::APP_URL; ?>/register" class="btn btn-secondary btn-lg">
                    <i class="fas fa-rocket"></i> Start Your Journey
                </a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

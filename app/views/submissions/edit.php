<?php include APP_PATH . '/views/shared/header.php'; ?>

<main class="form-container">
    <div class="container form-card">
        <div class="card">
            <div class="card-header text-center">
                <h2 class="card-title">
                    <i class="fas fa-edit"></i> Edit Submission
                </h2>
                <p>Update your submission details</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo Config::APP_URL; ?>/submission/update/<?php echo $submission['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="title" class="form-label">
                        <i class="fas fa-heading"></i> Suggestion Title *
                    </label>
                    <input type="text" id="title" name="title" class="form-control" required 
                           value="<?php echo htmlspecialchars($submission['title']); ?>"
                           placeholder="Brief title for your suggestion">
                </div>

                <div class="form-group">
                    <label for="department_id" class="form-label">
                        <i class="fas fa-building"></i> Government Department *
                    </label>
                    <select id="department_id" name="department_id" class="form-control" required>
                        <option value="">Select a department...</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" 
                                    <?php echo $submission['department_id'] == $dept['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Location
                    </label>
                    <input type="text" id="location" name="location" class="form-control" 
                           value="<?php echo htmlspecialchars($submission['location']); ?>"
                           placeholder="City, District, or specific location">
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left"></i> Detailed Description *
                    </label>
                    <textarea id="description" name="description" class="form-control" rows="6" required 
                              placeholder="Provide detailed information about your suggestion..."><?php echo htmlspecialchars($submission['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Update Submission
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="/JanataConnect/my-submissions" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to My Submissions
                </a>
            </div>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>


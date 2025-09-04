<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <div class="d-flex justify-between align-center">
                <div>
                    <h1>My Submissions</h1>
                    <p>Track the progress of your suggestions and reports.</p>
                </div>
                <a href="/JanataConnect/submit-suggestion" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Suggestion
                </a>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($submissions)): ?>
            <div class="row">
                <?php foreach ($submissions as $submission): ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="d-flex justify-between align-center">
                                <div>
                                    <h3><?php echo htmlspecialchars($submission['title']); ?></h3>
                                    <p class="text-muted">Department: <?php echo htmlspecialchars($submission['department_name']); ?></p>
                                    <p><?php echo htmlspecialchars(substr($submission['description'], 0, 150)) . '...'; ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-<?php echo str_replace('_', '-', $submission['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                    </span>
                                    <p class="text-muted mt-2"><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></p>
                                </div>
                            </div>
                            
                            <?php if ($submission['location']): ?>
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($submission['location']); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($submission['official_comment']): ?>
                                <div class="mt-3 p-3" style="background: #f8f9fa; border-left: 4px solid var(--primary-color);">
                                    <h5>Official Response:</h5>
                                    <p><?php echo htmlspecialchars($submission['official_comment']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <a href="/JanataConnect/submission/edit/<?php echo $submission['id']; ?>" 
                                   class="btn btn-sm btn-outline">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="deleteSubmission(<?php echo $submission['id']; ?>)" 
                                        class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card text-center">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>No Submissions Yet</h3>
                <p>You haven't submitted any suggestions yet. Start by submitting your first suggestion!</p>
                <a href="/JanataConnect/submit-suggestion" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Submit Your First Suggestion
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function deleteSubmission(id) {
    if (confirm('Are you sure you want to delete this submission?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/JanataConnect/submission/delete/' + id;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?php echo Config::generateCSRFToken(); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

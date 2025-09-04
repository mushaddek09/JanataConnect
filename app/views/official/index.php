<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>Official Dashboard</h1>
            <p>Review and manage submissions for your department.</p>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Recent Submissions for Your Department</h3>
                    </div>

                    <?php if (!empty($department_submissions)): ?>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Citizen</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($department_submissions as $submission): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($submission['title']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($submission['description'], 0, 100)) . '...'; ?></small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($submission['user_name']); ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($submission['user_email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($submission['location'] ?? 'Not specified'); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo str_replace('_', '-', $submission['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $submission['priority'] === 'high' ? 'danger' : ($submission['priority'] === 'medium' ? 'warning' : 'info'); ?>">
                                                    <?php echo ucfirst($submission['priority']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></td>
                                            <td>
                                                <a href="<?php echo Config::APP_URL; ?>/official/submissions" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <h4>No Submissions Yet</h4>
                            <p>No submissions have been made to your department yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($department_submissions); ?></div>
                    <div class="stat-label">Total Submissions</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo count(array_filter($department_submissions, function($s) { return $s['status'] === 'pending'; })); ?>
                    </div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo count(array_filter($department_submissions, function($s) { return $s['status'] === 'approved'; })); ?>
                    </div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo count(array_filter($department_submissions, function($s) { return $s['status'] === 'completed'; })); ?>
                    </div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

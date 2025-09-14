<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p>Role: <?php echo ucfirst($_SESSION['user_role']); ?></p>
        </div>

        <?php if ($_SESSION['user_role'] === 'citizen'): ?>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_submissions ?? 0; ?></div>
                    <div class="stat-label">Total Submissions</div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Submissions</h3>
                        </div>
                        <?php if (!empty($user_submissions)): ?>
                            <?php foreach ($user_submissions as $submission): ?>
                                <div style="border-bottom: 1px solid #eee; padding: 1rem 0;">
                                    <h4><?php echo htmlspecialchars($submission['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($submission['description'], 0, 100)) . '...'; ?></p>
                                    <span class="badge bg-<?php echo str_replace('_', '-', $submission['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No submissions yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($_SESSION['user_role'] === 'official'): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Department Submissions</h3>
                        </div>
                        <?php if (!empty($department_submissions)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Citizen</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($department_submissions as $submission): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['title']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['user_name']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo str_replace('_', '-', $submission['status']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No submissions for your department yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $statistics['total'] ?? 0; ?></div>
                    <div class="stat-label">Total Submissions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $statistics['pending'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $statistics['approved'] ?? 0; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $statistics['completed'] ?? 0; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_users ?? 0; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_departments ?? 0; ?></div>
                    <div class="stat-label">Departments</div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Submissions</h3>
                        </div>
                        <?php if (!empty($recent_submissions)): ?>
                            <?php foreach ($recent_submissions as $submission): ?>
                                <div style="border-bottom: 1px solid #eee; padding: 1rem 0;">
                                    <h4><?php echo htmlspecialchars($submission['title']); ?></h4>
                                    <p>By: <?php echo htmlspecialchars($submission['user_name']); ?></p>
                                    <span class="badge bg-<?php echo str_replace('_', '-', $submission['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No submissions yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>Admin Dashboard</h1>
            <p>Manage the JanataConnect platform and oversee all operations.</p>
        </div>

        <!-- Statistics Cards -->
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

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-4">
                <div class="card">
                    <h3>Quick Actions</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="<?php echo Config::APP_URL; ?>/admin/users" class="btn btn-primary">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a href="<?php echo Config::APP_URL; ?>/admin/departments" class="btn btn-primary">
                            <i class="fas fa-building"></i> Manage Departments
                        </a>
                        <a href="<?php echo Config::APP_URL; ?>/admin/submissions" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i> View All Submissions
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-8">
                <div class="card">
                    <h3>Recent Submissions</h3>
                    <?php if (!empty($recent_submissions)): ?>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_submissions as $submission): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($submission['title']); ?></td>
                                            <td><?php echo htmlspecialchars($submission['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($submission['department_name']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo str_replace('_', '-', $submission['status']); ?>">
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
                        <p>No submissions yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <h3>System Status</h3>
                    <div class="row">
                        <div class="col-3">
                            <div class="text-center">
                                <i class="fas fa-database" style="font-size: 2rem; color: var(--success-color);"></i>
                                <p>Database: <strong>Online</strong></p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <i class="fas fa-server" style="font-size: 2rem; color: var(--success-color);"></i>
                                <p>Server: <strong>Online</strong></p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <i class="fas fa-users" style="font-size: 2rem; color: var(--info-color);"></i>
                                <p>Active Users: <strong><?php echo $total_users ?? 0; ?></strong></p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <i class="fas fa-chart-line" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <p>Platform: <strong>Active</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

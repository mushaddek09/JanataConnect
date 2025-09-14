<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>All Submissions</h1>
            <p>View and manage all submissions in the system.</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Submissions</h3>
            </div>

            <?php if (!empty($submissions)): ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>User</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><?php echo $submission['id']; ?></td>
                                    <td><?php echo htmlspecialchars($submission['title']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['department_name']); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch($submission['status']) {
                                            case 'pending':
                                                $statusClass = 'bg-warning';
                                                break;
                                            case 'under_review':
                                                $statusClass = 'bg-info';
                                                break;
                                            case 'approved':
                                                $statusClass = 'bg-success';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'bg-danger';
                                                break;
                                            case 'completed':
                                                $statusClass = 'bg-primary';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $submission['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $submission['priority'] === 'high' ? 'danger' : ($submission['priority'] === 'medium' ? 'warning' : 'info'); ?>">
                                            <?php echo ucfirst($submission['priority']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo Config::APP_URL; ?>/submissions/<?php echo $submission['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No submissions found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

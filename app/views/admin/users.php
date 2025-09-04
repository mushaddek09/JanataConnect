<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>User Management</h1>
            <p>Manage all users in the system.</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Users</h3>
            </div>

            <?php if (!empty($users)): ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'official' ? 'warning' : 'info'); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['department_id']): ?>
                                            Department <?php echo $user['department_id']; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" 
                                                onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 'false' : 'true'; ?>)">
                                            <i class="fas fa-<?php echo $user['is_active'] ? 'ban' : 'check'; ?>"></i>
                                            <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
function toggleUserStatus(userId, newStatus) {
    const action = newStatus === 'true' ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo Config::APP_URL; ?>/admin/user/toggle/' + userId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'is_active';
        statusInput.value = newStatus;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?php echo Config::generateCSRFToken(); ?>';
        
        form.appendChild(statusInput);
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

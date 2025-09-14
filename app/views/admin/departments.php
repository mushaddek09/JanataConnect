<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>Department Management</h1>
            <p>Manage government departments in the system.</p>
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

        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Existing Departments</h3>
                    </div>

                    <?php if (!empty($departments)): ?>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Submissions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $dept): ?>
                                        <tr>
                                            <td><?php echo $dept['id']; ?></td>
                                            <td><?php echo htmlspecialchars($dept['name']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($dept['description'] ?? '', 0, 50)) . '...'; ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $dept['total_submissions'] ?? 0; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $dept['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $dept['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="deleteDepartment(<?php echo $dept['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No departments found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-4">
                <div class="card">
                    <h3>Add New Department</h3>
                    <form method="POST" action="<?php echo Config::APP_URL; ?>/admin/departments">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Department Name *</label>
                            <input type="text" id="name" name="name" class="form-control" required 
                                   placeholder="Enter department name">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3" 
                                      placeholder="Enter department description"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Department
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h3>Department Statistics</h3>
                    <div class="text-center">
                        <div class="stat-number"><?php echo count($departments); ?></div>
                        <div class="stat-label">Total Departments</div>
                    </div>
                    <div class="mt-3">
                        <?php
                        $activeCount = array_filter($departments, function($dept) { return $dept['is_active']; });
                        ?>
                        <p><strong>Active:</strong> <?php echo count($activeCount); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function deleteDepartment(deptId) {
    if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo Config::APP_URL; ?>/admin/department/delete/' + deptId;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?php echo $csrf_token; ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include APP_PATH . '/views/shared/footer.php'; ?>
<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>Review Submissions</h1>
            <p>Review and update the status of submissions from all departments.</p>
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
                            <div class="card-header">
                                <div class="d-flex justify-between align-center">
                                    <div>
                                        <h3><?php echo htmlspecialchars($submission['title']); ?></h3>
                                        <p class="text-muted">
                                            <strong>Department:</strong> <?php echo htmlspecialchars($submission['department_name'] ?? 'Unknown'); ?> | 
                                            <strong>Submitted by:</strong> <?php echo htmlspecialchars($submission['user_name']); ?> (<?php echo htmlspecialchars($submission['user_email']); ?>)
                                        </p>
                                        <?php if (isset($submission['priority'])): ?>
                                            <p class="text-muted">
                                                <strong>Priority:</strong> 
                                                <span class="badge bg-<?php echo $submission['priority'] === 'high' ? 'danger' : ($submission['priority'] === 'medium' ? 'warning' : 'info'); ?>">
                                                    <?php echo ucfirst($submission['priority']); ?>
                                                </span>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
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
                                        <p class="text-muted mt-2"><?php echo date('M d, Y H:i', strtotime($submission['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <h5>Description:</h5>
                                        <p><?php echo nl2br(htmlspecialchars($submission['description'])); ?></p>
                                        
                                        <?php if ($submission['location']): ?>
                                            <h5>Location:</h5>
                                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($submission['location']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($submission['official_comment']): ?>
                                            <h5>Previous Official Comment:</h5>
                                            <div class="p-3" style="background: #f8f9fa; border-left: 4px solid var(--primary-color);">
                                                <p><?php echo nl2br(htmlspecialchars($submission['official_comment'])); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-4">
                                        <div class="card">
                                            <h5>Update Status</h5>
                                            <form method="POST" action="<?php echo Config::APP_URL; ?>/official/submission/update/<?php echo $submission['id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                
                                                <div class="form-group">
                                                    <label for="status_<?php echo $submission['id']; ?>" class="form-label">Status</label>
                                                    <select id="status_<?php echo $submission['id']; ?>" name="status" class="form-control" required>
                                                        <option value="pending" <?php echo $submission['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="under_review" <?php echo $submission['status'] === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                                        <option value="approved" <?php echo $submission['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="rejected" <?php echo $submission['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                        <option value="completed" <?php echo $submission['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="comment_<?php echo $submission['id']; ?>" class="form-label">Official Comment</label>
                                                    <textarea id="comment_<?php echo $submission['id']; ?>" name="comment" class="form-control" rows="4" 
                                                              placeholder="Add your official response or comment..."><?php echo htmlspecialchars($submission['official_comment'] ?? ''); ?></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary w-100">
                                                        <i class="fas fa-save"></i> Update Status
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card text-center">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h3>No Submissions to Review</h3>
                <p>There are no submissions from any department at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

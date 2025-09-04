<?php include APP_PATH . '/views/shared/header.php'; ?>

<main>
    <div class="container">
        <div class="mt-4">
            <h1>Submit Your Suggestion</h1>
            <p>Help improve your community by submitting suggestions to government departments.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-8">
                <div class="card">
                    <form method="POST" action="/JanataConnect/submit-suggestion" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading"></i> Suggestion Title *
                            </label>
                            <input type="text" id="title" name="title" class="form-control" required 
                                   placeholder="Brief title for your suggestion">
                        </div>

                        <div class="form-group">
                            <label for="department_id" class="form-label">
                                <i class="fas fa-building"></i> Government Department *
                            </label>
                            <select id="department_id" name="department_id" class="form-control" required>
                                <option value="">Select a department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>">
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
                                   placeholder="Enter location (e.g., Dhaka, Chittagong)">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                <i class="fas fa-edit"></i> Detailed Description *
                            </label>
                            <textarea id="description" name="description" class="form-control" rows="6" required 
                                      placeholder="Describe your suggestion in detail. Include specific issues, proposed solutions, and expected benefits."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="images" class="form-label">
                                <i class="fas fa-images"></i> Supporting Images (Optional)
                            </label>
                            <div class="file-upload-container">
                                <input type="file" id="images" name="images[]" class="file-input" 
                                       accept="image/*" multiple>
                                <label for="images" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span class="file-upload-text">Choose Images or Drag & Drop</span>
                                    <small class="file-upload-hint">JPG, PNG, GIF up to 2MB each</small>
                                </label>
                                <div class="file-preview-container" id="filePreview"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Submit Suggestion
                            </button>
                            <a href="/JanataConnect/my-submissions" class="btn btn-outline">
                                <i class="fas fa-list"></i> View My Submissions
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-4">
                <div class="card">
                    <h3>Submission Guidelines</h3>
                    <ul>
                        <li>Be specific and detailed in your description</li>
                        <li>Include relevant location information</li>
                        <li>Focus on community benefits</li>
                        <li>Be respectful and constructive</li>
                        <li>Upload supporting images (JPG, PNG, GIF up to 2MB each)</li>
                        <li>Maximum 5 images per submission</li>
                    </ul>
                </div>

                <div class="card">
                    <h3>What Happens Next?</h3>
                    <ol>
                        <li>Your suggestion is submitted for review</li>
                        <li>Government officials will assess it</li>
                        <li>You'll receive status updates</li>
                        <li>Final decision will be communicated</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include APP_PATH . '/views/shared/footer.php'; ?>

<script src="<?php echo Config::APP_URL; ?>/public/js/file-upload.js"></script>

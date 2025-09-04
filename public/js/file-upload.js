// File Upload Functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('images');
    const filePreview = document.getElementById('filePreview');
    const uploadLabel = document.querySelector('.file-upload-label');
    const maxFiles = 5;
    const maxFileSize = 2 * 1024 * 1024; // 2MB (PHP limit)
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

    let selectedFiles = [];

    // File input change handler
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop handlers
    uploadLabel.addEventListener('dragover', handleDragOver);
    uploadLabel.addEventListener('dragleave', handleDragLeave);
    uploadLabel.addEventListener('drop', handleDrop);

    function handleFileSelect(event) {
        const files = Array.from(event.target.files);
        processFiles(files);
    }

    function handleDragOver(event) {
        event.preventDefault();
        uploadLabel.classList.add('dragover');
    }

    function handleDragLeave(event) {
        event.preventDefault();
        uploadLabel.classList.remove('dragover');
    }

    function handleDrop(event) {
        event.preventDefault();
        uploadLabel.classList.remove('dragover');
        const files = Array.from(event.dataTransfer.files);
        processFiles(files);
    }

    function processFiles(files) {
        // Check file count limit
        if (selectedFiles.length + files.length > maxFiles) {
            alert(`You can only upload up to ${maxFiles} images.`);
            return;
        }

        files.forEach(file => {
            // Validate file type
            if (!allowedTypes.includes(file.type)) {
                alert(`${file.name} is not a valid image file. Please upload JPG, PNG, or GIF files.`);
                return;
            }

            // Validate file size
            if (file.size > maxFileSize) {
                alert(`${file.name} is too large. Please upload files smaller than 5MB.`);
                return;
            }

            // Add to selected files
            selectedFiles.push(file);
            displayFilePreview(file);
        });

        // Update file input
        updateFileInput();
    }

    function displayFilePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="${file.name}" class="file-preview-image">
                <div class="file-preview-info">
                    <div>${file.name}</div>
                    <div>${formatFileSize(file.size)}</div>
                </div>
                <button type="button" class="file-preview-remove" onclick="removeFile('${file.name}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            filePreview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    }

    function removeFile(fileName) {
        // Remove from selected files array
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);
        
        // Remove from preview
        const previewItems = filePreview.querySelectorAll('.file-preview-item');
        previewItems.forEach(item => {
            const img = item.querySelector('img');
            if (img && img.alt === fileName) {
                item.remove();
            }
        });

        // Update file input
        updateFileInput();
    }

    function updateFileInput() {
        // Create new FileList
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;

        // Update upload label text
        const uploadText = document.querySelector('.file-upload-text');
        if (selectedFiles.length > 0) {
            uploadText.textContent = `${selectedFiles.length} file(s) selected`;
        } else {
            uploadText.textContent = 'Choose Images or Drag & Drop';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Make removeFile function globally available
    window.removeFile = removeFile;
});

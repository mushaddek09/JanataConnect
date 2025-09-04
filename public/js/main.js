// JanataConnect - Main JavaScript File

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips and other interactive elements
    initializeTooltips();
    initializeFormValidation();
    initializeFileUpload();
    initializeLocationServices();
});

// Tooltip initialization
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = event.target.getAttribute('data-tooltip');
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
    
    event.target.tooltipElement = tooltip;
}

function hideTooltip(event) {
    if (event.target.tooltipElement) {
        event.target.tooltipElement.remove();
        event.target.tooltipElement = null;
    }
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
}

function validateForm(event) {
    const form = event.target;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(input);
        }
        
        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showFieldError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Password validation
        if (input.type === 'password' && input.value) {
            if (input.value.length < 6) {
                showFieldError(input, 'Password must be at least 6 characters');
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        event.preventDefault();
    }
}

function showFieldError(input, message) {
    clearFieldError(input);
    
    input.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: var(--error-color);
        font-size: 0.8rem;
        margin-top: 0.25rem;
    `;
    
    input.parentNode.appendChild(errorDiv);
}

function clearFieldError(input) {
    input.classList.remove('error');
    const existingError = input.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// File upload functionality
function initializeFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', handleFileUpload);
    });
}

function handleFileUpload(event) {
    const input = event.target;
    const files = input.files;
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    
    Array.from(files).forEach(file => {
        if (file.size > maxSize) {
            alert(`File ${file.name} is too large. Maximum size is 5MB.`);
            input.value = '';
            return;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert(`File ${file.name} is not allowed. Please upload images or PDF files.`);
            input.value = '';
            return;
        }
    });
    
    // Show preview for images
    if (files.length > 0 && files[0].type.startsWith('image/')) {
        showImagePreview(files[0]);
    }
}

function showImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        let preview = document.getElementById('image-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'image-preview';
            preview.style.cssText = `
                margin-top: 1rem;
                text-align: center;
            `;
            document.querySelector('input[type="file"]').parentNode.appendChild(preview);
        }
        
        preview.innerHTML = `
            <img src="${e.target.result}" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
            <p style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--dark-gray);">Preview</p>
        `;
    };
    reader.readAsDataURL(file);
}

// Location services
function initializeLocationServices() {
    const locationButton = document.getElementById('get-location');
    if (locationButton) {
        locationButton.addEventListener('click', getCurrentLocation);
    }
}

function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by this browser.');
        return;
    }
    
    const button = document.getElementById('get-location');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
    button.disabled = true;
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Update hidden fields
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Get address from coordinates
            getAddressFromCoordinates(lat, lng);
            
            button.innerHTML = originalText;
            button.disabled = false;
        },
        function(error) {
            alert('Error getting location: ' + error.message);
            button.innerHTML = originalText;
            button.disabled = false;
        }
    );
}

function getAddressFromCoordinates(lat, lng) {
    // This would typically use a geocoding service
    // For now, we'll just show the coordinates
    const locationField = document.getElementById('location');
    if (locationField) {
        locationField.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}

// Utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
        ${message}
    `;
    
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// AJAX helper
function makeRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    resolve(xhr.responseText);
                }
            } else {
                reject(new Error(`Request failed with status ${xhr.status}`));
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        if (data) {
            xhr.send(JSON.stringify(data));
        } else {
            xhr.send();
        }
    });
}

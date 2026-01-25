/**
 * File Preview & Upload Enhancements
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeFileUpload();
    initializeFilePreviews();
});

function initializeFileUpload() {
    const fileInput = document.getElementById('file-upload-input');
    const dropZone = document.getElementById('file-drop-zone');
    const fileList = document.getElementById('file-upload-list');

    if (!fileInput) return;

    // Drag and drop
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('drag-over');
            });
        });

        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        });

        dropZone.addEventListener('click', () => fileInput.click());
    }

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        const allowedTypes = getAllowedFileTypes();
        const maxSize = getMaxFileSize();

        Array.from(files).forEach(file => {
            // Validate file type
            if (!isValidFileType(file, allowedTypes)) {
                showNotification(`File type not allowed: ${file.name}`, 'error');
                return;
            }

            // Validate file size
            if (file.size > maxSize) {
                const sizeMB = (maxSize / 1024 / 1024).toFixed(0);
                showNotification(`File too large: ${file.name}. Max size: ${sizeMB}MB`, 'error');
                return;
            }

            // Add to upload list
            addFileToList(file);
        });
    }

    function addFileToList(file) {
        if (!fileList) return;

        const fileItem = document.createElement('div');
        fileItem.className = 'file-upload-item';

        const fileIcon = getFileIcon(file.type);
        const fileSize = formatFileSize(file.size);

        fileItem.innerHTML = `
            <div class="file-icon">
                <i class="${fileIcon}"></i>
            </div>
            <div class="file-info">
                <div class="file-name">${file.name}</div>
                <div class="file-meta">
                    <span class="file-size">${fileSize}</span>
                    <span class="file-type">${file.type || 'unknown'}</span>
                </div>
                <div class="upload-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <span class="progress-text">0%</span>
                </div>
            </div>
            <div class="file-preview">
                ${getFilePreviewHTML(file)}
            </div>
            <button type="button" class="remove-file-btn" title="Remove">
                <i class="fas fa-times"></i>
            </button>
        `;

        fileList.appendChild(fileItem);

        // Preview image files
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = fileItem.querySelector('.file-preview img');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }

        // Remove button
        const removeBtn = fileItem.querySelector('.remove-file-btn');
        removeBtn.addEventListener('click', () => fileItem.remove());

        // Upload file
        uploadFile(file, fileItem);
    }

    function uploadFile(file, fileItem) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('document_type_id', document.getElementById('document-type-select')?.value || '');

        const taskId = document.getElementById('task-id-input')?.value;
        const projectId = document.getElementById('project-id-input')?.value;

        let uploadUrl = '/admin/files';
        if (taskId) {
            uploadUrl = `/admin/tasks/${taskId}/files`;
        } else if (projectId) {
            uploadUrl = `/admin/projects/${projectId}/files`;
        }

        const xhr = new XMLHttpRequest();

        // Progress handling
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                const progressFill = fileItem.querySelector('.progress-fill');
                const progressText = fileItem.querySelector('.progress-text');

                if (progressFill) progressFill.style.width = percentComplete + '%';
                if (progressText) progressText.textContent = Math.round(percentComplete) + '%';
            }
        });

        // Completion handling
        xhr.addEventListener('load', function() {
            if (xhr.status === 200 || xhr.status === 201) {
                fileItem.classList.add('upload-success');
                showNotification(`File uploaded: ${file.name}`, 'success');

                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                fileItem.classList.add('upload-error');
                showNotification(`Upload failed: ${file.name}`, 'error');
            }
        });

        xhr.addEventListener('error', function() {
            fileItem.classList.add('upload-error');
            showNotification(`Upload error: ${file.name}`, 'error');
        });

        xhr.open('POST', uploadUrl);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.send(formData);
    }
}

function initializeFilePreviews() {
    // File preview buttons
    document.querySelectorAll('.file-preview-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const fileUrl = this.dataset.fileUrl;
            const fileType = this.dataset.fileType;
            const fileName = this.dataset.fileName;

            openFilePreview(fileUrl, fileType, fileName);
        });
    });
}

function openFilePreview(fileUrl, fileType, fileName) {
    const modal = document.getElementById('file-preview-modal');
    if (!modal) return;

    const modalTitle = modal.querySelector('.modal-title');
    const modalBody = modal.querySelector('.modal-body');

    if (modalTitle) modalTitle.textContent = fileName;

    let previewHTML = '';

    if (fileType.startsWith('image/')) {
        previewHTML = `<img src="${fileUrl}" alt="${fileName}" style="max-width: 100%; height: auto;">`;
    } else if (fileType === 'application/pdf') {
        previewHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="600px">`;
    } else if (fileType.includes('video/')) {
        previewHTML = `<video controls style="max-width: 100%;"><source src="${fileUrl}" type="${fileType}"></video>`;
    } else if (fileType.includes('text/')) {
        // Load text file content
        fetch(fileUrl)
            .then(response => response.text())
            .then(text => {
                modalBody.innerHTML = `<pre style="white-space: pre-wrap; max-height: 600px; overflow-y: auto;">${text}</pre>`;
            });
        modal.style.display = 'block';
        return;
    } else {
        previewHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-file fa-5x" style="color: #9ca3af; margin-bottom: 20px;"></i>
                <p>Preview not available for this file type</p>
                <a href="${fileUrl}" download="${fileName}" class="btn btn-primary" style="margin-top: 20px;">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
        `;
    }

    if (modalBody) modalBody.innerHTML = previewHTML;
    modal.style.display = 'block';
}

function getAllowedFileTypes() {
    const input = document.getElementById('file-upload-input');
    if (!input) return [];

    const accept = input.getAttribute('accept');
    return accept ? accept.split(',').map(t => t.trim()) : [];
}

function getMaxFileSize() {
    // Default 10MB
    return 10 * 1024 * 1024;
}

function isValidFileType(file, allowedTypes) {
    if (allowedTypes.length === 0) return true;

    return allowedTypes.some(type => {
        if (type.startsWith('.')) {
            return file.name.toLowerCase().endsWith(type.toLowerCase());
        }
        if (type.includes('*')) {
            const typeGroup = type.split('/')[0];
            return file.type.startsWith(typeGroup + '/');
        }
        return file.type === type;
    });
}

function getFileIcon(fileType) {
    if (fileType.startsWith('image/')) return 'fas fa-image text-primary';
    if (fileType === 'application/pdf') return 'fas fa-file-pdf text-danger';
    if (fileType.includes('word')) return 'fas fa-file-word text-primary';
    if (fileType.includes('excel') || fileType.includes('spreadsheet')) return 'fas fa-file-excel text-success';
    if (fileType.includes('video/')) return 'fas fa-file-video text-purple';
    if (fileType.includes('audio/')) return 'fas fa-file-audio text-warning';
    return 'fas fa-file text-secondary';
}

function getFilePreviewHTML(file) {
    if (file.type.startsWith('image/')) {
        return '<img src="" alt="Preview" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">';
    }
    return '';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 10);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

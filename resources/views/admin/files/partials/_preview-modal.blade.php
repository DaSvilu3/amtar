<!-- File Preview Modal -->
<div id="file-preview-modal" class="modal" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">File Preview</h4>
                <button type="button" class="close-modal" onclick="document.getElementById('file-preview-modal').style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Preview content will be inserted here by JavaScript -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('file-preview-modal').style.display='none'">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- File Upload Drop Zone -->
<div class="file-upload-section">
    <div id="file-drop-zone" class="file-drop-zone">
        <div class="drop-zone-content">
            <i class="fas fa-cloud-upload-alt fa-3x"></i>
            <p class="drop-zone-text">Drag and drop files here or click to browse</p>
            <p class="drop-zone-info">
                Supported: PDF, JPG, PNG, DOCX, XLSX (Max: 10MB)
            </p>
        </div>
        <input
            type="file"
            id="file-upload-input"
            multiple
            accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.dwg"
            style="display: none;"
        >
    </div>

    <div id="file-upload-list" class="file-upload-list">
        <!-- Files will be added here dynamically -->
    </div>
</div>

<style>
.file-upload-section {
    margin: 20px 0;
}

.file-drop-zone {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.3s;
}

.file-drop-zone:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.file-drop-zone.drag-over {
    border-color: #10b981;
    background: #d1fae5;
}

.drop-zone-content {
    pointer-events: none;
}

.drop-zone-content i {
    color: #9ca3af;
    margin-bottom: 15px;
}

.drop-zone-text {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.drop-zone-info {
    font-size: 13px;
    color: #6b7280;
}

.file-upload-list {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.file-upload-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
}

.file-upload-item.upload-success {
    border-color: #10b981;
    background: #f0fdf4;
}

.file-upload-item.upload-error {
    border-color: #dc2626;
    background: #fef2f2;
}

.file-icon {
    font-size: 32px;
    width: 48px;
    text-align: center;
}

.file-info {
    flex: 1;
}

.file-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.file-meta {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 8px;
}

.upload-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    transition: width 0.3s;
}

.progress-text {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    min-width: 40px;
}

.file-preview {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-file-btn {
    background: #dc2626;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
}

.remove-file-btn:hover {
    background: #b91c1c;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    width: 90%;
    max-width: 900px;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.close-modal {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6b7280;
}

.close-modal:hover {
    color: #1f2937;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 2000;
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.3s;
}

.notification.show {
    opacity: 1;
    transform: translateY(0);
}

.notification-success {
    background: #10b981;
    color: white;
}

.notification-error {
    background: #dc2626;
    color: white;
}

.notification-info {
    background: #3b82f6;
    color: white;
}
</style>

<script src="{{ asset('js/file-preview.js') }}"></script>

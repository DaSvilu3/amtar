@extends('layouts.admin')

@section('title', 'Reports')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Reports</h1>
        <p class="text-muted mb-0">Generate and download business reports</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .report-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-icon.projects { background: rgba(33, 150, 243, 0.15); color: #2196f3; }
    .stat-icon.tasks { background: rgba(76, 175, 80, 0.15); color: #4caf50; }
    .stat-icon.revenue { background: rgba(243, 200, 135, 0.2); color: #f3c887; }

    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .stat-info p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .reports-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--primary-color, #2f0e13);
    }

    .report-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .report-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        transition: all 0.2s;
        border: 2px solid transparent;
        cursor: pointer;
    }

    .report-card:hover {
        border-color: var(--primary-color, #2f0e13);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .report-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary-color, #2f0e13), #5a2a30);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }

    .report-title {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .report-description {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .report-actions {
        display: flex;
        gap: 8px;
    }

    .btn-generate {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        background: var(--primary-color, #2f0e13);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-generate:hover {
        background: #1a0508;
    }

    .btn-preview {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        background: none;
        color: #64748b;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-preview:hover {
        border-color: var(--primary-color, #2f0e13);
        color: var(--primary-color, #2f0e13);
    }

    /* Report Generator Form */
    .generator-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .generator-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--primary-color, #2f0e13), #5a2a30);
        color: white;
    }

    .generator-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .generator-header p {
        margin: 4px 0 0;
        font-size: 13px;
        opacity: 0.8;
    }

    .generator-body {
        padding: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #2f0e13);
        box-shadow: 0 0 0 3px rgba(47, 14, 19, 0.1);
    }

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color, #2f0e13);
    }

    .checkbox-item span {
        font-size: 14px;
        color: #1e293b;
    }

    .generator-footer {
        padding: 20px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Recent Reports */
    .recent-reports {
        margin-top: 40px;
    }

    .reports-table {
        width: 100%;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .reports-table th {
        text-align: left;
        padding: 14px 20px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .reports-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #1e293b;
    }

    .reports-table tr:last-child td {
        border-bottom: none;
    }

    .report-type-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        background: #e3f2fd;
        color: #1565c0;
    }

    .empty-reports {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .empty-reports i {
        font-size: 48px;
        color: #e2e8f0;
        margin-bottom: 16px;
    }

    @media (max-width: 1200px) {
        .report-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .report-stats {
            grid-template-columns: 1fr;
        }

        .report-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Quick Stats -->
<div class="report-stats">
    <div class="stat-card">
        <div class="stat-icon projects">
            <i class="fas fa-project-diagram"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['projects_this_month'] }}</h3>
            <p>Projects This Month</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon tasks">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['tasks_completed_this_month'] }}</h3>
            <p>Tasks Completed This Month</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon revenue">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-info">
            <h3>{{ number_format($stats['revenue_this_month']) }}</h3>
            <p>Revenue This Month (OMR)</p>
        </div>
    </div>
</div>

<!-- Report Types -->
<div class="reports-section">
    <h3 class="section-title">
        <i class="fas fa-file-alt"></i>
        Available Reports
    </h3>
    <div class="report-grid">
        @foreach($reportTypes as $report)
            <div class="report-card" data-report-id="{{ $report['id'] }}">
                <div class="report-icon">
                    <i class="fas {{ $report['icon'] }}"></i>
                </div>
                <h4 class="report-title">{{ $report['name'] }}</h4>
                <p class="report-description">{{ $report['description'] }}</p>
                <div class="report-actions">
                    <button type="button" class="btn-generate" onclick="generateReport('{{ $report['id'] }}')">
                        <i class="fas fa-download"></i> Generate
                    </button>
                    <button type="button" class="btn-preview" onclick="previewReport('{{ $report['id'] }}')">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Custom Report Generator -->
<div class="reports-section">
    <h3 class="section-title">
        <i class="fas fa-cogs"></i>
        Custom Report Generator
    </h3>
    <div class="generator-card">
        <div class="generator-header">
            <h4>Build Custom Report</h4>
            <p>Select parameters to generate a customized report</p>
        </div>
        <form id="customReportForm">
            <div class="generator-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select class="form-control" name="report_type" id="reportType">
                            <option value="">Select a type...</option>
                            @foreach($reportTypes as $report)
                                <option value="{{ $report['id'] }}">{{ $report['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" class="form-control" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" class="form-control" name="date_to" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Include Sections</label>
                    <div class="checkbox-group">
                        <label class="checkbox-item">
                            <input type="checkbox" name="sections[]" value="summary" checked>
                            <span>Summary Statistics</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="sections[]" value="details" checked>
                            <span>Detailed Breakdown</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="sections[]" value="charts">
                            <span>Charts & Graphs</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="sections[]" value="recommendations">
                            <span>Recommendations</span>
                        </label>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 0;">
                    <div class="form-group">
                        <label>Output Format</label>
                        <select class="form-control" name="format">
                            <option value="pdf">PDF Document</option>
                            <option value="excel">Excel Spreadsheet</option>
                            <option value="csv">CSV File</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Group By</label>
                        <select class="form-control" name="group_by">
                            <option value="none">No Grouping</option>
                            <option value="project">Project</option>
                            <option value="client">Client</option>
                            <option value="user">Team Member</option>
                            <option value="status">Status</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <select class="form-control" name="sort">
                            <option value="date_desc">Newest First</option>
                            <option value="date_asc">Oldest First</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="generator-footer">
                <button type="button" class="btn-preview" onclick="previewCustomReport()">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="submit" class="btn-generate">
                    <i class="fas fa-download"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Recent Reports -->
<div class="recent-reports">
    <h3 class="section-title">
        <i class="fas fa-history"></i>
        Recent Reports
    </h3>
    <table class="reports-table">
        <thead>
            <tr>
                <th>Report Name</th>
                <th>Type</th>
                <th>Generated</th>
                <th>Format</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(count($recentReports) > 0)
                @foreach($recentReports as $report)
                    <tr>
                        <td>{{ $report['name'] }}</td>
                        <td><span class="report-type-badge">{{ $report['type'] }}</span></td>
                        <td>{{ $report['generated_at'] }}</td>
                        <td>{{ strtoupper($report['format']) }}</td>
                        <td>
                            <a href="#" class="btn-preview" style="display: inline-flex;">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">
                        <div class="empty-reports">
                            <i class="fas fa-file-alt"></i>
                            <h4>No Reports Generated Yet</h4>
                            <p>Generated reports will appear here for easy access.</p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Report Preview Modal -->
<div class="modal fade" id="reportPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reportPreviewContent">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="mt-3 text-muted">Loading preview...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadFromPreview()">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function generateReport(reportId) {
        // Show loading state
        const btn = event.target.closest('.btn-generate');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        btn.disabled = true;

        // Simulate report generation (in production, this would call an API)
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;

            // Show success message
            alert('Report generated successfully! Download will start automatically.');

            // In production: trigger download
            // window.location.href = `/admin/reports/download/${reportId}`;
        }, 2000);
    }

    function previewReport(reportId) {
        const modal = new bootstrap.Modal(document.getElementById('reportPreviewModal'));
        modal.show();

        // Load preview content
        const content = document.getElementById('reportPreviewContent');
        content.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <p class="mt-3 text-muted">Loading preview...</p>
            </div>
        `;

        // Simulate loading
        setTimeout(() => {
            content.innerHTML = `
                <div class="report-preview">
                    <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h5 style="margin: 0 0 10px;">Report Summary</h5>
                        <p style="color: #64748b; margin: 0;">Preview for ${reportId.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())} Report</p>
                    </div>
                    <p>This is a preview of the report content. The full report will contain detailed data based on your selected parameters.</p>
                    <ul>
                        <li>Summary statistics and KPIs</li>
                        <li>Detailed breakdown by category</li>
                        <li>Visual charts and graphs</li>
                        <li>Actionable recommendations</li>
                    </ul>
                </div>
            `;
        }, 1000);
    }

    function previewCustomReport() {
        const form = document.getElementById('customReportForm');
        const reportType = form.querySelector('[name="report_type"]').value;

        if (!reportType) {
            alert('Please select a report type first.');
            return;
        }

        previewReport(reportType);
    }

    function downloadFromPreview() {
        alert('Download started...');
        bootstrap.Modal.getInstance(document.getElementById('reportPreviewModal')).hide();
    }

    // Form submission
    document.getElementById('customReportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const reportType = this.querySelector('[name="report_type"]').value;
        if (!reportType) {
            alert('Please select a report type.');
            return;
        }

        generateReport(reportType);
    });
</script>
@endsection

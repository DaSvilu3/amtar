<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $contract->contract_number }} - {{ $contract->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 25mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: white;
            color: #333;
            line-height: 1.5;
            font-size: 11pt;
        }

        .page {
            width: 100%;
            padding: 0;
            background-color: white;
            position: relative;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1a365d;
        }

        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .company-subtitle {
            font-size: 11pt;
            color: #666;
            margin-bottom: 8px;
        }

        .company-contact {
            font-size: 9pt;
            color: #888;
        }

        .contract-number {
            font-size: 12pt;
            color: #c69c6d;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Content */
        .contract-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
            margin: 20px 0;
            text-decoration: underline;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #c69c6d;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 140px;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .description-text {
            text-align: justify;
            line-height: 1.6;
            color: #444;
            padding: 8px 0;
        }

        /* Services Table */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .services-table th {
            background-color: #1a365d;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        .services-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .services-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .stage-name {
            font-weight: bold;
            color: #1a365d;
        }

        /* Terms Box */
        .terms-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 12px;
            margin-top: 8px;
        }

        /* Financial Highlight */
        .financial-highlight {
            background-color: #f0f7ff;
            border: 1px solid #c4deff;
            padding: 15px;
            margin-top: 8px;
            text-align: center;
        }

        .total-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1a365d;
        }

        .total-words {
            font-size: 9pt;
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }

        /* Signatures */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
        }

        .signature-block {
            width: 45%;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 50px;
            display: block;
        }

        .signature-line {
            border-top: 2px solid #333;
            padding-top: 8px;
            text-align: center;
        }

        .signature-name {
            color: #666;
            font-size: 10pt;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }

        /* Page Number */
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-subtitle">Engineering & Design Consultancy</div>
            <div class="company-contact">
                {{ $company['address'] }} | {{ $company['phone'] }} | {{ $company['email'] }}
            </div>
            <div class="contract-number">{{ $contract->contract_number }}</div>
        </div>

        <!-- Contract Title -->
        <div class="contract-title">{{ $contract->title }}</div>

        <!-- Contract Details -->
        <div class="section">
            <div class="section-title">Contract Information</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Contract Number:</td>
                    <td class="info-value">{{ $contract->contract_number }}</td>
                </tr>
                <tr>
                    <td class="info-label">Contract Date:</td>
                    <td class="info-value">{{ $contract->created_at->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Status:</td>
                    <td class="info-value">{{ ucfirst($contract->status) }}</td>
                </tr>
                @if($contract->signed_date)
                <tr>
                    <td class="info-label">Signed Date:</td>
                    <td class="info-value">{{ $contract->signed_date->format('F d, Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Client Information -->
        <div class="section">
            <div class="section-title">Client Information</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Client Name:</td>
                    <td class="info-value">{{ $client->name ?? 'N/A' }}</td>
                </tr>
                @if($client->company_name ?? null)
                <tr>
                    <td class="info-label">Company:</td>
                    <td class="info-value">{{ $client->company_name }}</td>
                </tr>
                @endif
                @if($client->email ?? null)
                <tr>
                    <td class="info-label">Email:</td>
                    <td class="info-value">{{ $client->email }}</td>
                </tr>
                @endif
                @if($client->phone ?? null)
                <tr>
                    <td class="info-label">Phone:</td>
                    <td class="info-value">{{ $client->phone }}</td>
                </tr>
                @endif
                @if($client->address ?? null)
                <tr>
                    <td class="info-label">Address:</td>
                    <td class="info-value">{{ $client->address }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Project Information -->
        @if($project)
        <div class="section">
            <div class="section-title">Project Information</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Project Name:</td>
                    <td class="info-value">{{ $project->name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Project Number:</td>
                    <td class="info-value">{{ $project->project_number }}</td>
                </tr>
                @if($project->projectManager)
                <tr>
                    <td class="info-label">Project Manager:</td>
                    <td class="info-value">{{ $project->projectManager->name }}</td>
                </tr>
                @endif
                @if($project->location)
                <tr>
                    <td class="info-label">Location:</td>
                    <td class="info-value">{{ $project->location }}</td>
                </tr>
                @endif
                @if($project->start_date)
                <tr>
                    <td class="info-label">Start Date:</td>
                    <td class="info-value">{{ \Carbon\Carbon::parse($project->start_date)->format('F d, Y') }}</td>
                </tr>
                @endif
                @if($project->end_date)
                <tr>
                    <td class="info-label">End Date:</td>
                    <td class="info-value">{{ \Carbon\Carbon::parse($project->end_date)->format('F d, Y') }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Financial Terms -->
        <div class="section">
            <div class="section-title">Financial Terms</div>
            @if($contract->value)
            <div class="financial-highlight">
                <div class="total-value">{{ number_format($contract->value, 3) }} {{ $contract->currency ?? 'OMR' }}</div>
                @if($budgetWords)
                <div class="total-words">({{ $budgetWords }})</div>
                @endif
            </div>
            @endif
            <table class="info-table" style="margin-top: 10px;">
                @if($contract->start_date)
                <tr>
                    <td class="info-label">Contract Start:</td>
                    <td class="info-value">{{ $contract->start_date->format('F d, Y') }}</td>
                </tr>
                @endif
                @if($contract->end_date)
                <tr>
                    <td class="info-label">Contract End:</td>
                    <td class="info-value">{{ $contract->end_date->format('F d, Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Services -->
        @if($servicesByStage && count($servicesByStage) > 0)
        <div class="section">
            <div class="section-title">Scope of Services</div>
            <table class="services-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Stage</th>
                        <th>Services</th>
                        <th style="width: 10%;">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicesByStage as $stageData)
                    <tr>
                        <td class="stage-name">{{ $stageData['stage'] ?? 'General' }}</td>
                        <td>
                            @foreach($stageData['services'] as $service)
                                {{ $service->service->name ?? 'N/A' }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td style="text-align: center;">{{ $stageData['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Description -->
        @if($contract->description)
        <div class="section">
            <div class="section-title">Description</div>
            <div class="description-text">{!! nl2br(e($contract->description)) !!}</div>
        </div>
        @endif

        <!-- Terms and Conditions -->
        @if($contract->terms)
        <div class="section">
            <div class="section-title">Terms and Conditions</div>
            <div class="terms-box">
                {!! nl2br(e($contract->terms)) !!}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-block">
                        <span class="signature-label">For the Client:</span>
                        <div class="signature-line">
                            <div class="signature-name">{{ $client->name ?? 'Client Representative' }}</div>
                        </div>
                    </td>
                    <td style="width: 10%;"></td>
                    <td class="signature-block">
                        <span class="signature-label">For {{ $company['name'] }}:</span>
                        <div class="signature-line">
                            <div class="signature-name">Authorized Signatory</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <strong>{{ $company['name'] }}</strong><br>
        {{ $company['address'] }} | {{ $company['phone'] }} | {{ $company['email'] }} | {{ $company['website'] }}
    </div>
</body>
</html>

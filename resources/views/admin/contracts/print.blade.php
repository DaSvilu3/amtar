<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $contract->contract_number }} - {{ $contract->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: white;
            color: #333;
            line-height: 1.6;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm 35mm 20mm;
            margin: 0 auto;
            background-color: white;
            position: relative;
        }

        /* Letterhead Background */
        .letterhead {
            position: fixed;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: -1;
            opacity: 0.05;
            background-image: url('/images/letterhead-bg.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a365d;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .company-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .contract-number {
            font-size: 16px;
            color: #c69c6d;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Content */
        .contract-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #1a365d;
            margin: 30px 0;
            text-decoration: underline;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #c69c6d;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .info-label {
            font-weight: bold;
            min-width: 150px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .description-text {
            text-align: justify;
            line-height: 1.8;
            color: #444;
            padding: 10px 0;
        }

        .services-list {
            margin-top: 10px;
        }

        .service-stage {
            margin-bottom: 15px;
        }

        .service-stage-title {
            font-weight: bold;
            color: #1a365d;
            font-size: 14px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .service-item {
            padding-left: 20px;
            margin-bottom: 5px;
            position: relative;
        }

        .service-item:before {
            content: "✓";
            position: absolute;
            left: 5px;
            color: #c69c6d;
            font-weight: bold;
        }

        .terms-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            width: 45%;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            text-align: center;
        }

        .signature-label {
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }

        .signature-name {
            color: #666;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding: 10px 20mm;
            border-top: 1px solid #ddd;
        }

        .footer-content {
            display: flex;
            justify-content: space-around;
            margin-top: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: none;
                box-shadow: none;
                page-break-after: always;
            }

            .no-print {
                display: none !important;
            }

            .footer {
                position: fixed;
                bottom: 10mm;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #1a365d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #c69c6d;
        }

        @media screen {
            body {
                background-color: #f0f0f0;
                padding: 20px;
            }

            .page {
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        Print Contract
    </button>

    <div class="letterhead"></div>

    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="company-name">AMTAR</div>
            <div class="company-subtitle">Engineering & Design Consultancy</div>
            <div class="contract-number">{{ $contract->contract_number }}</div>
        </div>

        <!-- Contract Title -->
        <div class="contract-title">{{ $contract->title }}</div>

        <!-- Contract Details -->
        <div class="section">
            <div class="section-title">Contract Information</div>
            <div class="info-row">
                <span class="info-label">Contract Number:</span>
                <span class="info-value">{{ $contract->contract_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Contract Date:</span>
                <span class="info-value">{{ $contract->created_at->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($contract->status) }}</span>
            </div>
            @if($contract->signed_date)
            <div class="info-row">
                <span class="info-label">Signed Date:</span>
                <span class="info-value">{{ $contract->signed_date->format('F d, Y') }}</span>
            </div>
            @endif
        </div>

        <!-- Client Information -->
        <div class="section">
            <div class="section-title">Client Information</div>
            <div class="info-row">
                <span class="info-label">Client Name:</span>
                <span class="info-value">{{ $contract->client->name }}</span>
            </div>
            @if($contract->client->email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $contract->client->email }}</span>
            </div>
            @endif
            @if($contract->client->phone)
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $contract->client->phone }}</span>
            </div>
            @endif
            @if($contract->client->address)
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $contract->client->address }}</span>
            </div>
            @endif
        </div>

        <!-- Project Information -->
        @if($contract->project)
        <div class="section">
            <div class="section-title">Project Information</div>
            <div class="info-row">
                <span class="info-label">Project Name:</span>
                <span class="info-value">{{ $contract->project->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Project Number:</span>
                <span class="info-value">{{ $contract->project->project_number }}</span>
            </div>
            @if($contract->project->projectManager)
            <div class="info-row">
                <span class="info-label">Project Manager:</span>
                <span class="info-value">{{ $contract->project->projectManager->name }}</span>
            </div>
            @endif
            @if($contract->project->location)
            <div class="info-row">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ $contract->project->location }}</span>
            </div>
            @endif
            @if($contract->project->start_date)
            <div class="info-row">
                <span class="info-label">Project Start Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($contract->project->start_date)->format('F d, Y') }}</span>
            </div>
            @endif
            @if($contract->project->end_date)
            <div class="info-row">
                <span class="info-label">Project End Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($contract->project->end_date)->format('F d, Y') }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Contract Value -->
        <div class="section">
            <div class="section-title">Financial Terms</div>
            @if($contract->value)
            <div class="info-row">
                <span class="info-label">Contract Value:</span>
                <span class="info-value">{{ number_format($contract->value, 3) }} {{ $contract->currency ?? 'OMR' }}</span>
            </div>
            @endif
            @if($contract->start_date)
            <div class="info-row">
                <span class="info-label">Contract Start Date:</span>
                <span class="info-value">{{ $contract->start_date->format('F d, Y') }}</span>
            </div>
            @endif
            @if($contract->end_date)
            <div class="info-row">
                <span class="info-label">Contract End Date:</span>
                <span class="info-value">{{ $contract->end_date->format('F d, Y') }}</span>
            </div>
            @endif
        </div>

        <!-- Services -->
        @if($contract->services && count($contract->services) > 0)
        <div class="section">
            <div class="section-title">Scope of Services</div>
            <div class="services-list">
                @foreach($contract->services as $stageName => $serviceNames)
                <div class="service-stage">
                    <div class="service-stage-title">{{ $stageName }}</div>
                    @foreach($serviceNames as $serviceName)
                    <div class="service-item">{{ $serviceName }}</div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Description -->
        @if($contract->description)
        <div class="section">
            <div class="section-title">Description</div>
            <div class="description-text">{{ $contract->description }}</div>
        </div>
        @endif

        <!-- Terms and Conditions -->
        @if($contract->terms)
        <div class="section">
            <div class="section-title">Terms and Conditions</div>
            <div class="terms-box">
                {{ $contract->terms }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-block">
                <div class="signature-label">For the Client:</div>
                <div class="signature-line">
                    <div class="signature-name">{{ $contract->client->name }}</div>
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-label">For AMTAR:</div>
                <div class="signature-line">
                    <div class="signature-name">Authorized Signatory</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>AMTAR Engineering & Design Consultancy</strong>
            <div class="footer-content">
                <span>Email: info@amtar.om</span>
                <span>Phone: +968 XXXX XXXX</span>
                <span>Website: www.amtar.om</span>
            </div>
        </div>
    </div>
</body>
</html>

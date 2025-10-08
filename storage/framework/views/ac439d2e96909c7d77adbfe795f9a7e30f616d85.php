<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebula | Credential Verification</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%);
            min-height: 100vh;
            padding: 30px 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .verify-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.98);
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .logo-img {
            display: inline-block;
        }

        .logo-img img {
            max-width: 100%;
            height: auto;
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 35px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .verified-header {
            text-align: center;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .verified-icon {
            width: 56px;
            height: 56px;
            background: #10b981;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .verified-icon svg {
            width: 35px;
            height: 35px;
            stroke: white;
            stroke-width: 3;
            fill: none;
        }

        .verified-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }

        .verified-subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .badge-container {
            text-align: center;
            margin: 28px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .badge-img {
            max-width: 350px;
            width: 100%;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .credentials-section {
            margin: 28px 0;
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credential-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .credential-item {
            background: #f9fafb;
            padding: 14px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .credential-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .credential-value {
            font-size: 0.95rem;
            color: #111827;
            font-weight: 600;
        }

        .verification-code-item {
            background: #1f2937;
            border: 1px solid #374151;
            grid-column: span 2;
        }

        .verification-code-item .credential-label {
            color: #9ca3af;
        }

        .verification-code-item .credential-value {
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            letter-spacing: 1.5px;
        }

        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .status-valid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .status-invalid {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .action-section {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .btn-custom {
            padding: 10px 28px;
            font-weight: 600;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            border: none;
            margin: 0 6px;
            display: inline-block;
        }

        .btn-download {
            background: #2563eb;
            color: white;
        }

        .btn-download:hover {
            background: #1d4ed8;
            color: white;
        }

        .btn-secondary-custom {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary-custom:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .invalid-section {
            text-align: center;
            padding: 50px 30px;
        }

        .invalid-icon {
            width: 56px;
            height: 56px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .invalid-icon svg {
            width: 32px;
            height: 32px;
            stroke: white;
            stroke-width: 3;
            fill: none;
        }

        .invalid-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
        }

        .invalid-text {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            max-width: 550px;
            margin: 0 auto 25px;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .verify-card {
                padding: 25px 20px;
            }

            .logo-section {
                padding: 15px;
            }

            .logo-img img {
                width: 140px;
            }

            .credential-grid {
                grid-template-columns: 1fr;
            }

            .verification-code-item {
                grid-column: span 1;
            }

            .btn-custom {
                display: block;
                width: 100%;
                margin: 8px 0;
            }

            .verified-title {
                font-size: 1.5rem;
            }

            .invalid-title {
                font-size: 1.5rem;
            }

            .badge-img {
                max-width: 100%;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .verify-container {
                max-width: 90%;
            }

            .credential-grid {
                gap: 14px;
            }

            .badge-img {
                max-width: 320px;
            }
        }

        .footer-note {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8rem;
            margin-top: 25px;
            font-weight: 400;
        }
    </style>
</head>

<body>
<div class="verify-container">
    <div class="logo-section">
        <a href="https://nebula.lk" class="text-nowrap logo-img">
            <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula" width="180">
        </a>
    </div>

    <div class="verify-card">
        <?php if($badge): ?>
            <div class="verified-header">
                <div class="verified-icon">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h1 class="verified-title">Credential Verified</h1>
                <p class="verified-subtitle">This digital credential has been officially issued and authenticated by Nebula Institute of Technology</p>
            </div>

            <div class="badge-container">
                <?php if($badge->badge_image_path): ?>
                    <img src="<?php echo e(asset('storage/' . $badge->badge_image_path)); ?>" 
                         alt="Digital Credential" 
                         class="badge-img">
                <?php else: ?>
                    <div class="alert alert-warning">Credential image not available</div>
                <?php endif; ?>
            </div>

            <div class="credentials-section">
                <h2 class="section-title">Credential Details</h2>
                <div class="credential-grid">
                    <div class="credential-item">
                        <div class="credential-label">Credential Holder</div>
                        <div class="credential-value"><?php echo e($badge->student->full_name); ?> <?php echo e($badge->student->last_name); ?></div>
                    </div>

                    <div class="credential-item">
                        <div class="credential-label">Course Program</div>
                        <div class="credential-value"><?php echo e($badge->course->course_name); ?></div>
                    </div>

                    <div class="credential-item">
                        <div class="credential-label">Program Type</div>
                        <div class="credential-value"><?php echo e(ucfirst($badge->course->course_type)); ?></div>
                    </div>

                    <div class="credential-item">
                        <div class="credential-label">Batch Intake</div>
                        <div class="credential-value"><?php echo e($badge->intake->batch ?? 'N/A'); ?></div>
                    </div>

                    <div class="credential-item">
                        <div class="credential-label">Issue Date</div>
                        <div class="credential-value"><?php echo e(\Carbon\Carbon::parse($badge->issued_date)->format('d M Y')); ?></div>
                    </div>

                    <div class="credential-item status-item">
                        <div>
                            <div class="credential-label">Credential Status</div>
                        </div>
                        <div>
                            <?php if($badge->status === 'active'): ?>
                                <span class="status-badge status-valid">Valid</span>
                            <?php else: ?>
                                <span class="status-badge status-invalid">Revoked</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="credential-item verification-code-item">
                        <div class="credential-label">Verification Code</div>
                        <div class="credential-value"><?php echo e($badge->verification_code); ?></div>
                    </div>
                </div>
            </div>

            <div class="action-section">
                <a href="<?php echo e(asset('storage/' . $badge->badge_image_path)); ?>" 
                   class="btn btn-custom btn-download" download>
                    Download Credential
                </a>
                <a href="https://www.nebula.edu.lk/" 
                   class="btn btn-custom btn-secondary-custom">
                    Visit Nebula
                </a>
            </div>

        <?php else: ?>
            <div class="invalid-section">
                <div class="invalid-icon">
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
                <h2 class="invalid-title">Verification Failed</h2>
                <p class="invalid-text">
                    The verification code provided is invalid or this credential may have been revoked. 
                    Please verify the code and try again. For assistance, contact Nebula Institute of Technology.
                </p>
                <a href="https://nebula.lk" class="btn btn-custom btn-download">
                    Return to Nebula
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer-note">
        &copy; 2025 Nebula Institute of Technology. All rights reserved.
    </div>
</div>
</body>
</html><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/badges/verify.blade.php ENDPATH**/ ?>
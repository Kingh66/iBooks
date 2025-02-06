<?php
session_start();
$pageTitle = "iBooks - Terms of Service";
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .terms-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .hero-section {
            text-align: center;
            padding: 4rem 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: white;
            border-radius: 15px;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .section-title {
            font-size: 2rem;
            margin: 2rem 0 1.5rem;
            color: var(--primary-color);
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--secondary-color);
            margin: 1rem 0;
        }

        .terms-content {
            line-height: 1.8;
            color: #555;
        }

        .terms-content strong {
            color: var(--primary-color);
        }

        .terms-content ol {
            padding-left: 1.5rem;
            margin: 1.5rem 0;
        }

        .terms-content li {
            margin-bottom: 1rem;
        }

        .highlight {
            background: #f8f9fa;
            padding: 1.5rem;
            border-left: 4px solid var(--secondary-color);
            margin: 1.5rem 0;
            border-radius: 5px;
        }

        @media (max-width: 768px) {
            .terms-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .hero-section {
                padding: 2rem 0;
            }
            
            .section-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <main class="terms-container">
        <div class="hero-section">
            <h1>Terms of Service</h1>
            <p class="lead">Please read these terms carefully before using our services</p>
        </div>

        <div class="terms-content">
            <div class="highlight">
                <strong>Last Updated:</strong> <?= date('F j, Y') ?><br>
                By using iBooks, you agree to these terms and conditions.
            </div>

            <h2 class="section-title">Acceptance of Terms</h2>
            <p>By accessing or using the iBooks platform, you agree to be bound by these Terms of Service and our Privacy Policy.</p>

            <h2 class="section-title">User Responsibilities</h2>
            <ol>
                <li>You must be at least 13 years old to use our services</li>
                <li>Maintain the confidentiality of your account credentials</li>
                <li>Do not engage in unauthorized distribution of content</li>
                <li>Comply with all applicable laws and regulations</li>
            </ol>

            <h2 class="section-title">Intellectual Property</h2>
            <ol>
                <li>All content remains property of respective rights holders</li>
                <li>We grant limited, non-exclusive access to digital content</li>
                <li>No reproduction or distribution without authorization</li>
            </ol>

            <h2 class="section-title">Payments & Subscriptions</h2>
            <ol>
                <li>Recurring billing for subscription services</li>
                <li>30-day refund policy for digital content</li>
                <li>Prices subject to change with 30-day notice</li>
            </ol>

            <h2 class="section-title">Termination</h2>
            <p>We reserve the right to suspend or terminate accounts for:<br>
            - Violation of these terms<br>
            - Fraudulent activity<br>
            - Non-payment of fees</p>

            <h2 class="section-title">Disclaimers</h2>
            <div class="highlight">
                <p>Services provided "as-is" without warranties of any kind. We do not guarantee uninterrupted access or error-free operation.</p>
            </div>

            <h2 class="section-title">Limitation of Liability</h2>
            <p>iBooks shall not be liable for:<br>
            - Indirect or consequential damages<br>
            - Loss of data or content<br>
            - Third-party service interruptions</p>

            <h2 class="section-title">Governing Law</h2>
            <p>These terms shall be governed by and construed in accordance with the laws of South Africa. Any disputes shall be resolved in courts located in Durban.</p>

            <h2 class="section-title">Changes to Terms</h2>
            <p>We may modify these terms at any time. Continued use after changes constitutes acceptance of modified terms.</p>

            <h2 class="section-title">Contact Information</h2>
            <p>For questions regarding these terms:<br>
            <strong>Email:</strong> sizwemthembu03@gmail.com<br>
            <strong>Address:</strong> 123 KwaMthembu, Library City, Durban 4001</p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>
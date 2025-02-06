<?php
session_start();
$pageTitle = "iBooks - Privacy Policy";
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
        .privacy-container {
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

        .hero-section::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.1) 100%);
            transform: rotate(45deg);
        }


        h1 {
        font-size: 3.5rem;
        color: white;
        margin-bottom: 1.5rem;
        animation: slideIn 0.8s ease;
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

        .policy-content {
            line-height: 1.8;
            color: #555;
        }

        .policy-content strong {
            color: var(--primary-color);
        }

        .policy-content ol {
            padding-left: 1.5rem;
            margin: 1.5rem 0;
        }

        .policy-content li {
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .privacy-container {
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
    <main class="privacy-container">
        <div class="hero-section">
            <h1>Privacy Policy</h1>
            <p class="lead">Your Privacy Matters to Us</p>
        </div>

        <div class="policy-content">
            <h2 class="section-title">Introduction</h2>
            <p>At iBooks, we are committed to protecting your personal information and your right to privacy. This policy outlines our practices regarding data collection, use, and disclosure when you use our service.</p>

            <h2 class="section-title">Data Collection</h2>
            <p>We collect information to provide better services to our users:</p>
            <ol>
                <li><strong>Personal Information:</strong> Name, email address, and contact details when you create an account.</li>
                <li><strong>Usage Data:</strong> Information about how you interact with our platform.</li>
                <li><strong>Cookies:</strong> We use cookies to enhance your browsing experience.</li>
            </ol>

            <h2 class="section-title">Use of Data</h2>
            <p>Your information helps us to:</p>
            <ol>
                <li>Provide and maintain our service</li>
                <li>Notify you about changes to our service</li>
                <li>Allow participation in interactive features</li>
                <li>Provide customer support</li>
                <li>Monitor usage of our service</li>
            </ol>

            <h2 class="section-title">Data Protection</h2>
            <p>We implement security measures including:</p>
            <ol>
                <li>SSL encryption for data transmission</li>
                <li>Regular security audits</li>
                <li>Access controls to personal information</li>
            </ol>

            <h2 class="section-title">User Rights</h2>
            <p>You have the right to:</p>
            <ol>
                <li>Access your personal data</li>
                <li>Request correction of inaccurate data</li>
                <li>Request deletion of your data</li>
                <li>Object to processing of your data</li>
            </ol>

            <h2 class="section-title">Cookies</h2>
            <p>We use cookies for:</p>
            <ol>
                <li>Authentication and security</li>
                <li>Remembering preferences</li>
                <li>Analytics and performance monitoring</li>
            </ol>

            <h2 class="section-title">Third-Party Services</h2>
            <p>We may use third-party services that collect information used to:</p>
            <ol>
                <li>Process payments</li>
                <li>Analyze service usage</li>
                <li>Deliver targeted advertisements</li>
            </ol>

            <h2 class="section-title">Policy Changes</h2>
            <p>We may update this policy periodically. Significant changes will be notified through our platform or via email.</p>

            <h2 class="section-title">Contact Us</h2>
            <p>For privacy-related inquiries, contact our Data Protection Officer at:<br>
            <strong>Email:</strong> sizwemthembu03@gmail.com<br>
            <strong>Address:</strong> 123 KwaMthembu, Library City, Durban 4001</p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>
<?php
session_start();
require_once 'includes/db.php';
$pageTitle = "iBooks - Contact Us";
include 'includes/navbar.php';

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate inputs
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($message)) $errors[] = 'Message is required';

    // Process if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_queries 
                (name, email, phone, subject, message, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $success = true;
            
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Error saving your message. Please try again.";
        }
    }
}
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
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f8;
            color: #34495e;
            margin: 0;
            padding: 0;
        }

        /* Contact Container */
        .contact-container {
            max-width: 1200px;
            margin: 6rem auto;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
        }

        /* Contact Information Section */
        .contact-info {
            background: linear-gradient(135deg, #3498db 0%, #34495e 100%);
            padding: 2.5rem;
            border-radius: 15px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .contact-info::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .contact-info h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .contact-item:hover {
            transform: translateX(10px);
            background: rgba(255,255,255,0.15);
        }

        .contact-icon {
            font-size: 1.5rem;
            margin-right: 1.5rem;
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Map Container */
        .map-container {
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        .map-container iframe {
            filter: grayscale(20%) contrast(110%);
        }

        /* Form Styles */
        .contact-form {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-label {
            position: absolute;
            left: 1rem;
            top: 1.1rem;
            color: #95a5a6;
            pointer-events: none;
            transition: top 0.3s ease, font-size 0.3s ease, color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 1.5rem 1rem 1rem;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8f9fa;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .form-control:focus + .form-label,
        .form-control:not(:placeholder-shown) + .form-label {
            top: 0.5rem;
            font-size: 0.8rem;
            color: #3498db;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
            color: white;
            padding: 1.25rem 2.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.1) 100%);
            transform: rotate(45deg);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        /* Alerts */
        .alert {
            padding: 1.25rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .alert-danger {
            background: #fee;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .alert-danger::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: #e74c3c;
        }

        .alert-success {
            background: #effaf0;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .alert-success::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: #2ecc71;
        }

        .alert i {
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .contact-container {
                margin: 2rem;
                padding: 1.5rem;
            }
            
            .row {
                flex-direction: column;
            }

            .col-md-6 {
                width: 100%;
                margin-bottom: 2rem;
            }

            .contact-info {
                padding: 1.5rem;
            }

            .contact-item {
                padding: 1rem;
            }

            .map-container iframe {
                width: 100%;
            }

            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <main class="container contact-container">
        <h1 class="text-center" style="font-size: 2.5rem; margin-bottom: 3rem; color: var(--primary-color);">
            Get in Touch
            <div class="title-underline" style="width: 60px; height: 4px; background: var(--secondary-color); margin: 1rem auto;"></div>
        </h1>
        
        <!-- Display messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div>
                    <p class="mb-0">Thank you for your message! We'll respond within 24 hours.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="row" style="display: flex; gap: 2rem;">
            <div class="col-md-6">
                <div class="contact-info">
                    <h2>Contact Information</h2>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">Our Office</h4>
                            <p style="margin: 0;">123 KwaMthembu, Library City, Durban 4001</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">Call Us</h4>
                            <p style="margin: 0;">(+27) 79 655 0842</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">Email Us</h4>
                            <p style="margin: 0; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;">sizwemthembu03@gmail.com</p>

                        </div>
                    </div>

                    <div class="business-hours" style="margin-top: 2rem;">
                        <h3>Business Hours</h3>
                        <ul style="list-style: none; padding: 0;">
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <span style="font-weight: 600;">Mon-Fri:</span> 9am - 8pm
                            </li>
                            <li style="padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <span style="font-weight: 600;">Saturday:</span> 10am - 6pm
                            </li>
                            <li style="padding: 0.5rem 0;">
                                <span style="font-weight: 600;">Sunday:</span> Closed
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d443113.02427411004!2d30.550716794982787!3d-29.81172967211649!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ef7a9c9dc0cfb2f%3A0x3a40134958919c48!2sDurban%20Central%20Public%20Library!5e0!3m2!1sen!2sza!4v1738606627501!5m2!1sen!2sza" 
                    width="600" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                </div>
            </div>

            <div class="col-md-6">
                <form class="contact-form" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder=" "
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        <label class="form-label" for="name">Full Name *</label>
                    </div>

                    <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder=" "
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        <label class="form-label" for="email">Email Address *</label>
                    </div>

                    <div class="form-group">
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder=" "
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        <label class="form-label" for="phone">Phone Number</label>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="subject" name="subject" 
                               placeholder=" "
                               value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                        <label class="form-label" for="subject">Subject *</label>
                    </div>

                    <div class="form-group">
                        <textarea class="form-control" id="message" name="message" 
                                  rows="5" placeholder=" " required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        <label class="form-label" for="message">Message *</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </main>
    <script src="scripts/main.js"></script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
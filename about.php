<?php
session_start();
$pageTitle = "iBooks - About Us";
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
        .about-container {
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
            font-size: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--secondary-color);
            margin: 1rem auto;
        }

        .timeline {
            position: relative;
            padding: 2rem 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 4rem;
            position: relative;
        }

        .timeline-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 45%;
            position: relative;
            z-index: 1;
        }

        .timeline-content.left {
            margin-right: auto;
        }

        .timeline-content.right {
            margin-left: auto;
        }

        .timeline-icon {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background: var(--secondary-color);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            z-index: 2;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-member {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .member-image {
            height: 300px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .member-info {
            padding: 1.5rem;
            text-align: center;
        }

        .member-social {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .value-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .value-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .timeline-content {
                width: 90%;
                margin: 0 auto 2rem !important;
            }
            
            .timeline-item {
                flex-direction: column;
            }
            
            .timeline-icon {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="about-container">
        <div class="hero-section">
            <h1>Welcome to iBooks</h1>
            <p class="lead">Your digital library for book lovers. Explore, discover, and enjoy!</p>
        </div>

        <section class="our-story">
            <h2 class="section-title">Our Story</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content left">
                        <h3>2020: Humble Beginnings</h3>
                        <p>Founded in a small Durban apartment with just 100 curated titles, we began our journey to revolutionize digital reading.</p>
                    </div>
                    <div class="timeline-icon"><i class="fas fa-seedling"></i></div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content right">
                        <h3>2022: First Million Readers</h3>
                        <p>Reached our first million users and expanded our catalog to over 50,000 titles across multiple genres.</p>
                    </div>
                    <div class="timeline-icon"><i class="fas fa-rocket"></i></div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content left">
                        <h3>2024: Global Recognition</h3>
                        <p>Awarded "Best Digital Library Platform" at the Global Tech Awards, expanding to 15 new countries.</p>
                    </div>
                    <div class="timeline-icon"><i class="fas fa-trophy"></i></div>
                </div>
            </div>
        </section>

        <section class="our-team">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image" style="background-image: url('images/team1.jpg')"></div>
                    <div class="member-info">
                        <h4>Sizwe Mthembu</h4>
                        <p>CEO & Founder</p>
                        <div class="member-social">
                            <a href="https://www.linkedin.com/in/sizwe-philani-didiza-mthembu-72993a248/"><i class="fab fa-linkedin"></i></a>
                            <a href="https://github.com/Kingh66"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image" style="background-image: url('images/team2.jpg')"></div>
                    <div class="member-info">
                        <h4>Awonke Nyamela</h4>
                        <p>Head of Operations</p>
                        <div class="member-social">
                            <a href="https://www.linkedin.com/in/awonke-nyamela-27b21424a/"><i class="fab fa-linkedin"></i></a>
                            <a href="https://github.com/awonke-ai"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image" style="background-image: url('images/team4.jpg')"></div>
                    <div class="member-info">
                        <h4>Noluvuyo Nodangala</h4>
                        <p>Chief Technology Officer</p>
                        <div class="member-social">
                        <a href="https://www.linkedin.com/in/noluvuyo-nodangala-a79918235/"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image" style="background-image: url('images/team3.jpg')"></div>
                    <div class="member-info">
                        <h4>Othembela Nothanaza</h4>
                        <p>Chief Technology Officer</p>
                        <div class="member-social">
                        <a href="https://www.linkedin.com/in/othembela-nothanaza-242b70248/"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="our-values">
            <h2 class="section-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-book-open value-icon"></i>
                    <h4>Literacy for All</h4>
                    <p>We believe everyone deserves access to quality literature regardless of their background.</p>
                </div>

                <div class="value-card">
                    <i class="fas fa-heart value-icon"></i>
                    <h4>Passion for Stories</h4>
                    <p>Curating content that inspires, educates, and entertains is at our core.</p>
                </div>

                <div class="value-card">
                    <i class="fas fa-lock value-icon"></i>
                    <h4>Digital Security</h4>
                    <p>Your privacy and data security are our top priorities in everything we do.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>
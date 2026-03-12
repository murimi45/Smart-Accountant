<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimu - Simple, Powerful Tools for Modern School Data Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #2c3e50;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-size: 15px;
            font-weight: 500;
            color: #495057;
            margin: 0 15px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #79c347;
        }

        .btn-demo {
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-demo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(121, 195, 71, 0.4);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            padding: 120px 0 100px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -15%;
            width: 60%;
            height: 120%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
        }

        .hero-title {
            font-size: 56px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .hero-subtitle {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 40px;
            line-height: 1.6;
            max-width: 600px;
        }

        .hero-buttons .btn {
            padding: 15px 35px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            margin-right: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .btn-primary-hero {
            background: white;
            color: #79c347;
            border: none;
        }

        .btn-primary-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
            color: #5fa732;
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-hero:hover {
            background: white;
            color: #79c347;
            transform: translateY(-3px);
        }

        .hero-image {
            position: relative;
            z-index: 1;
        }

        .hero-image img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .hero-placeholder {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 80px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 2px dashed rgba(255, 255, 255, 0.3);
        }

        .hero-placeholder i {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: #f8f9fa;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-badge {
            display: inline-block;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 42px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .section-subtitle {
            font-size: 18px;
            color: #6c757d;
            max-width: 700px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 40px rgba(121, 195, 71, 0.2);
            border-color: #79c347;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            margin-bottom: 25px;
        }

        .feature-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .feature-description {
            font-size: 15px;
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
        }

        .feature-list li {
            padding: 10px 0;
            font-size: 14px;
            color: #495057;
            display: flex;
            align-items: center;
        }

        .feature-list li i {
            color: #79c347;
            margin-right: 12px;
            font-size: 16px;
        }

        .btn-learn-more {
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-learn-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(121, 195, 71, 0.4);
            color: white;
        }

        /* Pricing Section */
        .pricing-section {
            padding: 100px 0;
            background: white;
        }

        .pricing-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            height: 100%;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .pricing-card.featured {
            border-color: #79c347;
            transform: scale(1.05);
            box-shadow: 0 12px 40px rgba(121, 195, 71, 0.2);
        }

        .pricing-badge {
            position: absolute;
            top: -15px;
            right: 30px;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pricing-name {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .pricing-price {
            font-size: 48px;
            font-weight: 800;
            color: #79c347;
            margin-bottom: 5px;
        }

        .pricing-price span {
            font-size: 18px;
            color: #6c757d;
            font-weight: 500;
        }

        .pricing-description {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 30px;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin-bottom: 30px;
        }

        .pricing-features li {
            padding: 12px 0;
            font-size: 15px;
            color: #495057;
            display: flex;
            align-items: flex-start;
        }

        .pricing-features li i {
            color: #79c347;
            margin-right: 12px;
            margin-top: 3px;
            font-size: 18px;
        }

        .btn-pricing {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-pricing-primary {
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            color: white;
        }

        .btn-pricing-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(121, 195, 71, 0.4);
        }

        .btn-pricing-outline {
            background: white;
            color: #79c347;
            border: 2px solid #79c347;
        }

        .btn-pricing-outline:hover {
            background: #79c347;
            color: white;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 100px 0;
            background: #f8f9fa;
        }

        .testimonial-card {
            background: white;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .testimonial-stars {
            color: #ffc107;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .testimonial-text {
            font-size: 15px;
            color: #495057;
            line-height: 1.8;
            margin-bottom: 25px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-right: 15px;
        }

        .testimonial-info h5 {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .testimonial-info p {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #79c347 0%, #5fa732 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .cta-subtitle {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-cta {
            background: white;
            color: #79c347;
            padding: 18px 45px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
            color: #5fa732;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #5fa732 0%, #4a8a28 100%);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-brand {
            font-size: 28px;
            font-weight: 800;
            color: white;
            margin-bottom: 15px;
        }

        .footer-description {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .footer-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #79c347;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-icons a:hover {
            background: #79c347;
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 50px;
            padding-top: 30px;
            text-align: center;
        }

        .footer-bottom p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title {
                font-size: 42px;
            }

            .section-title {
                font-size: 36px;
            }

            .cta-title {
                font-size: 38px;
            }

            .pricing-card.featured {
                transform: scale(1);
            }
        }

        @media (max-width: 767px) {
            .hero-section {
                padding: 80px 0 60px;
            }

            .hero-title {
                font-size: 34px;
            }

            .hero-subtitle {
                font-size: 17px;
            }

            .hero-buttons .btn {
                display: block;
                width: 100%;
                margin-right: 0;
            }

            .section-title {
                font-size: 30px;
            }

            .features-section,
            .pricing-section,
            .testimonials-section,
            .cta-section {
                padding: 60px 0;
            }

            .cta-title {
                font-size: 30px;
            }

            .cta-subtitle {
                font-size: 17px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-graduation-cap me-2"></i>Elimu
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="#demo" class="btn btn-demo">
                            <i class="fas fa-play-circle me-2"></i>Request Demo
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge fade-in">
                        <i class="fas fa-check-circle me-2"></i>Trusted by 500+ Schools Across Kenya
                    </div>
                    <h1 class="hero-title fade-in">Simple, Powerful Tools for Modern Schools</h1>
                    <p class="hero-subtitle fade-in">Streamline financial management and academic performance tracking with our cloud-based solutions. Save time, reduce errors, and focus on what matters most.</p>
                    <div class="hero-buttons fade-in">
                        <button class="btn btn-primary-hero">
                            <i class="fas fa-rocket me-2"></i>Request Demo
                        </button>
                        <button class="btn btn-outline-hero">
                            <i class="fas fa-dollar-sign me-2"></i>View Pricing
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 hero-image mt-5 mt-lg-0">
                    <div class="hero-placeholder fade-in">
                        <i class="fas fa-school"></i>
                        <h4 class="mt-3">Dashboard Preview</h4>
                        <p class="mb-0">Interactive School Management Interface</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Our Solutions</span>
                <h2 class="section-title">Two Powerful Systems, One Platform</h2>
                <p class="section-subtitle">Everything you need to manage your school's finances and academic performance efficiently</p>
            </div>

            <div class="row g-4">
                <!-- Accounting System -->
                <div class="col-lg-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3 class="feature-title">Smart School Accounting System</h3>
                        <p class="feature-description">Automate your school's financial operations and eliminate manual errors with our intelligent accounting platform.</p>
                        
                        <ul class="feature-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Automated fee collection and tracking
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Real-time expense and income monitoring
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Instant ledgers and financial reports
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                SMS payment reminders to parents
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Cloud-based secure data storage
                            </li>
                        </ul>

                        <a href="#pricing" class="btn-learn-more">
                            <i class="fas fa-arrow-right me-2"></i>Learn More
                        </a>
                    </div>
                </div>

                <!-- Performance System -->
                <div class="col-lg-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Smart Performance & Report System</h3>
                        <p class="feature-description">Transform academic data into actionable insights with automated grading, reporting, and performance analytics.</p>
                        
                        <ul class="feature-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Automated student performance analysis
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Instant report card generation
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Visual progress tracking and analytics
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Teacher and parent dashboards
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Grade automation and ranking
                            </li>
                        </ul>

                        <a href="#pricing" class="btn-learn-more">
                            <i class="fas fa-arrow-right me-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Pricing Plans</span>
                <h2 class="section-title">Affordable Plans for Every School</h2>
                <p class="section-subtitle">Choose the system that fits your needs, or save more with our bundle offer</p>
            </div>

            <div class="row g-4">
                <!-- Accounting System Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card fade-in">
                        <h3 class="pricing-name">Accounting System</h3>
                        <div class="pricing-price">KES 150<span>/student/term</span></div>
                        <p class="pricing-description">Complete financial management for your school</p>
                        
                        <ul class="pricing-features">
                            <li>
                                <i class="fas fa-check"></i>
                                Fee collection & tracking
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Expense management
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Financial reports & ledgers
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                SMS payment reminders
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Cloud storage & backup
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Email support
                            </li>
                        </ul>

                        <button class="btn btn-pricing btn-pricing-outline">
                            Get Started
                        </button>
                    </div>
                </div>

                <!-- Bundle Plan (Featured) -->
                <div class="col-lg-4">
                    <div class="pricing-card featured fade-in">
                        <span class="pricing-badge">Best Value</span>
                        <h3 class="pricing-name">Complete Bundle</h3>
                        <div class="pricing-price">KES 239<span>/student/term</span></div>
                        <p class="pricing-description">Both systems at a discounted rate</p>
                        
                        <ul class="pricing-features">
                            <li>
                                <i class="fas fa-check"></i>
                                Everything in Accounting System
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Everything in Performance System
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <strong>Save KES 61 per student</strong>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Priority support
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Free training sessions
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Dedicated account manager
                            </li>
                        </ul>

                        <button class="btn btn-pricing btn-pricing-primary">
                            Get Started
                        </button>
                    </div>
                </div>

                <!-- Performance System Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card fade-in">
                        <h3 class="pricing-name">Performance System</h3>
                        <div class="pricing-price">KES 150<span>/student/term</span></div>
                        <p class="pricing-description">Advanced academic tracking and reporting</p>
                        
                        <ul class="pricing-features">
                            <li>
                                <i class="fas fa-check"></i>
                                Performance analysis
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Automated report cards
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Progress visualization
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Teacher & parent portals
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Grade automation
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Email support
                            </li>
                        </ul>

                        <button class="btn btn-pricing btn-pricing-outline">
                            Get Started
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Testimonials</span>
                <h2 class="section-title">Loved by School Administrators</h2>
                <p class="section-subtitle">See what schools are saying about Elimu</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="testimonial-card fade-in">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Elimu has transformed how we manage school finances. What used to take hours now takes minutes. The automated reports are incredibly accurate and save us so much time!"</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">MK</div>
                            <div class="testimonial-info">
                                <h5>Mary Kamau</h5>
                                <p>School Accountant, St. Mary's Academy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="testimonial-card fade-in">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"The performance tracking system is a game-changer. We can now identify struggling students early and provide targeted support. Parents love the real-time updates!"</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">JO</div>
                            <div class="testimonial-info">
                                <h5>James Omondi</h5>
                                <p>Principal, Greenfield High School</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="testimonial-card fade-in">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Best investment we've made! The bundle plan gives us everything we need at an affordable price. Customer support is excellent, always quick to help when needed."</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">SA</div>
                            <div class="testimonial-info">
                                <h5>Sarah Achieng</h5>
                                <p>Director, Nairobi Academy</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="demo">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title fade-in">Join 500+ Schools Using Elimu Today</h2>
                <p class="cta-subtitle fade-in">Start your free demo and see how we can transform your school's operations in just minutes</p>
                <button class="btn btn-cta fade-in">
                    <i class="fas fa-rocket me-2"></i>Start Free Demo
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="footer-brand">
                        <i class="fas fa-graduation-cap me-2"></i>Elimu
                    </h3>
                    <p class="footer-description">We are a Kenya-based EdTech company helping schools streamline financial and academic management through simple, powerful cloud-based solutions.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h4 class="footer-title">Product</h4>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#demo">Request Demo</a></li>
                        <li><a href="#">Case Studies</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h4 class="footer-title">Support</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Training</a></li>
                        <li><a href="#">System Status</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h4 class="footer-title">Contact</h4>
                    <ul class="footer-links">
                        <li><a href="mailto:info@elimu.co.ke">info@elimu.co.ke</a></li>
                        <li><a href="tel:+254700000000">+254 700 000 000</a></li>
                        <li><a href="#">Nairobi, Kenya</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 Elimu. All rights reserved. | <a href="#" style="color: #79c347; text-decoration: none;">Privacy Policy</a> | <a href="#" style="color: #79c347; text-decoration: none;">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Fade-in animation on scroll
        const fadeElements = document.querySelectorAll('.fade-in');

        const checkFade = () => {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementBottom = element.getBoundingClientRect().bottom;
                
                if (elementTop < window.innerHeight - 100 && elementBottom > 0) {
                    element.classList.add('visible');
                }
            });
        };

        window.addEventListener('scroll', checkFade);
        window.addEventListener('load', checkFade);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile menu close on link click
        const navLinks = document.querySelectorAll('.nav-link');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });
    </script>

</body>
</html><?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/welcome.blade.php ENDPATH**/ ?>
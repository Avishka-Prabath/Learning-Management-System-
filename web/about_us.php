<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EduMart</title>
    
    <!-- Bootstrap 5 CDN & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <?php include('navbar.php'); ?>

    <!-- HERO SECTION -->
    <section class="position-relative text-white py-5" style="background: linear-gradient(135deg, rgba(11, 26, 81, 0.95) 0%, rgba(0, 86, 179, 0.9) 100%), url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">
        <div class="container py-4 text-center">
            <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill mb-3">🚀 Learn About EduMart</span>
            <h1 class="display-4 fw-bold mb-3 text-white">Empowering Future Innovators</h1>
            <p class="lead mb-0 opacity-90 mx-auto" style="max-width: 680px;">We are on a mission to deliver world-class, accessible, and practical online education to students across Sri Lanka and beyond.</p>
        </div>
    </section>

    <!-- MAIN ZIG-ZAG LAYOUT CONTAINER -->
    <div class="container py-5 flex-grow-1">
        
        <!-- SECTION 1: WHO WE ARE (Left Image, Right Text) -->
        <section class="row align-items-center g-5 py-4 mb-4">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80" alt="EduMart Campus" class="feature-img">
            </div>
            <div class="col-lg-6">
                <h2 class="about-section-title">Who We Are</h2>
                <p class="lead-text">
                    Founded with a vision to revolutionize digital learning, <strong>EduMart</strong> stands as a premier online learning ecosystem in Sri Lanka. We bridge the critical gap between traditional academic theory and the fast-evolving requirements of modern global industries.
                </p>
                <p class="lead-text mb-0">
                    By bringing together seasoned industry practitioners, senior software engineers, and creative leads, EduMart offers an immersive educational experience designed to foster true innovation and practical expertise for students and working professionals alike.
                </p>
            </div>
        </section>

        <!-- SECTION 2: MISSION & VISION (Left Text, Right Image) -->
        <section class="row align-items-center g-5 py-4 mb-4 flex-column-reverse flex-lg-row">
            <div class="col-lg-6">
                <div class="mb-4">
                    <h2 class="about-section-title">Our Mission</h2>
                    <p class="lead-text mb-0">
                        Our mission is to democratize high-grade technical and business education by delivering structured, practical, and highly engaging online courses. We aim to break geographic and financial barriers, equipping learners across Sri Lanka and South Asia with industry-relevant skills and verified certifications.
                    </p>
                </div>
                <div>
                    <h2 class="about-section-title">Our Vision</h2>
                    <p class="lead-text mb-0">
                        To become South Asia's leading digital education platform, recognized globally for producing highly skilled, career-ready professionals, innovators, and leaders who drive technological progress.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80" alt="Mission and Vision" class="feature-img">
            </div>
        </section>

        <!-- SECTION 3: INTERNATIONAL RECOGNITION (Left Image, Right Text) -->
        <section class="row align-items-center g-5 py-4 mb-4">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80" alt="International Recognition" class="feature-img">
            </div>
            <div class="col-lg-6">
                <h2 class="about-section-title">International Recognition & Standards</h2>
                <p class="lead-text">
                    At EduMart, quality and global relevance are at the heart of our curriculum. Our programs are designed in alignment with international educational standards and global technology skill benchmarks.
                </p>
                <p class="lead-text">
                    We collaborate with leading tech enterprises to ensure course content reflects real-world job needs. EduMart certifications hold credentials recognized by top-tier employers globally.
                </p>
                <ul class="lead-text ps-3 mb-0">
                    <li class="mb-2"><strong>Global Industry Alignment:</strong> Continuously updated to reflect real-world tech stacks.</li>
                    <li class="mb-2"><strong>ISO Standards Integration:</strong> Strict quality assurance in digital delivery.</li>
                    <li><strong>Portfolio Readiness:</strong> Real-world projects that serve as verifiable portfolios.</li>
                </ul>
            </div>
        </section>

        <!-- SECTION 4: FACULTIES (Left Text, Right Image) -->
        <section class="row align-items-center g-5 py-4 mb-4 flex-column-reverse flex-lg-row">
            <div class="col-lg-6">
                <h2 class="about-section-title">Our Academic Faculties</h2>
                <p class="lead-text mb-4">
                    EduMart operates through specialized academic divisions led by experienced industry mentors and domain experts:
                </p>
                <div class="mb-3">
                    <h5 class="fw-bold text-dark"><i class="bi bi-laptop text-primary me-2"></i> Faculty of Computing</h5>
                    <p class="lead-text mb-0">Full-Stack Web Development, Mobile Architecture, Artificial Intelligence, and Cloud Computing.</p>
                </div>
                <div class="mb-3">
                    <h5 class="fw-bold text-dark"><i class="bi bi-palette text-success me-2"></i> Faculty of Design & Media</h5>
                    <p class="lead-text mb-0">UI/UX Product Design, Systems Prototyping, Brand Strategy, and Digital Media.</p>
                </div>
                <div>
                    <h5 class="fw-bold text-dark"><i class="bi bi-graph-up-arrow text-warning me-2"></i> Faculty of Business</h5>
                    <p class="lead-text mb-0">Digital Marketing, Business Analytics, Project Management, and Tech Entrepreneurship.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80" alt="EduMart Faculties" class="feature-img">
            </div>
        </section>

        <!-- SECTION 5: BOARD OF DIRECTORS -->
        <div class="pt-4 mb-4">
            <div class="text-center mb-5">
                <h2 class="about-section-title d-inline-block">Board of Directors & Leadership</h2>
                <p class="lead-text mt-2 mx-auto" style="max-width: 700px;">Our leadership team comprises visionary educators, technology veterans, and academic administrators.</p>
            </div>

            <!-- Director 1 -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden director-card">
                <div class="card-body p-4">
                    <div class="row align-items-center g-4">
                        <div class="col-md-4 col-lg-3 text-center">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80" alt="Prof. Anura Wijesinghe" class="director-img-side">
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <h4 class="fw-bold text-dark mb-1">Prof. Anura Wijesinghe</h4>
                            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Chairman & Managing Director</span>
                            <p class="lead-text mb-0">
                                With over 25 years of experience in higher education administration and university leadership, Prof. Wijesinghe guides EduMart's strategic vision, institutional partnerships, and academic quality frameworks across all regional divisions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Director 2 -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden director-card">
                <div class="card-body p-4">
                    <div class="row align-items-center g-4">
                        <div class="col-md-4 col-lg-3 text-center">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80" alt="Dr. Maheshika Perera" class="director-img-side">
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <h4 class="fw-bold text-dark mb-1">Dr. Maheshika Perera</h4>
                            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Director of Academic Affairs</span>
                            <p class="lead-text mb-0">
                                A renowned scholar in curriculum design and educational technology, Dr. Perera oversees academic standards, faculty development, and the alignment of EduMart programs with international accreditation guidelines.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Director 3 -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden director-card">
                <div class="card-body p-4">
                    <div class="row align-items-center g-4">
                        <div class="col-md-4 col-lg-3 text-center">
                            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=400&q=80" alt="Kavinda Fernando" class="director-img-side">
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <h4 class="fw-bold text-dark mb-1">Kavinda Fernando</h4>
                            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Chief Technology Officer (CTO)</span>
                            <p class="lead-text mb-0">
                                A seasoned tech entrepreneur and software architect, Kavinda leads the development of EduMart's digital learning infrastructure, ensuring a seamless, scalable, and interactive platform experience for thousands of active learners.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <?php include('footer.php'); ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
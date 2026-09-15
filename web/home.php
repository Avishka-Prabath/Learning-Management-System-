<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduMart - Campus & Learning Portal</title>
    
    <!-- Bootstrap 5 CDN & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Separate Dynamic Navbar Included Here -->
    <?php include('navbar.php'); ?>

    <!-- HERO SECTION (Dynamic Background Carousel) -->
    <section class="hero-section position-relative overflow-hidden text-white" style="background: linear-gradient(135deg, #0b1a51 0%, #0056b3 50%, #00d2ff 100%); min-height: 80vh;">
        <div class="container-fluid px-lg-5 h-100">
            <div class="row align-items-center min-vh-75 py-5">
                
                <!-- Left Text Content -->
                <div class="col-lg-6 py-4 z-2">
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill fw-semibold mb-3">🎓 Higher Education Campus</span>
                    <h1 class="display-3 fw-bold mb-3 text-white">Advance Your Qualification With A Degree</h1>
                    <p class="lead opacity-90 mb-4" style="max-width: 540px;">Discover world-class academic programs, modern labs, and expert mentorship designed to fast-track your global tech & business career.</p>
                    <div class="d-flex gap-3 flex-wrap align-items-center">
                        <a href="course.php" class="btn btn-light btn-lg text-primary fw-bold rounded-pill px-4 shadow">+ ENQUIRE NOW</a>
                        <a href="about_us.php" class="btn btn-outline-light btn-lg fw-semibold rounded-pill px-4">Explore Campus</a>
                    </div>
                </div>

                <!-- Right Angled Changing Image Carousel -->
                <div class="col-lg-6 p-0 position-relative h-100 d-none d-lg-block">
                    <div id="heroImageCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner h-100 shadow-lg" style="clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%); min-height: 520px;">
                            
                            <!-- Slide 1 -->
                            <div class="carousel-item active h-100">
                                <div class="w-100 h-100" style="min-height: 520px; background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;"></div>
                            </div>
                            
                            <!-- Slide 2 -->
                            <div class="carousel-item h-100">
                                <div class="w-100 h-100" style="min-height: 520px; background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;"></div>
                            </div>
                            
                            <!-- Slide 3 -->
                            <div class="carousel-item h-100">
                                <div class="w-100 h-100" style="min-height: 520px; background: url('https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;"></div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- STATS COUNTER BAR -->
    <section class="py-4 bg-white border-bottom shadow-sm">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary mb-0">15+</h2>
                    <small class="text-muted fw-semibold">Years Experience</small>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary mb-0">50+</h2>
                    <small class="text-muted fw-semibold">Degree Programs</small>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary mb-0">10,000+</h2>
                    <small class="text-muted fw-semibold">Graduated Students</small>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fw-bold text-primary mb-0">98%</h2>
                    <small class="text-muted fw-semibold">Employment Rate</small>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT US HIGHLIGHT SECTION -->
    <section class="py-5 bg-white border-bottom">
        <div class="container py-3">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="EduMart Campus Life" class="img-fluid rounded-4 shadow-sm border border-3 border-white">
                        <div class="position-absolute bottom-0 start-0 m-3 bg-white p-3 rounded-3 shadow-lg d-none d-sm-flex align-items-center gap-3">
                            <div class="bg-primary text-white p-3 rounded-circle fs-4">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">UGC Approved Degrees</h6>
                                <small class="text-muted">Global Recognition & Standards</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Why Choose EduMart</span>
                    <h2 class="fw-bold mb-3 text-dark">Building Modern Skills For Global Industry Success</h2>
                    <p class="text-muted mb-4">At EduMart Campus, we combine academic rigor with practical hands-on technical labs. We empower our students with industry-relevant qualifications and personal guidance from top-tier mentors.</p>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                <span class="fw-semibold text-dark">Modern High-Tech Labs</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                <span class="fw-semibold text-dark">Global Degree Pathways</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                <span class="fw-semibold text-dark">100% Internship Placement</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                <span class="fw-semibold text-dark">Flexible Payment Plans</span>
                            </div>
                        </div>
                    </div>

                    <a href="about_us.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">Read More About Us <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW SECTION 1: CAMPUS LEADERSHIP MESSAGE -->
    <section class="py-5 bg-light border-bottom">
        <div class="container py-3">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Leadership & Vision</span>
                <h2 class="fw-bold text-dark">Message From Campus Dean</h2>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-4 text-center">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80" alt="Campus Dean" class="img-fluid rounded-4 shadow" style="max-height: 320px; object-fit: cover;">
                        <h5 class="fw-bold mt-3 mb-0 text-dark">Prof. Arthur Kingsley</h5>
                        <small class="text-primary fw-semibold">Vice Chancellor & Campus Dean</small>
                    </div>
                    <div class="col-md-8">
                        <i class="bi bi-quote text-primary opacity-25 display-1 d-block mb-n3"></i>
                        <h4 class="fw-bold text-dark mb-3">"Empowering the next generation with practical skills and ethical leadership."</h4>
                        <p class="text-muted leading-relaxed">
                            Welcome to EduMart Campus. Our primary goal is to bridge the gap between academic theory and real-world industrial demands. Through cutting-edge curricula, hands-on lab projects, and partnerships with leading tech corporations, we ensure that every student leaves our doors fully prepared to make an impact globally.
                        </p>
                        <div class="d-flex gap-3 align-items-center mt-4">
                            <div class="border-start border-3 border-primary ps-3">
                                <span class="fw-bold d-block text-dark">EduMart Executive Board</span>
                                <small class="text-muted">Higher Education Commission Board Member</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- POPULAR COURSES SECTION (With Interactive Details Modal) -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Academic Programs</span>
                <h2 class="fw-bold text-dark">Explore Popular Degrees & Courses</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Choose from industry-recognized diploma, undergraduate, and master programs.</p>
            </div>

            <div class="row g-4">
                
                <!-- Course 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Software Engineering" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge bg-primary bg-opacity-10 text-primary w-auto me-auto px-3 py-2 rounded-pill mb-2">Computing & IT</span>
                            <h5 class="fw-bold mb-2">BSc (Hons) in Software Engineering</h5>
                            <p class="text-muted small flex-grow-1">Master software architecture, web development, mobile apps, and cloud integration.</p>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small fw-semibold">
                                    <span><i class="bi bi-clock me-1 text-primary"></i> 3 Years</span>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#courseModal1">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Business MBA" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge bg-success bg-opacity-10 text-success w-auto me-auto px-3 py-2 rounded-pill mb-2">Management</span>
                            <h5 class="fw-bold mb-2">Master of Business Administration (MBA)</h5>
                            <p class="text-muted small flex-grow-1">Empower your executive leadership, marketing strategy, and financial management skills.</p>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small fw-semibold">
                                    <span><i class="bi bi-clock me-1 text-primary"></i> 1 Year</span>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#courseModal2">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="International Business" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge bg-warning bg-opacity-10 text-warning text-dark w-auto me-auto px-3 py-2 rounded-pill mb-2">Business</span>
                            <h5 class="fw-bold mb-2">BBA in International Business</h5>
                            <p class="text-muted small flex-grow-1">Gain deep exposure to global trade standards, supply chains, and enterprise leadership.</p>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small fw-semibold">
                                    <span><i class="bi bi-clock me-1 text-primary"></i> 3 Years</span>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#courseModal3">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- NEW SECTION 2: LATEST NEWS & ANNOUNCEMENTS -->
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Latest News</span>
                    <h2 class="fw-bold text-dark mb-0">News & Campus Events</h2>
                </div>
                <a href="#" class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-semibold d-none d-md-inline-block">View All News</a>
            </div>

            <div class="row g-4">
                <!-- News Item 1 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Tech Symposium" style="height: 180px; object-fit: cover;">
                        <div class="card-body p-4">
                            <small class="text-primary fw-semibold"><i class="bi bi-calendar-event me-1"></i> Oct 24, 2026</small>
                            <h6 class="fw-bold text-dark mt-2 mb-2">Annual EduMart Tech Innovation Hackathon Announced</h6>
                            <p class="text-muted small mb-0">Students will compete to solve real-world industry problems with tech and AI solutions.</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 2 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                        <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Graduation Day" style="height: 180px; object-fit: cover;">
                        <div class="card-body p-4">
                            <small class="text-primary fw-semibold"><i class="bi bi-calendar-event me-1"></i> Nov 10, 2026</small>
                            <h6 class="fw-bold text-dark mt-2 mb-2">Grand Convocation Ceremony 2026 Registration Open</h6>
                            <p class="text-muted small mb-0">Over 1,200 graduates will receive their degrees at the BMICH Main Convention Center.</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 3 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-card">
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="UK Partnership" style="height: 180px; object-fit: cover;">
                        <div class="card-body p-4">
                            <small class="text-primary fw-semibold"><i class="bi bi-calendar-event me-1"></i> Dec 01, 2026</small>
                            <h6 class="fw-bold text-dark mt-2 mb-2">New Foreign University Transfer Pathways Introduced</h6>
                            <p class="text-muted small mb-0">EduMart partners with leading UK & Australian universities for final year study abroad programs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW SECTION 3: STUDENT RATINGS & TESTIMONIALS -->
    <section class="py-5 bg-light border-bottom">
        <div class="container py-3">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Student Reviews</span>
                <h2 class="fw-bold text-dark">What Our Students Say</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Read verified feedback and ratings from our undergraduates and alumni.</p>
            </div>

            <div class="row g-4">
                <!-- Rating 1 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80" alt="Student" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Nipuni Perera</h6>
                                <small class="text-muted">BSc Software Engineering</small>
                            </div>
                        </div>
                        <div class="text-warning mb-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="text-dark fw-bold ms-1">5.0</span>
                        </div>
                        <p class="text-muted small mb-0">"The hands-on labs and lecturer guidance helped me secure a software developer internship right in my 2nd year. Best campus environment!"</p>
                    </div>
                </div>

                <!-- Rating 2 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80" alt="Student" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Kasun Fernando</h6>
                                <small class="text-muted">MBA Graduate</small>
                            </div>
                        </div>
                        <div class="text-warning mb-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="text-dark fw-bold ms-1">4.8</span>
                        </div>
                        <p class="text-muted small mb-0">"Extremely flexible weekend lecture schedule for corporate professionals. The MBA faculty is top-notch with practical industry insights."</p>
                    </div>
                </div>

                <!-- Rating 3 -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" alt="Student" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Dilini Silva</h6>
                                <small class="text-muted">BBA International Business</small>
                            </div>
                        </div>
                        <div class="text-warning mb-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="text-dark fw-bold ms-1">5.0</span>
                        </div>
                        <p class="text-muted small mb-0">"State-of-the-art campus facilities and supportive staff. The international pathways opened great doors for my career."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INSTRUCTORS HIGHLIGHT SECTION (With Interactive Experience Modal) -->
    <section class="py-5 bg-white border-top">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Experienced Faculty</span>
                <h2 class="fw-bold text-dark">Meet Our Expert Lecturers</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Click on any instructor card to view their credentials and industry experience.</p>
            </div>

            <div class="row g-4">
                
                <!-- Instructor 1 -->
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="p-4 bg-light rounded-4 h-100 shadow-sm border course-card d-flex flex-column align-items-center">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80" class="rounded-circle mb-3 border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;" alt="Dr. Sarah">
                        <h6 class="fw-bold mb-1 text-dark">Dr. Sarah Jenkins</h6>
                        <small class="text-primary fw-semibold mb-2">Head of Computing</small>
                        <p class="text-muted small mb-3">Ph.D. in Computer Science | 12+ Years Industry & Research Experience</p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill mt-auto fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#teacherModal1">View Experience</button>
                    </div>
                </div>

                <!-- Instructor 2 -->
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="p-4 bg-light rounded-4 h-100 shadow-sm border course-card d-flex flex-column align-items-center">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80" class="rounded-circle mb-3 border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;" alt="Prof. David">
                        <h6 class="fw-bold mb-1 text-dark">Prof. David Miller</h6>
                        <small class="text-primary fw-semibold mb-2">Senior Business Mentor</small>
                        <p class="text-muted small mb-3">MBA (UK) | 15+ Years Corporate Strategic Management</p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill mt-auto fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#teacherModal2">View Experience</button>
                    </div>
                </div>

                <!-- Instructor 3 -->
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="p-4 bg-light rounded-4 h-100 shadow-sm border course-card d-flex flex-column align-items-center">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80" class="rounded-circle mb-3 border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;" alt="Dr. Emily">
                        <h6 class="fw-bold mb-1 text-dark">Dr. Emily Watson</h6>
                        <small class="text-primary fw-semibold mb-2">Data Science Specialist</small>
                        <p class="text-muted small mb-3">MSc in AI & Analytics | 8+ Years Data Engineer & Consultant</p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill mt-auto fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#teacherModal3">View Experience</button>
                    </div>
                </div>

                <!-- Instructor 4 -->
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="p-4 bg-light rounded-4 h-100 shadow-sm border course-card d-flex flex-column align-items-center">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80" class="rounded-circle mb-3 border border-3 border-primary" style="width: 100px; height: 100px; object-fit: cover;" alt="Mr. Robert">
                        <h6 class="fw-bold mb-1 text-dark">Mr. Robert Chen</h6>
                        <small class="text-primary fw-semibold mb-2">Software Engineering Lead</small>
                        <p class="text-muted small mb-3">Ex-Google Engineer | Full Stack & Cloud Specialist</p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill mt-auto fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#teacherModal4">View Experience</button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= COURSE MODALS ================= -->

    <!-- Course 1 Modal -->
    <div class="modal fade" id="courseModal1" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 bg-primary text-white p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold">BSc (Hons) in Software Engineering</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-primary">Course Overview</h6>
                    <p class="text-muted small">This degree program provides comprehensive training in software engineering, cloud architecture, web applications, and database management. Designed in collaboration with leading IT firms to ensure high employability.</p>
                    
                    <h6 class="fw-bold text-primary mt-3">Modules Covered</h6>
                    <ul class="text-muted small">
                        <li>Object-Oriented Programming (Java / C#)</li>
                        <li>Full-Stack Web Development (PHP, React, Node)</li>
                        <li>Database Systems & SQL Optimization</li>
                        <li>Cloud Computing & DevOps Pipelines</li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded-3">
                        <div>
                            <small class="text-muted d-block">Duration & Mode</small>
                            <strong class="text-dark">3 Years (Full-Time)</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Course Investment</small>
                            <strong class="text-primary fs-5">LKR 450,000</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="../student/register.php" class="btn btn-primary rounded-pill px-4 fw-bold">Apply Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Course 2 Modal -->
    <div class="modal fade" id="courseModal2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 bg-success text-white p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold">Master of Business Administration (MBA)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-success">Course Overview</h6>
                    <p class="text-muted small">Tailored for working professionals looking to transition into top executive leadership positions. Learn modern management skills, financial analysis, marketing strategies, and global business operations.</p>
                    
                    <h6 class="fw-bold text-success mt-3">Modules Covered</h6>
                    <ul class="text-muted small">
                        <li>Strategic Leadership & Corporate Governance</li>
                        <li>Financial Management & Investment Analysis</li>
                        <li>Digital Marketing & Brand Positioning</li>
                        <li>Global Supply Chain & Risk Assessment</li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded-3">
                        <div>
                            <small class="text-muted d-block">Duration & Mode</small>
                            <strong class="text-dark">1 Year (Part-Time / Weekend)</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Course Investment</small>
                            <strong class="text-success fs-5">LKR 550,000</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="../student/register.php" class="btn btn-success rounded-pill px-4 fw-bold text-white">Apply Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Course 3 Modal -->
    <div class="modal fade" id="courseModal3" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 bg-warning text-dark p-4 rounded-top-4">
                    <h5 class="modal-title fw-bold">BBA in International Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-dark">Course Overview</h6>
                    <p class="text-muted small">Equips students with global enterprise leadership skills. Covers cross-border trade policies, international negotiations, foreign exchange operations, and market expansion tactics.</p>
                    
                    <h6 class="fw-bold text-dark mt-3">Modules Covered</h6>
                    <ul class="text-muted small">
                        <li>International Economics & Trade Agreements</li>
                        <li>Cross-Cultural Communication & Negotiations</li>
                        <li>E-Commerce & Digital Enterprise Models</li>
                        <li>Entrepreneurship & Venture Creation</li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded-3">
                        <div>
                            <small class="text-muted d-block">Duration & Mode</small>
                            <strong class="text-dark">3 Years (Full-Time)</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Course Investment</small>
                            <strong class="text-dark fs-5">LKR 420,000</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="../student/register.php" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Apply Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TEACHER MODALS ================= -->

    <!-- Teacher 1 Modal -->
    <div class="modal fade" id="teacherModal1" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 p-3">
                <div class="text-center pt-3">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80" class="rounded-circle border border-3 border-primary mb-3" style="width: 110px; height: 110px; object-fit: cover;" alt="Dr. Sarah">
                    <h5 class="fw-bold mb-1 text-dark">Dr. Sarah Jenkins</h5>
                    <p class="text-primary fw-semibold small mb-3">Head of Computing Faculty</p>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-dark small"><i class="bi bi-briefcase me-2 text-primary"></i>Experience</h6>
                    <p class="text-muted small">12+ years of teaching software architecture and artificial intelligence. Published over 15 research papers in IEEE journals.</p>
                    
                    <h6 class="fw-bold text-dark small"><i class="bi bi-mortarboard me-2 text-primary"></i>Qualifications</h6>
                    <p class="text-muted small">Ph.D. in Computer Science (University of London), MSc in Software Engineering.</p>

                    <h6 class="fw-bold text-dark small"><i class="bi bi-star me-2 text-primary"></i>Key Expertise</h6>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary">Java & C#</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">System Architecture</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Cloud Computing</span>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light w-100 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher 2 Modal -->
    <div class="modal fade" id="teacherModal2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 p-3">
                <div class="text-center pt-3">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80" class="rounded-circle border border-3 border-primary mb-3" style="width: 110px; height: 110px; object-fit: cover;" alt="Prof. David">
                    <h5 class="fw-bold mb-1 text-dark">Prof. David Miller</h5>
                    <p class="text-primary fw-semibold small mb-3">Senior Business Mentor</p>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-dark small"><i class="bi bi-briefcase me-2 text-primary"></i>Experience</h6>
                    <p class="text-muted small">15+ years experience as Senior Vice President of Strategic Growth in corporate enterprise sectors across Southeast Asia.</p>
                    
                    <h6 class="fw-bold text-dark small"><i class="bi bi-mortarboard me-2 text-primary"></i>Qualifications</h6>
                    <p class="text-muted small">MBA (UK), Certified Corporate Advisor & Strategist.</p>

                    <h6 class="fw-bold text-dark small"><i class="bi bi-star me-2 text-primary"></i>Key Expertise</h6>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary">Executive Leadership</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Global Trade</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Risk Advisory</span>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light w-100 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher 3 Modal -->
    <div class="modal fade" id="teacherModal3" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 p-3">
                <div class="text-center pt-3">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80" class="rounded-circle border border-3 border-primary mb-3" style="width: 110px; height: 110px; object-fit: cover;" alt="Dr. Emily">
                    <h5 class="fw-bold mb-1 text-dark">Dr. Emily Watson</h5>
                    <p class="text-primary fw-semibold small mb-3">Data Science Specialist</p>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-dark small"><i class="bi bi-briefcase me-2 text-primary"></i>Experience</h6>
                    <p class="text-muted small">8+ years of experience leading Big Data pipelines, AI models, and machine learning research projects.</p>
                    
                    <h6 class="fw-bold text-dark small"><i class="bi bi-mortarboard me-2 text-primary"></i>Qualifications</h6>
                    <p class="text-muted small">Ph.D. in Data Analytics, MSc in Machine Learning (Australia).</p>

                    <h6 class="fw-bold text-dark small"><i class="bi bi-star me-2 text-primary"></i>Key Expertise</h6>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary">Python & AI</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Machine Learning</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Data Visualization</span>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light w-100 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher 4 Modal -->
    <div class="modal fade" id="teacherModal4" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 p-3">
                <div class="text-center pt-3">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80" class="rounded-circle border border-3 border-primary mb-3" style="width: 110px; height: 110px; object-fit: cover;" alt="Mr. Robert">
                    <h5 class="fw-bold mb-1 text-dark">Mr. Robert Chen</h5>
                    <p class="text-primary fw-semibold small mb-3">Software Engineering Lead</p>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-dark small"><i class="bi bi-briefcase me-2 text-primary"></i>Experience</h6>
                    <p class="text-muted small">Ex-Google Senior Software Engineer with 10+ years hands-on experience building microservice applications.</p>
                    
                    <h6 class="fw-bold text-dark small"><i class="bi bi-mortarboard me-2 text-primary"></i>Qualifications</h6>
                    <p class="text-muted small">BSc (Hons) Computer Engineering, Certified AWS Cloud Architect.</p>

                    <h6 class="fw-bold text-dark small"><i class="bi bi-star me-2 text-primary"></i>Key Expertise</h6>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary">React & Node.js</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Docker & Kubernetes</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">AWS Cloud</span>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light w-100 rounded-pill fw-semibold" data-bs-dismiss="modal">Close</button>
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
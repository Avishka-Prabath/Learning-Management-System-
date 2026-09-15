<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - EduMart</title>
    
    <!-- Bootstrap 5 CDN & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <?php include('navbar.php'); ?>

    <!-- HERO SECTION -->
    <section class="position-relative text-white py-5" style="background: linear-gradient(135deg, rgba(11, 26, 81, 0.95) 0%, rgba(0, 86, 179, 0.9) 100%), url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">
        <div class="container py-4 text-center">
            <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill mb-3">💬 24/7 Support</span>
            <h1 class="display-4 fw-bold mb-3 text-white">Get in Touch with Us</h1>
            <p class="lead mb-0 opacity-90 mx-auto" style="max-width: 650px;">Have questions about our platform or course enrollments? We are here to help!</p>
        </div>
    </section>

    <!-- CONTACT FORM & INFO SECTION -->
    <div class="container py-5 flex-grow-1">
        <div class="row g-4 mb-5">
            
            <!-- Contact Cards Sidebar -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h4 class="fw-bold mb-4 text-dark">Contact Details</h4>
                    
                    <!-- Location -->
                    <div class="d-flex gap-3 align-items-center mb-4">
                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(79, 70, 229, 0.12) !important;">
                            <i class="bi bi-geo-alt-fill fs-4" style="color: #4f46e5 !important;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Our Location</h6>
                            <p class="text-muted small mb-0">No. 123, Education Way, Colombo 03, Sri Lanka</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="d-flex gap-3 align-items-center mb-4">
                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(79, 70, 229, 0.12) !important;">
                            <i class="bi bi-envelope-fill fs-4" style="color: #4f46e5 !important;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Email Support</h6>
                            <p class="text-muted small mb-0">info@edumart.ac.lk</p>
                        </div>
                    </div>

                    <!-- Hotline -->
                    <div class="d-flex gap-3 align-items-center mb-4">
                        <div class="p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(79, 70, 229, 0.12) !important;">
                            <i class="bi bi-telephone-fill fs-4" style="color: #4f46e5 !important;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Direct Hotline</h6>
                            <p class="text-muted small mb-0">+94 11 7430000 / +94 77 0430000</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-3 border-top">
                        <h6 class="fw-bold mb-2 text-dark">Office Hours</h6>
                        <p class="text-muted small mb-0"><i class="bi bi-clock me-1" style="color: #4f46e5 !important;"></i> Mon - Fri: 8:30 AM - 5:30 PM</p>
                    </div>
                </div>
            </div>

            <!-- Interactive Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <h4 class="fw-bold mb-2 text-dark">Send Us a Message</h4>
                    <p class="text-muted small mb-4">Fill in your information and our support team will reach out within 24 hours.</p>

                    <!-- Form ID: contactForm -->
                    <form id="contactForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name *</label>
                                <input type="text" name="name" id="name" class="form-control rounded-3 py-2 bg-light border-0" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address *</label>
                                <input type="email" name="email" id="email" class="form-control rounded-3 py-2 bg-light border-0" placeholder="name@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Subject *</label>
                                <input type="text" name="subject" id="subject" class="form-control rounded-3 py-2 bg-light border-0" placeholder="Enrollment inquiry..." required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Your Message *</label>
                                <textarea name="message" id="message" class="form-control rounded-3 bg-light border-0" rows="5" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                    Send Message <i class="bi bi-send ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- GOOGLE MAP SECTION -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="p-4 border-bottom">
                        <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-map me-2" style="color: #4f46e5 !important;"></i>Find Us On Google Maps</h5>
                        <p class="text-muted small mb-0">Visit our main campus in Colombo for direct career guidance and counseling.</p>
                    </div>
                    <div class="ratio ratio-21x9" style="min-height: 350px;">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.7981600329437!2d79.85108427499645!3d6.914781993084752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25963120b1509%3A0x2db2c17d354b3bc3!2sColombo%2003%2C%20Colombo!5e0!3m2!1sen!2slk!4v1700000000000!5m2!1sen!2slk" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="ajax/js/contact_validation.js"></script>
</body>
</html>
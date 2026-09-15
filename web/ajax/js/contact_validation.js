jQuery(document).ready(function ($) {

    // SEND / SUBMIT CONTACT MESSAGE
    $("#contactForm").submit(function (event) {
        event.preventDefault();

        if (!$('#name').val() || $('#name').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your full name",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#name').focus();
        } else if (!$('#email').val() || $('#email').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your email address",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#email').focus();
        } else if (!$('#subject').val() || $('#subject').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter the subject",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#subject').focus();
        } else if (!$('#message').val() || $('#message').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your message",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#message').focus();
        } else {

            // Preloader start
            if ($.fn.preloader) {
                $('.someBlock').preloader();
            }

            $.ajax({
                url: "ajax/php/contact.php",
                type: 'POST',
                data: (function() {
                    let formData = new FormData($("#contactForm")[0]);
                    formData.append('action', 'send_message');
                    return formData;
                })(),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    if ($.fn.preloader) {
                        $('.someBlock').preloader('remove');
                    }

                    if (result.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: result.message || "Your message has been sent successfully!",
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 2000);

                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: result.message || "Something went wrong.",
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr, status, error) {
                    if ($.fn.preloader) {
                        $('.someBlock').preloader('remove');
                    }
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong with the AJAX request.",
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        return false;
    });

});
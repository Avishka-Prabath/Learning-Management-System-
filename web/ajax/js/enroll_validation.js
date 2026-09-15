jQuery(document).ready(function ($) {

    // SAVE / ENROLL STUDENT
    $("#enrollForm").submit(function (event) {
        event.preventDefault();

        if (!$('#full_name').val() || $('#full_name').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your full name",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#full_name').focus();
        } else if (!$('#nic').val() || $('#nic').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your NIC or Passport number",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#nic').focus();
        } else if (!$('#dob').val() || $('#dob').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select your date of birth",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#dob').focus();
        } else if (!$('#gender').val() || $('#gender').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select your gender",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#gender').focus();
        } else if (!$('#phone').val() || $('#phone').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your phone number",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#phone').focus();
        } else if (!$('#email').val() || $('#email').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your email address",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#email').focus();
        } else if (!$('#address').val() || $('#address').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter your permanent address",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#address').focus();
        } else if (!$('#course').val() || $('#course').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select a course",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#course').focus();
        } else if (!$('#study_mode').val() || $('#study_mode').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select study mode",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#study_mode').focus();
        } else if (!$('#intake').val() || $('#intake').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select preferred intake",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#intake').focus();
        } else if (!$('#qualification').val() || $('#qualification').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select your highest qualification",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#qualification').focus();
        } else if (!$('#guardian_name').val() || $('#guardian_name').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter guardian name",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#guardian_name').focus();
        } else if (!$('#guardian_phone').val() || $('#guardian_phone').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter guardian phone number",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#guardian_phone').focus();
        } else if (!$('#guardian_relation').val() || $('#guardian_relation').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select guardian relationship",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#guardian_relation').focus();
        } else if (!$('#declaration').is(':checked')) {
            Swal.fire({
                title: "Error!",
                text: "Please confirm the declaration before submitting",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#declaration').focus();
        } else {

            // Preloader start
            if ($.fn.preloader) {
                $('.someBlock').preloader();
            }

            $.ajax({
                url: "ajax/php/enroll.php",
                type: 'POST',
                data: (function() {
                    let formData = new FormData($("#enrollForm")[0]);
                    formData.append('action', 'enroll_student');
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
                            text: result.message || "Enrollment request submitted successfully!",
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
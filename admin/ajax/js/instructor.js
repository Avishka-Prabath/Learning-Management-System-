jQuery(document).ready(function () {

    // SAVE / REGISTER INSTRUCTOR
    $("#createInstructorForm").submit(function (event) {
        event.preventDefault();

        // Field Validations
        if (!$('#select_title').val() || $('#select_title').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select a title for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#full_name').val() || $('#full_name').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter a full name for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#email').val() || $('#email').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter an email address for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#phone').val() || $('#phone').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter a phone number for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#department').val() || $('#department').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please provide a department for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#username').val() || $('#username').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please provide a username for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });    
        } else if (!$('#password').val() || $('#password').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please provide a password for the instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else {

            let formData = new FormData($("#createInstructorForm")[0]);
            formData.append('action', 'create');

            $.ajax({
                url: "ajax/php/instructor.php", // නිවැරදි කරන ලද URL එක
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    if (result.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: result.message || "Instructor registered successfully!",
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
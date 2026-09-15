jQuery(document).ready(function ($) {

    // Edit Instructor Form Submission
    $("#editInstructorForm").submit(function (event) {
        event.preventDefault();

        // Field Validations (Password is OPTIONAL)
        if (!$('#title').val() || $('#title').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please select instructor title", 
                icon: 'error', timer: 2000, 
                showConfirmButton: false });
            $('#title').focus();

        } else if (!$('#full_name').val() || $('#full_name').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please enter instructor full name", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#full_name').focus();
        } else if (!$('#email').val() || $('#email').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please enter official email address", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#email').focus();
        } else if (!$('#phone').val() || $('#phone').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please enter phone number", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#phone').focus();
        } else if (!$('#department').val() || $('#department').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please select a department", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#department').focus();
        } else if (!$('#assigned_module').val() || $('#assigned_module').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please select a module to assign", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#assigned_module').focus();

        } else if (!$('#username').val() || $('#username').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please enter username", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#username').focus();

        } else if (!$('#password').val() || $('#password').val().trim().length === 0) {
            Swal.fire({ 
                title: "Error!", 
                text: "Please enter password", 
                icon: 'error', 
                timer: 2000, 
                showConfirmButton: false 
            });
            $('#password').focus();
        } else {

            let formData = new FormData($("#editInstructorForm")[0]);
            formData.append('action', 'update');

            $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            // AJAX Request Path - Fixed to edit-teacher.php
            $.ajax({
                url: "ajax/php/edit-teacher.php",
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-save-fill me-1"></i> Save & Assign Module');

                    if (result.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: result.message || "Instructor updated successfully!",
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        window.setTimeout(function () {
                            window.location.href = "teachers-list.php";
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
                    $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-save-fill me-1"></i> Save & Assign Module');
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
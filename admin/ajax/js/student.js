jQuery(document).ready(function ($) {

    $("#createStudentForm").submit(function (event) {
        event.preventDefault();

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isEditMode = parseInt($('input[name="student_db_id"]').val(), 10) > 0;

        if (!$('#campus_id').val() || $('#campus_id').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please enter a campus student ID.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#campus_id').focus();
        } else if (!$('#campus_email').val() || !emailPattern.test($('#campus_email').val().trim())) {
            Swal.fire({ title: "Error!", text: "Please enter a valid campus email.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#campus_email').focus();
        } else if (!isEditMode && (!$('#password').val() || $('#password').val().trim().length === 0)) {
            Swal.fire({ title: "Error!", text: "Please enter a portal password.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#password').focus();
        } else if (!$('#full_name').val() || $('#full_name').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please enter a full name for the student.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#full_name').focus();
        } else if (!$('#nic').val() || $('#nic').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please enter a NIC for the student.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#nic').focus();
        } else if (!$('#first_name').val() || $('#first_name').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a first name.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#first_name').focus();
        } else if (!$('#last_name').val() || $('#last_name').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a last name.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#last_name').focus();
        } else if (!$('#dob').val() || $('#dob').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a date of birth.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#dob').focus();
        } else if (!$('#gender').val() || $('#gender').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a gender.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#gender').focus();
        } else if (!$('#phone').val() || $('#phone').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a phone number.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#phone').focus();
        } else if (!emailPattern.test($('#email').val().trim())) {
            Swal.fire({ title: "Error!", text: "Please enter a valid email address.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#email').focus();
        } else if (!$('#address').val() || $('#address').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide an address.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#address').focus();
        } else if (!$('#course_id').val() || $('#course_id').val() === null) {
            Swal.fire({ title: "Error!", text: "Please select an academic course.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#course_id').focus();
        } else if (!$('#study_mode').val() || $('#study_mode').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a study mode.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#study_mode').focus();
        } else if (!$('#intake').val() || $('#intake').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide an intake.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#intake').focus();
        } else if (!$('#qualification').val() || $('#qualification').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a qualification.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#qualification').focus();
        } else if (!$('#school').val() || $('#school').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a school name.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#school').focus();
        } else if (!$('#guardian_name').val() || $('#guardian_name').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a guardian name.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#guardian_name').focus();
        } else if (!$('#guardian_phone').val() || $('#guardian_phone').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a guardian phone number.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#guardian_phone').focus();
        } else if (!$('#guardian_relation').val() || $('#guardian_relation').val().trim().length === 0) {
            Swal.fire({ title: "Error!", text: "Please provide a guardian relation.", icon: 'error', timer: 2000, showConfirmButton: false });
            $('#guardian_relation').focus();
        } else {

            let formData = new FormData($("#createStudentForm")[0]);
            formData.append('action', isEditMode ? 'update' : 'create');

            // Disable Submit Button to Prevent Duplicate Clicks
            $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');

            $.ajax({
                url: "ajax/php/student.php", // Fixed File Location Path
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-person-check-fill me-2"></i> Confirm & Complete Registration');

                    if (result.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: result.message || (isEditMode ? "Student updated successfully!" : "Student registered successfully!"),
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        window.setTimeout(function () {
                            window.location.href = "add-student.php";
                        }, 2000);

                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: result.message || "Something went wrong.",
                            icon: 'error',
                            confirmButtonColor: '#0066ff'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-person-check-fill me-2"></i> Confirm & Complete Registration');
                    console.error("AJAX Error Response: ", xhr.responseText);
                    
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong with the AJAX request. Check console for details.",
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
        return false;
    });

});
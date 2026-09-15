jQuery(document).ready(function ($) {

    // 1. Course Form Submission (Create Course)
    $("#addCourseForm").submit(function (event) {
        event.preventDefault();

        // 2. All Input Validations
        if (!$('#course_code').val() || $('#course_code').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter course code",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#course_code').focus();
        } else if (!$('#course_name').val() || $('#course_name').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter course title",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#course_name').focus();
        } else if (!$('#category').val() || $('#category').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select a category",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#category').focus();
        } else if (!$('#teacher_id').val() || $('#teacher_id').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select an instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#teacher_id').focus();
        } else if (!$('#duration').val() || $('#duration').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter course duration",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#duration').focus();
        } else if (!$('#price').val() || $('#price').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter course fee",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#price').focus();
        } else if (!$('#image').val() || $('#image').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter cover image URL",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#image').focus();
        } else if (!$('#description').val() || $('#description').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter course description",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#description').focus();
        } else {

            let formData = new FormData($("#addCourseForm")[0]);
            formData.append('action', 'create');

            // 3. AJAX Path to Backend
            $.ajax({
                url: "ajax/php/courses.php",
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
                            text: result.message || "Course created successfully!",
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // 4. Reload page after success
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

    // 5. Delete Course Handler
    $(document).on('click', '.delete-course', function (e) {
        e.preventDefault();

        let id = $(this).attr('data-id') || $(this).data('id');

        if (!id) {
            Swal.fire({ 
                title: "Error!", 
                text: "Course ID not found!", 
                icon: 'error' 
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "ajax/php/courses.php",
                    type: "POST",
                    data: {
                        action: 'delete',
                        id: id
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Course deleted successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            window.setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: response.message || "Failed to delete course.",
                                icon: 'error'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: "Error!",
                            text: "AJAX delete request failed.",
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // 6. Live Search Filter for Courses Table
    $("#adminSearchInput").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $("#coursesTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

});
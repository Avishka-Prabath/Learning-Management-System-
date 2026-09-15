jQuery(document).ready(function () {

    // SAVE / PUBLISH ASSIGNMENT
    $("#create-assignment-form").submit(function (event) {
        event.preventDefault();

        let courseName = $('input[name="course_name"]').val();
        let typeVal = $('select[name="type"]').val();
        let titleVal = $('input[name="title"]').val();
        let deadlineDate = $('input[name="deadline_date"]').val();
        let deadlineTime = $('input[name="deadline_time"]').val();

        if (!courseName || courseName.trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter the course name",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!typeVal || typeVal.trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select the assignment type",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!titleVal || titleVal.trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter the assignment title",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!deadlineDate || deadlineDate.trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter the assignment due date",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!deadlineTime || deadlineTime.trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select a time for the assignment",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else {

            // Preloader start
            if ($.fn.preloader) {
                $('.someBlock').preloader();
            }

            $.ajax({
                url: "ajax/php/assignment.php",
                type: 'POST',
                data: (function() {
                    let formData = new FormData($("#create-assignment-form")[0]);
                    formData.append('action', 'create_assignment');
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
                            text: result.message || "Assignment published successfully!",
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
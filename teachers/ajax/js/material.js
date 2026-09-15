jQuery(document).ready(function () {

    // SAVE / ENROLL STUDENT
    $("#uploadMaterialForm").submit(function (event) {
        event.preventDefault();

        if (!$('#select_course').val() || $('#select_course').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select a course",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#title').val() || $('#title').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter a title for the material",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#publish_date').val() || $('#publish_date').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter the publish date",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#upload_note').val() || $('#upload_note').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please upload a note or document for the material",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });

        } else if (!$('#external_link').val() || $('#external_link').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please provide an external link for the material",
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
                url: "ajax/php/material.php",
                type: 'POST',
                data: (function() {
                    let formData = new FormData($("#uploadMaterialForm")[0]);
                    formData.append('action', 'upload_material');
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
jQuery(document).ready(function () {

    // SAVE / UPLOAD LECTURE MATERIAL
    $("#uploadMaterialForm").submit(function (event) {
        event.preventDefault();

        if (!$('select[name="lecture"]').val() || $('select[name="lecture"]').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select a lecture number",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('input[name="title"]').val() || $('input[name="title"]').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter the material or document title",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('input[name="material_file"]').val()) {
            Swal.fire({
                title: "Error!",
                text: "Please select a lecture document to upload",
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
                            text: result.message || "Material uploaded successfully!",
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
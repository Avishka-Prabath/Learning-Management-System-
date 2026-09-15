jQuery(document).ready(function ($) {

    // 1. CREATE / REGISTER SCHEDULE
    $("#schedule-form").submit(function (event) {
        event.preventDefault();

        // Field Validations
        if (!$('#title').val() || $('#title').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter event title",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#title').focus();
        } else if (!$('#type').val() || $('#type').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter event type",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#type').focus();
        } else if (!$('#event_date').val() || $('#event_date').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter event date",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#event_date').focus();
        } else if (!$('#instructor_id').val() || $('#instructor_id').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select an instructor",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#instructor_id').focus();
        } else {

            let formData = new FormData($("#schedule-form")[0]);
            formData.append('action', 'create');

            $.ajax({
                url: "ajax/php/calendar.php",
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
                            text: result.message || "Event published successfully!",
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
                            text: result.message || "Something went wrong.",
                            icon: 'error'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong with the AJAX request.",
                        icon: 'error'
                    });
                }
            });
        }
        return false;
    });

    // 2. DELETE SCHEDULE EVENT HANDLER
    $(document).on('click', '.delete-schedule', function (e) {
        e.preventDefault();

        // Reliably get the ID from the button even if the icon inside it is clicked
        var id = $(this).attr('data-id') || $(this).closest('.delete-schedule').attr('data-id');

        if (!id) {
            Swal.fire({ title: "Error!", text: "Schedule ID not found!", icon: 'error' });
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
                    url: "ajax/php/calendar.php",
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
                                text: response.message || 'Schedule deleted successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: response.message || "Failed to delete schedule.",
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

});
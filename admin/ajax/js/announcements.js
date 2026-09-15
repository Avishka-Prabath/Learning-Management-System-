jQuery(document).ready(function ($) {

    $("#addAnnouncementForm").submit(function (event) {
        event.preventDefault();

        if (!$('#target_audience').val() || $('#target_audience').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please select target audience",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#target_audience').focus();
        } else if (!$('#title').val() || $('#title').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter notice title",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#title').focus();
        } else if (!$('#message').val() || $('#message').val().trim().length === 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter notice message",
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
            $('#message').focus();
        } else {

            let formData = new FormData($("#addAnnouncementForm")[0]);
            formData.append('action', 'create');

            $.ajax({
                url: "ajax/php/announcements.php",
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
                            text: result.message || "Announcement published successfully!",
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
                error: function () {
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

    $(document).on('click', '.delete-announcement', function (e) {
        e.preventDefault();

        let id = $(this).attr('data-id') || $(this).data('id');

        if (!id) {
            Swal.fire({ title: "Error!", text: "Announcement ID not found!", icon: 'error' });
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
                    url: "ajax/php/announcements.php",
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
                                text: response.message || 'Announcement deleted successfully.',
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
                                text: response.message || "Failed to delete announcement.",
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

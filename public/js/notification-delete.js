let notificationToDelete = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

$(document).on('click', '.delete-notification-btn', function () {
    notificationToDelete = $(this).data('id');
});

$('#delete-notification').click(function () {
    if (!notificationToDelete) return;

    $.ajax({
        url: `/notifications/${notificationToDelete}/read`,
        method: 'POST',
        data: {
            _token: csrfToken
        },
        success: function (res) {
            $('#removeNotificationModal').modal('hide');
            location.reload();
        },
        error: function (err) {
            alert('Failed to delete notification. Try again.');
            console.error(err);
        }
    });
});

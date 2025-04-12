$(document).ready(function () {
    $('.view-bid').on('click', function () {
        const userId = $(this).data('user');
        const productId = $(this).data('product');

        const url = window.fetchBidUrlTemplate
            .replace(':productId', productId)
            .replace(':userId', userId);

        $.ajax({
            url: url,
            method: 'GET',
            success: function (res) {
                if (res.status === 'success') {
                    let rows = '';
                    res.data.forEach((bid, index) => {
                        rows += `<tr><td>${index + 1}</td><td>$${parseFloat(bid.amount).toFixed(2)}</td></tr>`;
                    });
                    $('#bidTableBody').html(rows);
                    $('#bidModal').modal('show');
                } else {
                    Swal.fire('Error', res.message || 'Unable to fetch bids', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Something went wrong.', 'error');
            }
        });
    });
});

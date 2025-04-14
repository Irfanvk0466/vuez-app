const countdownIntervals = {};
const authUserId = document.querySelector('[data-auth-user-id]').dataset.authUserId;

function startCountdown(productId, endTime) {
    let end = new Date(endTime.replace(" ", "T")).getTime();

    function update() {
        const now = new Date().getTime();
        const remaining = Math.max(0, end - now);
        const days = Math.floor(remaining / (1000 * 60 * 60 * 24));
        const hours = Math.floor((remaining / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((remaining / (1000 * 60)) % 60);
        const seconds = Math.floor((remaining / 1000) % 60);

        const timer = document.getElementById(`timer-${productId}`);
        const button = document.querySelector(`.bid-button[data-id="${productId}"]`);

        if (remaining <= 0) {
            clearInterval(countdownIntervals[productId]);
            timer.innerText = "Auction Ended";
            if (button) {
                button.outerHTML = `<button class="btn btn-secondary" disabled>Auction Ended</button>`;
            }
        } else {
            let formatted = '';
            if (days > 0) formatted += `${days}d `;
            formatted += `${pad(hours)}h ${pad(minutes)}m ${pad(seconds)}s`;
            timer.innerText = formatted;
        }
    }

    function pad(n) {
        return n < 10 ? '0' + n : n;
    }

    countdownIntervals[productId] = setInterval(update, 1000);
    update();
}

$(document).ready(function () {
    $('.bid-button').click(function () {
        const id = $(this).data('id');
        $('#product_id').val(id);
        $('#bidModal').modal('show');
    });

    $('#bid-form').submit(function (e) {
        e.preventDefault();
        const productId = $('#product_id').val();
        const amount = $('#bid-amount').val();

        $.ajax({
            url: window.placeBidUrl,
            method: 'POST',
            data: {
                _token: window.csrfToken,
                product_id: productId,
                amount: amount
            },
            success: function (res) {
                $('#bidModal').modal('hide');
                $('#bid-amount').val('');
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message || 'Invalid bid.', 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong.', 'error');
            }
        });
    });

    Pusher.logToConsole = false;

    const pusher = new Pusher(window.PUSHER_APP_KEY, {
        cluster: window.PUSHER_APP_CLUSTER,
        forceTLS: true
    });
    
    const channel = pusher.subscribe('bids');

    channel.bind('App\\Events\\LiveAuction', function (data) {
        console.log(data);
        const bid = data.bid;
        const productId = bid.product_id;
        const previousBidder = data.previous_highest_bidder;
        const previousBidAmount = data.previous_bid_amount;
    
        // Update current price
        $('#current-price-' + productId).text('$' + parseFloat(bid.amount).toFixed(2));
    
        // Show outbid alert to previous bidder
        if (
            previousBidder &&
            previousBidder === parseInt(authUserId) &&
            bid.user_id !== parseInt(authUserId)
        ) {
            Swal.fire({
                icon: 'warning',
                title: 'Outbid!',
                html: `You’ve been outbid on <strong>${data.product_name}</strong>.<br>Your previous bid was <strong>$${parseFloat(previousBidAmount).toFixed(2)}</strong>.`,
            });
        }
    
        // Restart countdown and show +2 extension UI if time extended
        if (data.new_end_time) {
            clearInterval(countdownIntervals[productId]);
            startCountdown(productId, data.new_end_time);
    
            const notice = document.getElementById(`extend-notice-${productId}`);
            if (notice) {
                notice.style.display = 'block';
                setTimeout(() => {
                    notice.style.display = 'none';
                }, 2500); // Match animation duration
            }
        }
    });
    
});

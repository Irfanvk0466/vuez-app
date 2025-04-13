$(function () {
    const authId = $('meta[name="auth-id"]').attr("content");
    const receiverId = $('meta[name="receiver-id"]').attr("content");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const pusher = new Pusher(window.PUSHER_APP_KEY, {
        cluster: window.PUSHER_APP_CLUSTER,
        encrypted: true
    });

    const channel = pusher.subscribe(`chat-channel-${authId}`);
    channel.bind('new-message', function (data) {
        const time = new Date(data.chat.created_at).toLocaleTimeString('en-IN', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const isMe = String(data.chat.sender_id) === String(authId); // ✅ fixed

        const messageHtml = `
            <div class="d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'}">
                <div class="message ${isMe ? 'text-right' : 'text-left'}">
                    ${!isMe ? `<strong>${data.sender_name}</strong><br>` : ''}
                    ${data.chat.message}
                    <small>${time}</small>
                </div>
            </div>`;
        
        $('#messages').append(messageHtml);
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    });

    $('#sendMessage').on('submit', function (e) {
        e.preventDefault();

        const message = $('#message-input').val().trim();
        if (!message) return;

        const now = new Date();
        const formattedTime = now.toLocaleTimeString('en-IN', {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Append immediately as right-side
        const messageHtml = `
            <div class="d-flex justify-content-end">
                <div class="message text-right">
                    <strong>You:</strong> ${message}
                    <small>${formattedTime}</small>
                </div>
            </div>`;
        
        $('#messages').append(messageHtml);
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
        $('#message-input').val('');

        $.ajax({
            url: $('#sendMessage').attr('action'),
            method: 'POST',
            data: {
                _token: csrfToken,
                message: message,
                receiver_id: receiverId,
            },
            success: function () {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Message Sent',
                    showConfirmButton: false,
                    timer: 1000,
                    position: 'top-end'
                });
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Server Error', 'error');
            }
        });
    });
});

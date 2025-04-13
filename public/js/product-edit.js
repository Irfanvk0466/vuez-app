$(document).ready(function () {
    $('.edit-btn').on('click', function () {
        const productId = $(this).data('id');

        $.ajax({
            url: `/products/${productId}/edit`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.status === 'success') {
                    const p = response.product;

                    $('#edit_product_id').val(p.id);
                    $('#edit_name').val(p.name);
                    $('#edit_price').val(p.starting_price);
                    $('#edit_description').val(p.description);

                    const DateTime = luxon.DateTime;
                    const endTime = DateTime.fromISO(p.end_time, { zone: 'Asia/Kolkata' })
                        .toFormat("yyyy-MM-dd'T'HH:mm");

                    $('#edit_end_time').val(endTime);
                    $('#editProductForm').attr('action', `/products/${p.id}`);

                    const $imageContainer = $('#existing-images');
                    $imageContainer.empty();

                    if (p.images.length > 0) {
                        $.each(p.images, function (index, img) {
                            $imageContainer.append(`
                                <div class="image-box position-relative m-2" data-id="${img.id}">
                                    <img src="/storage/${img.image_path}" width="100" height="100"
                                        style="object-fit: cover; border: 1px solid #ccc; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-image"
                                        style="position: absolute; top: -5px; right: -5px; padding: 0 6px; border-radius: 50%;">
                                        ×
                                    </button>
                                    <input type="hidden" name="existing_image_ids[]" value="${img.id}">
                                </div>
                            `);
                        });
                    }

                    const modal = new bootstrap.Modal($('#editProductModal')[0]);
                    modal.show();
                }
            }
        });
    });

    $(document).on('click', '.remove-image', function () {
        $(this).closest('.image-box').remove();
    });
});

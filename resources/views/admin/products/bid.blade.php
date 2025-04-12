<!-- Bid Modal -->
<div class="modal fade" id="bidModal" tabindex="-1" aria-labelledby="bidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bidModalLabel">Place a Bid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bid-form">
                    <input type="hidden" id="product_id">
                    <div class="mb-3">
                        <label for="bid-amount" class="form-label">Your Bid</label>
                        <input type="number" class="form-control" id="bid-amount" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Bid</button>
                </form>
            </div>
        </div>
    </div>
</div>
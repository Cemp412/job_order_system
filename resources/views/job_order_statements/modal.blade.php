<div class="modal fade" id="josModal" tabindex="-1" aria-labelledby="josModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="josForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm JOS Generation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="contractor_id">
                    <input type="hidden" name="conductor_id">
                    <input type="hidden" name="month">

                    <div class="mb-3">
                        <label>Total Job Orders</label>
                        <input type="number" name="job_order_count" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Total Amount</label>
                        <input type="number" name="total_amount" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Paid Amount</label>
                        <input type="number" name="paid_amount" class="form-control" required step="0.01">
                    </div>

                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Generate JOS</button>
                </div>
            </div>
        </form>
    </div>
</div>

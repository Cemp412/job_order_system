<div class="modal fade" id="viewJOSModal" tabindex="-1" aria-labelledby="viewJOSModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="viewJOSModalLabel">Job Order Statement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6><strong>Reference #:</strong> <span id="viewRefNo"></span></h6>
                <h6><strong>Contractor:</strong> <span id="viewContractor"></span></h6>
                <h6><strong>Conductor:</strong> <span id="viewConductor"></span></h6>
                <h6><strong>Month:</strong> <span id="viewMonth"></span></h6>
                <h6><strong>Paid:</strong> ₹<span id="viewPaid"></span> | <strong>Balance:</strong> ₹<span id="viewBalance"></span></h6>

                <hr>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Job Order</th>
                            <th>Date</th>
                            <th>Type of Work</th>
                            <th>Actual Work</th>
                            <th>Rate</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="jobOrderList"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

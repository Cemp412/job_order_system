<!-- Job Order Modal -->
<div class="modal fade" id="jobOrderModal" tabindex="-1" aria-labelledby="jobOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="jobOrderForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Job Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Job Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="jobDate" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="jos_date" class="form-label">JOS Date</label>
                        <input type="date" name="jos_date" id="josDate" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="type_of_work_id" class="form-label">Type of Work</label>
                        <select name="type_of_work_id" id="typeOfWorkDropdown" class="form-select" required>
                            <option value="">Select Type</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="contractor_id" class="form-label">Contractor</label>
                        <select name="contractor_id" id="contractorDropdown" class="form-select" required>
                            <option value="">Select Contractor</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="conductor_id" class="form-label">Conductor</label>
                        <select name="conductor_id" id="conductorDropdown" class="form-select" required>
                            <option value="">Select Conductor</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="actual_work_completed" class="form-label">Actual Work Completed</label>
                        <input type="number" name="actual_work_completed" step="0.01" min="0" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('jobDate').setAttribute('min', today);
    
    document.getElementById('jobDate').addEventListener('change', function () {
        const selectedDate = this.value;
        const josInput = document.getElementById('josDate');
        josInput.setAttribute('min', selectedDate);
        if (josInput.value < selectedDate) josInput.value = selectedDate;
    });
});
</script>

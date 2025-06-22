<div class="modal fade" id="conductorModal" tabindex="-1" aria-labelledby="conductorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="conductorForm">
            <input type="hidden" name="id" id="conductorId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Conductor Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff ID</label>
                        <input type="text" name="staff_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="department_name" class="form-label">Department</label>
                        <input type="text" name="department_name" class="form-control" required>
                    </div>
                    <div class="mb-3" id="passwordField">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3" id="passwordConfirmationField">
                        <label for="password_confirmation">Password Confirmation</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

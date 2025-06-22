<div class="mb-3">
  <label>Name</label>
  <input name="name" class="form-control" required>
</div>
<div class="mb-3">
  <label>Date</label>
  <input name="date" type="date" class="form-control" required>
</div>
<div class="mb-3">
  <label>JOS Date</label>
  <input name="jos_date" type="date" class="form-control" required>
</div>
<div class="mb-3">
  <label>Type of Work</label>
  <select name="type_of_work_id" class="form-select" required></select>
</div>
<div class="mb-3">
  <label>Contractor</label>
  <select name="contractor_id" class="form-select" required></select>
</div>
<div class="mb-3">
  <label>Conductor</label>
  <select name="conductor_id" class="form-select" required></select>
</div>
<div class="mb-3">
    <label for="actual_work_completed" class="form-label">Actual Work Completed (in units)</label>
    <input type="number" step="0.01" name="actual_work_completed" class="form-control" required>
</div>
<div class="mb-3">
    <label for="remarks" class="form-label">Remarks</label>
    <textarea name="remarks" class="form-control" rows="3" required></textarea>
</div>

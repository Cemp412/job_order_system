@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold border-bottom pb-2 mb-4">Job Order Statements</h2>

    <div class="mb-3">
        <label for="monthFilter">Filter by Month</label>
        <input type="month" id="monthFilter" class="form-control" value="{{ now()->format('Y-m') }}">
    </div>

    <table class="table table-bordered" id="josTable">
        <thead>
            <tr>
                <th>Reference #</th>
                <th>Contractor</th>
                <th>Conductor</th>
                <th>Total JOs</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <hr class="my-5">
    <h4>Generate New Job Order Statement</h4>

    <div class="mb-3">
        <label for="eligibleMonth">Select Month</label>
        <input type="month" id="eligibleMonth" class="form-control" value="{{ now()->format('Y-m') }}">
    </div>

    <div id="eligibleGroups"></div>
</div>

<!-- View Attached Job Orders Modal -->
<div class="modal fade" id="jobOrdersModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Attached Job Orders</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="attachedJobsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Actual Work</th>
                    <th>Type of Work</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Create JOS Modal -->
<div class="modal fade" id="createJOSModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="createJOSForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Job Order Statement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="contractor_id">
                <input type="hidden" name="conductor_id">
                <input type="hidden" name="job_order_ids">

                <div id="jobOrderDetailsTable" class="mb-3"></div>

                <div class="mb-3">
                    <label>Paid Amount</label>
                    <input type="number" step="0.01" name="paid_amount" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let apiToken = '{{ auth()->user()?->createToken("ui-token")?->plainTextToken }}';

$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + apiToken
    }
});

function loadStatements(month) {
    const [year, mon] = month.split("-");
    $.get(`/api/admin/job-order-statements?year=${year}&month=${mon}`, function(res) {
        let rows = ''; console.log(res.data);
        res.data.forEach(item => {
            rows += `
                <tr>
                    <td>${item.reference_number}</td>
                    <td>${item.contractor}</td>
                    <td>${item.conductor}</td>
                    <td>${item.total_job_orders}</td>
                    <td>${item.total_amount}</td>
                    <td>${item.paid_amount}</td>
                    <td>${item.balance_amount}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="viewJobOrders('${item.id}')">View Job Orders</button>
                    </td>
                </tr>`;
        });
        $('#josTable tbody').html(rows);
    });
}

function viewJobOrders(josId) {
    $.get(`/api/admin/job-order-statements/${josId}/job-orders`, function(res) {
        let rows = '';
        res.forEach(item => {
            rows += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.date}</td>
                    <td>${item.actual_work_completed}</td>
                    <td>${item.type_of_work}</td>
                    <td>${item.type_of_work_rate}</td>
                </tr>`;
        });
        $('#attachedJobsTable tbody').html(rows);
        $('#jobOrdersModal').modal('show');
    });
}

function loadEligibleGroups(month) {
    $.get(`/api/admin/job-order-statements/grouped-job-orders?month=${month}`, function (res) {
        let content = '';
        res.data.forEach(group => {
            content += `
                <div class="card mb-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${group.contractor_name}</strong> / ${group.conductor_name}
                            (${group.job_order_count} JOs — ₹${group.total_amount})
                        </div>
                        <button class="btn btn-sm btn-success" onclick='openCreateJOSModal(${JSON.stringify(group)})'>Create JOS</button>
                    </div>
                </div>`;
        });
        $('#eligibleGroups').html(content || `<div class="alert alert-info">No eligible job orders found.</div>`);
    });
}

function openCreateJOSModal(group) {
    const jobOrderIds = group.job_orders.map(j => j.id);
    let rows = '';
    let totalAmount = 0;

    group.job_orders.forEach(jo => {
        const lineAmount = jo.actual_work_completed * jo.type_of_work_rate;
        totalAmount += lineAmount;
        rows += `
            <tr>
                <td>${jo.name}</td>
                <td>${jo.date}</td>
                <td>${jo.actual_work_completed}</td>
                <td>${jo.type_of_work}</td>
                <td>${jo.type_of_work_rate}</td>
            </tr>`;
    });

    const tableHtml = `
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Actual Work</th>
                    <th>Type of Work</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>`;

    $('#jobOrderDetailsTable').html(tableHtml);
    $('#createJOSForm input[name=contractor_id]').val(group.contractor_id);
    $('#createJOSForm input[name=conductor_id]').val(group.conductor_id);
    $('#createJOSForm input[name=job_order_ids]').val(JSON.stringify(jobOrderIds));
    $('#createJOSForm input[name=paid_amount]').val(totalAmount.toFixed(2)); // Auto-fill
    $('#createJOSModal').modal('show');
}

$('#createJOSForm').on('submit', function(e) {
    e.preventDefault();
    const formData = {
        contractor_id: $(this).find('[name=contractor_id]').val(),
        conductor_id: $(this).find('[name=conductor_id]').val(),
        paid_amount: $(this).find('[name=paid_amount]').val(),
        remarks: $(this).find('[name=remarks]').val(),
        job_order_ids: JSON.parse($(this).find('[name=job_order_ids]').val())
    };

    $.ajax({
        url: '/api/admin/job-order-statements',
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + apiToken },
        data: formData,
        success: function(res) {
            Swal.fire('Success', res.message, 'success');
            $('#createJOSModal').modal('hide');
            loadStatements($('#monthFilter').val());
            loadEligibleGroups($('#eligibleMonth').val());
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Validation failed', 'error');
        }
    });
});

$(document).ready(function () {
    const defaultMonth = $('#monthFilter').val();
    loadStatements(defaultMonth);
    loadEligibleGroups($('#eligibleMonth').val());

    $('#monthFilter').on('change', function () {
        loadStatements(this.value);
    });

    $('#eligibleMonth').on('change', function () {
        loadEligibleGroups(this.value);
    });
});
</script>
@endpush

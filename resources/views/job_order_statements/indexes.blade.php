@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold border-bottom pb-2 mb-4">Job Order Statements</h2>

    <div class="mb-3">
        <label for="monthFilter">Filter by Month</label>
        <input type="month" id="monthFilter" class="form-control" value="{{ now()->format('Y-m') }}">
    </div>
    <button class="btn btn-primary mb-3" onclick="triggerEligibleGroupLoad()">
        Find Eligible Job Orders
    </button>


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


<!-- Modal -->
<div class="modal fade" id="jobOrdersModal" tabindex="-1" aria-labelledby="jobOrdersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Attached Job Orders</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        let rows = '';
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
    $.ajax({
        url: `/api/admin/job-order-statements/grouped-job-orders?month=${month}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + apiToken
        },
        success: function (res) {
            let content = '';
            if (!res.data.length) {
                $('#eligibleGroups').html('<div class="alert alert-warning">No matching job orders found for this month.</div>');
            }
            else{
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
                $('#eligibleGroups').html(content );
            }

            // $('#eligibleGroups').html(content || `<div class="alert alert-info">No eligible job orders found.</div>`);
        },
        error: function (xhr) {
            console.error(xhr);
            Swal.fire('Error', 'Failed to load job orders', 'error');
        }
    });
}

function triggerEligibleGroupLoad() {
    const month = $('#eligibleMonth').val();
    if (month) {
        loadEligibleGroups(month);
    } else {
        Swal.fire('Please select a month first.', '', 'info');
    }
}

function openCreateJOSModal(group) {
    let rows = '';
    group.job_orders.forEach(jo => {
        rows += `
            <tr>
                <td>${jo.name}</td>
                <td>${jo.date}</td>
                <td>${jo.actual_work_completed}</td>
                <td>${jo.type_of_work}</td>
                <td>${jo.type_of_work_rate}</td>
            </tr>`;
    });

    const html = `
    <div class="modal fade" id="createJOSModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="createJOSForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Job Order Statement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="contractor_id" value="${group.contractor_id}">
                    <input type="hidden" name="conductor_id" value="${group.conductor_id}">
                    <input type="hidden" name="job_order_ids" value='${JSON.stringify(group.job_orders.map(j => j.id))}'>

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
                    </table>

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
    </div>`;

    $('body').append(html);
    $('#createJOSModal').modal('show');

    $('#createJOSModal').on('hidden.bs.modal', function () {
        $('#createJOSModal').remove();
    });


    $('#createJOSForm').on('submit', function(e) {
        e.preventDefault();
        $(this).find('button[type=submit]').prop('disabled', true);
        const formData = {
            contractor_id: group.contractor_id,
            conductor_id: group.conductor_id,
            paid_amount: $(this).find('[name=paid_amount]').val(),
            remarks: $(this).find('[name=remarks]').val(),
            job_order_ids: group.job_orders.map(j => j.id)
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
}

$('#eligibleMonth').on('change', function () {
    loadEligibleGroups(this.value);
});

$(document).ready(function () {
    const defaultMonth = $('#eligibleMonth').val();
    loadEligibleGroups(defaultMonth); // Load initially

    $('#eligibleMonth').on('change', function () {
        const selectedMonth = $(this).val();
        if (selectedMonth) {
            loadEligibleGroups(selectedMonth);
        }
    });
});



$(document).ready(() => {
    const initialMonth = $('#monthFilter').val();
    loadStatements(initialMonth);

    $('#monthFilter').on('change', function () {
        loadStatements(this.value);
    });
});


</script>
@endpush

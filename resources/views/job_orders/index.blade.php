@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold border-bottom pb-2">Job Orders</h2>
    <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#jobOrderModal" onclick="resetJobOrderForm()">Add Job Order</button>
    
    <table class="table table-bordered" id="jobOrderTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Reference #</th>
                <th>Date</th>
                <th>JOS Date</th>
                <th>Contractor</th>
                <th>Conductor</th>
                <th>Type of Work</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@include('job_orders.modal')
@endsection

@push('scripts')
<script>
let jobOrderId = null;
let apiToken = '{{ auth()->user()?->createToken("ui-token")?->plainTextToken ?? "" }}';

$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + apiToken
    }
});

function loadJobOrders() {
    $.get('/api/admin/job-orders', function(res) {
        let rows = '';
        res.data.forEach((item, index) => {
            rows += `<tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>${item.reference_number}</td>
                <td>${item.date}</td>
                <td>${item.jos_date}</td>
                <td>${item.contractor}</td>
                <td>${item.conductor}</td>
                <td>${item.type_of_work}</td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick='editJobOrder(${JSON.stringify(item)})'>Edit</button>
                    <button class="btn btn-danger btn-sm" onclick='deleteJobOrder("${item.id}")'>Delete</button>
                </td>
            </tr>`;
        });
        $('#jobOrderTable tbody').html(rows);
    });
}

function resetJobOrderForm() {
    $('#jobOrderForm')[0].reset();
    $('#jobOrderId').val('');
    $('#jobOrderForm').data('method', 'POST');
    $('#jobOrderForm').data('url', `/api/admin/job-orders`);

    loadDropdown('/api/admin/type-of-works', 'type_of_work_id');
    loadDropdown('/api/admin/contractors', 'contractor_id');
    loadDropdown('/api/admin/conductors', 'conductor_id');
}

function editJobOrder(item) {
    jobOrderId = item.id;

    $('#jobOrderForm input[name=name]').val(item.name);
    $('#jobOrderForm input[name=date]').val(item.date);
    $('#jobOrderForm input[name=jos_date]').val(item.jos_date);
    $('#jobOrderForm input[name=actual_work_completed]').val(item.actual_work_completed);
    $('#jobOrderForm textarea[name=remarks]').val(item.remarks);
    $('#jobOrderForm').data('url', `/api/admin/job-orders/${jobOrderId}`);
    // $('#jobOrderForm select[name=contractor_id]').val(item.contractor_id).trigger('change');
    // $('#jobOrderForm select[name=conductor_id]').val(item.conductor_id).trigger('change');
    // $('#jobOrderForm select[name=type_of_work_id]').val(item.type_of_work_id).trigger('change');


    // Load all dropdowns and open modal after all are done
    let loaded = 0;

    function checkAndShowModal() {
        loaded++;
        if (loaded === 3) {
            $('#jobOrderModal').modal('show');
        }
    }

    loadDropdown('/api/admin/type-of-works', 'type_of_work_id', item.type_of_work_id, checkAndShowModal);
    loadDropdown('/api/admin/contractors', 'contractor_id', item.contractor_id, checkAndShowModal);
    loadDropdown('/api/admin/conductors', 'conductor_id', item.conductor_id, checkAndShowModal);
}


function saveJobOrder() {
    let formData = $('#jobOrderForm').serialize();
    let method = jobOrderId ? 'PUT' : 'POST';
    let url = $('#jobOrderForm').data('url');

    $.ajax({
        url: url,
        type: method,
        data: formData,
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
            $('#jobOrderModal').modal('hide');
            loadJobOrders();
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Validation failed', 'error');
        }
    });
}

function deleteJobOrder(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/job-orders/${id}`,
                type: 'DELETE',
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadJobOrders();
                }
            });
        }
    });
}


function loadDropdown(apiUrl, selectName, selectedValue = null, callback = null) {
    $.ajax({
        url: apiUrl,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + apiToken
        },
        success: function(res) {
            let options = '<option value="">-- Select --</option>';
            res.data.forEach(item => {
                const label = selectName === 'conductor_id' ? item.first_name : item.name;
                options += `<option value="${item.id}">${label}</option>`;
            });

            const $select = $(`#jobOrderForm select[name="${selectName}"]`);
            $select.html(options);

            //Ensure selected value is applied after dropdown options are rendered
            if (selectedValue) {
                $select.val(selectedValue).trigger('change');
            }

            //Optional callback for when dropdown is ready (to show modal)
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function(xhr) {
            console.error(`Failed to load dropdown: ${selectName}`, xhr);
        }
    });
}



$(document).ready(() => {
    loadJobOrders();
    const today = new Date().toISOString().split('T')[0];
    const $dateInput = $('#jobOrderForm input[name="date"]');
    const $josDateInput = $('#jobOrderForm input[name="jos_date"]');

    // Prevent past dates
    $dateInput.attr('min', today);

    // When job date changes, set JOS date minimum
    $dateInput.on('change', function () {
        const selectedDate = $(this).val();
        $josDateInput.attr('min', selectedDate);

        // Optional: clear jos_date if it violates the new min
        if ($josDateInput.val() < selectedDate) {
            $josDateInput.val('');
        }
    });

    $('#jobOrderForm').on('submit', function(e) {
        e.preventDefault();
        saveJobOrder();
        const jobDate = new Date($dateInput.val());
        const josDate = new Date($josDateInput.val());

        if ($dateInput.val() && jobDate < new Date(today)) {
            e.preventDefault();
            Swal.fire('Invalid Date', 'Job Date cannot be in the past.', 'warning');
            return;
        }

        if ($josDateInput.val() && josDate < jobDate) {
            e.preventDefault();
            Swal.fire('Invalid JOS Date', 'JOS Date must be the same or after Job Date.', 'warning');
            return;
        }
    });
});
</script>
@endpush

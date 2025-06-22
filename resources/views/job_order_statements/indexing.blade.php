@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-3 border-bottom pb-2">Job Order Statements</h2>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="month" class="form-label">Filter by Month</label>
            <input type="month" id="monthFilter" class="form-control" value="{{ now()->format('Y-m') }}">
        </div>
    </div>

    <div id="groupedResults" class="row"></div>
</div>

@include('job_order_statements.modal')
@endsection

@push('scripts')
<script>
const apiToken = '{{ auth()->user()->createToken("ui-token")->plainTextToken }}';

$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + apiToken
    }
});

function loadGroupedJobOrders() {
    const month = $('#monthFilter').val();

    $.get(`/api/admin/job-order-statements/grouped?month=${month}`, function(res) {
        let html = '';
        res.data.forEach((group, index) => {
            html += `
            <div class="col-md-6">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Contractor: ${group.contractor_name}</h5>
                        <p class="card-text">
                            Conductor: ${group.conductor_name}<br>
                            Total Job Orders: ${group.job_order_count}<br>
                            Total Amount: ₹${group.total_amount}
                        </p>
                        <button class="btn btn-primary btn-sm" onclick='openJosModal(${JSON.stringify(group)})'>
                            Create JOS
                        </button>
                    </div>
                </div>
            </div>`;
        });

        $('#groupedResults').html(html);
    });
}

function openJosModal(group) {
    $('#josForm input[name=contractor_id]').val(group.contractor_id);
    $('#josForm input[name=conductor_id]').val(group.conductor_id);
    $('#josForm input[name=month]').val(group.month);
    $('#josForm input[name=total_amount]').val(group.total_amount);
    $('#josForm input[name=job_order_count]').val(group.job_order_count);
    $('#josForm input[name=paid_amount]').val('');
    $('#josForm textarea[name=remarks]').val('');

    $('#josModal').modal('show');
}

function saveJos() {
    const data = $('#josForm').serialize();

    $.ajax({
        url: '/api/admin/job-order-statements',
        method: 'POST',
        data,
        success: function(res) {
            Swal.fire('Success', res.message, 'success');
            $('#josModal').modal('hide');
            loadGroupedJobOrders();
        },
        error: function(err) {
            Swal.fire('Error', err.responseJSON.message || 'Failed to create JOS', 'error');
        }
    });
}

$(document).ready(() => {
    loadGroupedJobOrders();

    $('#monthFilter').on('change', loadGroupedJobOrders);

    $('#josForm').on('submit', function(e) {
        e.preventDefault();
        saveJos();
    });
});
</script>
@endpush

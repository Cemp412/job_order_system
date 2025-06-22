@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold border-bottom pb-2">Contractor Management</h2>
    <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#contractorModal" onclick="resetContractorForm()">Add Contractor</button>
    <table class="table table-bordered" id="contractorTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@include('contractors.modal')
@endsection

@push('scripts')
<script>
let contractorId = null;
let apiToken = '{{ auth()->user()?->createToken("ui-token")?->plainTextToken ?? "" }}';

$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + apiToken
    }
});

function loadContractors() {
    $.get('/api/admin/contractors', function(res) {
        let rows = '';
        res.data.forEach((item, index) => {
            rows += `<tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>${item.email}</td>
                <td>${item.phone_number}</td>
                <td>${item.company_name}</td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick='editContractor(${JSON.stringify(item)})'>Edit</button>
                    <button class="btn btn-danger btn-sm" onclick='deleteContractor("${item.id}")'>Delete</button>
                </td>
            </tr>`;
        });
        $('#contractorTable tbody').html(rows);
    });
}

function resetContractorForm() {
    $('#contractorForm')[0].reset();
    $('#conductorId').val('');
    conductorId = null;
    $('#passwordField').show(); // show password field only when creating
    $("#passwordConfirmationField").show();
    $('#contractorForm input[name="password"]').prop('required', true);
    $('#contractorForm input[name="password_confirmation"]').prop('required', true);
    $('#contractorForm').val('POST'); // Reset method to POST
    $('#contractorForm').data('url', `/api/admin/conductors`);
}

function editContractor(item) {
    contractorId = item.id;
    $('#contractorId').val(item.id);
    $('#contractorForm input[name=name]').val(item.name);
    $('#contractorForm input[name=code]').val(item.code).prop('readonly', true);
    $('#contractorForm input[name=email]').val(item.email).prop('readonly', true);
    $('#contractorForm input[name=phone_number]').val(item.phone_number).prop('readonly', true);
    $('#contractorForm input[name=company_name]').val(item.company_name);
    $('#contractorForm input[name=balance]').val(item.balance);
    $('#passwordField').hide();
    $('#passwordConfirmationField').hide();
    $('#contractorForm input[name="password"]').prop('required', false);
    $('#contractorForm input[name="password_confirmation"]').prop('required', false);
    $('#contractorForm').data('url', `/api/admin/contractor/${item.id}`);
    $('#contractorForm').val('PUT');
    $('#contractorModal').modal('show');
}

function saveContractor() {
    /* const data = {
        name: $('#contractorModal input[name=name]').val(),
        email: $('#contractorModal input[name=email]').val(),
        phone_number: $('#contractorModal input[name=phone_number]').val(),
        company_name: $('#contractorModal input[name=company_name]').val(),
        balance: $('#contractorModal input[name=balance]').val(),
        password: $('#contractorModal input[name=password]').val(),
        password_confirmation: $('#contractorModal input[name=password_confirmation]').val(),
    }; */

    let formData = $('#contractorForm').serialize();
    contractorId = $('#contractorId').val();
    const url = contractorId ? `/api/admin/contractor/${contractorId}` : '/api/admin/contractors';
    const method = contractorId ? 'PUT' : 'POST';

    $.ajax({
          
        url: url,
        type: method,
        data: formData,
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
            $('#contractorModal').modal('hide');
            loadContractors();
        },
        error: function(err) {
            Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
        }
    });
}

function deleteContractor(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/contractors/${id}`,
                type: 'DELETE',
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadContractors();
                }
            });
        }
    });
}

$(document).ready(() => {
    loadContractors();
    $('#contractorForm').on('submit', function(e) {
        e.preventDefault();
        saveContractor();
    });
});
</script>
@endpush

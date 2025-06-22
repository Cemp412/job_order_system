@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold border-bottom pb-2">Conductor Management</h2>

    <!-- Add New -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#conductorModal" onclick="resetConductorForm()">Add New</button>

    <!-- Table -->
    <table class="table table-bordered" id="conductorTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Staff ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@include('conductors.modal')
@endsection

@push('scripts')
<script>
let conductorId = null;

let apiToken = '{{ auth()->user()?->createToken("ui-token")?->plainTextToken ?? "" }}';

$.ajaxSetup({
    headers: {
        'Authorization': 'Bearer ' + apiToken
    }
});

function loadConductors() {
    $.get('/api/admin/conductors', function(res) {
        let rows = '';
        res.data.forEach((item, index) => {
            rows += `<tr>
                <td>${index + 1}</td>
                <td>${item.first_name} ${item.last_name}</td>
                <td>${item.staff_id}</td>
                <td>${item.email}</td>
                <td>${item.phone_number}</td>
                <td>${item.department_name}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick='editConductor(${JSON.stringify(item)})'>Edit</button>
                    <button class="btn btn-sm btn-danger" onclick='deleteConductor("${item.id}")'>Delete</button>
                </td>
            </tr>`;
        });
        $('#conductorTable tbody').html(rows);
    });
}

function resetConductorForm() {
    $('#conductorForm')[0].reset();
    $('#conductorId').val('');
    conductorId = null;
    $('#passwordField').show(); // show password field only when creating
    $("#passwordConfirmationField").show();
    $('#conductorForm input[name="password"]').prop('required', true);
    $('#conductorForm input[name="password_confirmation"]').prop('required', true);
    $('#conductorForm').val('POST'); // Reset method to POST
    $('#conductorForm').data('url', `/api/admin/conductors`);
}

function editConductor(item) {
    conductorId = item.id;
    $('#conductorId').val(item.id);
    $('#conductorForm input[name=first_name]').val(item.first_name);
    $('#conductorForm input[name=middle_name]').val(item.middle_name);
    $('#conductorForm input[name=last_name]').val(item.last_name);
    $('#conductorForm input[name=email]').val(item.email).prop('readonly', true);
    $('#conductorForm input[name=phone_number]').val(item.phone_number).prop('readonly', true);
    $('#conductorForm input[name=staff_id]').val(item.staff_id).prop('readonly', true);
    $('#conductorForm input[name=department_name]').val(item.department_name);
    $('#passwordField').hide();
    $('#passwordConfirmationField').hide();
    $('#conductorForm input[name="password"]').prop('required', false);
    $('#conductorForm input[name="password_confirmation"]').prop('required', false);
    $('#conductorForm').val('PUT'); // This sets _method to PUT
    $('#conductorForm').data('url', `/api/admin/conductor/${item.id}`);
    $('#conductorModal').modal('show');
}

function saveConductor() {
    let formData = $('#conductorForm').serialize();
    let conductorId = $("#conductorId").val();
    let method = conductorId ? 'PUT' : 'POST';
    let url = conductorId ? `/api/admin/conductor/${conductorId}` : '/api/admin/conductors';

    $.ajax({
        url: url,
        type: method,
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: response.message,
                timer: 1500,
                showConfirmButton: false
            });
            $('#conductorModal').modal('hide');
            loadConductors();
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                text: xhr.responseJSON?.message || 'Something went wrong',
            });
        }
    });
}

function deleteConductor(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/conductors/${id}`,
                type: 'DELETE',
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    loadConductors();
                }
            });
        }
    });
}

$(document).ready(() => {
    loadConductors();
    $('#conductorForm').on('submit', function(e) {
        e.preventDefault();
        saveConductor();
    });
});
</script>
@endpush

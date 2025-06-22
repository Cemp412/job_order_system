@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Type of Works</h2>

        <!-- Add New Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#typeOfWorkModal" onclick="resetForm()">Add New</button>

        <!-- Table -->
        <table class="table table-bordered" id="typeOfWorkTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Rate</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

<!-- Modal -->
    <div class="modal fade" id="typeOfWorkModal" tabindex="-1" aria-labelledby="typeOfWorkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="typeOfWorkForm">
                <input type="hidden" name="id" id="typeOfWorkId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Type Of Work</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control mb-3" placeholder="Name" required>
                        <input type="number" name="rate" class="form-control mb-3" placeholder="Rate" step="0.01" required>
                        <input type="text" name="code" class="form-control mb-3" placeholder="Code" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script>
    let apiToken = '{{ auth()->user()->createToken("ui-token")->plainTextToken ?? '' }}';

    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + apiToken
        }
    });

    function loadTypeOfWorks() {
        $.get('/api/admin/type-of-works', function(data) {
            let rows = '';
            data.data.forEach((item, index) => {
                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name}</td>
                        <td>${item.rate}</td>
                        <td>${item.code}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick='editType(${JSON.stringify(item)})'>Edit</button>
                            <button class="btn btn-sm btn-danger" onclick='deleteType(${JSON.stringify(item.id)})'>Delete</button>
                        </td>
                    </tr>`;
            });
            $('#typeOfWorkTable tbody').html(rows);
        });
    }

    loadTypeOfWorks();

    function resetForm() {
        $('#typeOfWorkForm')[0].reset();
        $('#typeOfWorkId').val('');
        $('#formMethod').val('POST'); // Reset method to POST
        $('#typeOfWorkForm').data('url', `/api/admin/type-of-works`);
    }

    function editType(item) {
        $('#typeOfWorkId').val(item.id);
        $('#typeOfWorkForm input[name="name"]').val(item.name);
        $('#typeOfWorkForm input[name="rate"]').val(item.rate);
        $('#typeOfWorkForm input[name="code"]').val(item.code);
        $('#formMethod').val('PUT'); // This sets _method to PUT
        $('#typeOfWorkForm').data('url', `/api/admin/type-of-work/${item.id}`);
        $('#typeOfWorkModal').modal('show');
    }

    $('#typeOfWorkForm').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        let id = $('#typeOfWorkId').val();
        let method = $('#formMethod').val();
        let url = id ? `/api/admin/type-of-work/${id}` : `/api/admin/type-of-works`;

        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST', // We always send POST; Laravel reads _method
            data: formData,
            success: function() {
                $('#typeOfWorkModal').modal('hide');
                loadTypeOfWorks();
                Swal.fire({
                    icon: 'success',
                    title: 'Saved successfully!',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Failed',
                    text: xhr.responseJSON.message || 'Something went wrong',
                });
            }
        });
        
    });

    function deleteType(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/admin/type-of-work/${id}`,
                    type: 'DELETE',
                    success: function() {
                        loadTypeOfWorks();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    }
</script>
@endpush

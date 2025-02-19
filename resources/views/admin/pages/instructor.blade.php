@extends('admin.dashboard')

@section('content')

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-center">Manage Instructors</h4>
                    </div>
                    <div class="table-responsive">
                        <table id="instructorsTable" class="table table-hover table-bordered text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody> <!-- DataTables will load data dynamically -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Changing Role -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Change Role</h5>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="userId" name="userId">
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role</label>
                        <select class="form-select" id="roleSelect" name="roleSelect" >
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveRoleBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Response Message -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Response</h5>
            </div>
            <div class="modal-body" id="responseMessage">
                <!-- Response message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this instructor?</p>
                <input type="hidden" id="deleteUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete Success Message -->
<div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSuccessModalLabel">Success</h5>
            </div>
            <div class="modal-body">
                User deleted successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        let table = $('#instructorsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('instructor.data') }}",
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "account_type" },
                { "data": null, "orderable": false, "searchable": false, "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary role-btn" data-id="${row.id}" data-role="${row.account_type}">
                            Role
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                            Delete
                        </button>
                    `;
                }}
            ]
        });

        // Open Modal and Populate Data for Role Change
        $(document).on('click', '.role-btn', function() {
            let userId = $(this).data('id');
            let currentRole = $(this).data('role');

            $('#userId').val(userId);
            $('#roleSelect').val(currentRole);
            $('#roleModal').modal('show');
        });

        // Save Role Changes
        $('#saveRoleBtn').on('click', function() {
            let userId = $('#userId').val();
            let newRole = $('#roleSelect').val();

            $.ajax({
                url: "{{ route('admin.instructor.update') }}",
                method: 'POST',
                data: {
                    id: userId,
                    account_type: newRole
                },
                success: function(response) {
                    $('#roleModal').modal('hide');
                    table.ajax.reload();

                    $('#responseMessage').text(response.success ? 'Role updated successfully!' : 'Failed to update role.');
                    $('#responseModal').modal('show');
                },
                error: function(xhr) {
                    $('#roleModal').modal('hide');
                    $('#responseMessage').text('An error occurred. Please try again.');
                    $('#responseModal').modal('show');
                }
            });
        });

        // Open Delete Confirmation Modal
        $(document).on('click', '.delete-btn', function() {
            let userId = $(this).data('id');
            $('#deleteUserId').val(userId);
            $('#deleteModal').modal('show');
        });

        // Confirm Delete
        $('#confirmDeleteBtn').on('click', function() {
            let userId = $('#deleteUserId').val();

            $.ajax({
                url: "{{ route('admin.instructor.destroy', '') }}/" + userId,
                method: 'DELETE',
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    if (response.success) {
                        $('#deleteSuccessModal').modal('show');
                        table.ajax.reload();
                    } else {
                        alert('Failed to delete user.');
                    }
                },
                error: function(xhr) {
                    $('#deleteModal').modal('hide');
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Ensure CSRF token is included in all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endpush

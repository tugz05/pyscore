@extends('admin.dashboard')

@section('content')

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-center">Manage Students</h4>
                    </div>
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-hover table-bordered text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="roleUpdateNotification" class="alert alert-success position-fixed text-center" style="display: none; top: 90%; left: 50%; transform: translate(-50%, -50%); width: 300px; z-index: 1050;">
    Role updated successfully!
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let table = $('#studentsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('admin.student') }}",
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "account_type" },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "render": function(data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenu${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Change Role
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu${row.id}">
                                    <li><a class="dropdown-item role-change" data-id="${row.id}" data-role="instructor">Instructor</a></li>
                                    <li><a class="dropdown-item role-change" data-id="${row.id}" data-role="student">Student</a></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ]
        });

        $(document).on('click', '.role-change', function() {
            let userId = $(this).data('id');
            let newRole = $(this).data('role');

            $.ajax({
                url: "{{ route('admin.student.update') }}",
                method: "POST",
                data: {
                    id: userId,
                    account_type: newRole
                },
                success: function(response) {
                    if (response.success) {
                        $('#roleUpdateNotification').fadeIn().delay(3000).fadeOut();
                        table.ajax.reload();
                    } else {
                        alert('Failed to update role.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

    });
</script>
@endpush

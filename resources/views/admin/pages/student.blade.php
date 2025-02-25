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
                        <h4 class="card-title text-center">Upgrade Requests</h4>
                        <table id="requestsTable" class="table table-hover table-bordered text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
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
        let requestTable = $('#requestsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('admin.student') }}", // Fetch only upgrade requests
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": null, "orderable": false, "searchable": false, "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary approve-request" data-id="${row.id}">
                            Approve
                        </button>
                        <button class="btn btn-sm btn-danger deny-request" data-id="${row.id}">
                            Deny
                        </button>
                    `;
                }}
            ]
        });

        $(document).on('click', '.approve-request', function() {
            let userId = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.student.update') }}",
                method: "POST",
                data: {
                    id: userId,
                    account_type: "instructor",
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        requestTable.ajax.reload();
                        alert('User approved as instructor.');
                    } else {
                        alert('Failed to approve request.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        $(document).on('click', '.deny-request', function() {
            let userId = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.student.deny') }}",
                method: "POST",
                data: {
                    id: userId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        requestTable.ajax.reload();
                        alert('Request denied successfully.');
                    } else {
                        alert('Failed to deny request.');
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

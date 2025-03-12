@extends('admin.dashboard')

@section('content')
<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-center">Manage Days of the Week</h4>
                        <button class="btn btn-success" id="addNewDay" data-bs-toggle="modal" data-bs-target="#addDayModal">
                            <i class="fas fa-plus"></i> Add a Day
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="dayTable" class="table table-hover table-bordered text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Day</th>
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

<!-- Add/Edit Day Modal -->
<div class="modal fade" id="addDayModal" tabindex="-1" aria-labelledby="addDayModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayModalTitle">Add a Day</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dayForm">
                @csrf
                <input type="hidden" id="day_id">
                <div class="modal-body">
                    <x-input type="text" name="name" id="name" placeholder="Enter a day" required />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<style>
    #dayTable td, #dayTable th {
        text-align: center !important;
        vertical-align: middle !important;
    }
</style>

<script>
    $(document).ready(function() {
        let table = $('#dayTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('day.list') }}",
            "columns": [
                { "data": "id", "className": "text-center" },
                { "data": "name", "className": "text-center" },
                {
                    "data": "id",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning editDay" data-id="${data}" data-name="${row.name}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger deleteDay" data-id="${data}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        `;
                    }
                }
            ]
        });

        // Handle Add New button click
        $("#addNewDay").click(function() {
            $("#dayForm")[0].reset();
            $("#day_id").val("");
            $("#dayModalTitle").text("Add a Day");
        });

        // Handle Edit button click
        $(document).on("click", ".editDay", function() {
            let id = $(this).data("id");
            let name = $(this).data("name");

            // Populate form fields
            $("#day_id").val(id);
            $("#name").val(name);

            // Change modal title
            $("#dayModalTitle").text("Edit Day");

            // Open modal
            $("#addDayModal").modal("show");
        });

        // Handle form submission via AJAX for both Add and Edit
        $("#dayForm").submit(function(e) {
            e.preventDefault();

            let id = $("#day_id").val();
            let name = $("#name").val();
            let _token = $("input[name='_token']").val();

            let url, method;
            if (id) {
                url = `/admin/days/${id}/update`;
                method = "PUT";
            } else {
                url = "{{ route('day.store') }}";
                method = "POST";
            }

            $.ajax({
                url: url,
                type: method,
                data: { name: name, _token: _token },
                success: function(response) {
                    $("#addDayModal").modal('hide');
                    $("#dayForm")[0].reset();
                    table.ajax.reload();
                    Swal.fire({
                            icon: "success",
                            text: response.message,
                        });

                },
                error: function(xhr) {
                    Swal.fire({
                            icon: "error",
                            text: 'Something went wrong. Please try again.',
                        });
                  
                }
            });
        });

        // Handle Delete button click
        $(document).on("click", ".deleteDay", function() {
    let id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/days/${id}/delete`,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    Swal.fire(
                        "Deleted!",
                        response.message,
                        "success"
                    );
                    table.ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire(
                        "Error!",
                        "Something went wrong. Please try again.",
                        "error"
                    );
                }
            });
        }
    });
});

    });
</script>
@endpush

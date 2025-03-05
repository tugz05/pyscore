@extends('admin.dashboard')

@section('content')
<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-center">Manage days of the week</h4>
                        <button class="btn btn-success" id="addNewDay" data-bs-toggle="modal" data-bs-target="#addDayModal">
                            <i class="fas fa-plus"></i> Add a day
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="dayTable" class="table table-hover table-bordered text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Room Number</th>
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

<!-- Add Room Modal -->
<div class="modal fade" id="addDayModal"> tabindex="-1" aria-labelledby="addDayModal">Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayModalTitle">Add a day</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="dayForm">
                @csrf
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
                { "data": "name", "className": "text-center" }
            ]
        });

        // Handle Add New button click
        $("#addNewDay").click(function() {
            $("#dayForm")[0].reset();
        });

        // Handle form submission via AJAX
        $("#dayForm").submit(function(e) {
            e.preventDefault();

            let dayName = $("#name").val();
            let _token = $("input[name='_token']").val();

            $.ajax({
                url: "{{ route('day.store') }}",
                type: "POST",
                data: {
                    name: dayName,
                    _token: _token
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addDayModal">').modal('hide');
                        $("#dayForm")[0].reset();
                        table.ajax.reload(); // Reload DataTable
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = "";
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + "\n";
                        });
                        alert(errorMessage);
                    } else {
                        alert("Something went wrong. Please try again.");
                    }
                }
            });
        });
    });
</script>
@endpush

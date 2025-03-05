@extends('admin.dashboard')

@section('content')
<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-center">Manage Rooms</h4>
                        <button class="btn btn-success" id="addNewRoom" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                            <i class="fas fa-plus"></i> Add Room Number
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="roomTable" class="table table-hover table-bordered text-center align-middle">
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
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalTitle">Add Room Number</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="roomForm">
                @csrf
                <div class="modal-body">
                    <x-input type="text" name="room_number" id="room_number" placeholder="Enter Room Number" required />
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
    #roomTable td, #roomTable th {
        text-align: center !important;
        vertical-align: middle !important;
    }
</style>

<script>
    $(document).ready(function() {
        let table = $('#roomTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('room.list') }}",
            "columns": [
                { "data": "id", "className": "text-center" },
                { "data": "room_number", "className": "text-center" }
            ]
        });

        // Handle Add New button click
        $("#addNewRoom").click(function() {
            $("#roomForm")[0].reset();
        });

        // Handle form submission via AJAX
        $("#roomForm").submit(function(e) {
            e.preventDefault();

            let roomNumber = $("#room_number").val();
            let _token = $("input[name='_token']").val();

            $.ajax({
                url: "{{ route('room.store') }}",
                type: "POST",
                data: {
                    room_number: roomNumber,
                    _token: _token
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addRoomModal').modal('hide');
                        $("#roomForm")[0].reset();
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

@extends('admin.dashboard')

@section('content')
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 grid-margin stretch-card">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title text-center">Manage Rooms</h4>
                            <button class="btn btn-success" id="addNewRoom" data-bs-toggle="modal"
                                data-bs-target="#addRoomModal">
                                <i class="fas fa-plus"></i> Add Room Number
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="roomTable" class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Room Number</th>
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

    <!-- Add/Edit Room Modal -->
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
                    <input type="hidden" id="room_id">
                    <div class="modal-body">
                        <x-input type="text" name="room_number" id="room_number" placeholder="Enter Room Number"
                            required />
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
        #roomTable td,
        #roomTable th {
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
                "columns": [{
                        "data": "id",
                        "className": "text-center"
                    },
                    {
                        "data": "room_number",
                        "className": "text-center"
                    },
                    {
                        "data": "id",
                        "className": "text-center",
                        "render": function(data, type, row) {
                            return `
                            <button class="btn btn-sm btn-warning editRoom" data-id="${data}" data-room_number="${row.room_number}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger deleteRoom" data-id="${data}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        `;
                        }
                    }
                ]
            });

            // Handle Add New Room button click
            $("#addNewRoom").click(function() {
                $("#roomForm")[0].reset();
                $("#room_id").val("");
                $("#roomModalTitle").text("Add Room Number");
            });

            // Handle Edit button click
            $(document).on("click", ".editRoom", function() {
                let id = $(this).data("id");
                let roomNumber = $(this).data("room_number");

                // Populate form fields
                $("#room_id").val(id);
                $("#room_number").val(roomNumber);

                // Change modal title
                $("#roomModalTitle").text("Edit Room Number");

                // Open modal
                $("#addRoomModal").modal("show");
            });

            // Handle form submission via AJAX for both Add and Edit
            $("#roomForm").submit(function(e) {
                e.preventDefault();

                let id = $("#room_id").val();
                let roomNumber = $("#room_number").val();
                let _token = $("input[name='_token']").val();

                let url, method;
                if (id) {
                    url = `/admin/rooms/${id}/update`;
                    method = "PUT";
                } else {
                    url = "{{ route('room.store') }}";
                    method = "POST";
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        room_number: roomNumber,
                        _token: _token
                    },
                    success: function(response) {
                        $("#addRoomModal").modal('hide');
                        $("#roomForm")[0].reset();
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
            $(document).on("click", ".deleteRoom", function() {
                let id = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to delete this room?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/rooms/${id}/delete`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    text: response.message,
                                });

                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: "error",
                                    text: "Something went wrong. Please try again.",
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush

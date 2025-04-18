@extends('instructor.dashboard')

@section('content')
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 grid-margin stretch-card">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title text-center">Manage Sections</h4>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                <i class="fas fa-plus"></i> Add Section
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Section Name</th>
                                        <th class="text-center">Schedule</th>
                                        <th class="text-center">Days</th>
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

    <!-- Add/Edit Section Modal -->
    <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Section</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addSectionForm">
                    <input type="hidden" id="section_id">
                    <div class="modal-body">
                        @csrf
                        <x-input type="text" name="name" id="name" label="Section Name" placeholder="Enter Section Name" required />
                        <x-input type="time" name="schedule_from" id="schedule_from" label="From" required />
                        <x-input type="time" name="schedule_to" id="schedule_to" label="To" required />

                        <!-- Checkboxes for Days -->
                        <div class="form-group">
                            <label>Select Days</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" id="monday" value="M">
                                    <label class="form-check-label" for="monday">Monday</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" id="tuesday" value="T">
                                    <label class="form-check-label" for="tuesday">Tuesday</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" id="wednesday" value="W">
                                    <label class="form-check-label" for="wednesday">Wednesday</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" id="thursday" value="Th">
                                    <label class="form-check-label" for="thursday">Thursday</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" id="friday" value="F">
                                    <label class="form-check-label" for="friday">Friday</label>
                                </div>
                            </div>
                        </div>
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
    <script>
        $(document).ready(function() {
            let table = $('#myTable').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": "{{ route('sections.data') }}",
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return convertTo12Hour(row.schedule_from) + " - " + convertTo12Hour(row.schedule_to);
                        }
                    },
                    {
                        "data": "day", // Display the days column
                        "render": function(data, type, row) {
                            return data.split(',').join(', '); // Format the days for display
                        }
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}"
                                    data-name="${row.name}" data-schedule_from="${row.schedule_from}"
                                    data-schedule_to="${row.schedule_to}" data-day="${row.day}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            // Function to Convert 24-hour time to 12-hour AM/PM format
            function convertTo12Hour(timeString) {
                let [hours, minutes] = timeString.split(':');
                let period = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12; // Convert 0 to 12 for 12 AM
                return `${hours}:${minutes} ${period}`;
            }

            // Submit Add/Edit Section
            // Submit Add/Edit Section
$('#addSectionForm').submit(function(e) {
    e.preventDefault();

    let saveBtn = $(this).find('button[type="submit"]');
    saveBtn.prop('disabled', true).text('Saving...'); // Disable and show feedback

    let id = $('#section_id').val();
    let url = id ? `sections/${id}` : "{{ route('sections.store') }}";
    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: $(this).serialize(),
        success: function(response) {
            $('#addSectionModal').modal('hide');
            $('#addSectionForm')[0].reset();
            $('#section_id').val('');
            $('.modal-title').text('Add Section');
            $('#myTable').DataTable().ajax.reload();

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Section saved successfully!',
            });
        },
        error: function(xhr) {
            console.log("Submission Error: ", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON.message || 'An error occurred. Please try again.',
            });
        },
        complete: function() {
            // Always re-enable button after request completes
            saveBtn.prop('disabled', false).html('Save');
        }
    });
});


            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $('#section_id').val(id);
                $('#name').val($(this).data('name'));
                $('#schedule_from').val($(this).data('schedule_from'));
                $('#schedule_to').val($(this).data('schedule_to'));

                // Pre-select checkboxes for days
                let days = $(this).data('day').split(',');
                $('input[name="days[]"]').prop('checked', false); // Uncheck all first
                days.forEach(day => {
                    $(`input[name="days[]"][value="${day}"]`).prop('checked', true);
                });

                $('.modal-title').text('Edit Section');
                $('#addSectionModal').modal('show');
            });

            // Delete Button Click
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure you want to delete this section?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `sections/${id}`,
                            type: "DELETE",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }, // Include CSRF token
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.success ||
                                        'Section deleted successfully!',
                                });
                            },
                            error: function(xhr) {
                                console.log("Delete Error: ", xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message ||
                                        'An error occurred. Please try again.',
                                });
                            }
                        });
                    }
                });
            });

            // Ensure CSRF token is included in all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // When clicking the "Add Section" button, reset the modal
$(document).on('click', '[data-bs-target="#addSectionModal"]', function () {
    let isEdit = $(this).hasClass('edit-btn');

    if (!isEdit) { // Only reset if NOT editing
        $('#addSectionForm')[0].reset(); // Clear all input fields
        $('#section_id').val(''); // Clear hidden input for ID
        $('.modal-title').text('Add Section'); // Set the correct modal title
        $('input[name="days[]"]').prop('checked', false); // Uncheck all checkboxes
    }
});

        });
    </script>
@endpush

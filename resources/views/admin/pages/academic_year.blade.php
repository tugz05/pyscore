@extends('admin.dashboard')

@section('content')
    <div class="page-content">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 grid-margin stretch-card">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title text-center">Manage Academic Year</h4>
                            <button class="btn btn-success" id="addNewAcademicYear" data-bs-toggle="modal" data-bs-target="#addAcademicYearModal">
                                <i class="fas fa-plus"></i> Add Academic Year
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="academicYearTable" class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">Semester</th>
                                        <th class="text-center">Start Year</th>
                                        <th class="text-center">End Year</th>
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

    <!-- Add/Edit Academic Year Modal -->
    <div class="modal fade" id="addAcademicYearModal" tabindex="-1" aria-labelledby="addAcademicYearModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="academicYearModalTitle">Add Academic Year</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="academicYearForm">
                    @csrf
                    <input type="hidden" id="academic_year_id" name="academic_year_id">
                    <div class="modal-body">
                        <x-select name="semester" label="Select Semester"
                            :options="['1st Semester' => '1st Semester', '2nd Semester' => '2nd Semester']" required />

                        <x-select name="start_year" id="start_year" label="Start Year"
                            :options="array_combine(range(2024, 2099), range(2024, 2099))" required />

                        <x-select name="end_year" id="end_year" label="End Year"
                            :options="array_combine(range(2024, 2099), range(2024, 2099))" required />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveAcademicYear">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<style>
    #academicYearTable td, #academicYearTable th {
        text-align: center !important;
        vertical-align: middle !important;
    }
</style>

<script>
    $(document).ready(function() {
        let table = $('#academicYearTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('academic_year.list') }}",
            "columns": [
                { "data": "semester", "className": "text-center" },
                { "data": "start_year", "className": "text-center" },
                { "data": "end_year", "className": "text-center" },
                {
                    "data": "id",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning editAcademicYear" data-id="${data}" data-semester="${row.semester}" data-start_year="${row.start_year}" data-end_year="${row.end_year}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteAcademicYear" data-id="${data}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });

        // Handle Add New button click
        $("#addNewAcademicYear").click(function() {
            $("#academicYearForm")[0].reset();
            $("#academic_year_id").val("");
            $("#academicYearModalTitle").text("Add Academic Year");
        });

        // Handle Edit button click
        $(document).on("click", ".editAcademicYear", function() {
            let id = $(this).data("id");
            let semester = $(this).data("semester");
            let startYear = $(this).data("start_year");
            let endYear = $(this).data("end_year");

            // Populate form fields
            $("#academic_year_id").val(id);
            $("select[name='semester']").val(semester).change();
            $("select[name='start_year']").val(startYear).change();
            $("select[name='end_year']").val(endYear).change();

            // Change modal title
            $("#academicYearModalTitle").text("Edit Academic Year");

            // Open modal
            $("#addAcademicYearModal").modal("show");
        });

        // Handle Delete button click
        $(document).on("click", ".deleteAcademicYear", function() {
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
                url: `/admin/academic_year/${id}/delete`,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    Swal.fire({
                        title: "Deleted!",
                        text: response.success,
                        icon: "success"
                    });
                    table.ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        icon: "error"
                    });
                }
            });
        }
    });
});

        // Handle form submission via AJAX for both Add and Edit
        $("#academicYearForm").submit(function(e) {
            e.preventDefault();

            let id = $("#academic_year_id").val();
            let formData = {
                _token: "{{ csrf_token() }}",
                semester: $("select[name='semester']").val(),
                start_year: $("select[name='start_year']").val(),
                end_year: $("select[name='end_year']").val()
            };

            let url, method;
            if (id) {
                url = `/admin/academic_year/${id}/update`;
                method = "PUT";
            } else {
                url = "{{ route('academic_year.store') }}";
                method = "POST";
            }

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    $("#addAcademicYearModal").modal('hide');
                    $("#academicYearForm")[0].reset();
                    table.ajax.reload();
                    Swal.fire({
                            icon: "success",
                            text: response.message,
                        });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = "";
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + "\n";
                        });
                        Swal.fire({
                            icon: "error",
                            text: errorMessage,
                        });

                    } else {
                        Swal.fire({
                            icon: "error",
                            text: 'Something went wrong. Please try again.',
                        });

                    }
                }
            });
        });

    });
</script>
@endpush

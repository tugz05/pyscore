@extends('user.dashboard')
@section('content')

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Custom CSS for hover effects */
        .hover-effect {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            cursor: pointer;
        }

        .hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<div class="container-fluid bg-gray-100 mx-2">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-brands fa-python text-primary"></i> Welcome Instructor
        </h1>
        <h1 class="h3 mb-0 text-gray-800">
            <a class="btn btn-success" data-toggle="modal" data-target="#joinClassModal">
                <i class="fa-solid fa-plus mr-3"></i>
                Join a Class
            </a>
        </h1>
    </div>

    <!-- Content Row -->
    <div class="row" id="classCards">
        <!-- AJAX-loaded class cards will appear here -->
    </div>
</div>

<!-- Join Class Modal -->
<div class="modal fade" id="joinClassModal" tabindex="-1" role="dialog" aria-labelledby="joinClassModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="joinClassModalLabel">Join Class</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- User Account Info -->
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $user->avatar }}" class="rounded-circle mr-2" alt="User Profile">
                    <div>
                        <strong>{{ $user->name }}</strong> <br>
                        <small>{{ $user->email }}</small>
                    </div>
                    <button class="btn btn-outline-primary btn-sm ml-auto">Switch account</button>
                </div>

                <!-- Class Code Input -->
                <form id="joinClassForm">
                    @csrf
                    <div class="form-group">
                        <label for="classlist_id">Class code</label>
                        <input type="text" class="form-control" id="classlist_id" name="classlist_id" placeholder="Enter class code" required>
                        <small class="form-text text-muted">
                            Use a class code with 5-7 letters or numbers, and no spaces or symbols.
                        </small>
                    </div>

                    <!-- Help Link -->
                    <p class="text-muted">
                        If you have trouble joining the class, go to the
                        <a href="#" class="text-primary">Help Center article</a>.
                    </p>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Join</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
$(document).ready(function() {
    loadClasses();

    function loadClasses() {
        $.ajax({
            url: "{{ route('user.classlist.data') }}",
            type: "GET",
            success: function(response) {
                console.log("Class Data:", response);

                let classCards = '';

                if (!response.data || response.data.length === 0) {
                    classCards = `
                        <div class="d-flex align-items-center justify-content-center w-100" style="height: 75vh;">
                            <div class="text-center">
                                <img src="{{ asset('assets/img/undraw_posting_photo.svg') }}" style="max-width: 50%; height: auto; padding: 20px;">
                                <h1>No classes available</h1>
                            </div>
                        </div>
                    `;
                } else {
                    $.each(response.data, function(index, classlist) {
                        classCards += `
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <a href="{{ url('student/class/i') }}/${classlist.id}">
                                    <div class="card shadow-lg rounded-4 border-1 hover-effect h-100">
                                        <img src="https://picsum.photos/300/120" class="card-img-top rounded-top-4" alt="Course Image">
                                        <div class="card-body p-3 d-flex flex-column">
                                            <h5 class="card-title text-primary fw-bold">${classlist.name}</h5>
                                            <p class="card-text text-muted">${classlist.section?.name || 'No Section'} | ${classlist.academic_year || 'N/A'}</p>
                                            <p class="card-text text-muted"><b>Room:</b> ${classlist.room || 'N/A'}</p>

                                        </div>
                                    </div>
                                </a>
                            </div>`;
                    });
                }
                $('#classCards').html(classCards);
            },
            error: function(xhr) {
                console.log("AJAX Error:", xhr.responseText);
                $('#classCards').html(`
                    <div class="alert alert-danger">Failed to load classes. Please try again later.</div>
                `);
            }
        });
    }

    $('#joinClassForm').submit(function(e) {
        e.preventDefault();
        let classlistId = $('#classlist_id').val();

        $.ajax({
            url: "{{ route('joinclass.store') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                classlist_id: classlistId
            },
            success: function(response) {
                $('#joinClassModal').modal('hide');
                $('#joinClassForm')[0].reset();
                loadClasses();
                alert(response.success);
            },
            error: function(xhr) {
                console.log("AJAX Error:", xhr.responseText);
                alert("Error: " + xhr.responseJSON.error);
            }
        });
    });
});
</script>
@endpush

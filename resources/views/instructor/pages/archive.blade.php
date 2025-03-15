@extends('instructor.dashboard')
@section('content')
    <!-- Bootstrap & FontAwesome Icons -->

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
            /* Custom CSS for hover effects */
            .hover-effect {
                transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
                cursor: pointer;
                /* Makes the cursor a hand on hover */
            }

            .hover-effect:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>

    <div class="container-fluid bg-gray-100 mx-2">
        <!-- Page Heading -->
       

        <!-- Content Row -->

        <div class="row" id="classCards">
            <!-- AJAX-loaded class cards will appear here -->
        </div>

    </div>
    <!-- Add/Edit Class Modal -->
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            loadArchives();

            function loadArchives() {
                $.ajax({
                    url: "{{ route('archive.list') }}",
                    type: "GET",
                    success: function(response) {
                        let classCards = '';
                        if (response.data.length === 0) {
                            classCards = `
                            <div class="d-flex align-items-center justify-content-center w-100" style="height: 75vh;">
                                <div class="text-center">
                                    <img src="{{ asset('assets/img/undraw_posting_photo.svg') }}" style="max-width: 50%; height: auto; padding: 20px;">
                                    <h1>No Archived Classes</h1>
                                </div>
                            </div>
                        `;
                        } else {
                            $.each(response.data, function(index, archive) {
                                let classlist = archive.classlist; // Get the actual class

                                classCards += `
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card shadow-lg rounded-4 border-1 hover-effect h-100">
                                         <img src="{{ asset('assets/course_images') }}/${classlist.course_image}" class="card-img-top rounded-top-4" alt="Course Image">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <a href="{{ route('class.view', '') }}/${classlist.id}" class="text-decoration-none">
                                            <h5 class="card-title text-primary fw-bold">${classlist.name}</h5>
                                        </a>
                                        <p class="card-text text-muted">${classlist.section.name} | ${classlist.academic_year}</p>
                                        <p class="card-text text-muted"><b>Room:</b> ${classlist.room}</p>
                                        <div class="mt-auto d-flex justify-content-between">
                                            <div class="dropdown">
                                                <button class="btn btn-light" type="button" id="dropdownMenu${classlist.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu${classlist.id}">
                                                    <li>
                                                        <a class="dropdown-item restore-btn" data-id="${classlist.id}">
                                                            Restore
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item delete-btn text-danger" data-id="${classlist.id}">
                                                            Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                            });
                        }
                        $('#classCards').html(classCards);
                    }
                });
            }
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();

            let id = $(this).data('id');

            if (!confirm("Are you sure you want to permanently delete this class? This action cannot be undone!")) {
                return;
            }

            $.ajax({
                url: "{{ route('archive.destroy', '') }}/" + id,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadArchives(); // Reload the archive list
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                }
            });
        });

        $(document).on('click', '.restore-btn', function(e) {
            e.preventDefault();

            let id = $(this).data('id');

            if (!confirm("Are you sure you want to restore this class?")) {
                return;
            }

            $.ajax({
                url: "{{ route('archive.restore') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadArchives(); // Reload the archive list
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                }
            });
        });
    </script>
@endpush

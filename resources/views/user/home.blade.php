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
    <div class="container-fluid">
        <div class="container-fluid bg-gray-100 mx-2">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <!-- Search Input Field -->
                <div class="input-group w-50">
                    <input type="text" id="searchClass" class="form-control" placeholder="Search class..."
                        onkeyup="filterClasses()">
                </div>
                <!-- Academic Year Filter -->
                <div class="ml-3">
                    <select id="filterAcademicYear" class="form-control" onchange="filterClasses()">
                        <option value="">All Academic Years</option>
                        @foreach ($academic_year as $year)
                            <option value="{{ $year->semester . ' ' . $year->start_year . '-' . $year->end_year }}">
                                {{ $year->semester . ' ' . $year->start_year . '-' . $year->end_year }}
                            </option>
                        @endforeach
                    </select>
                </div>

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
    </div>
    <!-- Join Class Modal -->
    <div class="modal fade" id="joinClassModal" tabindex="-1" role="dialog" aria-labelledby="joinClassModalLabel"
        aria-hidden="true">
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

                    </div>

                    <!-- Class Code Input -->
                    <form id="joinClassForm">
                        @csrf
                        <div class="form-group">
                            <label for="classlist_id">Class code</label>
                            <input type="text" class="form-control" id="classlist_id" name="classlist_id"
                                placeholder="Enter class code" required oninput="validateClassCode(this)"
                                onkeydown="restrictInput(event)">
                            <small class="form-text text-muted">
                                Format: xxx-xxxx-xxx (Lowercase letters and numbers only).
                            </small>
                        </div>


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
        function filterClasses() {
            let input = document.getElementById("searchClass").value.toLowerCase();
            let selectedYear = document.getElementById("filterAcademicYear").value.toLowerCase();
            let classCards = document.querySelectorAll("#classCards .col-lg-3");

            classCards.forEach(card => {
                let className = card.querySelector(".card-title").innerText.toLowerCase();
                let section = card.querySelector(".card-text:nth-child(2)").innerText.toLowerCase();
                let academicYear = card.querySelector(".card-text:nth-child(2)").innerText.toLowerCase();
                let room = card.querySelector(".card-text:nth-child(3)").innerText.toLowerCase();

                let matchesSearch = className.includes(input) || section.includes(input) || room.includes(input);
                let matchesYear = selectedYear === "" || academicYear.includes(selectedYear);

                if (matchesSearch && matchesYear) {
                    card.style.display = "block"; // Show matching cards
                } else {
                    card.style.display = "none"; // Hide non-matching cards
                }
            });
        }

        function validateClassCode(input) {
            let value = input.value.toLowerCase(); // Convert to lowercase automatically
            let formattedValue = value.replace(/[^a-z0-9-]/g, ''); // Remove invalid characters

            // Auto-insert dashes at the correct positions
            formattedValue = formattedValue.replace(/-/g, ''); // Remove existing dashes
            if (formattedValue.length > 3) formattedValue = formattedValue.slice(0, 3) + '-' + formattedValue.slice(3);
            if (formattedValue.length > 8) formattedValue = formattedValue.slice(0, 8) + '-' + formattedValue.slice(8);

            // Ensure length does not exceed required format
            if (formattedValue.length > 12) formattedValue = formattedValue.slice(0, 12);

            input.value = formattedValue;
        }

        function restrictInput(event) {
            let allowedKeys = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'];
            let regex = /^[a-z0-9-]$/;

            if (!allowedKeys.includes(event.key) && !regex.test(event.key.toLowerCase())) {
                event.preventDefault(); // Prevent invalid characters
            }
        }
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
                                    <div class="card shadow-lg rounded-4 border-1 hover-effect h-100" >
                                        <a href="{{ url('student/class/i') }}/${classlist.id}">
                                          <img src="{{ asset('assets/course_images') }}/${classlist.course_image}" class="card-img-top rounded-top-4" alt="Course Image">

                                        <div class="card-body p-3 d-flex flex-column">
                                            <h5 class="card-title text-primary fw-bold">${classlist.name}</h5>
                                            <p class="card-text text-muted">${classlist.section?.name || 'No Section'} | ${classlist.academic_year || 'N/A'}</p>
                                            <p class="card-text text-muted"><b>Room:</b> ${classlist.room || 'N/A'}</p>

                                            <!-- Vertical Ellipsis Dropdown (Placed Below Room) -->
                                            <div class="dropup">
                                                <button class="btn btn-light  " type="button" id="dropdownMenu${classlist.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu${classlist.id}">
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fa-solid  me-2"></i> Unenroll
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Joined class successfully',
                            text: response.success,
                        });

                    },
                    error: function(xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to join class',
                            text: "Error: " + xhr.responseJSON.error,
                        });

                    }
                });
            });

        });
    </script>
@endpush

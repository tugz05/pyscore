@extends('instructor.dashboard')
@section('content')
    <!-- Bootstrap & FontAwesome Icons -->

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
            /* Custom CSS for hover effects */
            .swal-share-class {
                font-size: 17px !important;
                /* Adjust the size as needed */
            }

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
            <!-- Create Class Button -->
            <h1 class="h3 mb-0 text-gray-800">
                <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="fa-brands fa-plus mr-3"></i>
                    Create Class
                </a>
            </h1>
        </div>



        <!-- Content Row -->

        <div class="row" id="classCards">
            <!-- AJAX-loaded class cards will appear here -->
        </div>

    </div>
    <!-- Add/Edit Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Class</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addClassForm">
                    <input type="hidden" id="classlist_id">
                    <div class="modal-body">
                        @csrf
                        <x-input type="text" name="name" id="name" label="Class Name" required />
                        <x-select name="section_id" id="section_id" label="Select Section" :options="$sections->pluck('name', 'id')->toArray()" required />
                        <x-select name="academic_year" id="academic_year" label="Academic Year" :options="$academic_year
                            ->mapWithKeys(
                                fn($year) => [
                                    $year->semester . ' ' . $year->start_year . '-' . $year->end_year =>
                                        $year->semester . ' ' . $year->start_year . '-' . $year->end_year,
                                ],
                            )
                            ->toArray()"
                            required />

                        <x-select name="room" id="room" label="Room" :options="$rooms->pluck('room_number', 'room_number')->toArray()" required />

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Share Modal --}}
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share Class</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="fw-bold">Share this code to allow others to join your class:</p>
                    <input type="text" id="shareCode" class="form-control text-center fw-bold" readonly>
                    <button class="btn btn-success mt-3" onclick="copyShareCode()">
                        <i class="fas fa-clipboard-list text-white fs-1 mr-2"></i>
                        Copy Code
                    </button>
                    <button class="btn btn-primary mt-3 mr-3" onclick="copyLink()">
                        <i class="fas fa-link text-white fs-1 mr-2"></i>
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Archive Confirmation Modal -->
    <div class="modal fade" id="archiveConfirmModal" tabindex="-1" aria-labelledby="archiveConfirmLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveConfirmLabel">Archive Class</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to archive this class?</p>
                    <p class="text-muted">Archived classes can only be <b>viewed</b> by teachers or students and cannot be
                        <b>modified</b>
                        unless they are restored.
                    </p>
                    <input type="hidden" id="archiveClassId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmArchive" data-loading-text="Archiving...">Archive</button>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        function copyShareCode() {
            let copyText = document.getElementById("shareCode");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            Swal.fire({
                position: "bottom",
                title: "Share code copied: " + copyText.value,
                showConfirmButton: false,
                timer: 1100,

                customClass: {
                    title: 'swal-share-class' // Assign a CSS class to the title
                }
            });


        }

        function copyLink() {
            let copyText = document.getElementById("shareCode");
            let baseUrl = window.location.origin; // Dynamically gets the base URL
            let fullUrl = `${baseUrl}/student/join/class/s/${copyText.value}`;

            navigator.clipboard.writeText(fullUrl).then(() => {

                Swal.fire({
                    position: "bottom",
                    title: "Share code link copied: " + fullUrl,
                    showConfirmButton: false,
                    timer: 1100,
                    customClass: {
                        title: 'swal-share-class' // Assign a CSS class to the title
                    }
                });

            }).catch(err => {
                console.error("Error copying text:", err);
            });
        }

        $(document).ready(function() {
            loadClasses();

            function loadClasses() {
                $.ajax({
                    url: "{{ route('classlist.data') }}",
                    type: "GET",
                    success: function(response) {

                        let classCards = '';
                        if (response.data.length === 0) {
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
        <div class="card shadow-lg rounded-4 border-1 hover-effect h-100 class-card"
            data-url="{{ route('class.view', '') }}/${classlist.id}">

           <img src="{{ asset('assets/course_images') }}/${classlist.course_image}" class="card-img-top rounded-top-4" alt="Course Image">

            <div class="card-body p-3 d-flex flex-column">
                <h5 class="card-title text-primary fw-bold">${classlist.name}</h5>
                <p class="card-text text-muted">${classlist.section.name} | ${classlist.academic_year}</p>
                <p class="card-text text-muted"><b>Room:</b> ${classlist.room}</p>

                <div class="mt-auto d-flex justify-content-between">
                    <div>
                        <div class="dropup">
                            <button class="btn btn-light" type="button" id="dropdownMenu${classlist.id}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu${classlist.id}">
                                <li>
                                    <a class="dropdown-item share-btn" data-id="${classlist.id}"
                                        data-share_code="${classlist.id}">
                                        Share class
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item edit-btn" data-id="${classlist.id}"
                                        data-name="${classlist.name}" data-section_id="${classlist.section_id}"
                                        data-academic_year="${classlist.academic_year}" data-room="${classlist.room}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item archive-btn text-danger" data-id="${classlist.id}">
                                        Archive
                                    </a>
                                </li>
                            </ul>
                        </div>
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
            $(document).on('click', '.class-card', function(e) {
                // Prevent click event when clicking on buttons or dropdown menu inside the card
                if (!$(e.target).closest('.dropup, .btn, .dropdown-item').length) {
                    window.location.href = $(this).data('url'); // Redirect to class view page
                }
            });

            $(document).on('click', '.archive-btn', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                $('#archiveClassId').val(id);
                $('#archiveConfirmModal').modal('show'); // Show the modal
                $('#confirmArchive').click(function() {
                    let id = $('#archiveClassId').val();
                    let $btn = $(this);
 // Disable and update button text
 $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Archiving...');

                    $.ajax({
                        url: "{{ route('archive.data') }}",
                        type: "POST",
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#archiveConfirmModal').modal(
                                'hide'); // Hide the modal after successful archive
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Class archived successfully',
                            });
                            loadClasses(); // Reload class list

                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.',
                            });

                        },
                        complete: function() {
            // Restore button
            $btn.prop('disabled', false).html('Archive');
        }
                    });
                });

            });
            $('#addClassForm').submit(function(e) {
    e.preventDefault();

    let saveBtn = $(this).find('button[type="submit"]');
    saveBtn.prop('disabled', true).text('Saving...'); // Disable and show loading text

    let id = $('#classlist_id').val();
    let url = id ? `classlist/${id}` : "{{ route('classlist.store') }}";
    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#addClassModal').modal('hide');
            $('#addClassForm')[0].reset();
            $('#classlist_id').val('');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.success,
            });
            loadClasses();
        },
        error: function(xhr) {
            console.log("AJAX Error:", xhr.responseText);

            let message = "Something went wrong. Please try again.";
            if (xhr.status === 409) {
                message = xhr.responseJSON.error;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
            });
        },
        complete: function() {
            saveBtn.prop('disabled', false).text('Save'); // Re-enable button
        }
    });
});

            $(document).on('click', '.share-btn', function() {
                let shareCode = $(this).data('share_code');
                $('#shareCode').val(shareCode);
                $('#shareModal').modal('show');
            });

            // Function to Copy Share Code to Clipboard
            $(document).on('click', '.edit-btn', function() {
                $('#classlist_id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#section_id').val($(this).data('section_id'));
                $('#academic_year').val($(this).data('academic_year'));
                $('#room').val($(this).data('room'));
                $('#addClassModal').modal('show');
            });


        });
        function filterClasses() {
    let input = document.getElementById("searchClass").value.toLowerCase();
    let selectedYear = document.getElementById("filterAcademicYear").value.toLowerCase();
    let classCards = document.querySelectorAll("#classCards .col-lg-3");
    let classCardsContainer = document.getElementById("classCards");
    let noClassesDiv = document.getElementById("noClassesMessage");

    let hasVisibleCard = false; // Track if at least one card is visible

    classCards.forEach(card => {
        let className = card.querySelector(".card-title").innerText.toLowerCase();
        let section = card.querySelector(".card-text:nth-child(2)").innerText.toLowerCase(); // Section
        let academicYear = card.querySelector(".card-text:nth-child(2)").innerText.toLowerCase(); // Academic Year
        let room = card.querySelector(".card-text:nth-child(3)").innerText.toLowerCase(); // Room

        let matchesSearch = className.includes(input) || section.includes(input) || room.includes(input);
        let matchesYear = selectedYear === "" || academicYear.includes(selectedYear);

        if (matchesSearch && matchesYear) {
            card.style.display = "block"; // Show matching cards
            hasVisibleCard = true; // At least one card is visible
        } else {
            card.style.display = "none"; // Hide non-matching cards
        }
    });

    // If no matching classes, show the "No classes available" message
    if (!hasVisibleCard) {
        if (!noClassesDiv) {
            noClassesDiv = document.createElement("div");
            noClassesDiv.id = "noClassesMessage";
            noClassesDiv.className = "d-flex align-items-center justify-content-center w-100";
            noClassesDiv.style.height = "75vh";
            noClassesDiv.innerHTML = `
                <div class="text-center">
                    <img src="{{ asset('assets/img/undraw_posting_photo.svg') }}" style="max-width: 50%; height: auto; padding: 20px;">
                    <h1>No classes available</h1>
                </div>
            `;
            classCardsContainer.appendChild(noClassesDiv);
        }
    } else {
        if (noClassesDiv) {
            noClassesDiv.remove();
        }
    }
}

    </script>
@endpush

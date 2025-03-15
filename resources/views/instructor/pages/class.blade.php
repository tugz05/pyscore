@extends('instructor.dashboard')

@section('content')
    <style>
        .swal-share-class {
            font-size: 17px !important;
            /* Adjust the size as needed */
        }
    </style>
    <div class="container-fluid">
        <!-- Class Header -->
        <div class="card shadow-lg rounded border-0">
            <div class="card-body text-white d-flex align-items-center justify-content-between"
                style="background: url('{{ asset('assets/course_images') }}/{{ $classlist->course_image }}') no-repeat center center;
                       background-size: cover; min-height: 200px;">

                <div style="background: rgba(0, 0, 0, 0.3); padding: 15px; border-radius: 10px;">
                    <h2 class="fw-bold">{{ $classlist->name }}</h2>
                    <p class="mb-0">{{ $classlist->section->name }} | {{ $classlist->section->day }}</p>

                </div>

            </div>
        </div>

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#stream">Stream</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#people">People</a>
            </li>
        </ul>
        {{-- Stream Tab --}}
        <div class="tab-content mt-3">
            <!-- Stream Tab -->
            <div class="tab-pane fade show active" id="stream">
                <div class="row mt-4">
                    <!-- Left Sidebar -->
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body text-center">

                                <input type="text" id="shareCode" class="form-control text-center fw-bold mb-3"
                                    value="{{ $classlist->id }}" readonly>
                                <button class="btn btn-success w-100 m-2" onclick="copyShareCode()">Copy Code</button>
                                <button class="btn btn-primary w-100 m-2" onclick="copyLink()">Copy Link</button>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-md-9">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <img src="{{ $classlist->user->avatar }}" class="rounded-circle me-3" alt="User"
                                    style="width: 50px; height: 50px; border: 1; border-color: black; border-style: ;">
                                {{-- <input type="text" class="form-control" placeholder="Announce something to your class"> --}}
                                <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                                    <i class="fas fa-fw fa-plus mr-3"></i>
                                    Add Activity
                                </a>
                            </div>
                        </div>
                        <div id="classCards">
                            <!-- AJAX-loaded class cards will appear here -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- Close Stream Tab --}}
        {{-- Classwork Tab --}}
        <div class="tab-content mt-3">
            <!-- Stream Tab -->
            <div class="tab-pane fade show" id="classwork">
                <h1>Classwork</h1>
            </div>
        </div>
        {{-- Close Classwork Tab --}}

        {{-- People Tab --}}
        <div class="tab-content mt-3">
            <!-- Stream Tab -->
            <div class="tab-pane fade show" id="people">
                @include('instructor.pages.people')
            </div>
        </div>
        {{-- Close People Tab --}}

    </div>

    <!-- Add Activity Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Activity</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addActivityForm">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}">
                    <input type="hidden" name="classlist_id" id="classlist_id" value="{{ $classlist->id }}">
                    <input type="hidden" name="section_id" id="section_id" value="{{ $classlist->section->id }}">
                    <input type="hidden" name="activity_id" id="activity_id">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Activity Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                placeholder="Enter Activity Title" required>
                        </div>
                        <div class="mb-3">
                            <label for="instruction" class="form-label fw-bold">Instructions</label>
                            <textarea name="instruction" id="instruction" class="form-control summernote"
                                placeholder="Enter detailed instructions..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 mb-3">
                                <label for="points" class="form-label fw-bold">Points</label>
                                <input type="number" name="points" id="points" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Due Date -->
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label fw-bold">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" required>
                            </div>
                            <!-- Due Time -->
                            <div class="col-md-6 mb-3">
                                <label for="due_time" class="form-label fw-bold">Due Time</label>
                                <input type="time" name="due_time" id="due_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="container">
                                <div class="row mb-3 ml-2">
                                    <div class="col-md-4">
                                        <input type="checkbox" name="schedule_activity" id="schedule_activity"
                                            class="form-check-input">
                                        <label for="schedule_activity" class="form-check-label">Schedule Activity</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="checkbox" name="share_activity" id="share_activity"
                                            class="form-check-input" value="0">
                                        <label for="share_activity" class="form-check-label fw-bold"> Share
                                            Activity</label>
                                    </div>
                                </div>
                                <div class="row" id="schedule_fields" style="display: none;">
                                    <!-- Accessible Date -->
                                    <div class="col-md-6 mb-3">
                                        <label for="accessible_date" class="form-label fw-bold">Accessible Date</label>
                                        <input type="date" name="accessible_date" id="accessible_date"
                                            class="form-control">
                                    </div>
                                    <!-- Accessible Time -->
                                    <div class="col-md-6 mb-3">
                                        <label for="accessible_time" class="form-label fw-bold">Accessible Time</label>
                                        <input type="time" name="accessible_time" id="accessible_time"
                                            class="form-control">
                                    </div>
                                </div>

                                <!-- Container for dynamically loaded class checkboxes -->
                                <div id="classlist_container" class="mt-4 mb-4 border rounded p-4"
                                    style="display: none;">
                                    <label class="form-label fw-bold">Select Classes to Share</label>
                                    <div id="classlist_checkboxes" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                    </div>
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
    <div class="modal fade" id="removeConfirmModal" tabindex="-1" aria-labelledby="removeConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveConfirmLabel">Remove Student</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove this student?</p>
                    <input type="text" id="removeClassId">
                    <input type="text" id="userID">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemove">Remove</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('.summernote').summernote({
                height: 200, // Set height
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                ]
            });
            $(document).on('click', '.remove-btn', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let userID = $(this).data('user');
                $('#removeClassId').val(id);
                $('#userID').val(userID);
                $('#removeConfirmModal').modal('show'); // Show the modal
                $('#confirmRemove').click(function() {
                    let id = $('#removeClassId').val();
                    let userID = $('#userID').val();

                    $.ajax({
                        url: "{{ route('remove.student') }}",
                        type: "POST",
                        data: {
                            userID: userID,
                            id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            console.log(response);
                            $('#removeConfirmModal').modal(
                                'hide'); // Hide the modal after successful archive
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Class unenrolled successfully',
                            });
                            // loadClasses(); // Reload class list

                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.',
                            });

                        }
                    });
                });

            });

            // Ensure content is passed correctly on form submission
            $('#addActivityForm').on('submit', function() {
                let instructionContent = $('.summernote').summernote('code');
                $('#instruction').val(instructionContent);
            });
        });

        document.getElementById('share_activity').addEventListener('change', function() {
            let hiddenInput = document.getElementById('share_activity_hidden');
            hiddenInput.value = this.checked ? "1" : "0";
        });
        document.getElementById('share_activity').addEventListener('change', function() {
            let classlistContainer = document.getElementById('classlist_container');
            let classlistCheckboxes = document.getElementById('classlist_checkboxes');
            let classlistId = "{{ $classlist->id }}"; // Get the current class ID

            if (this.checked) {
                classlistContainer.style.display = 'block'; // Show checkboxes

                // Fetch classes dynamically from the backend
                fetch(`/instructor/get-classes/${classlistId}`) // Pass current class ID
                    .then(response => response.json())
                    .then(data => {
                        classlistCheckboxes.innerHTML = ''; // Clear previous checkboxes

                        data.forEach(classItem => {
                            let div = document.createElement('div');
                            div.classList.add('col'); // Responsive column

                            let checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'selected_classes[]';
                            checkbox.value = classItem.id;
                            checkbox.id = 'class_' + classItem.id;
                            checkbox.classList.add('form-check-input');

                            let label = document.createElement('label');
                            label.htmlFor = 'class_' + classItem.id;
                            label.textContent = classItem.name + ' | ' + classItem.section.name;
                            label.classList.add('form-check-label');

                            let checkContainer = document.createElement('div');
                            checkContainer.classList.add('form-check');
                            checkContainer.appendChild(checkbox);
                            checkContainer.appendChild(label);

                            div.appendChild(checkContainer);
                            classlistCheckboxes.appendChild(div);
                        });
                    })
                    .catch(error => console.error('Error fetching class list:', error));
            } else {
                classlistContainer.style.display = 'none'; // Hide checkboxes
            }
        });


        document.getElementById('schedule_activity').addEventListener('change', function() {
            let scheduleFields = document.getElementById('schedule_fields');

            if (this.checked) {
                scheduleFields.style.display = 'flex'; // Show fields
            } else {
                scheduleFields.style.display = 'none'; // Hide fields
            }
        });
        /** Copy Share Code Functionality **/
        function copyShareCode() {
            let copyText = document.getElementById("shareCode");
            copyText.select();
            navigator.clipboard.writeText(copyText.value).then(() => {
                Swal.fire({
                    position: "bottom",
                    title: "Share code copied: " + copyText.value,
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
            $(document).on("click", ".dropdown-menu", function(event) {
                event.stopPropagation();
            });

            // Handle card clicks, excluding the dropdown menu
            $(document).on("click", ".activity-card", function(event) {
                if (!$(event.target).closest(".dropdown").length) {
                    let url = $(this).data("url"); // Get the activity URL
                    window.location.href = url; // Redirect to the activity details page
                }
            });
            let classlistId = "{{ $classlist->id }}"; // Pass classlist_id from Blade to JavaScript

            loadActivities(classlistId); // Load activities dynamically

            function loadActivities(classlistId) {
                $.ajax({
                    url: `/instructor/activities/list/${classlistId}`, // Correctly append classlistId
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        let classCards = '';
                        let classlist = response.classlist;
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
                            console.log(response.data);
                            $.each(response.data, function(index, activity) {
                                classCards += `
                             <div class="card shadow-sm border-0 rounded-3 p-2 mb-3 activity-card" data-url="/instructor/activity/${activity.id}" style="cursor: pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <!-- Left Side: Icon -->
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3 mr-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-clipboard-list text-white"></i>
                                        </div>
                                        <!-- Text Content -->
                                        <div>
                                            <p class="fw-bold mb-0">${classlist.user.name} posted a new assignment: <span class="text-dark">${activity.title}</span></p>
                                            ${activity.created_at ? new Date(activity.created_at).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' }) : 'No date available'}
                                        </div>
                                    </div>

                                    <!-- Right Side: Three-dot Menu -->
                                    <div class="dropdown">
                                        <button class="btn btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                                <path d="M9.5 12.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                            </svg>
                                            </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>

                                                <a class="dropdown-item edit-btn" href="#"
                                                    data-id="${activity.id || ''}"
                                                    data-title="${activity.title || ''}"
                                                    data-points="${activity.points || ''}"
                                                    data-instruction="${encodeURIComponent(activity.instruction) || ''}"
                                                    data-due_date="${activity.due_date || ''}"
                                                    data-due_time="${activity.due_time || ''}"
                                                    data-accessible_date="${activity.accessible_date || ''}"
                                                    data-accessible_time="${activity.accessible_time || ''}">
                                                    Edit
                                                </a>
                                            </li>
                                            <li><a class="dropdown-item delete-btn"  data-id="${activity.id}">Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            </a>
                `;
                            });
                        }
                        $('#classCards').html(classCards);
                    }
                });
            }


            $('#addActivityForm').submit(function(e) {
                e.preventDefault();
                let id = $('#activity_id').val().trim();
                let classlistId = $('#classlist_id').val(); // Always keep classlist_id
                let sectionId = $('#section_id').val(); // Always keep section_id
                let url = id ? `activity/update/${id}` : "{{ route('activity.store') }}";
                let method = id ? 'PUT' : 'POST';
                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#addActivityModal').modal('hide');
                        // Clear all fields except hidden ones
                        $('#addActivityForm').find('input:not([type=hidden]), textarea').val(
                            '');
                        $('#addActivityForm').find('.summernote').summernote('code', '');
                        // Reassign hidden field values
                        $('#classlist_id').val("{{ $classlist->id }}");
                        $('#section_id').val("{{ $classlist->section->id }}");
                        $('#classlist_id').val(classlistId); // Restore classlist_id after reset
                        $('#section_id').val(sectionId); // Restore section_id after reset
                        Swal.fire({
                            icon: "success",
                            text: "Activity saved successfully!",
                        });
                        loadActivities(classlistId);

                    },
                    error: function(xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                        alert("Error: " + xhr.responseJSON.error);
                    }
                });
            });
            /** Edit Activity **/
            $(document).on("click", ".edit-btn", function() {
                $("#activity_id").val($(this).data('id'));
                $("#title").val($(this).data('title'));
                $("#points").val($(this).data('points'));

                // Retrieve and decode HTML content
                let instructionHtml = decodeURIComponent($(this).data('instruction'));

                // Set HTML content inside Summernote
                $('#instruction').summernote('code', instructionHtml);

                $("#due_date").val($(this).data('due_date'));
                $("#due_time").val($(this).data('due_time'));
                $("#accessible_date").val($(this).data('accessible_date'));
                $("#accessible_time").val($(this).data('accessible_time'));

                $("#addActivityModal").modal("show");
            });


            /** Delete Activity **/
            $(document).on('click', '.delete-btn', function() {
                let classlistId = "{{ $classlist->id }}";
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this Activity?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `activity/${id}`,
                            type: "DELETE",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                loadActivities(classlistId);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Activity has been deleted successfully.',
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                });
                            }
                        });
                    }
                });
            });


            /** Fix Bootstrap Dropdown **/
            $(".dropdown-toggle").dropdown();
        });
    </script>
@endpush

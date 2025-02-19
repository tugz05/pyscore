@extends('user.dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Class Header -->
        <div class="card shadow-lg rounded border-0">
            <div class="card-body bg-primary text-white d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold">{{ $classlist->name }}</h2>
                    <p class="mb-0">{{ $classlist->section->name }}</p>
                </div>
                <button class="btn btn-light">Customize</button>
            </div>
        </div>
        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#stream">Stream</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#classwork">Classwork</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#people">People</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#grades">Grades</a>
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
                                <button class="btn btn-primary w-100" onclick="copyShareCode()">Copy Code</button>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-bold">Upcoming</h6>
                                <p class="text-muted">No work due soon</p>
                                <a href="#" class="text-primary fw-bold">View all</a>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-md-9">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <img src="{{ $classlist->user->avatar }}" class="rounded-circle me-3" alt="User" style="width: 50px; height: 50px; border: 1; border-color: black; border-style: ;">
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
                <h1>People</h1>
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
                            <textarea name="instruction" id="instruction" class="form-control" placeholder="Enter detailed instructions..."
                                rows="5"></textarea>
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
                            <!-- Accessible Date -->
                            <div class="col-md-6 mb-3">
                                <label for="accessible_date" class="form-label fw-bold">Accessible Date</label>
                                <input type="date" name="accessible_date" id="accessible_date" class="form-control"
                                    required>
                            </div>
                            <!-- Accessible Time -->
                            <div class="col-md-6 mb-3">
                                <label for="accessible_time" class="form-label fw-bold">Accessible Time</label>
                                <input type="time" name="accessible_time" id="accessible_time" class="form-control"
                                    required>
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
        /** Copy Share Code Functionality **/
        function copyShareCode() {
            let copyText = document.getElementById("shareCode");
            copyText.select();
            navigator.clipboard.writeText("http://127.0.0.1:8000/" + copyText.value).then(() => {
                alert("Share code copied: " + copyText.value);
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
                                                    data-instruction="${activity.instruction || ''}"
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

                        // Reassign hidden field values
                        $('#classlist_id').val("{{ $classlist->id }}");
                        $('#section_id').val("{{ $classlist->section->id }}");
                        $('#classlist_id').val(classlistId); // Restore classlist_id after reset
                        $('#section_id').val(sectionId); // Restore section_id after reset
                        alert(response.success || "Activity saved successfully!");
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
                $("#instruction").val($(this).data('instruction'));
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
                if (confirm('Delete this Activity?')) {
                    $.ajax({
                        url: `activity/${id}`,
                        type: "DELETE",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            loadActivities(classlistId);
                            alert("Activity deleted successfully!");
                        }
                    });
                }
            });

            /** Fix Bootstrap Dropdown **/
            $(".dropdown-toggle").dropdown();
        });
    </script>
@endpush

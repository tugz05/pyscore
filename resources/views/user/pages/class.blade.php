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
                {{-- <button class="btn btn-light">Customize</button> --}}
            </div>
        </div>

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#stream">Stream</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#classwork">Classwork</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#people">People</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#grades">Grades</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <!-- Stream Tab -->
            <div class="tab-pane fade show active" id="stream">
                <div class="row mt-4">
                    <!-- Left Sidebar -->
                    <div class="col-md-3">


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
                                <img src="{{ Auth::user()->avatar }}" class="rounded-circle me-3" alt="User" style="width: 50px; height: 50px;">
                            </div>
                        </div>
                        <div id="classCards">
                            <!-- AJAX-loaded class cards will appear here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classwork Tab -->
            <div class="tab-pane fade show" id="classwork">
                <h1>Classwork</h1>
            </div>

            <!-- People Tab -->
            <div class="tab-pane fade show" id="people">
                <h1>People</h1>
            </div>
        </div>
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
                    {{-- <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}"> --}}
                    <input type="hidden" name="classlist_id" id="classlist_id" value="{{ $classlist->id }}">
                    <input type="hidden" name="section_id" id="section_id" value="{{ $classlist->section->id }}">
                    <input type="hidden" name="activity_id" id="activity_id">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Activity Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Activity Title" required>
                        </div>
                        <div class="mb-3">
                            <label for="instruction" class="form-label fw-bold">Instructions</label>
                            <textarea name="instruction" id="instruction" class="form-control" placeholder="Enter detailed instructions..." rows="5"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 mb-3">
                                <label for="points" class="form-label fw-bold">Points</label>
                                <input type="number" name="points" id="points" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label fw-bold">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="due_time" class="form-label fw-bold">Due Time</label>
                                <input type="time" name="due_time" id="due_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="accessible_date" class="form-label fw-bold">Accessible Date</label>
                                <input type="date" name="accessible_date" id="accessible_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="accessible_time" class="form-label fw-bold">Accessible Time</label>
                                <input type="time" name="accessible_time" id="accessible_time" class="form-control" required>
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
            navigator.clipboard.writeText(copyText.value).then(() => {
                alert("Share code copied: " + copyText.value);
            }).catch(err => {
                console.error("Error copying text:", err);
            });
        }
        function copyLink() {
            let copyText = document.getElementById("shareCode");
            copyText.select();
            navigator.clipboard.writeText("http://127.0.0.1:8000/student/join/class/s/" + copyText.value).then(() => {
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
        url: `/student/activities/list/${classlistId}`,
        type: "GET",
        dataType: "json",
        success: function(response) {
            let classCards = '';
            let classlist = response.classlist;
            let activityPromises = []; // Store all submission status fetches

            if (response.data.length === 0) {
                classCards = `
                    <div class="d-flex align-items-center justify-content-center w-100" style="height: 75vh;">
                        <div class="text-center">
                            <img src="/assets/img/undraw_posting_photo.svg" style="max-width: 50%; height: auto; padding: 20px;">
                            <h1>No activity available</h1>
                        </div>
                    </div>
                `;
                $('#classCards').html(classCards);
                return;
            }

            console.log(response.data);

            $.each(response.data, function(index, activity) {
                let user_id = {{ Auth::id() }};
                let submissionPromise = $.ajax({
                    url: `/student/submission-status/${user_id}/${activity.id}`,
                    method: 'GET',
                    dataType: 'json'
                }).then(submissionResponse => {
                    let submissionStatus = submissionResponse.status;
                    let statusClass, statusClassBadge;

                    if (submissionStatus === 'Submitted') {
                        statusClass = 'text-success';
                        statusClassBadge = 'bg-success';
                    } else if (submissionStatus === 'Missing') {
                        statusClass = 'text-danger';
                        statusClassBadge = 'bg-danger';
                    } else { // Pending status
                        statusClass = 'text-warning';
                        statusClassBadge = 'bg-warning';
                    }
                    let submissionAssignedScore = submissionResponse.assigned_score;
                    console.log(submissionResponse.total_score);

                    classCards += `
                        <div class="card shadow-sm border-0 rounded-3 p-2 mb-3 activity-card" data-url="/student/activity/${activity.id}" style="cursor: pointer;">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <!-- Left Side: Icon -->
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3 mr-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-clipboard-list text-white"></i>
                                    </div>
                                    <!-- Text Content -->
                                    <div class="col">
                                        <p class="fw-bold mb-0">${classlist.user.name} posted a new assignment:
                                            <span class="text-dark">${activity.title}</span>
                                        </p>
                                        ${activity.created_at ? new Date(activity.created_at).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' }) : 'No date available'}
                                        <span class="badge ${statusClassBadge} ml-3 text-white">${submissionAssignedScore} / ${activity.points}</span>
                                    </div>
                                </div>
                                <!-- Submission Status -->
                                <span class="fw-bold ${statusClass} float-end">
                                    ${submissionStatus}
                                </span>
                            </div>
                        </div>
                    `;
                });

                activityPromises.push(submissionPromise);
            });

            // After all AJAX calls finish, update the UI
            Promise.all(activityPromises).then(() => {
                $('#classCards').html(classCards);
            });
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
                        Swal.fire({
                        icon: "success",
                        title: "Activity Created!",
                        text: `${response.success}` || `Activity saved successfully!`,
                        timer: 3000,
                        showConfirmButton: false
                    });

                        loadActivities(classlistId);

                    },
                    error: function(xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                        alert("Error: " + xhr.responseJSON.error);
                        Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: `${response.error}` || `An error occurred. Please try again.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
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

@extends('user.dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Class Header -->
        <div class="card-body text-white d-flex align-items-center justify-content-between"
            style="background: url('{{ asset('assets/course_images') }}/{{ $classlist->course_image }}') no-repeat center center;
               background-size: cover; min-height: 200px;">

            <div style="background: rgba(0, 0, 0, 0.3); padding: 15px; border-radius: 10px;">
                <h2 class="fw-bold">{{ $classlist->name }}</h2>
                <p class="mb-0">{{ $classlist->section->name }}</p>
            </div>

        </div>

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#stream">Stream</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#people">People</a>
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

                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-md-9">
                        <div id="classCards">
                            <!-- AJAX-loaded class cards will appear here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- People Tab (Should only show instructor & classmates) -->
            <div class="tab-pane fade" id="people">
                @include('user.pages.people')
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
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

            let classlistId = "{{ $classlist->id }}"; // Get classlist_id from Blade

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

                        let currentDate = new Date().toISOString().split('T')[0]; // Get YYYY-MM-DD
                        let currentTime = new Date().toTimeString().split(' ')[0]; // Get HH:MM:SS

                        $.each(response.data, function(index, activity) {
                            let activityDate = activity.accessible_date;
                            let activityTime = activity.accessible_time;
                            let shouldDisplay = false;

                            if (!activityDate && !activityTime) {
                                shouldDisplay = true;
                            } else if (activityDate) {
                                let activityDateTime = new Date(`${activityDate} ${activityTime || "00:00:00"}`);
                                let currentDateTime = new Date();
                                shouldDisplay = currentDateTime >= activityDateTime;
                            }
                            if (!shouldDisplay) return;

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
                                } else {
                                    statusClass = 'text-warning';
                                    statusClassBadge = 'bg-warning';
                                }

                                let submissionAssignedScore = activity.user_score !== null ? activity.user_score : '--';

                                classCards += `
                                    <div class="card shadow-sm border-0 rounded-3 p-2 mb-3 activity-card" data-url="/student/activity/${activity.id}" style="cursor: pointer;">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3 mr-3" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-clipboard-list text-white"></i>
                                                </div>
                                                <div class="col">
                                                    <p class="fw-bold mb-0">${classlist.user.name} posted a new assignment:
                                                        <span class="text-dark">${activity.title}</span>
                                                    </p>
                                                    ${new Date(activity.created_at).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' })}
                                                    <span class="badge ${statusClassBadge} ml-3 text-white">${submissionAssignedScore} / ${activity.points}</span>
                                                </div>
                                            </div>
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

            /** Fix Bootstrap Dropdown **/
            $(".dropdown-toggle").dropdown();
        });
    </script>
@endpush


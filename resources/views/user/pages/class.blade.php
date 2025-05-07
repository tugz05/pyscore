@extends('user.dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Class Header -->
        <div class="card-body text-white d-flex align-items-center justify-content-between"
            style="background: url('{{ asset('assets/course_images') }}/{{ $classlist->course_image }}') no-repeat center center;
               background-size: cover; min-height: 200px;">

            <div style="background: rgba(0, 0, 0, 0.3); padding: 15px; border-radius: 10px;">
                <h2 class="fw-bold">{{ $classlist->name }}</h2>
                <p class="mb-0">{{ $classlist->section->name }} | {{ $classlist->section->day }}</p>
            </div>

        </div>

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#stream">Stream</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#people">People</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#rubric">Rubric</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <!-- Stream Tab -->
            <div class="tab-pane fade show active" id="stream">
                <div class="row mt-4">
                    <!-- Left Sidebar -->
                    @if(!$classlist->is_archive)
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">

                                <h7 class="fw-bold">Due Today:</h3>
                                    <div id="upcoming-activities">
                                        <p class="text-muted">Loading upcoming activities...</p>
                                    </div>
                                    <h7 id="due-tomorrow" class="fw-bold">Due Tomorrow:</h3>
                                        <div id="upcoming-activities-tomorrow">
                                        </div>
                            </div>
                        </div>

                    </div>

@else
<div class="col-md-3">
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body text-center">

            <h4 >CLASS HAS BEEN ARCHIVED</h4>
            <p class="text-muted">Archived classes can only be <b>viewed</b> by the students and cannot be
                <b>modified</b>
                unless they are restored by the instructor.
            </p>
        </div>
    </div>
</div>
@endif
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
            <div class="tab-pane fade" id="rubric">
                @include('user.pages.rubric')
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
                        let classCards = "";
                        let upcomingToday = "";
                        let upcomingTomorrow = "";
                        let hasToday = false;

                        let classlist = response.classlist;
                        let activityPromises = [];

                        let todayDate = new Date().toISOString().split('T')[
                            0]; // Get today's date in YYYY-MM-DD
                        let tomorrowDate = new Date();
                        tomorrowDate.setDate(tomorrowDate.getDate() + 1);
                        tomorrowDate = tomorrowDate.toISOString().split('T')[
                            0]; // Convert to YYYY-MM-DD

                        // ===========================
                        // 📌 Handle Upcoming Activities (Due Today)
                        // ===========================
                        response.data.forEach(activity => {
                            let activityDueDate = activity
                                .due_date; // Ensure format is YYYY-MM-DD

                            console.log(
                                `Activity: ${activity.title}, Due Date: ${activityDueDate}, Today: ${todayDate}`
                            ); // Debugging log

                            let isAccessible = true;

if (activity.accessible_date) {
    const accessibleDateTime = new Date(`${activity.accessible_date}T${activity.accessible_time || "00:00:00"}`);
    const now = new Date();
    isAccessible = now >= accessibleDateTime;
}

if (!isAccessible) return; // Skip if it's not yet accessible

if (activityDueDate === todayDate) {
    hasToday = true;
    upcomingToday +=
        `<p class="mb-1">
            <strong>${activity.due_time || "No time specified"}</strong> |
            <a href="/student/activity/${activity.id}" class="text-primary fw-bold">${activity.title}</a>
        </p>`;
} else if (activityDueDate === tomorrowDate) {
    upcomingTomorrow +=
        `<p class="mb-2">
            <strong>${activity.due_time || "No time specified"}</strong> |
            <a href="/student/activity/${activity.id}" class="text-primary fw-bold">${activity.title}</a>
        </p>`;
}

                        });

                        if (!hasToday) {
                            upcomingToday = `<p class="text-muted">No work due today</p>`;
                        }

                        $("#upcoming-activities").html(upcomingToday);
                        if (upcomingTomorrow === "") {
                            upcomingTomorrow = `<p class="text-muted">No work due tomorrow</p>`;
                        }
                        $("#upcoming-activities-tomorrow").html(upcomingTomorrow);

                        // ===========================
                        // 📌 Handle Activity Listing
                        // ===========================
                        if (response.data.length === 0) {
                            classCards =
                                `<div class="d-flex align-items-center justify-content-center w-100" style="height: 75vh;">
                                    <div class="text-center">
                                        <img src="/assets/img/undraw_posting_photo.svg" style="max-width: 50%; height: auto; padding: 20px;">
                                        <h1>No activity available</h1>
                                    </div>
                                </div>`;
                            $('#classCards').html(classCards);
                            return;
                        }

                        let currentDate = new Date().toISOString().split('T')[0];
                        let currentTime = new Date().toTimeString().split(' ')[0];

                        $.each(response.data, function(index, activity) {
                            let activityDate = activity.accessible_date;
                            let activityTime = activity.accessible_time;
                            let shouldDisplay = false;

                            if (!activityDate && !activityTime) {
                                shouldDisplay = true;
                            } else if (activityDate) {
                                let activityDateTime = new Date(
                                    `${activityDate} ${activityTime || "00:00:00"}`);
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

                                let submissionAssignedScore = submissionStatus ===
                                    'Missing' ? 0 : (activity.user_score !== null ?
                                        activity.user_score : '--');
                                if (submissionAssignedScore === '--') {
                                    statusClass = 'text-warning';
                                    statusClassBadge = 'bg-warning';
                                    submissionStatus = 'Pending';
                                }
                                classCards +=
                                    `<div class="card shadow-sm border-0 rounded-3 p-2 mb-3 activity-card" data-url="/student/activity/${activity.id}" style="cursor: pointer;">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3 mr-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-clipboard-list text-white"></i>
                                    </div>
                                    <div class="col">
                                        <p class="fw-bold mb-0">${classlist.user.name} posted a new assignment:
                                            <span class="text-dark">${activity.title}</span>
                                        </p>Posted:
                                        ${new Date(activity.created_at).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' })}
                                        <span class="badge ${statusClassBadge} ml-3 text-white">${submissionAssignedScore} / ${activity.points}</span>
                                    </div>
                                </div>
                                <span class="fw-bold ${statusClass} float-end">
                                    ${submissionStatus}
                                </span>
                            </div>
                        </div>`;
                            });

                            activityPromises.push(submissionPromise);
                        });

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

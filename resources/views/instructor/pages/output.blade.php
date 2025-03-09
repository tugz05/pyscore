<style>
    .student-list {
        max-height: 700px;
        overflow-y: auto;
    }

    .student-list::-webkit-scrollbar {
        width: 6px;
    }

    .student-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    .student-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .code-editor {
        width: 100%;
        height: 500px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">

        <!-- Left Column: Student List -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold">Student List</h6>

                    <!-- Score Filtering Dropdown -->
                    <select id="scoreFilter" class="form-select form-select-sm text-dark" style="width: 150px;">
                        <option value="default">Sort by Score</option>
                        <option value="asc">Lowest to Highest</option>
                        <option value="desc">Highest to Lowest</option>
                    </select>
                </div>

                <div class="card-body p-0 student-list">
                    <ul id="studentList" class="list-group list-group-flush">
                        <!-- Dynamic Student List -->
                        @forelse ($students as $student)
                        <li class="list-group-item student-item d-flex align-items-center justify-content-between p-3"
                            data-user-id="{{ $student->user->id }}"
                            data-activity-id="{{ $activity->id }}"
                            data-score="{{ $student->score == '--' ? 0 : $student->score }}">

                            <div class="d-flex align-items-center">
                                <img src="{{ $student->user->avatar ?? 'https://via.placeholder.com/45' }}"
                                    alt="Profile" class="rounded-circle me-3 ml-3" width="45" height="45">
                                <div class="ml-3">
                                    <span class="fw-bold">{{ $student->user->name }}</span>
                                    <span class="fw-bold text-success">{{ $student->score }}/{{ $activity->points }}</span>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Student</p>
                                </div>
                            </div>
                        </li>
                        @empty
                            <div class="d-flex align-items-center">
                                <h5 class="text-center">No students enrolled</h5>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column: Output, Score, Feedback -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Evaluation</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="studentOutput" class="form-label">Student Output</label>
                        <div id="editor" class="code-editor"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    Score
                                    <div id="score" class="h5 mb-0 font-weight-bold">--/{{ $activity->points }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body">
                                    Feedback
                                    <div id="feedback" class="small">No feedback available.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<!-- Include ACE Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>

<script>
    $(document).ready(function () {
        let editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/python");
        editor.setReadOnly(true);

        $(".student-item").on("click", function () {
            let userId = $(this).data("user-id");
            let activityId = $(this).data("activity-id");

            $.ajax({
                url: `/instructor/get-student-output/${userId}/${activityId}`,
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        editor.setValue(response.output.code);
                        $("#score").text(response.output.score + "/{{ $activity->points }}");
                        $("#feedback").text(response.output.feedback);
                    } else {
                        editor.setValue("");
                        $("#score").text("/{{ $activity->points }}");
                        $("#feedback").text("No feedback available.");
                    }
                },
                error: function () {
                    alert("Error fetching student output.");
                }
            });
        });

        // Score Filtering Logic
        $("#scoreFilter").on("change", function () {
            let order = $(this).val();
            let studentList = $("#studentList");

            let students = $(".student-item").get();
            students.sort(function (a, b) {
                let scoreA = parseInt($(a).data("score"));
                let scoreB = parseInt($(b).data("score"));

                if (order === "asc") {
                    return scoreA - scoreB; // Sort Lowest to Highest
                } else if (order === "desc") {
                    return scoreB - scoreA; // Sort Highest to Lowest
                }
                return 0; // Default Order
            });

            $.each(students, function (index, student) {
                studentList.append(student);
            });
        });
    });
</script>
@endpush

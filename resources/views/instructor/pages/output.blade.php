<style>
    /* Hover effect when selecting */
    .student-item:hover {
        background-color: #ececec;
        cursor: pointer;
    }

    /* Highlight the selected student */
    .student-item.active {
        background-color: #dadada;
        color: black !important;
    }

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

    /* Custom styling for the score filter dropdown */
    #scoreFilter {
        appearance: none;
        background-color: #e0e0e0;
        color: black;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    /* Change background on focus */
    #scoreFilter:focus {
        outline: none;
    }

    /* Copy button styles */
    .copy-btn {
        transition: all 0.3s ease;
        padding: 5px 10px;
        font-size: 0.8rem;
    }

    .copy-btn.copied {
        background-color: #28a745 !important;
        color: white !important;
    }
    /* To remove the gray border effect on click */
#copyBtn:active,
#copyBtn:focus {
    box-shadow: none !important;
    border-color: #6c757d !important; /* Same as default */
    outline: none !important;
}

/* Or to customize the click effect */
#copyBtn:active {
    border-color: #28a745 !important; /* Green border when clicked */
    background-color: rgba(40, 167, 69, 0.1) !important; /* Light green background */
}
</style>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">Student Submissions</h4>
        <button class="btn btn-primary" id="refreshBtn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    <div class="row mt-3">
        <!-- Left Column: Student List -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold">Student List</h6>
                    <select id="scoreFilter" class="form-select form-select-sm text-dark" style="width: 150px;">
                        <option value="default">Sort by Score</option>
                        <option value="asc">Lowest to Highest</option>
                        <option value="desc">Highest to Lowest</option>
                    </select>
                </div>

                <div class="card-body p-0 student-list">
                    <ul id="studentList" class="list-group list-group-flush" data-activity-id="{{ $activity->id }}">
                        @forelse ($students as $student)
                            <li class="list-group-item student-item d-flex align-items-center justify-content-between p-3"
                                data-user-id="{{ $student->user->id }}" data-activity-id="{{ $activity->id }}"
                                data-score="{{ $student->score == '--' ? 0 : $student->score }}">

                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->user->avatar ?? 'https://via.placeholder.com/45' }}"
                                        alt="Profile" class="rounded-circle me-3 ml-3" width="45" height="45">
                                    <div class="ml-3">
                                        <span class="fw-bold">{{ $student->user->name }}</span>
                                        <span class="fw-bold text-success">{{ $student->score }}/{{ $activity->points }}</span>

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
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Student Evaluation</h6>
                    <button id="copyBtn" class="btn btn-sm btn-outline-secondary copy-btn">
                        <i class="fas fa-copy"></i> <span class="btn-text">Copy</span>
                    </button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            let editor = ace.edit("editor");
            editor.setTheme("ace/theme/monokai");
            editor.session.setMode("ace/mode/python");
            editor.setReadOnly(true);

            function attachStudentItemHandlers() {
                $(".student-item").on("click", function() {
                    let userId = $(this).data("user-id");
                    let activityId = $(this).data("activity-id");

                    $(".student-item").removeClass("active");
                    $(this).addClass("active");

                    $.ajax({
                        url: `/instructor/get-student-output/${userId}/${activityId}`,
                        type: "GET",
                        success: function(response) {
                            if (response.success) {
                                editor.setValue(response.output.code, -1);
                                $("#score").text(response.output.score + "/{{ $activity->points }}");
                                $("#feedback").text(response.output.feedback);
                            } else {
                                editor.setValue("");
                                $("#score").text("--/{{ $activity->points }}");
                                $("#feedback").text("No feedback available.");
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Error fetching student output.' });
                        }
                    });
                });
            }

            attachStudentItemHandlers();

            $("#scoreFilter").on("change", function() {
                let order = $(this).val();
                let students = $(".student-item").get();

                students.sort(function(a, b) {
                    let scoreA = parseInt($(a).data("score"));
                    let scoreB = parseInt($(b).data("score"));

                    return order === "asc" ? scoreA - scoreB : scoreB - scoreA;
                });

                $.each(students, function(_, student) {
                    $("#studentList").append(student);
                });
            });

            function refreshStudentList() {
                let refreshButton = $("#refreshBtn");
                refreshButton.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...').prop("disabled", true);

                const activityId = $('#studentList').data('activity-id');

                $.ajax({
                    url: `/instructor/activity/${activityId}/students`,
                    type: "GET",
                    success: function(response) {
                        let html = response.length > 0 ? response.map(student => `
                            <li class="list-group-item student-item d-flex align-items-center justify-content-between p-3"
                                data-user-id="${student.user_id}" data-activity-id="${student.activity_id}"
                                data-score="${student.score_value}">
                                <div class="d-flex align-items-center">
                                    <img src="${student.avatar}" alt="Profile" class="rounded-circle me-3 ml-3" width="45" height="45">
                                    <div class="ml-3">
                                        <span class="fw-bold">${student.name}</span>
                                        <span class="fw-bold text-success">${student.score}/${student.points}</span>
                                    </div>
                                </div>
                            </li>
                        `).join("") : `<h5 class="text-center">No students enrolled</h5>`;

                        $('#studentList').html(html);
                        attachStudentItemHandlers();
                        refreshButton.html('<i class="fas fa-sync-alt"></i> Refresh').prop("disabled", false);
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to refresh student list' });
                        refreshButton.html('<i class="fas fa-sync-alt"></i> Refresh').prop("disabled", false);
                    }
                });
            }

            $("#refreshBtn").on("click", refreshStudentList);

            // Copy button functionality
            $("#copyBtn").on("click", function() {
                const code = editor.getValue();
                if (code.trim() === "") {
                    return;
                }

                // Copy to clipboard
                navigator.clipboard.writeText(code).then(() => {
                    const $btn = $(this);
                    const $icon = $btn.find("i");
                    const $text = $btn.find(".btn-text");

                    // Change to check icon and "Copied!" text
                    $icon.removeClass("fa-copy").addClass("fa-check");
                    $text.text("Copied!");
                    $btn.addClass("copied");

                    // Revert after 2 seconds
                    setTimeout(() => {
                        $icon.removeClass("fa-check").addClass("fa-copy");
                        $text.text("Copy");
                        $btn.removeClass("copied");
                    }, 800);
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            });
        });
    </script>
@endpush

<head>
    {{-- Ace Editor for Python Code Input --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        .code-editor {
            width: 100%;
            height: 500px; /* Adjusted default height */
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .code-editor {
                height: 300px; /* Reduce height for smaller screens */
            }
        }

        .custom-file-label::after {
            content: "Browse";
        }
    </style>
</head>

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <h1 id="page-title" class="h3 mb-3 mb-md-0 text-gray-800">
            <i class="fa-solid fa-code text-primary"></i> Submit Python Code
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-7 col-sm-12 mb-4">
            <!-- Code Editor -->
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Python Code Editor</h5>
                </div>
                <div class="card-body">
                    <div id="editor" class="code-editor"></div>

                    <form id="codeForm">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="section_id" id="section_id" value="{{ $activity->section_id }}">
                        <input type="hidden" name="activity_id" id="activity_id" value="{{ $activity->id }}">
                        <input type="hidden" name="python_code" id="python_code">

                        <div class="mt-3 text-right">
                            <button type="button" class="btn btn-success btn-block" id="submitCode">
                                <i class="fas fa-paper-plane"></i> Submit Code
                            </button>
                        </div>
                    </form>

                    <div id="responseMessage" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Feedback & Score Section -->
        <div class="col-lg-4 col-md-5 col-sm-12">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Feedback</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted" id="feedback-text">No submission found.</p>
                </div>
                <div class="card-footer text-center">
                    <h5 id="score-text" class="text-success"></h5>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    // Initialize Ace Editor
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/python");
    editor.setOptions({
        fontSize: "16px",
        wrap: true,
        showPrintMargin: false
    });

    // Adjust editor height dynamically based on screen size
    function adjustEditorHeight() {
        if (window.innerWidth < 768) {
            editor.container.style.height = "300px"; // Mobile height
        } else {
            editor.container.style.height = "500px"; // Default height
        }
        editor.resize();
    }

    // Call function on load and window resize
    adjustEditorHeight();
    window.addEventListener("resize", adjustEditorHeight);

    // Fetch student submission
    function checkSubmission() {
        $.ajax({
            url: "{{ route('check.submission') }}",
            type: "GET",
            data: {
                user_id: $("#user_id").val(),
                activity_id: $("#activity_id").val()
            },
            success: function (response) {
                if (response.submitted) {
                    // Display score and feedback
                    $("h1#page-title").html('<i class="fa-solid fa-code text-primary"></i> Submitted Python Code');
                    $("#feedback-text").html("<strong>Feedback:</strong> " + response.feedback);
                    $("#score-text").html("Score: " + response.score + "/"+ response.assigned_score);

                    // Disable submit button
                    $("#submitCode").prop("disabled", true).addClass("btn-secondary").removeClass("btn-success");

                    // Load the submitted code into the editor
                    editor.setValue(response.python_code, -1);
                    editor.setReadOnly(true);
                }
                else{
                    $("h1#page-title").html('<i class="fa-solid fa-code text-primary"></i> Submit Python Code');
                }
            },
            error: function () {
                console.log("Error checking submission.");
            }
        });
    }

    // Call the function on page load
    checkSubmission();

    // AJAX Submission
    $('#submitCode').on('click', function () {
        var pythonCode = editor.getValue();
        $('#python_code').val(pythonCode);

        $.ajax({
            url: "{{ route('submit.python.code') }}",
            type: "POST",
            data: $('#codeForm').serialize(),
            success: function (response) {
                $('#responseMessage').html(
                    '<div class="alert alert-success">Code submitted successfully!</div>'
                );

                // Refresh the submission status
                checkSubmission();
            },
            error: function (xhr) {
                $('#responseMessage').html(
                    '<div class="alert alert-danger">Error submitting code. Please try again.</div>'
                );
            }
        });
    });

</script>
@endpush

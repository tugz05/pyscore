<head>
    {{-- Ace Editor for Python Code Input --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        .code-editor {
            width: 100%;
            height: 500px;
            /* Adjusted default height */
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .code-editor {
                height: 300px;
                /* Reduce height for smaller screens */
            }
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loading-overlay .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 24px;
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
<div class="loading-overlay">
    <div class="spinner">
        <i class="fas fa-spinner fa-spin"></i> Submitting your code...
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

        function showLoading() {
            $(".loading-overlay").fadeIn();
            $("#submitCode").prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        }

        // Function to hide loading UI
        function hideLoading() {
            $(".loading-overlay").fadeOut();
            $("#submitCode").prop("disabled", false).html('<i class="fas fa-paper-plane"></i> Submit Code');
        }

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
                success: function(response) {
                    if (response.is_missing) {
                        // If activity is missing, set score to 0 and display assigned points
                        $("#feedback-text").html(response.feedback);
                        $("#score-text").html("Score: 0 / " + (response.assigned_score ||
                            100)); // Default to 100 if undefined
                        $("#submitCode").prop("disabled", true).addClass("btn-secondary").removeClass(
                            "btn-success");
                        editor.setReadOnly(true); // Make editor read-only
                        return; // Stop further execution
                    }

                    if (response.submitted) {
                        $("h1#page-title").html(
                            '<i class="fa-solid fa-code text-primary"></i> Submitted Python Code');
                        $("#feedback-text").html(response.feedback);
                        $("#score-text").html("Score: " + response.score + " / " + (response.assigned_score ||
                            100));

                        $("#submitCode").prop("disabled", true).addClass("btn-secondary").removeClass(
                            "btn-success");

                        editor.setValue(response.python_code, -1);
                        editor.setReadOnly(true);
                    } else {
                        $("h1#page-title").html(
                            '<i class="fa-solid fa-code text-primary"></i> Submit Python Code');
                    }
                },
                error: function() {
                    console.log("Error checking submission.");
                }
            });
        }

        // Call function on page load
        checkSubmission();


        // Call function on page load
        checkSubmission();


        // AJAX Submission
        $('#submitCode').on('click', function() {
            var pythonCode = editor.getValue();
            var encodedCode = btoa(pythonCode); // base64 encode the code

            Swal.fire({
                title: "Are you sure you want to submit?",
                text: "You won't be able to edit or unsubmit your solution!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Submit"
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading();

                    let formData = new FormData();
                    formData.append('code', encodedCode);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: "{{ route('submit.python.code') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            hideLoading();
                            Swal.fire({
                                icon: "success",
                                title: "Code Submitted!",
                                text: "Your code has been successfully submitted.",
                                timer: 3000,
                                showConfirmButton: false
                            });

                            // Refresh the submission status
                            checkSubmission();
                        },
                        error: function(xhr) {
                            hideLoading();
                            Swal.fire({
                                icon: "error",
                                title: "Submission Failed!",
                                text: "There was an error submitting your code. Please try again.",
                                confirmButtonColor: "#d33"
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush

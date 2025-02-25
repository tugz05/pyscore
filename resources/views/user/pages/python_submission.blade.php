<head>
    {{-- Ace Editor for Python Code Input --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>

    <style>
        .code-editor {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .custom-file-label::after {
            content: "Browse";
        }
    </style>
</head>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-code text-primary"></i> Submit Python Code
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Code Editor -->
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Python Code Editor</h5>
                </div>
                <div class="card-body">
                    <div id="editor" class="code-editor"></div>

                    <form method="POST" action="{{ route('submit.python.code') }}">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="section_id" id="section_id" value="{{ $activity->section_id }}">
                        <input type="hidden" name="activity_id" id="activity_id" value="{{ $activity->id }}">
                        <input type="hidden" name="python_code" id="python_code">

                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-success" onclick="setPythonCode()">
                                <i class="fas fa-paper-plane"></i> Submit Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Upload Python File</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="#" enctype="multipart/form-data">
                        @csrf
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pythonFile" name="pythonFile" accept=".py">
                            <label class="custom-file-label" for="pythonFile">Choose Python file...</label>
                        </div>
                        <div class="mt-3 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                        </div>
                    </form>
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
        fontSize: "18px",
        wrap: true,
        showPrintMargin: false
    });

    // Set Python code in the hidden input before submission
    function setPythonCode() {
        document.getElementById("python_code").value = editor.getValue();
    }

    // Update file label when selected
    document.getElementById("pythonFile").addEventListener("change", function () {
        var fileName = this.value.split("\\").pop();
        this.nextElementSibling.innerHTML = fileName;
    });
</script>
@endpush

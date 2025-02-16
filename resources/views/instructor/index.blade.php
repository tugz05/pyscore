@extends('instructor.dashboard')
@section('content')

<!-- Bootstrap & FontAwesome Icons -->
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Custom CSS for hover effects */
        .hover-effect {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            cursor: pointer; /* Makes the cursor a hand on hover */
        }
        .hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<div class="container-fluid bg-gray-100 mx-2">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-brands fa-python text-primary"></i> Welcome Admin
        </h1>
        <h1 class="h3 mb-0 text-gray-800">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClassModal">
                <i class="fa-brands fa-plus mr-3"></i>
                Create Class
            </a>
        </h1>
    </div>

    <!-- Content Row -->

        <div class="row" id="classCards">
            <!-- AJAX-loaded class cards will appear here -->
        </div>

</div>
<!-- Add/Edit Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Class</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <form id="addClassForm">
                <input type="hidden" id="classlist_id">
                <div class="modal-body">
                    @csrf
                    <x-input type="text" name="name" id="name" label="Class Name" required />
                    <x-select name="section_id" id="section_id" label="Select Section" :options="$sections->pluck('name', 'id')->toArray()" required />
                    <x-select name="academic_year" id="academic_year" label="Academic Year"
                        :options="['2024-2025' => '2024-2025', '2025-2026' => '2025-2026']" required />
                    <x-input type="text" name="room" id="room" label="Room" required />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Share Modal --}}
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Class</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-bold">Share this code to allow others to join your class:</p>
                <input type="text" id="shareCode" class="form-control text-center fw-bold">
                <button class="btn btn-success mt-3" onclick="copyShareCode()">Copy Code</button>
            </div>
        </div>
    </div>
</div>




@endsection
@push('script')
<script>
    function copyShareCode() {
    let copyText = document.getElementById("shareCode");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Share code copied: " + copyText.value);
}
    $(document).ready(function() {
        loadClasses();

        function loadClasses() {
            $.ajax({
                url: "{{ route('classlist.data') }}",
                type: "GET",
                success: function(response) {
                    let classCards = '';
                    $.each(response.data, function(index, classlist) {
                        classCards += `
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg rounded-4 border-0 hover-effect h-100">
                <img src="https://picsum.photos/300/120" class="card-img-top rounded-top-4" alt="Course Image">
                <div class="card-body p-3 d-flex flex-column">
                    <h5 class="card-title text-primary fw-bold">${classlist.name}</h5>
                    <p class="card-text text-muted">${classlist.section.name} | ${classlist.academic_year}</p>
                                        <p class="card-text text-muted"><b>Room:</b> ${classlist.room}</p>
                    <div class="mt-auto d-flex justify-content-between">
                        <a class="btn btn-warning edit-btn" data-id="${classlist.id}" data-name="${classlist.name}" data-section_id="${classlist.section_id}" data-academic_year="${classlist.academic_year}" data-room="${classlist.room}">
                            <i class="fas fa-fw fa-pen-to-square"></i>
                        </a>
                        <a class="btn btn-danger delete-btn" data-id="${classlist.id}">
                            <i class="fas fa-fw fa-trash"></i>
                        </a>
                        <button class="btn btn-primary share-btn" data-id="${classlist.id}" data-share_code="${classlist.code}">
                    <i class="fas fa-fw fa-share"></i>
                </button>
                    </div>
                </div>
            </div>
        </div>`;
                    });
                    $('#classCards').html(classCards);
                }
            });
        }

        $('#addClassForm').submit(function(e) {
            e.preventDefault();
            let id = $('#classlist_id').val();
            let url = id ? `classlist/${id}` : "{{ route('classlist.store') }}";
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#addClassModal').modal('hide');
                    $('#addClassForm')[0].reset();
                    $('#classlist_id').val('');
                    loadClasses();
                    alert(response.success);
                },
                error: function(xhr) {
                    console.log("AJAX Error:", xhr.responseText);
                    alert("Error: " + xhr.responseJSON.error);
                }
            });
        });
        $(document).on('click', '.share-btn', function() {
    let shareCode = $(this).data('share_code');
    $('#shareCode').val(shareCode);
    $('#shareModal').modal('show');
});

// Function to Copy Share Code to Clipboard
        $(document).on('click', '.edit-btn', function() {
            $('#classlist_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#section_id').val($(this).data('section_id'));
            $('#academic_year').val($(this).data('academic_year'));
            $('#room').val($(this).data('room'));
            $('#addClassModal').modal('show');
        });

        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            if (confirm('Delete this class?')) {
                $.ajax({
                    url: `classlist/${id}`,
                    type: "DELETE",
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        loadClasses();
                        alert(response.success);
                    }
                });
            }
        });
    });
</script>
@endpush


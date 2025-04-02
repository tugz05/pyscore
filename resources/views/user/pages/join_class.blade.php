@extends('user.dashboard')

@section('content')
    <div class="container">
        <!-- SB Admin 2 Styled Modal -->
        <div class="modal fade show" id="joinClassModal" tabindex="-1" role="dialog" aria-labelledby="joinClassLabel"
            aria-hidden="true" style="display: block; background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinClassModalLabel">Join Class</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="closeModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- User Account Info -->
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ Auth::user()->avatar }}" class="rounded-circle mr-2" alt="User Profile">
                            <div>
                                <strong>{{ Auth::user()->name }}</strong> <br>
                                <small>{{ Auth::user()->email }}</small>
                            </div>

                        </div>

                        <!-- Class Code Input -->
                        <form id="joinClassForm">
                            @csrf
                            <div class="form-group">
                                <label for="classlist_id">Ask your teacher for the class code, then enter it here.</label>
                                <input type="text" class="form-control" id="classlist_id" name="classlist_id"
                                    placeholder="Enter class code" value="{{ $id ?? '' }}">
                                <small class="form-text text-muted">
                                    Format: xxx-xxxx-xxx (Lowercase letters and numbers only).
                                </small>
                            </div>

                            <!-- Help Link -->


                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Join</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function closeModal() {
            $("#joinClassModal").fadeOut(500, function() {
                window.location.href = "{{ route('user.index') }}"; // Redirect after fadeOut
            });
        }
        $(document).ready(function() {
            $('#joinClassForm').submit(function(e) {
                e.preventDefault();
                let classlistId = $('#classlist_id').val();

                $.ajax({
                    url: "{{ route('joinclass.store') }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        classlist_id: classlistId
                    },
                    success: function(response) {
                        $('#joinClassModal').modal('hide');
                        $('#joinClassForm')[0].reset();
                        closeModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'You have successfully joined the class.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was an error joining the class. Please try again.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });
    </script>
@endpush

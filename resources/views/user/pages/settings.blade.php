    @extends('user.dashboard')

    @section('content')
    <div class="container d-flex flex-column align-items-center mt-4" >
        <!-- User Avatar -->

        <img class="img-profile rounded-circle mb-3"
            src="{{ Auth::user()->avatar }}"
            alt="User Avatar"
            style="width: 150px; height: 150px; border: 5px solid #4e73df; object-fit: cover;">

        <!-- User Name -->
        <h3 class="text-bold text-center" style="color: black;">{{ Auth::user()->name }}</h3>

        <h1 class="h5 text-gray-900 mb-4">Account type: <span class="h5 text-primary mb-4">{{ ucfirst(Auth::user()->account_type) }}</span></h1>    <!-- Upgrade to Instructor Button -->
        <form id="upgradeForm">
            @csrf
            <button type="button" id="upgradeButton" class="btn btn-success mt-3"
                {{ Auth::user()->request_status == 'pending' ? 'disabled' : '' }}>
                {{ Auth::user()->request_status == 'denied' ? 'Request Denied' : 'Upgrade to Instructor Account' }}
            </button>
        </form>
    </div>
    @endsection

    @push('script')
    <script>
        $(document).ready(function() {
            $('#upgradeButton').click(function() {
                $.ajax({
                    url: "{{ route('user.request.upgrade') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            alert("Upgrade request sent successfully!");
                            $('#upgradeButton').prop('disabled', true).text("Request Sent");
                        } else {
                            alert("Something went wrong. Please try again.");
                        }
                    },
                    error: function() {
                        alert("An error occurred.");
                    }
                });
            });
        });
    </script>
    @endpush

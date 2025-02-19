@extends('user.dashboard')
@section('content')

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Custom CSS for hover effects */
        .hover-effect {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            cursor: pointer;
            /* Makes the cursor a hand on hover */
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
            <i class="fa-brands fa-python text-primary"></i> Welcome Instructor
        </h1>
        <h1 class="h3 mb-0 text-gray-800">
            <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClassModal">
                <i class="fa-brands fa-plus mr-3"></i>
                Join a Class
                </a>
        </h1>
    </div>

    <!-- Content Row -->

    <div class="row" id="classCards">
        <!-- AJAX-loaded class cards will appear here -->
    </div>

</div>


@endsection
@push('script')

@endpush

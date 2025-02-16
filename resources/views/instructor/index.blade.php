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
            <a href="index.html" class="btn btn-primary btn-user btn-block">
                <i class="fa-brands fa-plus mr-3"></i>
                Create Class
            </a>
        </h1>
    </div>

    <!-- Content Row -->
    <div class="row d-flex flex-wrap gx-3 gy-4">
        @for ($i = 0; $i < 8; $i++) <!-- Generates 8 Cards -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg rounded-4 border-0 hover-effect h-100">
                <img src="https://picsum.photos/300/120" class="card-img-top rounded-top-4" alt="Course Image">
                <div class="card-body p-3 d-flex flex-column">
                    <h5 class="card-title text-primary fw-bold">CS 212 - Object Oriented Programming</h5>
                    <p class="card-text text-muted">2CSF</p>
                    <div class="mt-auto d-flex justify-content-between">
                        <a href="#" class="btn btn-outline-danger rounded-circle">
                            <i class="fas fa-fw fa-heart"></i>
                        </a>
                        <a href="#" class="btn btn-outline-secondary rounded-circle">
                            <i class="fas fa-fw fa-folder"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

@endsection

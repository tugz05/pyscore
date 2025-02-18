@extends('instructor.dashboard')

@section('content')

<div class="container-fluid">
    <!-- Class Header -->
    <div class="card shadow-lg rounded border-0">
        <div class="card-body bg-primary text-white d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold">IT1 - Living in the IT Era</h2>
                <p class="mb-0">1CSE</p>
            </div>
            <button class="btn btn-light">Customize</button>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body text-center">
                    <h6 class="fw-bold">tug-asdf-ass</h6>
                    <button class="btn btn-primary w-100">Copy Code</button>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body text-center">
                    <h6 class="fw-bold">Class Code</h6>
                    <p class="text-primary fs-4 fw-bold">ga4o34k</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">Upcoming</h6>
                    <p class="text-muted">No work due soon</p>
                    <a href="#" class="text-primary fw-bold">View all</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex align-items-center">
                    <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User">
                    <input type="text" class="form-control" placeholder="Announce something to your class">
                </div>
            </div>

            <!-- Assignment Posts -->
            @php
                $assignments = [
                    ['title' => 'Final Requirements', 'date' => 'Dec 13, 2024'],
                    ['title' => 'Assignment 8 - Invitation Letter', 'date' => 'Dec 9, 2024'],
                    ['title' => 'Assignment 7 - Movie Poster', 'date' => 'Dec 2, 2024'],
                    ['title' => 'Assignment 6 - Logo Design', 'date' => 'Dec 1, 2024']
                ];
            @endphp

            @foreach ($assignments as $assignment)
            {{-- <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-file-alt text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">Virgilio Jr. Tuga posted a new assignment: {{ $assignment['title'] }}</p>
                        <small class="text-muted">{{ $assignment['date'] }}</small>
                    </div>
                </div>
            </div> --}}
            <div class="card shadow-sm border-0 rounded-3 p-2 mb-3">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <!-- Left Side: Icon -->
                    <div class="d-flex align-items-center">
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <!-- Text Content -->
                        <div>
                            <p class="fw-bold mb-0">Virgilio Jr. Tuga posted a new assignment: <span class="text-dark">Final Requirements</span></p>
                            <small class="text-muted">Dec 13, 2024</small>
                        </div>
                    </div>

                    <!-- Right Side: Three-dot Menu -->
                    <div class="dropdown">
                        <button class="btn btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Edit</a></li>
                            <li><a class="dropdown-item" href="#">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</div>

@endsection

@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas "></i> Welcome Admin
        </h1>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Cards Section (Total Students & Instructors) -->
        <div class="col-lg-4">
            <div class="row">
                <!-- Students Count Card -->
                <div class="col-12 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-s font-weight-bold text-warning text-uppercase mb-1">
                                        Total Students
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $studentCount }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructors Count Card -->
                <div class="col-12 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-s font-weight-bold text-success text-uppercase mb-1">
                                        Total Instructors
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $instructorCount }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requests Chart -->
                <div class="col-12">

                </div>
            </div>
        </div>

        <!-- Instructor Table Section -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Faculty Members</h6>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($instructors as $instructor)
                            <tr>
                                <td>
                                    <img class="img-profile rounded-circle" src="{{ $instructor->avatar }}" width="40" height="40">
                                    <span class="ml-2">{{ $instructor->name }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($instructor->created_at)->format('F d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



@endsection

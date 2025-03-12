@extends('user.dashboard')

@section('content')
    <div class="container-fluid mx-6">
        <!-- Page Heading -->
        {{-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Assignment</h1>
    </div> --}}

        <div class="card shadow-lg border-0">
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mt-3" id="assignmentTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#instruction">Instruction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#submitOutput">Submit</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Instruction Tab -->
                    <div class="tab-pane fade show active" id="instruction">
                        <!-- Assignment Header -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3 mr-3"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-clipboard-list text-white fs-1"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ $activity->title }}</h4>
                                    <p class="text-muted mb-0">
                                        {{ $activity->user->name }} •
                                        {{ $activity->created_at ? $activity->created_at->format('F d, Y') : 'No date available' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Description Content -->
                        <p style="margin-top: 50px;"><b>Instructions: </b><br>{!! $activity->instruction !!}
                            <hr>

                            <!-- Points and Due Date -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="fw-bold"><b>{{ $activity->points }}</b> points</p>
                            <p class="text-danger">
                                <b>Due:</b>
                                {{ $activity->due_date ? \Carbon\Carbon::parse($activity->due_date)->format('F d, Y') : 'No date available' }}
                                at
                                {{ $activity->due_time ? \Carbon\Carbon::parse($activity->due_time)->format('h:i A') : 'No time available' }}
                            </p>
                        </div>
                    </div>

                    <!-- Submit Output Tab -->
                    <div class="tab-pane fade" id="submitOutput">
                        @include('user.pages.python_submission')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Ensure Bootstrap Tabs work correctly
            $('.nav-tabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endpush

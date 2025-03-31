@extends('instructor.dashboard')

@section('content')
<div class="container-fluid mx-6">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-body">
            <!-- Tabs -->
            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#instruction">Instruction</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#work">Student Work</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#comparison">Similarities</a>
                </li>

            </ul>

            <div class=" tab-content p-4">
                <div class="tab-pane fade show active" id="instruction">
                <!-- Assignment Header -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3 mr-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-clipboard-list text-white fs-1"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">{{ $activity->title }}</h4>
                                <p class="text-muted mb-0">{{ $activity->user->name }} • {{ $activity->created_at ? $activity->created_at->format('F d, Y') : 'No date available' }}
                                </p>
                            </div>
                        </div>
                        {{-- <div>
                            <button class="btn btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </div> --}}
                    </div>
                    <!-- Description Content -->

                    <p style="margin-top: 50px;" class="align-items-center"><b>Instructions: </b><br>{!! $activity->instruction !!}
                    </p>
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
            </div>
            <div class="tab-content mt-3">
                <!-- Stream Tab -->
                <div class="tab-pane fade show" id="work">
                    @include('instructor.pages.output')
                </div>
                 <!-- Stream Tab -->
                 <div class="tab-pane fade show" id="comparison">
                    @include('instructor.pages.comparison')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

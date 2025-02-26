<div class="container">
    <!-- Teachers Section -->
    <div class="mb-4">
        <h3 class="fw-bold">Teachers</h3>
        <div class="d-flex align-items-center justify-content-between">
            @if($instructor)
                <div class="d-flex align-items-center">
                    <img src="{{ asset($instructor->avatar) }}" alt="Teacher Image" class="rounded-circle me-2" width="40" height="40">
                    <span class="fw-bold">{{ $instructor->name }}</span>
                </div>
            @endif
            <a class="btn btn-light rounded-circle m-2">
                <i class="fas fa-user-plus"></i>
            </a>
        </div>
    </div>

    <!-- Students Section -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="fw-bold">Students</h3>
            <div class="d-flex align-items-center">
                <span class="text-muted me-2">{{ count($students) }} students</span>
                <a class="btn btn-light rounded-circle m-2">
                    <i class="fas fa-user-plus"></i>
                </a>
            </div>
        </div>

        <div class="list-group">
            @foreach($students as $student)
            <div class="list-group-item d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <input type="checkbox" class="m-4">
                    @if($student->avatar)
                        <img src="{{ asset($student->avatar) }}" alt="Profile" class="rounded-circle m-4" width="40" height="40">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="fw-bold">{{ $student->name }}</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm" type="button" id="dropdownMenu{{ $student->id }}" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                            <path d="M9.5 12.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $student->id }}">

                        <li><a class="dropdown-item text-danger" href="#">Remove</a></li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

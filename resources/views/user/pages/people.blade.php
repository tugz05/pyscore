<div class="container">
    <!-- Instructor Section -->
    <div class="mb-4">
        <h3 class="fw-bold">Instructor</h3>
        <div class="d-flex align-items-center">
            @if($instructor)
                <img src="{{ asset($instructor->avatar) }}" alt="Instructor Image" class="rounded-circle me-2" width="40" height="40">
                <span class="fw-bold"> &nbsp;{{ $instructor->name }}</span>
            @endif
        </div>
    </div>

    <!-- Students Section -->
    <div class="mb-4">
        <h3 class="fw-bold">Classmates</h3>
        <span class="text-muted "> {{ count($students) }} students</span>

        <div class="list-group mt-3">
            @foreach($students as $student)
            <div class="list-group-item d-flex align-items-center">
                @if($student->avatar)
                    <img src="{{ asset($student->avatar) }}" alt="Profile" class="rounded-circle me-3" width="40" height="40">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
                <span class="fw-bold">{{ $student->name }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

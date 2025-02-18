@foreach ($activities as $activity)
    <a href="{{ route('activity.view', $activity->id) }}" class="text-decoration-none">
        <div class="card shadow-sm border-0 rounded-3 p-2 mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-clipboard-list text-white"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">{{ $activity->title }}</p>
                        <small class="text-muted">
                            {{ $activity->created_at ? $activity->created_at->format('F d, Y') : 'No date available' }}
                        </small>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item editActivity"
                               data-id="{{ $activity->id }}"
                               data-title="{{ $activity->title }}"
                               data-instruction="{{ $activity->instruction }}"
                               data-due_date="{{ $activity->due_date }}"
                               data-due_time="{{ $activity->due_time }}"
                               data-accesible_date="{{ $activity->accesible_date }}"
                               data-accessible_time="{{ $activity->accessible_time }}">Edit</a></li>
                        <li><a class="dropdown-item deleteActivity" data-id="{{ $activity->id }}">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </a>
@endforeach

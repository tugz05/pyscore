<style>
    .truncate-text {
    display: block;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px; /* Adjust width as needed */
}
</style>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('user.index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <svg width="30" height="30" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
                <path fill="#306998" d="M63.88 0C47.3 0 34.18 11.06 34.18 27.29v9.11h29.34v2.99H22.57C10.17 39.39 0 47.7 0 61.76v20.47c0 12.95 10.04 22.46 22.57 22.46h7.19v-9.64c0-9.61 8.2-17.93 18.15-17.93h29.34c8.04 0 14.58-6.52 14.58-14.54V27.29C91.83 11.06 79.17 0 63.88 0zM48.06 8.68a4.5 4.5 0 0 1 4.5 4.51 4.5 4.5 0 0 1-4.5 4.51 4.5 4.5 0 0 1-4.5-4.51 4.5 4.5 0 0 1 4.5-4.51z"/>
                <path fill="#FFD43B" d="M63.88 128c16.58 0 29.7-11.06 29.7-27.29v-9.11H64.24v-2.99h40.95c12.4 0 22.57-8.31 22.57-22.37V45.77c0-12.95-10.04-22.46-22.57-22.46h-7.19v9.64c0 9.61-8.2 17.93-18.15 17.93H50.5c-8.04 0-14.58 6.52-14.58 14.54v44.47C35.92 116.94 48.58 128 63.88 128zM79.7 119.32a4.5 4.5 0 0 1-4.5-4.51 4.5 4.5 0 0 1 4.5-4.51 4.5 4.5 0 0 1 4.5 4.51 4.5 4.5 0 0 1-4.5 4.51z"/>
            </svg>
        </div>
        <div class="sidebar-brand-text mx-3">PyScore</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('user.index') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Home</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        MANAGE
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed"  data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo" >
            <i class="fas fa-graduation-cap"></i>
            <span>Enrolled</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Subjects:</h6>
                {{-- <h1>{{$classlists}}</h1> --}}
                @if(count($classlists) > 0)
                    @foreach ($classlists as $class)
                    <a class="collapse-item truncate-text" href="{{ route('user.class.view', $class->id) }}" title="{{ $class->classlist->name }}">
                        {{$class->classlist->name }}
                    </a>
                    @endforeach
                @else
                    <p class="collapse-item text-muted">No classes available</p>
                @endif
            </div>
        </div>

    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="">
            <i class="fas fa-fw fa-archive"></i>
            <span>Archived Classes </span></a>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->

</ul>

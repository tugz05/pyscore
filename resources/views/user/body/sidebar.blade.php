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
        <img src="{{ asset('assets/png/pyscore_logo2.png') }}" alt="PyScore Logo"
            style="height: 30px; width: 30px; display: inline-block; ">
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
                    @forelse ($classlists as $class)
                    <a class="collapse-item truncate-text" href="{{ route('user.class.view', $class->classlist->id) }}" title="{{ $class->classlist->name }}">
                        {{$class->classlist->name }}
                    </a>
                    @empty
                        <p class="collapse-item text-muted">No classes available</p>
                    @endforelse
                @else
                    <p class="collapse-item text-muted">No classes available</p>
                @endif
            </div>
        </div>

    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('user.archive') }}">
            <i class="fas fa-fw fa-archive"></i>
            <span>Archived Classes </span></a>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->

</ul>

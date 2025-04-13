<meta name="csrf-token" content="{{ csrf_token() }}">

<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('dashboard') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">
                @if(Auth::check() && Auth::user()->isBidder())
                    <!-- Notifications -->
                    <div class="dropdown topbar-head-dropdown ms-1 header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class='bx bx-bell fs-22'></i>
                            @if(isset($notifications) && $notifications->count())
                                <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                                    {{ $notifications->count() }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                             aria-labelledby="page-header-notifications-dropdown">
                            <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                <div class="p-3">
                                    <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                                </div>
                            </div>
                            <div class="p-2">
                                @forelse($notifications as $note)
                                    <div class="text-reset notification-item d-flex align-items-start dropdown-item">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $note->title }}</h6>
                                            <div class="fs-13 text-muted">
                                                <p class="mb-1">{{ $note->message }}</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> {{ $note->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <button class="btn btn-sm btn-link text-danger p-0 delete-notification-btn"
                                                    data-id="{{ $note->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#removeNotificationModal">
                                                <i class="bx bx-trash fs-18"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-3">
                                        <p class="mb-0">No new notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                <!-- User Menu -->
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                 src="{{ asset('assets/images/users/user-dummy-img.jpg') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{ ucfirst(Auth::user()->role) }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="bx bx-power-off font-size-16 align-middle me-1"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Notification Delete Confirmation Modal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                               colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this notification?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/notification-delete.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

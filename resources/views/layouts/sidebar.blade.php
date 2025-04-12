<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-new.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo-new.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-new.png') }}" alt="" height="20">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo-new.png') }}" alt="" height="42">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                            <i class="ri-dashboard-line"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('products.index') }}">
                            <i class="ri-shopping-cart-2-line"></i> <span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('chat-users') }}">
                            <i class="ri-chat-3-line"></i> <span>Messages</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('bidders') }}">
                            <i class="ri-group-line"></i> <span>Bidders</span>
                        </a>
                    </li>
                @endif
                @if(Auth::user()->isBidder())
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                            <i class="ri-auction-line"></i> <span>LiveAuction</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="{{ route('chat-showForBidder') }}">
                            <i class="ri-chat-3-line"></i> <span>Messages</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
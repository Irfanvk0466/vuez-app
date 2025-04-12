@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.signin')
@endsection

@section('content')
<div class="auth-page-wrapper pt-5">
    <!-- auth page bg -->
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>

        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
                <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
    </div>

    <!-- auth page content -->
    <div class="auth-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mt-sm-5 mb-4 text-white-50">
                    <div><p>Veuz Concepts Pvt.Ltd.</p></div>
                    <p class="mt-3 fs-15 fw-medium">Live Auction Platform</p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">Welcome Back!</h5>
                                <p class="text-muted">Sign in to continue to Live Auction Platform.</p>
                            </div>

                            <!-- Flash Error Message -->
                            @if(session('error'))
                                <div class="alert alert-danger text-center mt-3 mb-0">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="p-2 mt-4">
                                <form action="{{ route('login') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="username" name="email" placeholder="Enter username">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password-input" class="form-label">Password</label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror" name="password" placeholder="Enter password" id="password-input">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="auth-remember-check">
                                        <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-success w-100" type="submit">Sign In</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Register link -->
                    <div class="mt-4 text-center">
                        <p class="mb-0">Don't have an account?
                            <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-underline">Signup</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center text-muted small">
                    &copy; {{ date('Y') }} Veuz Concepts Pvt.Ltd.
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/password-addon.init.js') }}"></script>
@endsection

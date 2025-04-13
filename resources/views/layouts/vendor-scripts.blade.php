<!-- Core Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.bundle.min.js') }}"></script>

<!-- UI Enhancements -->
<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/feather-icons/feather-icons.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/pages/plugins/lord-icon-2.1.0.min.js') }}"></script>

<!-- Real-Time & Alert Tools -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Date/Time Tools -->
<script src="https://cdn.jsdelivr.net/npm/luxon@3/build/global/luxon.min.js"></script>

<!-- Notifications -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js" defer></script>

<!-- App Plugins -->
<script src="{{ URL::asset('assets/js/plugins.min.js') }}"></script>

<!-- Blade Script Injections -->
@yield('script')
@yield('script-bottom')

<!-- Scripts -->
@routes
<script type="text/javascript">
    var serverTimezone = "{{ date_default_timezone_get() }}";
    var defaultCurrency = @json(\App\Currency::query()->findOrFail(1));
</script>
<script src="{{ asset('static/js/jquery-3.3.1.min.js') }}"></script>
<!-- <script src="{{ asset('static/js/velocity.min.js') }}"></script> -->
<script src="{{ asset('static/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('static/semantic-ui/semantic.min.js') }}"></script>

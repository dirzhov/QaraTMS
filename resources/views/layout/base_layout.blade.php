<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QaraTMS - Open Source Test Management System</title>
    <link rel="icon" type="image/x-icon" href="{{asset('/img/favicon.ico')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{asset('css/main.css')}}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js"></script>
    <script src="{{asset('js/js.cookie.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('js/dayjs.min.js')}}"></script>
    <script src="{{asset('js/duration.min.js')}}"></script>
    <script>dayjs.extend(window.dayjs_plugin_duration)</script>
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"--}}
{{--            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"--}}
{{--            crossorigin="anonymous"></script>--}}
    <script>
        jiraUrl = "{{ Config::get('app.jira_host') }}";
        jiraToken = "{{ Config::get('app.jira_token') }}";
        userId = {{Auth::id()}};

        function renderPriority(value, includeText) {
            if (value == {{\App\Enums\CasePriority::LOWEST}})
                return `<i class='bi me-1 bi-chevron-double-down text-warning'></i>${includeText?'P5':''}`
            if (value == {{\App\Enums\CasePriority::LOW}})
                return `<i class='bi me-1 bi-chevron-down text-warning'></i>${includeText?'P4':''}`
            if (value == {{\App\Enums\CasePriority::MEDIUM}})
                return `<i class='bi me-1 bi-list text-info'></i>${includeText?'P3':''}`
            if (value == {{\App\Enums\CasePriority::HIGH}})
                return `<i class='bi me-1 bi-chevron-up text-danger'></i>${includeText?'P2':''}`
            if (value == {{\App\Enums\CasePriority::HIGHEST}})
                return `<i class='bi me-1 bi-chevron-double-up text-danger'></i>${includeText?'P1':''}`
        }
    </script>
    @yield('head')
</head>
<body>
<div class="row sticky-top">
    @include('layout.header_nav')
</div>
<div class="container-fluid">
    <div class="row fh">
        @yield('content')
    </div>
</div>

<div class="modal fade" id="any_img_lightbox" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="position-absolute top-50 start-50 translate-middle">
            <img id="any_img_lightbox_image" src="" alt="">
        </div>
    </div>
</div>

<div class="modal fade" id="test_case_overlay" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="test_case_overlay_data">
    </div>
</div>

<script src="{{asset('js/main.js')}}"></script>
@yield('footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>

</body>
</html>

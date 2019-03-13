<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">

    <title>Laravel</title>

    <link rel="stylesheet" href="{{asset("css/app.css")}}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <!--  plugins -->
    {{--<link rel="stylesheet" href="{{asset('plugins/bootstrap/css/bootstrap.min.css')}}">--}}
    <link rel="stylesheet" href="{{asset('plugins/toastr/css/toastr.min.css')}}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset("css/layout.css")}}">
    @stack("css")
</head>
<body>
<div class="content">
    <div class="container">
        <div class="title m-b-md">
            @yield("title",config("app.name"))
        </div>
        <div class="container-fluid">
            @include('flash::message')
            @yield("content")
        </div>
    </div>
</div>
<script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/toastr/js/toastr.min.js')}}"></script>
<script>
    function reloadTooltips() {
        $("[data-toggle=tooltip]").tooltip();
    }
    $(function () {
        reloadTooltips();
        $(".atras").click(function (e) {
            e.preventDefault();
            window.history.back();
        });
    });
</script>
@stack("js")
</body>
</html>

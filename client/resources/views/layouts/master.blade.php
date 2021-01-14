<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') | Invoicer</title>
    <!--Import Google Icon Font-->
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <style type="text/css">
        body {  display: flex; min-height: 100vh; flex-direction: column; }
        main {  flex: 1 0 auto; }
        strong {  font-weight: 700; }
        footer.page-footer { padding-top: 0; }
    </style>
</head>

<body>

<nav>
    <div class="nav-wrapper">
        <a href="{{route('home')}}" class="brand-logo" style="margin-left: 1rem;">Invoicer</a>
        <div style="width: 30vw; height: 100%; position: relative; display: inline-block; margin-left: 20vw;">
            <form action="{{route('invoices-list')}}" method="get">
                <div class="input-field">
                    <input id="search" name="query" value="{{app('request')->input('query')}}" type="search" required>
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons">close</i>
                </div>
            </form>
        </div>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li><a href="{{route('invoices-list')}}">Invoices</a></li>
            <li><a href="{{route('settings-list')}}">Settings</a></li>
            <li><a href="/auth/logout">Logout</a></li>
        </ul>
    </div>
</nav>



<main>
    @yield('content')
</main>

<footer class="page-footer">
    <!-- <div class="container">
        <div class="row">
            <div class="col l6 s12">
                <h5 class="white-text">Footer Content</h5>
                <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
            </div>
            <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Links</h5>
                <ul>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
                </ul>
            </div>
        </div>
    </div> -->
    <div class="footer-copyright">
        <div class="container">
            &copy; {{date('Y')}} Invoicer Client
            <a class="grey-text text-lighten-4 right" href="https://github.com/dlucian/invoicer">GitHub</a>
        </div>
    </div>
</footer>

<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="//code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<script type="text/javascript">
    $( document ).ready(function(){
        $(".button-collapse").sidenav();
    });
</script>

@yield('scripts')
</body>
</html>
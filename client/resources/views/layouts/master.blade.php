<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') | Invoicer</title>
    <!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css"  media="screen,projection"/>

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
        <div class="row">
            <div class="col s12">
                <a href="{{route('home')}}" class="brand-logo">Invoicer</a>
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>

                <ul id="nav-desktop" class="right hide-on-med-and-down">
                    <li><a href="{{route('invoices-list')}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Invoices"><i class="material-icons">view_list</i></a></li>
                    <li><a href="{{route('settings-list')}}" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Settings"><i class="material-icons">settings</i></a></li>
                    <li><a href="/auth/logout" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Logout"><i class="material-icons">power_settings_new</i></a></li>
                </ul>

                <form class="right hide-on-med-and-down" action="{{route('invoices-list')}}" method="get">
                    <div class="input-field">
                        <input name="query" id="search" type="search" required>
                        <input type="hidden" name="from" value="2000-01-01">
                        <input type="hidden" name="to" value="{{date('Y-m-d')}}">
                        <label for="search"><i class="material-icons">search</i></label>
                        <i class="material-icons">close</i>
                    </div>
                </form>

                <ul id="nav-mobile" class="side-nav">
                    <li><a href="{{route('invoices-list')}}">Invoices</a></li>
                    <li><a href="{{route('settings-list')}}">Settings</a></li>
                    <li><a href="/auth/logout">Logout</a></li>
                </ul>
            </div>
        </div>
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
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>

<script type="text/javascript">
    $( document ).ready(function(){
        $(".button-collapse").sideNav();
    });
</script>

@yield('scripts')
</body>
</html>
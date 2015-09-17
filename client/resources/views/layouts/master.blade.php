<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') | Invoicer</title>
    <!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/css/materialize.min.css"  media="screen,projection"/>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body>

<nav>
    <div class="nav-wrapper">
        <div class="row">
            <div class="col s12">
                <a href="{{route('invoices-list')}}" class="brand-logo">Invoicer</a>
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>

                <ul id="nav-desktop" class="right hide-on-med-and-down">
                    <li><a href="{{route('invoices-list')}}"><i class="material-icons">view_list</i></a></li>
                    <li><a href="/auth/logout"><i class="material-icons">power_settings_new</i></a></li>
                </ul>
                <ul id="nav-mobile" class="side-nav">
                    <li><a href="{{route('invoices-list')}}">Invoices List</a></li>
                    <li><a href="/auth/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

@yield('content')

<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>

<script type="text/javascript">
    $( document ).ready(function(){
        $(".button-collapse").sideNav();
    });
</script>

@yield('scripts')
</body>
</html>
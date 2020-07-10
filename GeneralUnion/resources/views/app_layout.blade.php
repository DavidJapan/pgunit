<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>General Union Administration Database ver {{ config('app.version')}}</title>

        <link href="/css/welcome/bootstrap-3.3.7.css" rel="stylesheet">
        <!-- Fonts -->
        <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]> 
                <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]--> 
        <style type="text/css">
            .centred
            {
                padding: 70px 0;
                text-align: center;
            }            
        </style>
    </head>
    <body>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://www.generalunion.org">General Union</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        @if (Auth::guest())
                        <li><a href="/login">Login</a></li>
                        @else
                        <li><a href="/authorised">Authorised area</a></li>
                        <li>
                            <form id="form-logout" method="post" action="/logout" style="display: none">
                                @csrf
                            </form>
                            <a href="/logout"  onclick="event.preventDefault(); document.getElementById('form-logout').submit();">Logout</a>
                        </li>
                        @endif
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Version {{ config('app.version')}}</a></li> 
                        <li><a href="http://generalunionadmin.org">General Union Administration Database</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid --> 
        </nav>
        @yield('content')
        <script src="/js/jquery-3.4.1.min.js" type="text/javascript"></script>
        <script src="/js/welcome/bootstrap.min.js"></script>
    </body>
</html>

<!DOCTYPE html>
<html>
    <head> 
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!--https://warlord0blog.wordpress.com/2018/02/18/laravel-api-token-auth/-->
        <meta name="api_token" content="{{ (Auth::user()) ? Auth::user()->api_token : '' }}">        
        <title>Triggering an Event</title>
        <style type="text/css">
            #wrapper {
                margin:5px;
            }

            #left {
                float:left;
                width:45%;
                overflow:hidden;
                padding: 5px;
                border: solid 1px #ccc
            }
            #right {
                float:left;
                width:45%;
                overflow:hidden;
                padding: 5px;
                border: solid 1px #ccc
            }
        </style>
    </head>
    <body>
        <h1>Handling a triggered event</h1>

        <script src="/js/jquery-3.4.1.min.js"></script>
        <script src="/js/pusher.min.js"></script>
        <script>
$(function () {
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Authorization': 'Bearer ' + $('meta[name="api_token"]').attr('content')
    }
});
});
// Enable pusher logging - don't include this in production

Pusher.logToConsole = true;
var pusher = new Pusher('8abc8d1405e5fd6db944', {
    cluster: 'ap3'
});
var channel = pusher.subscribe('gudb0605Channel');
channel.bind('Gudb0605Event', function (data) {
    console.log(data);
    var newDiv = document.createElement("li");
    $(newDiv).text(data.message);
    $("#messages").append(newDiv);
});
function sendRequest() {
$.ajax({
    url: "/send_ajax",
    method: "GET"
}).done(function (json, textStatus, jqXHR) {
    console.log("Ajax request to /send_ajax executed successfully.");
});
}
function sendApiRequest() {
    $.ajax({
        url: "/api/test",
        method: "GET",
        accept: "application/json"
    }).done(function (json, textStatus, jqXHR) {
            var newLi = document.createElement("li");
            $(newLi).text("Ajax request to /api/test executed successfully.");
            $("#messages").append(newLi);
            newLi = document.createElement("li");
            $(newLi).text(JSON.stringify(json));
            $("#messages").append(newLi);
        });
}
        </script>
        <div id="wrapper">
            <div id="left">
                <h1>Testing Event Broadcasting and api routes</h1>
                <p>This button sends an Ajax request to a controller that fires an event. This page is listening for that event
                    and pops up an alert box.</p>
                <button onclick="sendRequest()">Send Ajax request</button>
                <p>This button sends an Ajax request to an api route specified in api.php. Api routes don't need the CSRF token
                    but authorised users need an api token. I've given the users table and api_token field and I've given
                    myself the token 12345. Like a CSRF token, you can add it in a meta tag and pass that as a header to
                    the ajaxSetup method. That allows you to avoid sending a query string like ?api_token=12345.</p>
                <button onclick="sendApiRequest()">Send Ajax request via Api route</button>
            </div>
            <div id="right">
                <h1>Messages from server</h1>
                <ul id="messages">
                    
                </ul>
            </div>
        </div>
    </body>
</html>

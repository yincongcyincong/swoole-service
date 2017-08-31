<!DOCTYPE html>
<html>
    <head>
            <title></title>
    </head>
    <body>
           <div >
                <ul id="member">
                </ul>
           </div>
           <div id="container"  style="width:500px;height:300px; border:1px solid red;" ></div>
           <textarea id="message" style="width:500px; height:100px;" ></textarea>
           <button onclick="send()" >发送</button>
    </body>
    <script src="./jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
        var wesocket = new WebSocket("ws://47.94.130.85:8080");
        wesocket.onopen = function (event) {
            wesocket.send('{"server": "guest"}');
        };
        wesocket.onmessage = function (event) {
            if (event.data == 'service is not online') {
                window.location.href="./form.php";
            } else {
                $('#container').html($('#container').html()+'<br>'+event.data);
            }
        }
        function send(){
            var message = $('#message').val();
            wesocket.send('{"server": "guest", "message": "'+message+'"}');
            $('#message').val('');
            $('#container').html($('#container').html()+'<br>'+message);
        }
    </script>
</html>


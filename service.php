<?php
    session_start();
    if (empty($_SESSION['service'])) {
         header('location:./login.html');
    }
?>

<!DOCTYPE html>
<html>
    <head>
            <title></title>
    </head>
    <style>
        .user{
            border:1px solid red;
            width:100px;
            height:40px;
            margin-left:10px;
            margin-bottom:10px;
        }
        .container {
            width:500px;height:300px; border:1px solid red;
        }
    </style>
    <body>
            <div id="member" style="margin-bottom:5px;">
           </div>
           <textarea id="message" style="width:500px; height:100px;" ></textarea>
           <button onclick="send()" >发送</button>
    </body>
    <script src="./jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
        var wesocket = new WebSocket("ws://47.94.130.85:8080");
        wesocket.onopen = function (event) {
            wesocket.send('{"server": "server"}');
        };
        wesocket.onmessage = function (event) {
            var data = eval("("+event.data+")");
            switch (data.data) {
                case ('new guest') :
                $('#member').append('<span onclick="choose('+data.guest+')"  class="user" id="guest_'+data.guest+'">'+data.guest+'</span>');
                var show = true;
                $('.container').each(function(){
                    if($(this).css('display') == 'block') {
                        show = false;
                    }
                });
                if (show) {
                    $('#message').before('<div class="container" id="content'+data.guest+'"></div>');
                    $('#guest_'+data.guest).addClass('choose');
                } else {
                    $('#message').before('<div class="container" id="content'+data.guest+'" style="display:none;"></div>');
                } 
                break;
                case ('leave guest'):
                $('#guest_'+data.guest).remove();
                $('#content'+data.guest).remove();
                break;
                case('new server'):
                for (var i in data.guest) {
                    $('#member').append('<span  class="user" onclick="choose('+data.guest[i]+')"  id="guest_'+data.guest[i]+'">'+data.guest[i]+'</span>');
                    $('#message').before('<div class="container" id="content'+data.guest[i]+'" style="display:none;"></div>');
                }
                $('.user').eq(0).addClass('choose');
                $('.container').eq(0).show();
                break;
                default:
                $('#content'+data.guest).append('<br>'+data.data);
                break;
            }
        }
        function send(){
            var message = $('#message').val().trim();
            var guest = $('.choose').html();
            wesocket.send('{"server": "server", "message": "'+message+'", "guest": "'+guest+'"}');
            $('#message').val('');
            $('#content'+guest).html($('#content'+guest).html()+'<br>'+message);
        }

        function choose(id){
            $('.container').hide();
            $('#content'+id).show();
            $('.user').removeClass('choose');
            $('#guest_'+id).addClass('choose');
           
        }
    </script>
</html>

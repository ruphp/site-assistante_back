<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> чат </title>
    <script src="/js/jquery-3.3.1.min.js"></script>
</head>
<style>
    ul,li{list-style: none;padding: 0;margin: 0;}
    body{width: 50%;float: left;margin: 5px 25%;}
    .msg_box{width: 100%;border:1px solid #ddd;height: 500px; float: left;overflow: scroll;}
    .msg_box li{padding: 10px;border-bottom:1px solid #ddd;}
    .op_box{width: 100%;float: left;}
    .op_box textarea{width: 96%;float: left;padding: 2%;border:1px solid #ddd;height: 50px;resize: none;}
    .op_box a{width: 200px;display: block;float: right;height: 30px;line-height: 30px;background: #ddd;color: black;text-align: center;text-underline-style: none;margin-top: 5px;}

</style>
<body>
<ul class="msg_box"></ul>
<div class="op_box">
    <textarea name = "msg" placeholder = "Пожалуйста, введите сообщение"> </textarea>
    <a href="javascript:;" class="send_btn"> Отправить </a>
</div>
</body>
<script>
    var data='';
    var name = window.prompt ('Пожалуйста, введите ник', 'jungshen');
    var touser = window.prompt ('Пожалуйста, введите псевдоним друга', 'mirror');
    $ ('title'). html ('Вы:' + name + 'общается с' + touser + '_' + $ ('title'). html ());
    if(name){
        var ws = new WebSocket("ws://5.180.137.60:9501");

        ws.onopen = function(evt) {
            console.log("Connection open ...");
            //ws.send("Hello WebSockets!");
            data={'name':name,'type':'bind'}
            ws.send(JSON.stringify( data ));
        };

        ws.onmessage = function(evt) {
            console.log( "Received Message: " + evt.data);
            dataObj=eval('('+evt.data+')');
            $('.msg_box').append('<li>'+dataObj.name+':'+dataObj.data+'</li>')
            //ws.close();
        };
        ws.onclose = function(evt) {
            console.log("Connection closed.");
        };
        ws.onerror = function(evt) {
            console.log('error');
        };
    }


    $('.send_btn').click(function(){
        var msg=$('textarea').val();
        if(msg){
            data={'name':name,'msg':msg,'type':'msg','touser':touser}
            ws.send(JSON.stringify( data ));
            $('textarea').val('');
        }
    });
    document.onkeydown=function(event){
        if(event.keyCode==13)
        {
            $('.send_btn').click();
            return false;
        }
    }

</script>
</html>
<?php

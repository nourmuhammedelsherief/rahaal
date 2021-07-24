<!doctype html>
<html>
<head>
    <title>Socket.IO chat</title>
</head>
<body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
<script>
    var socket = io('127.0.0.1:2060' );
    console.log('أدخل برجلك  اليمين');
    socket.emit('user-connected',{"room_id":"7","api_token":"1Cht1ILGrHg"});
    socket.emit('send-message',{"lang":"ar","message":"لا  اله  الا  الله","file_type":"text","api_token":"1Cht1ILGrHg","room_id":"7","duration":0,"file":""});
    // function emitSocket() {
    //
    // }
</script>
</body>
</html>

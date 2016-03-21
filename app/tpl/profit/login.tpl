<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>{{$title}}</title>
<link href='http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<link href='{{$site_base}}/favicon.ico' type=image/x-icon rel='shortcut icon'>
<style>
body {background-color: #242424}
.help-logo {position: absolute; top: 10px; right: 10px}
.help-logo img {width: 24px}
.login-title {color: white}
.login {
    margin: auto;
}
</style>
</head>
<body>
<div class='container login'>
    <div class='row'>
        <div class='col-md-2 col-md-offset-5'>
        <h1 class='login-title'>系统登录</h1>
        <br />
        <form role='form' method='post'>
            <div class='form-group'>
                <input type='text' name='username' placeholder='用户名'>
            </div>
            <div class='form-group'>
                <input type='password' name='password' placeholder='密码'>
            </div>
            <button type='button' class='btn btn-primary' onclick='login()'>登录</button>
            <button type='reset' class='btn btn-default'>重置</button>
        </form>
        </div>
    </div>
</div>
</body>
<script src='http://cdn.bootcss.com/jquery/1.12.1/jquery.min.js'></script>
<script src='http://cdn.bootcss.com/blueimp-md5/2.3.0/js/md5.min.js'></script>
<script src='http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<script>
    $(function() {
    })
    function login() {
        var username = $('input[name=username]').val().trim();
        var password = md5($('input[name=password]').val().trim());
        console.log('username: ' + username + ', password: ' + password);
        $.ajax({
            type: 'POST',
            url: '{{$context}}/profit/Login',
            data: {username: username, password: password},
            dataType: 'html',
            success: function(data) {
                console.log('return data: ' + data);
                if(data == 'success') {
                    location.href = '{{$context}}/profit/Base';
                } else {
                    alert('登录失败:' + data);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log('ajax request ERROR! XMLHttpRequest.status: ' + XMLHttpRequest.status
                        + ', XMLHttpRequest.readyState: ' + XMLHttpRequest.readyState
                        + ', textStatus: ' + textStatus);
                alert('服务器请求失败');
            }
        });
    }
</script>
</html>
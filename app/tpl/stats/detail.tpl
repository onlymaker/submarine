<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='http://cdn.bootcss.com/jquery/1.12.1/jquery.min.js'></script>
<script src='http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
</style>
</head>
<body>
<div class='container' style='margin: 70px auto'>
    <div class="page-header">
        <h1>{{$model}} <small>{{$description}}</small></h1>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>渠道</h3>
                </div>
            </div>
            <table class='table table-bordered'>
                {{foreach $channelStats as $item}}
                    <tr><td>{{$item['channel']}}</td><td>{{$item['quantity']}}</td><td>{{$item['amount']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
        <div class='col-md-6'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>尺码</h3>
                </div>
            </div>
            <table class='table table-bordered'>
                {{foreach $sizeStats as $item}}
                    <tr><td>{{$item['size']}}</td><td>{{$item['quantity']}}</td><td>{{$item['amount']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
    </div>
</div>
</body>
</html>
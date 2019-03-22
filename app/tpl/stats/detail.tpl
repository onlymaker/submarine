<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='//stackpath.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='//code.jquery.com/jquery-1.12.4.min.js'></script>
<script src='//stackpath.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
</style>
</head>
<body>
<div class='container' style='margin: 70px auto'>
    <div class="page-header">
        <h1>{{$title|upper}} <small>{{$description}}</small></h1>
    </div>
    <div class='row'>
        {{if $mode != 'channel'}}
            <div class='col-md-6'>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>渠道</h3>
                    </div>
                </div>
                <table class='table table-bordered'>
                    {{foreach $channelStats as $item}}
                        <tr><td>{{$item['channel']}}</td><td>{{$item['quantity']}}</td></tr>
                    {{/foreach}}
                </table>
            </div>
        {{/if}}
        {{if $mode != 'model'}}
            <div class='col-md-6'>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>型号</h3>
                    </div>
                </div>
                <table class='table table-bordered'>
                    {{foreach $modelStats as $item}}
                        <tr><td>{{$item['model']}}</td><td>{{$item['quantity']}}</td></tr>
                    {{/foreach}}
                </table>
            </div>
        {{/if}}
        {{if $mode != 'size'}}
            <div class='col-md-6'>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>尺码</h3>
                    </div>
                </div>
                <table class='table table-bordered'>
                    {{foreach $sizeStats as $item}}
                        <tr><td>{{$item['size']}}</td><td>{{$item['quantity']}}</td></tr>
                    {{/foreach}}
                </table>
            </div>
        {{/if}}
    </div>
</div>
</body>
</html>

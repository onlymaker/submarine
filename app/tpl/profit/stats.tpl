<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='//stackpath.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='//cdn.bootcss.com/jquery/1.12.4/jquery.min.js'></script>
<script src='//stackpath.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
</style>
</head>
<body>
{{include file='profit/common/top-nav.tpl'}}
<div class='container' style='margin: 70px auto'>
    <div class='row'>
        <table class='table table-bordered'>
            {{foreach $stats as $item}}
                <tr><td>{{$year}} 年 {{$item['i']}}{{$meta['chinese']}}</td><td>{{$item['profit']}}</td></tr>
            {{/foreach}}
        </table>
    </div>
</div>
</body>
</html>

<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='http://cdn.bootcss.com/jquery/1.12.1/jquery.min.js'></script>
<script src='http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
.label {font-weight: 500}
.label, .table-hover {cursor: pointer}
.label-highlight {background-color: #dedede}
.label-brand {font-weight: 700; color: #000}
</style>
</head>
<body>
{{include file='stats/common/top-nav.tpl'}}
<div class='container' style='margin: 70px auto'>
    {{include file='stats/common/tab-nav.tpl'}}
    <div class='row'>
        {{foreach $weekStats as $item}}
            <span class='label label-brand' data='{{$item["week"]}}'>
                &nbsp;<strong>{{$year}}W{{$item['week']}} </strong>同比销量: {{$item['quantityRatio']}},同比销售额: {{$item['amountRatio']}}&nbsp;
            </span>
        {{/foreach}}
    </div>
    <div class='row'>
        <div class='alert alert-success'>
            销售量:&nbsp;{{$stats['quantity']}}&nbsp;|&nbsp;销售额:&nbsp;{{$stats['amount']}}
        </div>
    </div>
    <div class='row'>
        <div class='col-md-4'>
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
        <div class='col-md-4'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>产品型号</h3>
                </div>
            </div>
            <table class='table table-bordered table-hover'>
                {{foreach $modelStats as $item}}
                    <tr data='{{$item["model"]}}'><td>{{$item['model']}}</td><td>{{$item['quantity']}}</td><td>{{$item['amount']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
        <div class='col-md-4'>
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
<script>
    $(function(){
        $('.label-brand').each(function() {
            var data = $(this).attr('data');
            var w = '{{$w}}'
            if(data == w) $(this).addClass('label-highlight');
        })
        $('.label-brand').click(function() {
            var data = $(this).attr('data');
            location.href = '{{$context}}/stats/Week?t={{$t}}&w=' + data;
        })
        $('.table-hover tr').click(function() {
            var model = $(this).attr('data');
            location.href = '{{$context}}/stats/Detail?t={{$t}}&model=' + model;
        })
    });
</script>
</html>
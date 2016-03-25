<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='http://cdn.bootcss.com/jquery/1.12.1/jquery.min.js'></script>
<script src='http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
.alert{cursor: pointer}
.label {font-weight: 500}
.label, .table-hover {cursor: pointer}
.label-highlight {background-color: #dedede}
.label-brand {color: #000}
.stats-range {margin-bottom: 20px; display: none}
.stats-switch {float: right}
</style>
</head>
<body>
{{include file='stats/common/top-nav.tpl'}}
<div class='container' style='margin: 70px auto'>
    {{include file='stats/common/tab-nav.tpl'}}
    <div class='row' style='margin-top: 15px'>
        <div class='alert alert-success'>
            <strong>{{$year}} 年 {{$i}}{{$meta['chinese']}}</strong> 销量：{{$stats[$i-1]['quantity']}}，同比：{{$stats[$i-1]['quantityRatio']}} | 销售额：{{$stats[$i-1]['amount']}}，同比：{{$stats[$i-1]['amountRatio']}}
            <span class="glyphicon glyphicon-list-alt stats-switch">&nbsp;</span>
        </div>
    </div>
    <div class='row stats-range'>
        {{foreach $stats as $item}}
            <span class='label label-brand' data='{{$item["i"]}}'>
                【<strong>{{$year}}{{$meta['short']}}{{$item['i']}}</strong> 销量：{{$item['quantity']}} | {{$item['quantityRatio']}}，销售额：{{$item['amount']}} | {{$item['amountRatio']}}】
            </span>
        {{/foreach}}
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
                    <tr><td>{{$item['channel']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['amount']}}</td></tr>
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
                    <tr class='model' data='{{$item["model"]}}'><td>{{$item['model']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['amount']}}</td></tr>
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
            var i = '{{$i}}'
            if(data == i) $(this).addClass('label-highlight');
        })
        $('.label-brand').click(function() {
            var data = $(this).attr('data');
            location.href = '{{$context}}/stats/{{$meta["full"]}}?t={{$t}}&y={{$year}}&i=' + data;
        })
        $('.model').click(function() {
            var model = $(this).attr('data');
            console.log('click:'+ model);
            window.open( '{{$context}}/stats/Detail?y={{$year}}&d={{$meta["full"]}}&i={{$i}}&model=' + model);
        })
        $('.alert').click(function() {
            var range = $('.stats-range');
            if(range.css('display') == 'none') range.show();
            else range.hide();
        })
    });
</script>
</html>

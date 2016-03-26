<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='http://cdn.bootcss.com/jquery/1.12.1/jquery.min.js'></script>
<script src='http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
.stats-highlight {background-color: #dff0d8}
.stats-highlight, .stats-list-item{cursor: pointer}
.stats-list {margin: 15px; display: none}
</style>
</head>
<body>
{{include file='stats/common/top-nav.tpl'}}
<div class='container' style='margin: 70px auto'>
    {{include file='stats/common/tab-nav.tpl'}}
    <div class='row' style='margin-top: 15px'>
        <table class='table'>
            <tr class='stats-highlight'>
                <td><strong>{{$year}}{{$meta['short']}}{{$i}}</strong></td>
                <td>销量</td><td>{{$stats[$i-1]['quantity']}}</td><td>{{$stats[$i-1]['quantityRatio']}}</td>
                <td>销售额</td><td>{{$stats[$i-1]['amount']}}</td><td>{{$stats[$i-1]['amountRatio']}}</td>
            </tr>
        </table>
    </div>
    <div class='row stats-list'>
        <table class='table table-hover table-bordered'>
            {{foreach $stats as $item}}
                <tr class='stats-list-item' data='{{$item["i"]}}'>
                    <td><strong>{{$year}}{{$meta['short']}}{{$item['i']}}</strong></td>
                    <td>销量</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td>
                    <td>销售额</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td>
                </tr>
            {{/foreach}}
        </table>
    </div>
    <div class='row'>
        <div class='col-md-4'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>渠道</h3>
                </div>
            </div>
            <table class='table table-bordered table-condensed'>
                {{foreach $channelStats as $item}}
                    <tr><td>{{$item['channel']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
        <div class='col-md-4'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>产品型号</h3>
                </div>
            </div>
            <table class='table table-bordered table-condensed table-hover'>
                {{foreach $modelStats as $item}}
                    <tr class='model' data='{{$item["model"]}}'><td>{{$item['model']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
        <div class='col-md-4'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>尺码</h3>
                </div>
            </div>
            <table class='table table-bordered table-condensed'>
                {{foreach $sizeStats as $item}}
                    <tr><td>{{$item['size']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
    </div>
</div>
</body>
<script>
    $(function(){
        $('.stats-list-item').each(function() {
            var data = $(this).attr('data');
            var i = '{{$i}}'
            if(data == i) $(this).css('background-color', '#dff0d8');
        })
        $('.stats-list-item').click(function() {
            var data = $(this).attr('data');
            location.href = '{{$context}}/stats/{{$meta["full"]}}?t={{$t}}&y={{$year}}&i=' + data;
        })
        $('.model').click(function() {
            var model = $(this).attr('data');
            console.log('click:'+ model);
            window.open( '{{$context}}/stats/Detail?y={{$year}}&d={{$meta["full"]}}&i={{$i}}&model=' + model);
        })
        $('.stats-highlight').click(function() {
            var statsList = $('.stats-list');
            statsList.show();
            statsList.prev().hide();
        })
    });
</script>
</html>

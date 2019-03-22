<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='//stackpath.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='//code.jquery.com/jquery-1.12.4.min.js'></script>
<script src='//stackpath.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
.stats-highlight {background-color: #dff0d8}
.stats-highlight, .stats-list-item, .channel, .model, .size{cursor: pointer}
.stats-list {margin: 15px; display: none}
.panel-title {display: inline-block}
.panel-title+span {float: right; cursor: pointer}
</style>
</head>
<body>
{{include file='stats/common/top-nav.tpl'}}
<div class='container' style='margin: 70px auto'>
    {{include file='stats/common/tab-nav.tpl'}}
    <div class='row' style='margin-top: 15px'>
        <table class='table'>
            <tr class='stats-highlight'>
                <td><strong>{{$year}}{{$meta['short']}}{{$i}}{{if $meta['short'] == 'W'}} ({{$meta['monday']}} : {{$meta['sunday']}}){{/if}}</strong></td>
                <td>销量</td><td>{{$stats[$i-1]['quantity']}}</td><td>{{$stats[$i-1]['quantityRatio']}}</td>
                <td>销售额</td><td>{{$stats[$i-1]['amount']}}</td><td>{{$stats[$i-1]['amountRatio']}}</td>
            </tr>
        </table>
    </div>
    <div class='row stats-list'>
        <table class='table table-hover table-bordered'>
            {{foreach $stats as $item}}
                <tr class='stats-list-item' data='{{$item["i"]}}'>
                    <td><strong>{{$year}}{{$meta['short']}}{{$item['i']}}{{if $meta['short'] == 'W'}} ({{$item['monday']}} : {{$item['sunday']}}){{/if}}</strong></td>
                    <td>销量</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td>
                    <td>销售额</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td>
                </tr>
            {{/foreach}}
        </table>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>渠道</h3>
                    <span class='glyphicon glyphicon-retweet sort' data='channel'></span>
                </div>
            </div>
            <table class='table table-bordered table-condensed'>
                {{foreach $channelStats as $item}}
                    <tr class='channel' data='{{$item["channel"]}}'><td>{{$item['channel']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
        <div class='col-md-6'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>产品标签</h3>
                    <span class='glyphicon glyphicon-retweet sort' data='tag'></span>
                </div>
            </div>
            <table class='table table-bordered table-condensed'>
                {{foreach $tagStats as $item}}
                    <tr class='tag' data='{{$item["tag"]}}'><td>{{$item['tag']}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
    </div>
    <div class="row">
        <div class='col-md-6'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>产品型号</h3>
                    <span class='glyphicon glyphicon-retweet sort' data='model'></span>
                </div>
            </div>
            <table class='table table-bordered table-condensed'>
                {{foreach $modelStats as $item}}
                    <tr class='model' data='{{$item["model"]}}'><td>{{$item['model']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
        <div class='col-md-6'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>尺码</h3>
                    <span class='glyphicon glyphicon-retweet sort' data='size'></span>
                </div>
            </div>
            <table class='table table-bordered table-condensed'>
                {{foreach $sizeStats as $item}}
                    <tr class='size' data='{{$item["size"]}}'><td>{{$item['size']|upper}}</td><td>{{$item['quantity']}}</td><td>{{$item['quantityRatio']}}</td><td>{{$item['amount']}}</td><td>{{$item['amountRatio']}}</td></tr>
                {{/foreach}}
            </table>
        </div>
    </div>
</div>
</body>
<script>
    $(function(){
        registerModelClick();
        registerChannelClick();
        registerSizeClick();
        $('.stats-list-item').each(function() {
            var data = $(this).attr('data');
            var i = '{{$i}}'
            if(data == i) $(this).css('background-color', '#dff0d8');
        })
        $('.stats-list-item').click(function() {
            var data = $(this).attr('data');
            location.href = '{{$context}}/stats/{{$meta["full"]}}?t={{$t}}&y={{$year}}&i=' + data;
        })
        $('.stats-highlight').click(function() {
            var statsList = $('.stats-list');
            statsList.show();
            statsList.prev().hide();
        })
        $('.sort').click(function() {
            var type = $(this).attr('data');
            var sortIdx = nextSortIdx(type);
            var items = $(this).parent().parent().next().find('tr');
            var sortArray = [];
            var dataArray = [];
            for(var i = 0; i < items.length; i++) {
                var text = items.eq(i).children().eq(sortIdx).text();
                var data = parseFloat(text);
                sortArray.push(i);
                dataArray.push(data);
            }
            for(var i = 0; i < sortArray.length; i++) {
                for(j = i; j < sortArray.length; j++) {
                    if(dataArray[sortArray[i]] < dataArray[sortArray[j]]) {
                        sortArray[i] += sortArray[j];
                        sortArray[j] = sortArray[i] - sortArray[j];
                        sortArray[i] = sortArray[i] - sortArray[j];
                    }
                }
            }
            var sortHtml = '';
            sortArray.forEach(function(i) {
                var html = items.eq(i).prop('outerHTML')
                sortHtml += html
            })
            var table = items.parent();
            items.remove();
            table.append(sortHtml);
            $(this).parent().parent().next().find('tr').each(function() {
                $(this).children().each(function($i) {
                    if($i == sortIdx) $(this).css('background-color', '#f5f5f5');
                    else $(this).css('background-color', '#ffffff')
                });
            })
            type == 'model' && registerModelClick();
            type == 'channel' && registerChannelClick();
            type == 'size' && registerSizeClick();
        })
    });
    function registerModelClick() {
        $('.model').click(function() {
            var model = $(this).attr('data');
            window.open( '{{$context}}/stats/Detail?y={{$year}}&d={{$meta["full"]}}&i={{$i}}&model=' + model);
        })
    }
    function registerChannelClick() {
        $('.channel').click(function() {
            var channel = $(this).attr('data');
            window.open( '{{$context}}/stats/Detail?y={{$year}}&d={{$meta["full"]}}&i={{$i}}&channel=' + channel);
        })
    }
    function registerSizeClick() {
        $('.size').click(function() {
            var size = $(this).attr('data');
            window.open( '{{$context}}/stats/Detail?y={{$year}}&d={{$meta["full"]}}&i={{$i}}&size=' + size);
        })
    }
    var sortIdxArray = {
        channel: 1,
        model: 1,
        size: 1,
        tag: 1
    }
    function nextSortIdx(name) {
        (sortIdxArray[name] == 4) ? sortIdxArray[name] = 1 : ++sortIdxArray[name];
        return sortIdxArray[name];
    }
</script>
</html>

<!DOCTYPE html>
<head>
    {{include file='stats/v2/common/header.tpl'}}
    <style>
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>父ASIN销售统计 <small>【{{strtoupper($data['head']['asin'])}}】【{{$data['head']['start']}}】【{{$data['head']['end']}}】</small></h1>
    </div>
    <table class="table table-striped">
        <th>SKU</th>
        {{foreach $data['head']['channels'] as $channel}}
        <th colspan="2">{{$channel}}</th><th colspan="2">{{$channel}}-FBA</th>
        {{/foreach}}
        <th>中国</th><th>美国</th><th>德国</th><th>英国</th>
        {{foreach $data['sku'] as $name => $stats}}
            <tr>
                <td>{{$name}}</td>
                {{foreach $stats['channel'] as $channel}}
                <td>{{$channel['count']}}</td><td>{{$channel['ratio']}}</td><td>{{$channel['fbaCount']}}</td><td>{{$channel['fbaRatio']}}</td>
                {{/foreach}}
                <td>{{$stats['china']}}</td>
                <td>{{$stats['america']}}</td>
                <td>{{$stats['german']}}</td>
                <td>{{$stats['england']}}</td>
            </tr>
        {{/foreach}}
    </table>
    <hr/>
    <table class="table table-striped">
        <th>SIZE</th>
        {{foreach $data['head']['channels'] as $channel}}
        <th colspan="2">{{$channel}}</th><th colspan="2">{{$channel}}-FBA</th>
        {{/foreach}}
        {{foreach $data['size'] as $name => $stats}}
            <tr>
                <td>{{$name}}</td>
                <td>{{$stats['count']}}</td><td>{{$stats['ratio']}}</td>
                <td>{{$stats['fbaCount']}}</td><td>{{$stats['fbaRatio']}}</td>
            </tr>
        {{/foreach}}
    </table>
</div>
<script>
    $(function () {
    })
</script>
</body>
</html>

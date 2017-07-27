<!DOCTYPE html>
<head>
    {{include file='stats/v2/common/header.tpl'}}
    <style>
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>产品属性统计 <small>【{{$data['head']['attribute']}}】【{{$data['head']['start']}}】【{{$data['head']['end']}}】</small></h1>
    </div>
    <table class="table table-striped">
        <th>{{$data['head']['attribute']}}</th><th>total</th><th>ratio</th><th>chain ratio</th>
        {{foreach $data['body'] as $attr => $attrStats}}
            <tr>
                <td>{{$attr}}</td>
                <td>{{$attrStats['count']}}</td>
                <td>{{$attrStats['ratio']}}</td>
                <td>{{$attrStats['chainRatio']}}</td>
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

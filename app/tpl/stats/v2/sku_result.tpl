<!DOCTYPE html>
<head>
    {{include file='stats/v2/common/header.tpl'}}
    <style>
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>SKU销售统计 <small>【{{strtoupper($data['head']['model'])}}】【{{strtoupper($data['head']['market'])}}】【{{$data['head']['start']}}】【{{$data['head']['end']}}】</small></h1>
    </div>
    <table class="table table-striped">
        <th>SIZE</th>
        {{foreach $data['head']['stores'] as $store}}
            <th>{{$store}}</th>
        {{/foreach}}
        <th>中国</th><th>美国</th><th>德国</th><th>英国</th>
        {{foreach $data['body'] as $size => $sizeStats}}
            <tr>
                <td>{{$size}}</td>
                {{foreach $data['head']['stores'] as $store}}
                    <td>
                        {{if $sizeStats[$store]}}
                            {{$sizeStats[$store]['count']}} ({{$sizeStats[$store]['ratio']}})
                        {{/if}}
                    </td>
                {{/foreach}}
                <td>{{$sizeStats['cn']}}</td><td>{{$sizeStats['us']}}</td><td>{{$sizeStats['de']}}</td><td>{{$sizeStats['uk']}}</td>
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

<!DOCTYPE html>
<head>
    {{include file='stats/v2/common/header.tpl'}}
    <style>
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>SKU利润统计（仅供参考） <small>【{{strtoupper($data['head']['model'])}}】【{{$data['head']['start']}}】【{{$data['head']['end']}}】</small></h1>
    </div>
    <table class="table table-striped">
        <th>Size</th><th>Channel</th><th>Average Price</th><th>Average Profit</th><th>Product Cost Ratio</th><th>Express Cost Ratio</th><th>Total Sales</th><th>Returns</th>
        {{foreach $data['body'] as $size => $sizeStats}}
            <tr>
                <td>{{$size}}</td>
                <td>{{$sizeStats['channel']}}</td>
                <td>{{$sizeStats['averagePrice']}}</td>
                <td>{{$sizeStats['averageProfit']}}</td>
                <td>{{$sizeStats['costRatio']}}</td>
                <td>{{$sizeStats['expressRatio']}}</td>
                <td>{{$sizeStats['count']}}</td>
                <td>{{$sizeStats['return']}}</td>
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

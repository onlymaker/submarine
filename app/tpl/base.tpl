<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Navi</title>
    <link rel="icon" href="http://qiniu.syncxplus.com/logo/testbird.png?imageView2/0/w/100"/>
    <script src="http://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
    <link href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        .container {
            margin-top: 25px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="list-group">
        <li class="list-group-item active">Version 1</li>
        <a href="{{$context}}/stats" class="list-group-item" target="_blank">View</a>
    </div>
    <div class="list-group">
        <li class="list-group-item active">Version 2</li>
        <a href="{{$context}}/stats/sku" class="list-group-item">SKU</a>
        <a href="{{$context}}/stats/asin" class="list-group-item">ASIN</a>
        <a href="{{$context}}/stats/product" class="list-group-item">Product</a>
        <a href="{{$context}}/stats/profit" class="list-group-item">Profit</a>
    </div>
</div>
<script>
    $(function () {
    })
</script>
</body>
</html>

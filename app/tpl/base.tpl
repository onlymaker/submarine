<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Navi</title>
    <link rel="icon" href="//qiniu.syncxplus.com/logo/testbird.png?imageView2/0/w/100"/>
    <script src="//cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        .container {
            margin-top: 25px;
        }
        .sku-stats, .sku-download {
            cursor: pointer;
        }
        .sku-download {
            float: right;
            background-color: darkgray;
            color: white;
            padding-left: 3px;
            padding-right: 3px;
            z-index: 1000;
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
        <li class="list-group-item sku-stats">SKU<span class="sku-download">下载</span></li>
        <a href="{{$context}}/stats/asin" class="list-group-item">ASIN</a>
        <a href="{{$context}}/stats/product" class="list-group-item">Product</a>
        <a href="{{$context}}/stats/profit" class="list-group-item">Profit</a>
    </div>
</div>
<script>
    $(function () {
        $(".sku-stats").on("click", function () {
            console.log("sku-stats");
            location.href = "{{$context}}/stats/sku";
        });
        $(".sku-download").on("click", function (e) {
            console.log("sku-download");
            e.stopPropagation();
            window.open("{{$context}}/stats/sku/download");
        })
    })
</script>
</body>
</html>

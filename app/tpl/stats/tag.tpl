<!DOCTYPE HTML>
<html>
<head>
<title>{{$title}}</title>
<link href='//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
<script src='//cdn.bootcss.com/jquery/1.12.4/jquery.min.js'></script>
<script src='//cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js'></script>
<style>
body {font-family: '微软雅黑', 'Microsoft Yahei', '宋体', 'songti', STHeiti, Helmet, Freesans, 'Helvetica Neue', Helvetica, Arial, sans-serif;}
</style>
</head>
<body>
<div class='container' style='margin: 70px auto'>
    <div class="page-header">
        <h1>{{$title|upper}} <small>{{$description}}</small></h1>
    </div>
    <div class="row">
        <table class="table table-hover"></table>
    </div>
</div>
<script>
    $.ajax({
        url: location.href,
        method: "POST"
    }).done(function (data) {
        var tags = JSON.parse(data)
        tags.forEach(function (tag) {
            $(".table").append("<tr><td>" + tag + "</td></tr>")
        })
    })
</script>
</body>
</html>

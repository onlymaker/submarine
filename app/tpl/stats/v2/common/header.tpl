<meta charset="UTF-8">
<title>{{$title}}</title>
<link rel="icon" href="//qiniu.syncxplus.com/logo/testbird.png?imageView2/0/w/100"/>
<script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<link href="//stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
<script src="//stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<script>
    $(function () {
        $("#cancel-btn").on('click', function () {
            location.href = "{{$context}}/";
        });
    });
</script>

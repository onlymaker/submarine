<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand"><strong>销售统计</strong></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{$context}}/stats/Week">WEEK</a></li>
                <li><a href="{{$context}}/stats/Month">MONTH</a></li>
                <li><a href="{{$context}}/stats/Season">SEASON</a></li>
            </ul>
        </div>
    </div>
</nav>
<script>
    $(function() {
        $(".navbar-fixed-top li a").each(function() {
            var url = $(this).attr("href");
            if(location.href.indexOf(url) != -1) {
                $(this).parent().addClass("active");
            }
        });
    });
</script>

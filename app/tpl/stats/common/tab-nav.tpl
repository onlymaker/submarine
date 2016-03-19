<ul class='nav nav-tabs'>
    <li role='presentation'><a href='{{$context}}/stats/{{$meta['full']}}?t=shoe&y={{$year}}'>鞋类</a></li>
    <li role='presentation'><a href='{{$context}}/stats/{{$meta['full']}}?t=smallware&y={{$year}}'>小商品</a></li>
</ul>
<script>
    $(function() {
        var url = location.href;
        if(url.indexOf('smallware') > 0) $('.nav-tabs li').eq(1).addClass('active');
        else $('.nav-tabs li').eq(0).addClass('active');
    });
</script>

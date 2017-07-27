<!DOCTYPE html>
<head>
    {{include file='stats/v2/common/header.tpl'}}
    <style>
        .btn-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <form method="post">
        <div class="form-group">
            <label>Attributte</label>
            <select name="attribute" class="form-control">
                <option></option>
                {{foreach $meta as $item}}
                <option value="{{$item}}">{{$item}}</option>
                {{/foreach}}
            </select>
        </div>
        <div class="form-group">
            <label>Start Date</label>
            <input name="start-date" class="form-control" placeholder="xxxx-xx-xx"/>
        </div>
        <div class="form-group">
            <label>End Date</label>
            <input name="end-date" class="form-control" placeholder="xxxx-xx-xx"/>
        </div>
    </form>
    <div class="btn-group">
        <button id="cancel-btn" class="btn btn-default">取消</button>
        <button id="submit-btn" class="btn btn-primary">确定</button>
    </div>
</div>
<script>
    $(function () {
        var submitBtn = $("#submit-btn");
        submitBtn.click(function () {
            var attribute = $("select[name=attribute]").children("option:selected").val();
            if (!attribute) {
                return alert("attribute 不能为空");
            }
            var start = $("input[name=start-date]").val().trim();
            var end = $("input[name=end-date]").val().trim();
            var reg = /^\d{4}-\d{2}-\d{2}$/;
            if (!reg.test(start) || !reg.test(end)) {
                return alert('无效的日期');
            }
            submitBtn.attr("disabled", true).append("<i class='fa fa-spinner fa-spin'></i>");
            $.post("{{$context}}/stats/product/validate", {
                'attribute': attribute,
                'start': start,
                'end': end
            })
                .done(function (data) {
                    var json = JSON.parse(data);
                    if (json.error.code == 0) {
                        $("form").submit();
                    } else {
                        alert(json.error.message);
                    }
                })
                .fail(function (error) {
                    console.error(error);
                    alert(error);
                })
                .always(function () {
                    submitBtn.removeAttr("disabled");
                    $(".fa.fa-spinner.fa-spin").remove();
                });
        })
    })
</script>
</body>
</html>

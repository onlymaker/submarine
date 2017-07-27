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
            <label>SKU</label>
            <input name="sku" class="form-control"/>
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
            var sku = $("input[name=sku]").val().trim();
            if (!sku) {
                return alert("sku 不能为空");
            }
            submitBtn.attr("disabled", true).append("<i class='fa fa-spinner fa-spin'></i>");
            $.post("{{$context}}/stats/profit/validate", {'sku': sku})
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

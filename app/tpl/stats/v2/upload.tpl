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
    <div class="form-group">
        <label>上传类型</label>
        <select id="upload-type" class="form-control">
            <option></option>
            <option value="product-meta">产品属性</option>
            <option value="product-asin">父子关系</option>
        </select>
    </div>
    <div class="form-group">
        <label>选择文件</label>
        <input id="upload-file" class="form-control" type="file" name="file"/>
    </div>
    <div class="btn-group">
        <button id="cancel-btn" class="btn btn-default">取消</button>
        <button id="upload-btn" class="btn btn-primary">上传</button>
    </div>
</div>
<script>
    $(function () {
        var uploadBtn = $("#upload-btn");
        uploadBtn.click(function () {
            var type = $("#upload-type").children("option:selected").val();
            if (!type) {
                alert("尚未选择文件类型");
                return false;
            }
            var files = $("#upload-file").get(0).files;
            if (!files.length) {
                alert("尚未选择文件路径");
                return false;
            }
            var formData = new FormData();
            formData.append("file", files[0]);
            formData.append("type", type);
            uploadBtn.attr("disabled", true).append("<i class='fa fa-spinner fa-spin'></i>");
            $.ajax({
                url: location.href,
                type: "POST",
                cache: false,
                data: formData,
                contentType: false,
                processData: false,
            })
                .done(function (data) {
                    console.log(data);
                    var json = JSON.parse(data);
                    var result = (json.error.code == 0) ? "上传完成" : "上传失败";
                    for(var i in json.result) {
                        var row = json.result[i].row;
                        row++;
                        result += "\n第" + row + "行: " + json.result[i].error;
                    }
                    alert(result);
                })
                .fail(function (error) {
                    console.error(error);
                    alert(error)
                })
                .always(function () {
                    uploadBtn.removeAttr("disabled");
                    $(".fa.fa-spinner.fa-spin").remove();
                })
        })
    })
</script>
</body>
</html>

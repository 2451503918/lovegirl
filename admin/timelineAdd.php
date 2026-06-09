<?php
session_start();
include_once 'Nav.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">新增轨迹</h4>
                <form class="needs-validation" action="timelineAddPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label>类型</label>
                        <select class="form-control" name="type">
                            <option value="love">恋爱</option>
                            <option value="travel">旅行</option>
                            <option value="life">生活</option>
                            <option value="work">工作</option>
                            <option value="study">学习</option>
                            <option value="other">其他</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>标题</label>
                        <input type="text" class="form-control" name="title" placeholder="请输入标题" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>内容</label>
                        <textarea class="form-control" name="content" rows="4" placeholder="请输入内容" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>日期</label>
                        <input type="date" class="form-control col-sm-4" name="date" value="<?php echo date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>地点</label>
                        <input type="text" class="form-control" name="location" placeholder="请输入地点（选填）">
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="timelineAddPost">添加轨迹</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$("#timelineAddPost").click(function () {
    var type = $("select[name='type']").val();
    var title = $("input[name='title']").val();
    var content = $("textarea[name='content']").val();
    var date = $("input[name='date']").val();
    var location = $("input[name='location']").val();

    if (!title.trim()) { toastr["error"]("请输入标题"); return; }
    if (!content.trim()) { toastr["error"]("请输入内容"); return; }

    $.ajax({
        url: "timelineAddPost.php",
        data: {
            type: type, title: title, content: content,
            date: date, location: location,
            csrf_token: $("#csrf_token").val(),
        },
        type: "POST",
        dataType: "text",
        success: function (res) {
            if (res === "1") {
                toastr["success"]("添加轨迹成功！", "Like_Girl");
                setTimeout(function(){ window.location.href='timelineSet.php'; }, 1000);
            } else {
                toastr["error"]("添加轨迹失败！", "Like_Girl");
            }
        },
        error: function () {
            toastr["error"]("网络错误 请稍后重试！", "Like_Girl");
        }
    });
});
</script>

<?php include_once 'Footer.php'; ?>
</body>
</html>

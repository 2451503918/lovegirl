<?php
session_start();
include_once 'Function.php';
include_once 'Nav.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("<script>alert('参数错误');history.back();</script>");
}

$timelineItem = null;
if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT id, type, title, content, date, location FROM timeline WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && mysqli_num_rows($res) > 0) {
        $timelineItem = mysqli_fetch_assoc($res);
    }
    mysqli_stmt_close($stmt);
}

if (!$timelineItem) {
    die("<script>alert('轨迹不存在');history.back();</script>");
}

$typeLabels = ['love'=>'恋爱','travel'=>'旅行','life'=>'生活','work'=>'工作','study'=>'学习','other'=>'其他'];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">修改轨迹</h4>
                <form class="needs-validation" action="timelineUpdaPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?php echo intval($timelineItem['id']) ?>">
                    <div class="form-group mb-3">
                        <label>类型</label>
                        <select class="form-control" name="type">
                            <?php foreach ($typeLabels as $val => $label): ?>
                            <option value="<?php echo $val ?>" <?php echo ($timelineItem['type'] ?? '') === $val ? 'selected' : '' ?>><?php echo $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>标题</label>
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($timelineItem['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>内容</label>
                        <textarea class="form-control" name="content" rows="4" required><?php echo htmlspecialchars($timelineItem['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>日期</label>
                        <input type="date" class="form-control col-sm-4" name="date" value="<?php echo htmlspecialchars($timelineItem['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>地点</label>
                        <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($timelineItem['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="timelineUpdaPost">保存修改</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$("#timelineUpdaPost").click(function () {
    var id = $("input[name='id']").val();
    var type = $("select[name='type']").val();
    var title = $("input[name='title']").val();
    var content = $("textarea[name='content']").val();
    var date = $("input[name='date']").val();
    var location = $("input[name='location']").val();

    if (!title.trim()) { toastr["error"]("请输入标题"); return; }
    if (!content.trim()) { toastr["error"]("请输入内容"); return; }

    $.ajax({
        url: "timelineUpdaPost.php",
        data: {
            id: id, type: type, title: title, content: content,
            date: date, location: location,
            csrf_token: $("#csrf_token").val(),
        },
        type: "POST",
        dataType: "text",
        success: function (res) {
            if (res === "1") {
                toastr["success"]("修改轨迹成功！", "Like_Girl");
                setTimeout(function(){ window.location.href='timelineSet.php'; }, 1000);
            } else {
                toastr["error"]("修改轨迹失败！", "Like_Girl");
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

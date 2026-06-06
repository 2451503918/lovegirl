<?php
session_start();
include_once 'Nav.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = "SELECT id, articletitle, articletext, articletime, articlename FROM article WHERE id=? limit 1";
$stmt = mysqli_prepare($connect, $article);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mod = mysqli_fetch_array($result);
?>

<link href="/admin/editormd/css/editormd.css" rel="stylesheet">
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3 size_18">修改文章—— <?php echo htmlspecialchars($mod['articletitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></h4>

                <form class="needs-validation" action="littleupda.php" method="post" onsubmit="return check()"
                      novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label for="validationCustom01">标题</label>
                        <input name="articletitle" type="text" class="form-control" id="validationCustom01"
                               placeholder="请输入标题" value="<?php echo htmlspecialchars($mod['articletitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div id="test-editor">
                        <textarea name="articletext"><?php echo htmlspecialchars($mod['articletext'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="form-group mb-3 text_right">
                        <input name="id" value="<?php echo intval($id) ?>" type="hidden">
                        <button class="btn btn-primary" type="button" id="littleupda">修改发布</button>
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>


<script src="https://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="/admin/editormd/editormd.js"></script>
<script type="text/javascript">
    $(function () {
        var editor = editormd("test-editor", {
            htmlDecode: true,
            path: "/admin/editormd/lib/"

        });
    });
</script>

<?php
include_once 'Footer.php';
?>

</body>
</html>
<?php
session_start();
include_once 'Function.php';
include_once 'Nav.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">新增相册</h4>
                <form class="needs-validation" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label>相册标题</label>
                        <input type="text" class="form-control" name="title" placeholder="请输入相册标题" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>相册编码</label>
                        <input type="text" class="form-control" name="code" placeholder="请输入唯一编码，如P20240101" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>封面图URL</label>
                        <input type="text" class="form-control" name="img" placeholder="请输入封面图片URL" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>相册描述</label>
                        <textarea class="form-control" name="desc" rows="3" placeholder="请输入相册描述"></textarea>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="ImgAddPost">提交新增</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Footer.php'; ?>
<script src="../Style/toastr/toastr.min.js"></script>
<script src="ajax.js"></script>

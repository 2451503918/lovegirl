<?php
session_start();
include_once 'Function.php';
include_once 'Nav.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">新增恋爱清单</h4>
                <form class="needs-validation" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label>事件名称</label>
                        <input type="text" class="form-control" name="eventname" placeholder="请输入事件名称" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>是否完成</label>
                        <select class="form-control" name="icon">
                            <option value="0">未完成</option>
                            <option value="1">已完成</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>图片URL（选填）</label>
                        <input type="text" class="form-control" name="img" placeholder="完成后的图片URL">
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="listaddPost">提交新增</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Footer.php'; ?>
<script src="../Style/toastr/toastr.min.js"></script>
<script src="ajax.js"></script>

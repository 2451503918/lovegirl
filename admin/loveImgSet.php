<?php
session_start();
include_once 'Function.php';
include_once 'Nav.php';
$loveImg = "SELECT id, title, img, `desc`, date FROM photo ORDER BY id DESC";
$resImg = mysqli_query($connect, $loveImg);
?>


<link href="assets/css/vendor/dataTables.bootstrap4.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/vendor/responsive.bootstrap4.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/vendor/buttons.bootstrap4.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/vendor/select.bootstrap4.css" rel="stylesheet" type="text/css"/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3 size_18">恋爱相册<a href="/admin/loveImgAdd.php">
                        <button type="button" class="btn btn-success btn-sm right_10">
                            <i class="mdi mdi-circle-edit-outline"></i>新增
                        </button>
                    </a></h4>
                <table id="basic-datatable" class="table dt-responsive nowrap" width="100%">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>图片描述</th>
                        <th>日期</th>
                        <th style="width:150px;">操作</th>
                    </tr>
                    </thead>


                    <tbody>
                    <?php
                    $SerialNumber = 0;
                    while ($list = mysqli_fetch_array($resImg)) {
                        $SerialNumber++;
                        ?>
                        <tr>
                            <td>
                                <div class="SerialNumber">
                                    <?php echo $SerialNumber ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($list['title'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?php echo htmlspecialchars($list['date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="modImg.php?id=<?php echo intval($list['id']) ?>">
                                    <button type="button" class="btn btn-secondary btn-rounded">
                                        <i class=" mdi mdi-clipboard-text-play-outline mr-1"></i>修改
                                    </button>
                                </a>
                                <form method="POST" action="delImg.php" style="display:inline" onsubmit="return confirm('您确认要删除描述为 <?php echo htmlspecialchars($list['title'], ENT_QUOTES, 'UTF-8') ?> 的相册图片吗')">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($list['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn btn-danger btn-rounded">
                                        <i class=" mdi mdi-delete-empty mr-1"></i>删除
                                    </button>
                                </form></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>


<?php
include_once 'Footer.php';
?>
<!-- third party js -->
<script src="assets/js/vendor/jquery.dataTables.min.js"></script>
<script src="assets/js/vendor/dataTables.bootstrap4.js"></script>
<script src="assets/js/vendor/dataTables.responsive.min.js"></script>
<script src="assets/js/vendor/responsive.bootstrap4.min.js"></script>
<script src="assets/js/vendor/dataTables.buttons.min.js"></script>
<script src="assets/js/vendor/buttons.bootstrap4.min.js"></script>
<script src="assets/js/vendor/buttons.html5.min.js"></script>
<script src="assets/js/vendor/buttons.flash.min.js"></script>
<script src="assets/js/vendor/buttons.print.min.js"></script>
<script src="assets/js/vendor/dataTables.keyTable.min.js"></script>
<script src="assets/js/vendor/dataTables.select.min.js"></script>
<!-- third party js ends -->
<!-- demo app -->
<script src="assets/js/pages/demo.datatable-init.js"></script>
<!-- end demo js-->



</body>
</html>
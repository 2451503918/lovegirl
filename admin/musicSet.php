<?php
session_start();
include_once 'Function.php';
include_once 'Nav.php';

$musicList = [];
if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT id, music_name, music_artist, music_url, music_cover, music_lrc FROM music ORDER BY id DESC");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $resMusic = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($resMusic)) {
            $musicList[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
$musicCount = count($musicList);
?>

<link href="/admin/assets/css/vendor/dataTables.bootstrap4.css" rel="stylesheet" type="text/css"/>
<link href="/admin/assets/css/vendor/responsive.bootstrap4.css" rel="stylesheet" type="text/css"/>
<link href="/admin/assets/css/vendor/buttons.bootstrap4.css" rel="stylesheet" type="text/css"/>
<link href="/admin/assets/css/vendor/select.bootstrap4.css" rel="stylesheet" type="text/css"/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3 size_18">音乐管理
                    <a href="/admin/musicAdd.php">
                        <button type="button" class="btn btn-success btn-sm right_10">
                            <i class="mdi mdi-circle-edit-outline"></i>新增
                        </button>
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm btn-rounded margin_left">
                        共<b><?php echo $musicCount ?></b>首
                    </button>
                </h4>
                <table id="basic-datatable" class="table dt-responsive nowrap" width="100%">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>歌曲名称</th>
                        <th>歌手</th>
                        <th>播放地址</th>
                        <th>封面</th>
                        <th style="width:125px;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $SerialNumber = 0;
                    foreach ($musicList as $item):
                        $SerialNumber++;
                    ?>
                    <tr>
                        <td>
                            <div class="SerialNumber"><?php echo $SerialNumber ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($item['music_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?php echo htmlspecialchars($item['music_artist'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div class="textHide index" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($item['music_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <?php echo htmlspecialchars($item['music_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($item['music_cover'])): ?>
                            <img src="<?php echo htmlspecialchars($item['music_cover'], ENT_QUOTES, 'UTF-8') ?>" style="width:40px;height:40px;border-radius:6px;object-fit:cover;" alt="cover">
                            <?php else: ?>
                            <span class="badge badge-secondary-lighten">无封面</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="modMusic.php?id=<?php echo intval($item['id']) ?>">
                                <button type="button" class="btn btn-secondary btn-rounded">
                                    <i class="mdi mdi-clipboard-text-play-outline mr-1"></i>修改
                                </button>
                            </a>
                            <form method="POST" action="delMusic.php" style="display:inline" onsubmit="return confirm('确认删除歌曲 <?php echo htmlspecialchars($item['music_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> 吗？')">
                                <input type="hidden" name="id" value="<?php echo intval($item['id']) ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-danger btn-rounded">
                                    <i class="mdi mdi-delete-empty mr-1"></i>删除
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Footer.php'; ?>

<script src="/admin/assets/js/vendor/jquery.dataTables.min.js"></script>
<script src="/admin/assets/js/vendor/dataTables.bootstrap4.js"></script>
<script src="/admin/assets/js/vendor/dataTables.responsive.min.js"></script>
<script src="/admin/assets/js/vendor/responsive.bootstrap4.min.js"></script>
<script src="/admin/assets/js/vendor/dataTables.buttons.min.js"></script>
<script src="/admin/assets/js/vendor/buttons.bootstrap4.min.js"></script>
<script src="/admin/assets/js/vendor/buttons.html5.min.js"></script>
<script src="/admin/assets/js/vendor/buttons.flash.min.js"></script>
<script src="/admin/assets/js/vendor/buttons.print.min.js"></script>
<script src="/admin/assets/js/vendor/dataTables.keyTable.min.js"></script>
<script src="/admin/assets/js/vendor/dataTables.select.min.js"></script>
<script src="/admin/assets/js/pages/demo.datatable-init.js"></script>

</body>
</html>

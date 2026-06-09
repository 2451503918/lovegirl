<?php
session_start();
include_once 'Function.php';
include_once 'Nav.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("<script>alert('参数错误');history.back();</script>");
}

$musicItem = null;
if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT id, music_name, music_artist, music_url, music_cover, music_lrc FROM music WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && mysqli_num_rows($res) > 0) {
        $musicItem = mysqli_fetch_assoc($res);
    }
    mysqli_stmt_close($stmt);
}

if (!$musicItem) {
    die("<script>alert('音乐不存在');history.back();</script>");
}
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">修改音乐</h4>

                <form class="needs-validation" action="musicUpdaPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?php echo intval($musicItem['id']) ?>">
                    <div class="form-group mb-3">
                        <label>歌曲名称</label>
                        <input type="text" class="form-control" name="music_name" value="<?php echo htmlspecialchars($musicItem['music_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>歌手</label>
                        <input type="text" class="form-control" name="music_artist" value="<?php echo htmlspecialchars($musicItem['music_artist'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>播放地址</label>
                        <input type="text" class="form-control" name="music_url" value="<?php echo htmlspecialchars($musicItem['music_url'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>封面图片</label>
                        <input type="text" class="form-control" name="music_cover" value="<?php echo htmlspecialchars($musicItem['music_cover'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label>歌词</label>
                        <textarea class="form-control" name="music_lrc" rows="5"><?php echo htmlspecialchars($musicItem['music_lrc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="musicUpdaPost">保存修改</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$("#musicUpdaPost").click(function () {
    var id = $("input[name='id']").val();
    var music_name = $("input[name='music_name']").val();
    var music_artist = $("input[name='music_artist']").val();
    var music_url = $("input[name='music_url']").val();
    var music_cover = $("input[name='music_cover']").val();
    var music_lrc = $("textarea[name='music_lrc']").val();

    if (!music_name.trim()) { toastr["error"]("请输入歌曲名称"); return; }
    if (!music_artist.trim()) { toastr["error"]("请输入歌手名"); return; }
    if (!music_url.trim()) { toastr["error"]("请输入播放地址"); return; }

    $.ajax({
        url: "musicUpdaPost.php",
        data: {
            id: id,
            music_name: music_name,
            music_artist: music_artist,
            music_url: music_url,
            music_cover: music_cover,
            music_lrc: music_lrc,
            csrf_token: $("#csrf_token").val(),
        },
        type: "POST",
        dataType: "text",
        success: function (res) {
            if (res === "1") {
                toastr["success"]("修改音乐成功！", "Like_Girl");
                setTimeout(function(){ window.location.href='musicSet.php'; }, 1000);
            } else {
                toastr["error"]("修改音乐失败！", "Like_Girl");
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

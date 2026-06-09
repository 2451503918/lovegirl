<?php
session_start();
include_once 'Nav.php';

$musicCount = 0;
if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT COUNT(*) as cnt FROM music");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) $musicCount = intval($row['cnt']);
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">新增音乐</h4>

                <form class="needs-validation" action="musicAddPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label>歌曲名称</label>
                        <input type="text" class="form-control" name="music_name" placeholder="请输入歌曲名称" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>歌手</label>
                        <input type="text" class="form-control" name="music_artist" placeholder="请输入歌手名" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>播放地址</label>
                        <input type="text" class="form-control" name="music_url" placeholder="请输入音乐文件URL（mp3等）" required>
                        <small class="form-text text-muted">支持直链mp3地址或网易云外链</small>
                    </div>
                    <div class="form-group mb-3">
                        <label>封面图片</label>
                        <input type="text" class="form-control" name="music_cover" placeholder="请输入封面图片URL（选填）">
                    </div>
                    <div class="form-group mb-3">
                        <label>歌词</label>
                        <textarea class="form-control" name="music_lrc" rows="5" placeholder="请粘贴LRC格式歌词（选填）"></textarea>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="musicAddPost">添加音乐</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$("#musicAddPost").click(function () {
    var music_name = $("input[name='music_name']").val();
    var music_artist = $("input[name='music_artist']").val();
    var music_url = $("input[name='music_url']").val();
    var music_cover = $("input[name='music_cover']").val();
    var music_lrc = $("textarea[name='music_lrc']").val();

    if (!music_name.trim()) { toastr["error"]("请输入歌曲名称"); return; }
    if (!music_artist.trim()) { toastr["error"]("请输入歌手名"); return; }
    if (!music_url.trim()) { toastr["error"]("请输入播放地址"); return; }

    $.ajax({
        url: "musicAddPost.php",
        data: {
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
                toastr["success"]("添加音乐成功！", "Like_Girl");
                setTimeout(function(){ window.location.href='musicSet.php'; }, 1000);
            } else if (res === "0") {
                toastr["error"]("添加音乐失败！", "Like_Girl");
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

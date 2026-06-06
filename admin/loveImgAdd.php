<?php
session_start();
include_once 'Nav.php';
$inv_date = date("Y-m-d");
?>

<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3 size_18">新增图片</h4>

                <form class="needs-validation" action="ImgAddPost.php" method="post" id="imgForm" onsubmit="return check()"
                      novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label for="validationCustom01">日期</label>
                        <input class="form-control col-sm-4" id="example-date" type="date" name="imgDatd" class="form-control" placeholder="日期" value="<?php echo $inv_date ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="validationCustom01">图片描述<span class="margin_left badge badge-success-lighten">尽量控制在25个字符以内 </span></label>
                        <input name="imgText" type="text" class="form-control" placeholder="请输入图片描述" value="" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>图片上传方式</label>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="btn-url" onclick="switchUploadType('url')">
                                <i class="mdi mdi-link"></i> 输入URL
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btn-upload" onclick="switchUploadType('upload')">
                                <i class="mdi mdi-upload"></i> 本地上传
                            </button>
                        </div>
                    </div>

                    <div class="form-group mb-3" id="img_url">
                        <label for="validationCustom01">图片URL</label>
                        <input type="text" name="imgUrl" class="form-control" placeholder="请输入图片URL地址" value="" required>
                    </div>

                    <div class="form-group mb-3" id="img_upload" style="display:none;">
                        <label>选择图片</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="imageFile" accept="image/jpeg,image/png,image/gif,image/webp">
                            <label class="custom-file-label" for="imageFile">选择文件...</label>
                        </div>
                        <small class="form-text text-muted">支持 JPG、PNG、GIF、WebP 格式，最大 5MB（选择后自动上传）</small>
                        <div id="uploadStatus" class="mt-2"></div>
                        <div id="previewContainer" class="mt-2" style="display:none;">
                            <img id="previewImg" src="" style="max-width:200px;max-height:200px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                        </div>
                    </div>

                    <div class="form-group mb-3 text_right">
                        <button class="btn btn-primary" type="button" id="ImgAddPost">
                            <i class="mdi mdi-plus"></i> 新增图片
                        </button>
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>

<style>
    .btn-group .btn {
        padding: 8px 20px;
        font-size: 14px;
        border-radius: 6px;
        margin-right: 5px;
        transition: all 0.3s ease;
    }
    .btn-group .btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    .btn-group .btn:hover {
        transform: translateY(-2px);
    }
    #ImgAddPost {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 10px 30px;
        font-size: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }
    #ImgAddPost:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .custom-file-input:focus~.custom-file-label {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .custom-file-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #uploadStatus .alert {
        padding: 8px 15px;
        font-size: 13px;
    }
</style>

<script>
    // 切换上传方式
    function switchUploadType(type) {
        if (type === 'url') {
            $('#img_url').show();
            $('#img_upload').hide();
            $('#img_url input').attr('required', true);
            $('#btn-url').addClass('active');
            $('#btn-upload').removeClass('active');
        } else {
            $('#img_url').hide();
            $('#img_upload').show();
            $('#img_url input').attr('required', false);
            $('#btn-url').removeClass('active');
            $('#btn-upload').addClass('active');
        }
    }

    // 文件选择后自动上传
    $('#imageFile').change(function() {
        var file = this.files[0];
        if (!file) return;

        // 更新文件选择标签
        $(this).next('.custom-file-label').html(file.name);

        // 预览图片
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImg').attr('src', e.target.result);
            $('#previewContainer').show();
        }
        reader.readAsDataURL(file);

        // 自动上传
        var formData = new FormData();
        formData.append('image', file);

        $('#uploadStatus').html('<div class="alert alert-info"><i class="mdi mdi-loading mdi-spin"></i> 上传中...</div>');
        $('#imageFile').prop('disabled', true);

        $.ajax({
            url: 'uploadImg.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.code === 1) {
                    $('#uploadStatus').html('<div class="alert alert-success"><i class="mdi mdi-check"></i> ' + res.msg + '</div>');
                    // 将上传的URL填入隐藏字段
                    $('input[name="imgUrl"]').val(res.url);
                    // 显示服务端预览
                    $('#previewImg').attr('src', '../' + res.url);
                } else {
                    $('#uploadStatus').html('<div class="alert alert-danger"><i class="mdi mdi-alert"></i> ' + res.msg + '</div>');
                }
            },
            error: function() {
                $('#uploadStatus').html('<div class="alert alert-danger"><i class="mdi mdi-alert"></i> 上传失败，请重试</div>');
            },
            complete: function() {
                $('#imageFile').prop('disabled', false);
            }
        });
    });

    // 提交表单
    function check() {
        let title = document.getElementsByName('imgText')[0].value.trim();
        if (title.length == 0) {
            alert("图片描述不能为空");
            return false;
        }
        let url = document.getElementsByName('imgUrl')[0].value.trim();
        if (url.length == 0) {
            alert("请先上传图片或输入图片URL");
            return false;
        }
        return true;
    }

    // 使用AJAX提交表单
    $('#ImgAddPost').click(function() {
        if (!check()) return;

        var formData = {
            imgDatd: $('input[name="imgDatd"]').val(),
            imgText: $('input[name="imgText"]').val(),
            imgUrl: $('input[name="imgUrl"]').val(),
            csrf_token: $('input[name="csrf_token"]').val()
        };

        $(this).prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> 提交中...');

        $.ajax({
            url: 'ImgAddPost.php',
            type: 'POST',
            data: formData,
            dataType: 'text',
            success: function(res) {
                if (res === '1') {
                    alert('图片添加成功！');
                    location.reload();
                } else {
                    alert('添加失败，请重试');
                }
            },
            error: function() {
                alert('网络错误，请重试');
            },
            complete: function() {
                $('#ImgAddPost').prop('disabled', false).html('<i class="mdi mdi-plus"></i> 新增图片');
            }
        });
    });
</script>

<?php
include_once 'Footer.php';
?>

</body>
</html>
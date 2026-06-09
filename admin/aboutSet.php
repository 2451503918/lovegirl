<?php
session_start();
include_once 'Nav.php';
$absql = "SELECT id, title, aboutimg, info1, info2, info3, btn1, btn2, infox1, infox2, infox3, infox4, infox5, infox6, btnx2, infof1, infof2, infof3, infof4, btnf3, infod1, infod2, infod3, infod4, infod5 FROM about";
$resab = mysqli_query($connect, $absql);
$about = mysqli_fetch_array($resab);
?>
<form class="needs-validation" action="aboutPost.php" method="post" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">对话配置——1</h4>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">对话标题</label>
                        <input type="text" class="form-control" id="validationCustom01" placeholder="请输入标题" name="title"
                               value="<?php echo htmlspecialchars($about['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">对话模块背景图片地址</label>
                        <input type="text" class="form-control" id="validationCustom01" placeholder="请输入图片URL地址"
                               name="aboutimg" value="<?php echo htmlspecialchars($about['aboutimg'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">对话1文本</label>
                        <input type="text" class="form-control" id="validationCustom02" placeholder="请填写对话内容"
                               name="info1" value="<?php echo htmlspecialchars($about['info1'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">对话2文本</label>
                        <input type="text" class="form-control" id="validationCustom03" placeholder="请填写对话内容"
                               name="info2" value="<?php echo htmlspecialchars($about['info2'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">对话3文本</label>
                        <input type="text" class="form-control" id="validationCustom04" placeholder="请填写对话内容"
                               name="info3" value="<?php echo htmlspecialchars($about['info3'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话1按钮确认文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="btn1"
                               value="<?php echo htmlspecialchars($about['btn1'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写确认按钮文本">
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话1按钮取消文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="btn2"
                               value="<?php echo htmlspecialchars($about['btn2'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写取消按钮文本">
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">对话配置——2</h4>

                    <div class="form-group mb-3">
                        <label for="validationCustom01">对话2-1文本</label>
                        <input type="text" class="form-control" id="validationCustom01" placeholder="请填写对话内容"
                               name="infox1" value="<?php echo htmlspecialchars($about['infox1'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">对话2-2文本</label>
                        <input type="text" class="form-control" id="validationCustom02" placeholder="请填写对话内容"
                               name="infox2" value="<?php echo htmlspecialchars($about['infox2'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">对话2-3文本</label>
                        <input type="text" class="form-control" id="validationCustom03" placeholder="请填写对话内容"
                               name="infox3" value="<?php echo htmlspecialchars($about['infox3'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">对话2-4文本</label>
                        <input type="text" class="form-control" id="validationCustom04" placeholder="请填写对话内容"
                               name="infox4" value="<?php echo htmlspecialchars($about['infox4'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话2-5文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="infox5"
                               value="<?php echo htmlspecialchars($about['infox5'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写对话内容">
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话2-6文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="infox6"
                               value="<?php echo htmlspecialchars($about['infox6'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写对话内容">
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话2-1按钮文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="btnx2"
                               value="<?php echo htmlspecialchars($about['btnx2'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写按钮文本">
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

    </div>


    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">对话配置——3</h4>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">对话3-1文本</label>
                        <input type="text" class="form-control" id="validationCustom01" placeholder="请填写对话内容"
                               name="infof1" value="<?php echo htmlspecialchars($about['infof1'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">对话3-2文本</label>
                        <input type="text" class="form-control" id="validationCustom02" placeholder="请填写对话内容"
                               name="infof2" value="<?php echo htmlspecialchars($about['infof2'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">对话3-3文本</label>
                        <input type="text" class="form-control" id="validationCustom03" placeholder="请填写对话内容"
                               name="infof3" value="<?php echo htmlspecialchars($about['infof3'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">对话3-4文本</label>
                        <input type="text" class="form-control" id="validationCustom04" placeholder="请填写对话内容"
                               name="infof4" value="<?php echo htmlspecialchars($about['infof4'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话3-1按钮文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="btnf3"
                               value="<?php echo htmlspecialchars($about['btnf3'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写按钮文本">
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">对话配置——4</h4>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">对话4-1文本</label>
                        <input type="text" class="form-control" id="validationCustom01" placeholder="请填写对话内容"
                               name="infod1" value="<?php echo htmlspecialchars($about['infod1'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">对话4-2文本</label>
                        <input type="text" class="form-control" id="validationCustom02" placeholder="请填写对话内容"
                               name="infod2" value="<?php echo htmlspecialchars($about['infod2'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">对话4-3文本</label>
                        <input type="text" class="form-control" id="validationCustom03" placeholder="请填写对话内容"
                               name="infod3" value="<?php echo htmlspecialchars($about['infod3'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">对话4-4文本</label>
                        <input type="text" class="form-control" id="validationCustom04" placeholder="请填写对话内容"
                               name="infod4" value="<?php echo htmlspecialchars($about['infod4'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">对话4-5文本</label>
                        <input type="text" class="form-control" id="validationCustom05" name="infod5"
                               value="<?php echo htmlspecialchars($about['infod5'], ENT_QUOTES, 'UTF-8') ?>" placeholder="请填写对话内容">
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>

    <div class="form-group mb-3 text_right">
        <button class="btn btn-primary" type="button" id="aboutPost">提交修改</button>
    </div>
</form>
<?php
include_once 'Footer.php';
?>

</body>
</html>
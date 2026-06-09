<?php
session_start();
include_once 'Nav.php';
?>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">基本设置</h4>

                <form class="needs-validation" action="adminPost.php" method="post" onsubmit="return check()" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label for="validationCustom01">站点标题</label>
                        <input type="text" class="form-control"  placeholder="请输入站点标题"
                               name="title" value="<?php echo htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">站点LOGO</label>
                        <input type="text" class="form-control" placeholder="请填写站点LOGO文字"
                               name="logo" value="<?php echo htmlspecialchars($text['logo'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">站点文案</label>
                        <input type="text" class="form-control"  placeholder="显示在顶部的文案"
                               name="writing" value="<?php echo htmlspecialchars($text['writing'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="validationCustom06">是否关闭头像背景高斯模糊</label>
                        <select class="form-control" id="example-select" name="WebBlur">
                            <option value="1" <?php  if($diy['Blurkg'] === "1"){ ?> selected <?php } ?>>开启</option>
                            <option value="0" <?php  if($diy['Blurkg'] === "0"){ ?> selected <?php } ?> >关闭</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="validationCustom07">是否开启前端无刷新加载</label>
                        <select class="form-control" id="example-select" name="WebPjax">
                            <option value="1" <?php  if($diy['Pjaxkg'] === "1"){ ?> selected <?php } ?>>开启</option>
                            <option value="0" <?php  if($diy['Pjaxkg'] === "0"){ ?> selected <?php } ?> >关闭</option>
                        </select>
                    </div>
                    <div class="form-group mb-3 text_right">
                        <button class="btn btn-primary" type="button" id="adminPost">提交修改</button>
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">情侣配置</h4>

                <form class="needs-validation" action="loveadminPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label for="validationCustom01">男方Name</label>
                        <input type="text" class="form-control"  placeholder="请输入男方Name"
                               name="boy" value="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">女方Name</label>
                        <input type="text" class="form-control" placeholder="请输入女方Name"
                               name="girl" value="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">男方QQ</label>
                        <input type="text" class="form-control"  placeholder="请输入男方QQ（用于显示头像）"
                               name="boyimg" value="<?php echo htmlspecialchars($text['boyimg'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">女方QQ</label>
                        <input type="text" class="form-control"  placeholder="请输入女方QQ（用于显示头像）"
                               name="girlimg" value="<?php echo htmlspecialchars($text['girlimg'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">起始时间</label>
                        <input type="datetime-local" class="form-control"  placeholder="请输入起始时间"
                               name="startTime" value="<?php echo htmlspecialchars($text['startTime'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="loveadminPost">提交修改</button>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">位置配置</h4>

                <form class="needs-validation" action="locationPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label for="boyCity">男方城市</label>
                        <input type="text" class="form-control" placeholder="如：北京"
                               name="boyCity" value="<?php echo htmlspecialchars(isset($text['boyCity']) ? $text['boyCity'] : '北京', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label for="boyLat">男方纬度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="39.9042"
                                   name="boyLat" value="<?php echo htmlspecialchars(isset($text['boyLat']) ? $text['boyLat'] : '39.9042', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="boyLng">男方经度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="116.4074"
                                   name="boyLng" value="<?php echo htmlspecialchars(isset($text['boyLng']) ? $text['boyLng'] : '116.4074', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="girlCity">女方城市</label>
                        <input type="text" class="form-control" placeholder="如：上海"
                               name="girlCity" value="<?php echo htmlspecialchars(isset($text['girlCity']) ? $text['girlCity'] : '上海', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label for="girlLat">女方纬度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="31.2304"
                                   name="girlLat" value="<?php echo htmlspecialchars(isset($text['girlLat']) ? $text['girlLat'] : '31.2304', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="girlLng">女方经度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="121.4737"
                                   name="girlLng" value="<?php echo htmlspecialchars(isset($text['girlLng']) ? $text['girlLng'] : '121.4737', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button class="btn btn-primary" type="button" id="locationPost">提交修改</button>
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">卡片配置&版权配置</h4>

                <form class="needs-validation" action="CardadminPost.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group mb-3">
                        <label for="validationCustom01">背景图片URL地址</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="bgimg" value="<?php echo htmlspecialchars($text['bgimg'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">卡片1Name</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="card1" value="<?php echo htmlspecialchars($text['card1'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">卡片1描述</label>
                        <input type="text" class="form-control" placeholder="请输入卡片描述"
                               name="deci1" value="<?php echo htmlspecialchars($text['deci1'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">卡片2Name</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="card2" value="<?php echo htmlspecialchars($text['card2'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">卡片2描述</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片描述"
                               name="deci2" value="<?php echo htmlspecialchars($text['deci2'], ENT_QUOTES, 'UTF-8') ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">卡片3Name</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="card3" value="<?php echo htmlspecialchars($text['card3'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">卡片3描述</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片描述"
                               name="deci3" value="<?php echo htmlspecialchars($text['deci3'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">域名备案号</label>
                        <input type="text" class="form-control"  placeholder="没有请留空" name="icp"
                               value="<?php echo htmlspecialchars($text['icp'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">站点版权信息</label>
                        <input type="text" class="form-control"  placeholder="请输入站点版权信息"
                               name="Copyright" value="<?php echo htmlspecialchars($text['Copyright'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group mb-3 text_right">
                        <button class="btn btn-primary" type="button" id="CardadminPost">提交修改</button>
                    </div>
                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>

<?php
include_once 'Footer.php';
?>

</body>
</html>
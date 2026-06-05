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
                    <div class="form-group mb-3">
                        <label for="validationCustom01">站点标题</label>
                        <input type="text" class="form-control"  placeholder="请输入站点标题"
                               name="title" value="<?php echo $text['title'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">站点LOGO</label>
                        <input type="text" class="form-control" placeholder="请填写站点LOGO文字"
                               name="logo" value="<?php echo $text['logo'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">站点文案</label>
                        <input type="text" class="form-control"  placeholder="显示在顶部的文案"
                               name="writing" value="<?php echo $text['writing'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="validationCustom06">是否关闭头像背景高斯模糊</label>
                        <select class="form-control" id="example-select" name="WebBlur">
                            <option value="1" <?php  if($diy['Blurkg'] === "1"){ ?> selected <?php } ?>>开启</option>
                            <option value="2" <?php  if($diy['Blurkg'] === "2"){ ?> selected <?php } ?> >关闭</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="validationCustom07">是否开启前端无刷新加载</label>
                        <select class="form-control" id="example-select" name="WebPjax">
                            <option value="1" <?php  if($diy['Pjaxkg'] === "1"){ ?> selected <?php } ?>>开启</option>
                            <option value="2" <?php  if($diy['Pjaxkg'] === "2"){ ?> selected <?php } ?> >关闭</option>
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
                    <div class="form-group mb-3">
                        <label for="validationCustom01">男方Name</label>
                        <input type="text" class="form-control"  placeholder="请输入男方Name"
                               name="boy" value="<?php echo $text['boy'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">女方Name</label>
                        <input type="text" class="form-control" placeholder="请输入女方Name"
                               name="girl" value="<?php echo $text['girl'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">男方QQ</label>
                        <input type="text" class="form-control"  placeholder="请输入男方QQ（用于显示头像）"
                               name="boyimg" value="<?php echo $text['boyimg'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">女方QQ</label>
                        <input type="text" class="form-control"  placeholder="请输入女方QQ（用于显示头像）"
                               name="girlimg" value="<?php echo $text['girlimg'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">起始时间</label>
                        <input type="datetime-local" class="form-control"  placeholder="请输入起始时间"
                               name="startTime" value="<?php echo $text['startTime'] ?>" required>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">位置配置</h4>

                <form class="needs-validation" action="locationPost.php" method="post" novalidate>
                    <div class="form-group mb-3">
                        <label for="boyCity">男方城市</label>
                        <input type="text" class="form-control" placeholder="如：北京"
                               name="boyCity" value="<?php echo isset($text['boyCity']) ? $text['boyCity'] : '北京' ?>" required>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label for="boyLat">男方纬度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="39.9042"
                                   name="boyLat" value="<?php echo isset($text['boyLat']) ? $text['boyLat'] : '39.9042' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="boyLng">男方经度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="116.4074"
                                   name="boyLng" value="<?php echo isset($text['boyLng']) ? $text['boyLng'] : '116.4074' ?>" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="girlCity">女方城市</label>
                        <input type="text" class="form-control" placeholder="如：上海"
                               name="girlCity" value="<?php echo isset($text['girlCity']) ? $text['girlCity'] : '上海' ?>" required>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label for="girlLat">女方纬度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="31.2304"
                                   name="girlLat" value="<?php echo isset($text['girlLat']) ? $text['girlLat'] : '31.2304' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="girlLng">女方经度</label>
                            <input type="number" step="0.000001" class="form-control" placeholder="121.4737"
                                   name="girlLng" value="<?php echo isset($text['girlLng']) ? $text['girlLng'] : '121.4737' ?>" required>
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
                    <div class="form-group mb-3">
                        <label for="validationCustom01">背景图片URL地址</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="bgimg" value="<?php echo $text['bgimg'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">卡片1Name</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="card1" value="<?php echo $text['card1'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom02">卡片1描述</label>
                        <input type="text" class="form-control" placeholder="请输入卡片描述"
                               name="deci1" value="<?php echo $text['deci1'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom03">卡片2Name</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="card2" value="<?php echo $text['card2'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom04">卡片2描述</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片描述"
                               name="deci2" value="<?php echo $text['deci2'] ?>" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">卡片3Name</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片Name"
                               name="card3" value="<?php echo $text['card3'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">卡片3描述</label>
                        <input type="text" class="form-control"  placeholder="请输入卡片描述"
                               name="deci3" value="<?php echo $text['deci3'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">域名备案号</label>
                        <input type="text" class="form-control"  placeholder="没有请留空" name="icp"
                               value="<?php echo $text['icp'] ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="validationCustom05">站点版权信息</label>
                        <input type="text" class="form-control"  placeholder="请输入站点版权信息"
                               name="Copyright" value="<?php echo $text['Copyright'] ?>" required>
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
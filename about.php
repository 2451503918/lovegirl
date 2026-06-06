<?php
include_once 'head.php';
$absql = "SELECT * FROM about";
$resab = null;
$about = [];
if ($connect) {
    $resab = mysqli_query($connect, $absql);
    if ($resab) {
        $about = mysqli_fetch_array($resab);
    }
}
if (empty($about)) {
    $about = [
        'aboutimg' => 'Style/img/bg.jpg',
        'title' => $text['title'] ?? '情侣小站',
        'info1' => '欢迎来到我们的小站！',
        'info2' => '这里记录着我们的故事...',
        'info3' => '想了解我们吗？',
        'btn1' => '想啊',
        'btn2' => '不想',
        'infox1' => '那就开始吧~',
        'infox2' => '我们的故事从很久以前就开始了...',
        'infox3' => '每一天都是珍贵的回忆',
        'infox4' => '每一个瞬间都值得被记录',
        'infox5' => '感谢你来到这里',
        'infox6' => '希望你能感受到我们的幸福',
        'btnx2' => '继续',
        'infof1' => '生活中有很多美好',
        'infof2' => '而最美好的就是遇见你',
        'infof3' => '愿我们永远在一起',
        'infof4' => '这就是我们的故事',
        'btnf3' => '还有吗',
        'infod1' => '故事还在继续...',
        'infod2' => '每一天都是新的篇章',
        'infod3' => '期待未来的每一天',
        'infod4' => '谢谢你的聆听',
        'infod5' => '愿你也找到属于你的幸福',
    ];
}
?>

    <div id="pjax-container">
        <div class="lgnewui-page-header">
            <div class="lgnewui-meta-container">
                <div class="lgnewui-meta-line"></div>
                <div class="lgnewui-meta-tag">
                    <i class="ph-bold ph-book-open-text lgnewui-meta-icon"></i>
                    Story Replay
                </div>
                <div class="lgnewui-meta-line"></div>
            </div>
            <h2 class="lgnewui-hero-title">用对话回放我们的故事</h2>
        </div>
        <style>
            .central-600 {
                background: url("<?php echo $about['aboutimg'] ?>") no-repeat center top;
                background-size: cover;
            }
            .central {
                padding: 0;
            }
        </style>
        <div class="central central-600">
            <div class="botui-app-container" id="botui-app">
                <bot-ui></bot-ui>
            </div>
        </div>

        <script>
            var botui = new BotUI("botui-app");
            botui.message.bot({
                delay: 200,
                content: "<?php echo $about['info1'] ?>"
            }).then(function () {
                return botui.message.bot({
                    delay: 1000,
                    content: "<?php echo $about['info2'] ?>"
                })
            }).then(function () {
                return botui.message.bot({
                    delay: 1000,
                    content: "<?php echo $about['info3'] ?>"
                })
            }).then(function () {
                return botui.action.button({
                    delay: 1500,
                    action: [{
                        text: "<?php echo $about['btn1'] ?>",
                        value: "and"
                    },
                    {
                        text: "<?php echo $about['btn2'] ?>",
                        value: "gg"
                    }]
                })
            }).then(function (res) {
                if (res.value === "and") {
                    other()
                }
                if (res.value === "gg") {
                    return botui.message.bot({
                        delay: 1500,
                        content: " ![告辞](https://img.gejiba.com/images/4f6983d663bea83530b8ac3a8a6b9220.jpg) "
                    })
                }
            });

            var other = function () {
                botui.message.bot({
                    delay: 1500,
                    content: "<?php echo $about['infox1'] ?>"
                }).then(function () {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infox2'] ?>"
                    })
                }).then(function () {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infox3'] ?>"
                    })
                }).then(function () {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infox4'] ?>"
                    })
                }).then(function () {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infox5'] ?>"
                    })
                }).then(function () {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infox6'] ?>"
                    })
                }).then(function () {
                    return botui.action.button({
                        delay: 1500,
                        action: [{
                            text: "<?php echo $about['btnx2'] ?>",
                            value: "next"
                        }]
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infof1'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infof2'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infof3'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infof4'] ?>"
                    })
                }).then(function () {
                    return botui.action.button({
                        delay: 1500,
                        action: [{
                            text: "<?php echo $about['btnf3'] ?>",
                            value: "next"
                        }]
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infod1'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infod2'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infod3'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infod4'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "<?php echo $about['infod5'] ?>"
                    })
                }).then(function (res) {
                    return botui.message.bot({
                        delay: 1500,
                        content: "本次会话结束..."
                    })
                }).then(function () {
                    return botui.message.bot({
                        delay: 1500,
                        content: "  "
                    })
                });
            }
        </script>
    </div>
    <?php
    include_once 'footer.php';
    ?>
</body>

</html>
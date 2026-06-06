<?php
include_once 'head.php';

// 获取留言数据
$shu = 0;
$jiequ = 10;
$leav = ['shu' => 0];

if ($connect) {
    $nub = "select count(id) as shu from leaving";
    $res = mysqli_query($connect, $nub);
    if ($res) {
        $leav = mysqli_fetch_array($res) ?? ['shu' => 0];
        $shu = $leav['shu'] ?? 0;
    }
    
    $leavSet = "select * from leavSet order by id desc";
    $Set = mysqli_query($connect, $leavSet);
    if ($Set) {
        $Setinfo = mysqli_fetch_array($Set);
        $jiequ = $Setinfo['jiequ'] ?? 10;
    }
}

// 获取留言列表
$messages = [];
if ($conn) {
    $liuyan = "SELECT * FROM leaving order by id desc limit ?";
    $stmt = $conn->prepare($liuyan);
    if ($stmt) {
        $stmt->bind_param("i", $jiequ);
        $stmt->execute();
        $stmt->bind_result($id, $name, $qq, $text, $time, $ip, $city);
        while ($stmt->fetch()) {
            $messages[] = [
                'id' => $id,
                'name' => $name,
                'qq' => $qq,
                'text' => $text,
                'time' => $time,
                'ip' => $ip,
                'city' => $city
            ];
        }
        $stmt->close();
    }
}
?>

<div id="pjax-container">
    <!-- 页面标题栏 -->
    <div class="lgnewui-page-header">
        <div class="lgnewui-meta-container">
            <div class="lgnewui-meta-line"></div>
            <div class="lgnewui-meta-tag">
                <i class="ph-bold ph-chat-teardrop-dots lgnewui-meta-icon"></i>
                Kind Messages
            </div>
            <div class="lgnewui-meta-line"></div>
        </div>
        <h2 class="lgnewui-hero-title">留下想说的话与温柔回应</h2>
    </div>

    <div class="lgnewui-container">
        <!-- 留言统计 -->
        <div class="lgnewui-widget lgnewui-widget--message-stats lgnewui-mb-4" data-aos="fade-up">
            <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-chat-teardrop-dots"></i></div>
            <div class="lgnewui-message-stats-content">
                <span class="lgnewui-num-huge" id="lgnewui-message-count"><?php echo $shu; ?></span>
                <span class="lgnewui-message-stats-label">条祝福留言</span>
                <span class="lgnewui-message-stats-note">（显示最新 <?php echo $jiequ; ?> 条）</span>
            </div>
        </div>

        <!-- 留言列表 -->
        <div class="lgnewui-message-list">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg):
                    // 计算用户等级
                    $userLevel = '访客';
                    $levelClass = 'lgnewui-level-visitor';
                    if ($msg['id'] <= 10) {
                        $userLevel = '元老';
                        $levelClass = 'lgnewui-level-elder';
                    } elseif ($msg['id'] <= 50) {
                        $userLevel = '常客';
                        $levelClass = 'lgnewui-level-regular';
                    }
                    
                    // 判断是否管理员
                    $isAdmin = ($msg['name'] === 'Ki' || $msg['name'] === '管理员');
                    if ($isAdmin) {
                        $userLevel = '管理员';
                        $levelClass = 'lgnewui-level-admin';
                    }

                    // 检测设备类型
                    $deviceIcon = '💻';
                    $deviceName = '电脑';
                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    if (preg_match('/mobile|android|iphone|ipad/i', $ua)) {
                        $deviceIcon = '📱';
                        $deviceName = '手机';
                    }

                    // 相对时间
                    $timeStr = '';
                    if ($msg['time']) {
                        $diff = time() - $msg['time'];
                        if ($diff < 60) {
                            $timeStr = '刚刚';
                        } elseif ($diff < 3600) {
                            $timeStr = floor($diff / 60) . '分钟前';
                        } elseif ($diff < 86400) {
                            $timeStr = floor($diff / 3600) . '小时前';
                        } elseif ($diff < 2592000) {
                            $timeStr = floor($diff / 86400) . '天前';
                        } else {
                            $timeStr = date('m月d日', $msg['time']);
                        }
                    }
                ?>
                <div class="lgnewui-message-card" data-aos="fade-up">
                    <div class="lgnewui-message-card__avatar-section">
                        <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $msg['qq']; ?>&s=100" 
                             alt="<?php echo htmlspecialchars($msg['name']); ?>" 
                             class="lgnewui-message-card__avatar"
                             onerror="this.src='Style/img/default-avatar.svg'">
                    </div>
                    <div class="lgnewui-message-card__content">
                        <div class="lgnewui-message-card__header">
                            <div class="lgnewui-message-card__user-info">
                                <span class="lgnewui-message-card__name"><?php echo htmlspecialchars($msg['name']); ?></span>
                                <span class="lgnewui-badge <?php echo $levelClass; ?>"><?php echo $userLevel; ?></span>
                            </div>
                            <span class="lgnewui-message-card__time"><?php echo $timeStr; ?></span>
                        </div>
                        <div class="lgnewui-message-card__text"><?php echo htmlspecialchars($msg['text']); ?></div>
                        <div class="lgnewui-message-card__footer">
                            <span class="lgnewui-chip">
                                <i class="ph-fill ph-map-pin"></i>
                                <?php echo $msg['city'] ? htmlspecialchars($msg['city']) : '未知'; ?>
                            </span>
                            <span class="lgnewui-chip">
                                <i class="ph-fill ph-thermometer"></i>
                                <?php echo $deviceName; ?>
                            </span>
                            <span class="lgnewui-chip lgnewui-chip--highlight">
                                <i class="ph-fill ph-clock"></i>
                                <?php echo date('Y-m-d H:i', $msg['time']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="lgnewui-empty-state" data-aos="fade-up">
                    <div class="lgnewui-empty-state__icon"><i class="ph-fill ph-chat-teardrop-dots"></i></div>
                    <h3>还没有留言</h3>
                    <p>成为第一个留下祝福的人吧</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- 留言表单 -->
        <div class="lgnewui-widget lgnewui-widget--message-form lgnewui-mt-4" data-aos="fade-up">
            <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-pencil-simple"></i></div>
            <h3 class="lgnewui-form-title">撰写留言</h3>
            <form action="admin/leavingPost.php" method="post" id="messageForm">
                <div class="lgnewui-form-row">
                    <div class="lgnewui-form-group">
                        <label class="lgnewui-form-label">
                            <i class="ph-fill ph-user"></i>
                            QQ号码
                        </label>
                        <input type="text" name="qq" id="qqInput" class="lgnewui-form-input" placeholder="输入QQ号码" maxlength="10">
                    </div>
                    <div class="lgnewui-form-group">
                        <label class="lgnewui-form-label">
                            <i class="ph-fill ph-identification-card"></i>
                            昵称
                        </label>
                        <input type="text" name="name" id="nameInput" class="lgnewui-form-input" placeholder="输入您的昵称" maxlength="20">
                    </div>
                </div>
                <div class="lgnewui-form-group">
                    <label class="lgnewui-form-label">
                        <i class="ph-fill ph-chat-circle-dots"></i>
                        留言内容
                    </label>
                    <textarea name="text" id="textInput" class="lgnewui-form-textarea" placeholder="写下你想说的话..." rows="4" maxlength="500"></textarea>
                </div>
                <div class="lgnewui-form-footer">
                    <span class="lgnewui-char-count"><span id="charCount">0</span>/500</span>
                    <button type="submit" class="lgnewui-btn-primary" id="submitBtn">
                        <i class="ph-fill ph-paper-plane-tilt"></i>
                        提交留言
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function() {
    // 留言按钮点击滚动到底部
    $('#MessageBtn').click(function() {
        var targetOffset = $('#messageForm').offset().top;
        $('html, body').animate({
            scrollTop: targetOffset
        }, 800);
    });

    // 字符计数
    $('#textInput').on('input', function() {
        $('#charCount').text($(this).val().length);
    });

    // 表单提交
    $('#messageForm').on('submit', function(e) {
        e.preventDefault();
        
        var qq = $('#qqInput').val().trim();
        var name = $('#nameInput').val().trim();
        var text = $('#textInput').val().trim();
        
        // 验证
        if (!qq) {
            toastr.warning('请填写QQ号码！', 'Like_Girl');
            return false;
        }
        if (!name) {
            toastr.warning('请填写您的昵称！', 'Like_Girl');
            return false;
        }
        if (!text) {
            toastr.warning('请填写您要留言的内容！', 'Like_Girl');
            return false;
        }
        if (text.length <= 2) {
            toastr.warning('请填写两个字符以上的内容！', 'Like_Girl');
            return false;
        }
        
        var qqRegex = /^[0-9]{6,10}$/;
        if (!qqRegex.test(qq)) {
            toastr.warning('您的QQ号码格式错误<br/>请输入6-10位的数字！', 'Like_Girl');
            return false;
        }
        
        $('#submitBtn').prop('disabled', true).html('<i class="ph-fill ph-spinner"></i> 提交中...');
        
        $.ajax({
            url: 'admin/leavingPost.php',
            data: { qq: qq, name: name, text: text },
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    toastr.success('留言提交成功！', 'Like_Girl');
                    // 清空表单
                    $('#qqInput').val('');
                    $('#nameInput').val('');
                    $('#textInput').val('');
                    $('#charCount').text('0');
                    
                    // 刷新页面查看新留言
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error('留言提交失败！', 'Like_Girl');
                }
            },
            error: function(err) {
                if (err.responseText) {
                    var code = err.responseText.trim();
                    if (code == '3' || code == '30') {
                        toastr.error('留言失败——QQ号码格式错误', 'Like_Girl');
                    } else if (code == '8') {
                        toastr.error('你今天已经留言过了~', 'Like_Girl');
                    } else {
                        toastr.error('网络错误，请稍后重试！', 'Like_Girl');
                    }
                } else {
                    toastr.error('网络错误，请稍后重试！', 'Like_Girl');
                }
            },
            complete: function() {
                setTimeout(function() {
                    $('#submitBtn').prop('disabled', false).html('<i class="ph-fill ph-paper-plane-tilt"></i> 提交留言');
                }, 3000);
            }
        });
    });
});
</script>

<?php
include_once 'footer.php';
?>

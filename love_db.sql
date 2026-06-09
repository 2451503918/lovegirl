-- ============================================================================
-- Like Girl v5.2.1 数据库
-- 完整表结构 + 索引 + 演示数据
-- 兼容原 v5.2.0 表结构（保留 article/loveImg/old_*），并补充新模块所需表
-- 数据库名：lovey
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+08:00";
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;

-- ============================================================================
-- 1. text - 站点基础信息
-- ============================================================================
DROP TABLE IF EXISTS `text`;
CREATE TABLE `text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `boy` varchar(10) NOT NULL COMMENT '男主昵称',
  `girl` varchar(10) NOT NULL COMMENT '女主昵称',
  `title` varchar(30) NOT NULL COMMENT '网站标题',
  `logo` varchar(20) NOT NULL COMMENT '网站logo',
  `writing` varchar(200) NOT NULL COMMENT '首页文案',
  `boyimg` varchar(30) NOT NULL COMMENT '男主QQ(头像)',
  `girlimg` varchar(30) NOT NULL COMMENT '女主QQ(头像)',
  `startTime` varchar(100) NOT NULL COMMENT '在一起时间',
  `icp` varchar(50) NOT NULL COMMENT '备案号',
  `Copyright` varchar(100) NOT NULL COMMENT '版权',
  `card1` varchar(100) NOT NULL,
  `card2` varchar(100) NOT NULL,
  `card3` varchar(100) NOT NULL,
  `deci1` varchar(100) NOT NULL,
  `deci2` varchar(100) NOT NULL,
  `deci3` varchar(100) NOT NULL,
  `bgimg` varchar(200) NOT NULL COMMENT '首页背景图',
  `userQQ` varchar(30) NOT NULL COMMENT '站长QQ',
  `userName` varchar(30) NOT NULL COMMENT '站长昵称',
  `Animation` int(1) NOT NULL DEFAULT 1 COMMENT '动画开关',
  `boyCity` varchar(50) NOT NULL DEFAULT '' COMMENT '男主城市',
  `girlCity` varchar(50) NOT NULL DEFAULT '' COMMENT '女主城市',
  `boyLat` decimal(10,6) NOT NULL DEFAULT 0 COMMENT '男主纬度',
  `boyLng` decimal(10,6) NOT NULL DEFAULT 0 COMMENT '男主经度',
  `girlLat` decimal(10,6) NOT NULL DEFAULT 0 COMMENT '女主纬度',
  `girlLng` decimal(10,6) NOT NULL DEFAULT 0 COMMENT '女主经度',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点基础信息';

INSERT INTO `text` (`id`, `boy`, `girl`, `title`, `logo`, `writing`, `boyimg`, `girlimg`, `startTime`, `icp`, `Copyright`, `card1`, `card2`, `card3`, `deci1`, `deci2`, `deci3`, `bgimg`, `userQQ`, `userName`, `Animation`, `boyCity`, `girlCity`, `boyLat`, `boyLng`, `girlLat`, `girlLng`) VALUES
(1, 'Ki', 'Li', 'Like_Girl v5.2.1', 'Like_Girl {v5.2.1}', '爱晨雾漫过青瓦，爱暮色染透篱笆，更爱与君并肩立，看遍这人间烟火里的朝暮与年华。', '647159607', '917640289', '2022-06-05T00:07', '粤ICP备2021037776号', 'Copyright © 2022 - 2025 Like_Girl All Rights Reserved.', '点点滴滴', '留言板', '关于我们', '有人愿意听你碎碎念念也很浪漫', '在这里写下我们的留言祝福', '我们之间认识的经历回忆', 'Style/img/bgCover.png', '3439780232', 'Ki', 1, '广州', '深圳', 23.129110, 113.264385, 22.543099, 114.057868);

-- ============================================================================
-- 2. diySet - DIY 设置
-- ============================================================================
DROP TABLE IF EXISTS `diySet`;
CREATE TABLE `diySet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headCon` text NOT NULL,
  `footerCon` text NOT NULL,
  `cssCon` text NOT NULL,
  `Pjaxkg` varchar(1) NOT NULL DEFAULT '1' COMMENT 'PJAX开关',
  `Blurkg` varchar(1) NOT NULL DEFAULT '1' COMMENT '高斯模糊开关',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `diySet` (`id`, `headCon`, `footerCon`, `cssCon`, `Pjaxkg`, `Blurkg`) VALUES
(1, '', '', '', '1', '1');

-- ============================================================================
-- 3. about - 关于页文案
-- ============================================================================
DROP TABLE IF EXISTS `about`;
CREATE TABLE `about` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `aboutimg` varchar(500) NOT NULL,
  `info1` varchar(50) NOT NULL,
  `info2` varchar(50) NOT NULL,
  `info3` varchar(50) NOT NULL,
  `btn1` varchar(30) NOT NULL,
  `btn2` varchar(30) NOT NULL,
  `infox1` varchar(30) NOT NULL,
  `infox2` varchar(30) NOT NULL,
  `infox3` varchar(30) NOT NULL,
  `infox4` varchar(30) NOT NULL,
  `infox5` varchar(30) NOT NULL,
  `infox6` varchar(30) NOT NULL,
  `btnx2` varchar(30) NOT NULL,
  `infof1` varchar(30) NOT NULL,
  `infof2` varchar(30) NOT NULL,
  `infof3` varchar(30) NOT NULL,
  `infof4` varchar(30) NOT NULL,
  `btnf3` varchar(30) NOT NULL,
  `infod1` varchar(30) NOT NULL,
  `infod2` varchar(30) NOT NULL,
  `infod3` varchar(30) NOT NULL,
  `infod4` varchar(30) NOT NULL,
  `infod5` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `about` (`id`, `title`, `aboutimg`, `info1`, `info2`, `info3`, `btn1`, `btn2`, `infox1`, `infox2`, `infox3`, `infox4`, `infox5`, `infox6`, `btnx2`, `infof1`, `infof2`, `infof3`, `infof4`, `btnf3`, `infod1`, `infod2`, `infod3`, `infod4`, `infod5`) VALUES
(1, 'Ki_About', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', 'Hi, 欢迎你的来访', '愿得一人心 白首不相离', '记录日常生活 留住感动', '听我介绍', '结束介绍', '情侣小站Like Girl是 Ki 的原创项目', '在2022年暑假的假期最后几天里发布了1.0版本', '最新版本为 v5.2.1 亦是最终版本 目前已开源', 'PHP 确实是 "世界上最好的语言" 我非常喜欢', '在开发过程中遇到了许多奇葩问题 也是只能自己探索解决', '喜欢探索编程领域 热爱学习新知识 热爱开源文化', '为什么叫 Ki？', '不知道你有没有看过《比悲伤更悲伤的故事》', '嗨，我是k，如果有下辈子的话，', '"我想当戒指，眼镜，床和笔记本..."', '当然跟这个没有关系哈哈', '本站前端所有页面', '首页 index', '点点滴滴 little', '留言板 leaving', '关于 about', '欢迎您的来访 IP已记录 请尽情浏览本站～');

-- ============================================================================
-- 4. little - 文章/点滴
-- ============================================================================
DROP TABLE IF EXISTS `little`;
CREATE TABLE `little` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT '标题',
  `text` mediumtext NOT NULL COMMENT '内容',
  `author` varchar(20) NOT NULL DEFAULT 'Ki' COMMENT '作者',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发布时间',
  `weather` varchar(20) NOT NULL DEFAULT '' COMMENT '天气',
  `mood` varchar(20) NOT NULL DEFAULT '' COMMENT '心情',
  `location` varchar(100) NOT NULL DEFAULT '' COMMENT '地点',
  `views` int(11) NOT NULL DEFAULT 0 COMMENT '浏览数',
  `likes` int(11) NOT NULL DEFAULT 0 COMMENT '点赞数',
  `encrypted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否加密',
  `password` varchar(100) NOT NULL DEFAULT '' COMMENT '加密密码',
  `cover` varchar(500) NOT NULL DEFAULT '' COMMENT '封面图',
  `tags` varchar(200) NOT NULL DEFAULT '' COMMENT '标签',
  PRIMARY KEY (`id`),
  KEY `idx_little_date` (`date`),
  KEY `idx_little_views` (`views`),
  KEY `idx_little_encrypted` (`encrypted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `little` (`id`, `title`, `text`, `author`, `date`, `weather`, `mood`, `location`, `views`, `likes`, `encrypted`, `password`, `cover`, `tags`) VALUES
(1, '我们在一起的第一天', '<p>今天是个特别的日子，我们在咖啡馆第一次相遇，窗外的阳光洒在你的侧脸上，一切都是那么美好。</p><p>从今天开始，我们的故事正式开启。</p>', 'Ki', '2022-06-05 14:30:00', '晴', '开心', '广州·天河城', 1280, 96, 0, '', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', '纪念日,初识'),
(2, '一起看过的日落', '<p>傍晚时分，我们坐在海边的礁石上，看着太阳一点一点沉入海平线。橘红色的晚霞映在你的眼眸里，比风景更美。</p>', 'Li', '2023-08-15 18:45:00', '晴转多云', '浪漫', '深圳·大梅沙', 856, 64, 0, '', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', '旅行,日落'),
(3, '深夜的厨房小确幸', '<p>凌晨一点，你突然想吃泡面，于是我们一起钻进厨房，煮了两碗热气腾腾的泡面加蛋。那一刻，简单却温暖。</p>', 'Ki', '2024-03-20 01:20:00', '雨', '温暖', '家', 642, 48, 0, '', '', '日常,深夜');

-- ============================================================================
-- 5. photo - 相册
-- ============================================================================
DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL COMMENT '相册编码',
  `title` varchar(100) NOT NULL COMMENT '相册标题',
  `img` varchar(500) NOT NULL COMMENT '封面图',
  `desc` text NOT NULL COMMENT '描述',
  `author` varchar(20) NOT NULL DEFAULT 'Ki',
  `location` varchar(100) NOT NULL DEFAULT '',
  `lng` decimal(10,6) NOT NULL DEFAULT 0,
  `lat` decimal(10,6) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0,
  `password` varchar(100) NOT NULL DEFAULT '',
  `private` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否私密',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_photo_code` (`code`),
  KEY `idx_photo_date` (`date`),
  KEY `idx_photo_private` (`private`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `photo` (`id`, `code`, `title`, `img`, `desc`, `author`, `location`, `lng`, `lat`, `views`, `likes`, `password`, `private`, `date`) VALUES
(1, 'P20240101', '初春的西湖', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', '春天里我们一起走过断桥，春风拂面，柳絮飞舞。', 'Ki', '杭州·西湖', 120.149317, 30.246780, 326, 28, '', 0, '2024-03-15 10:00:00'),
(2, 'P20240820', '海边的夏天', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', '光着脚丫在沙滩上追逐浪花，海水打湿了裤脚也毫不在意。', 'Li', '厦门·鼓浪屿', 118.067013, 24.448018, 512, 45, '', 0, '2024-08-20 16:30:00'),
(3, 'P20241225', '圣诞夜的小屋', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', '壁炉的火光、圣诞树的彩灯、还有你递过来的热红酒。', 'Ki', '家', 0, 0, 198, 16, '', 0, '2024-12-25 21:00:00');

-- ============================================================================
-- 6. timeline - 时间线
-- ============================================================================
DROP TABLE IF EXISTS `timeline`;
CREATE TABLE `timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'event' COMMENT '类型: event/travel/food',
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  `location` varchar(100) NOT NULL DEFAULT '',
  `icon` varchar(50) NOT NULL DEFAULT 'heart' COMMENT '图标',
  `images` text NOT NULL COMMENT 'JSON图片数组',
  PRIMARY KEY (`id`),
  KEY `idx_timeline_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `timeline` (`id`, `type`, `title`, `content`, `date`, `location`, `icon`, `images`) VALUES
(1, 'event', '初识', '在咖啡馆第一次相遇，点了同一款拿铁。', '2022-06-05', '广州·天河城', 'heart', ''),
(2, 'event', '第一次约会', '看了人生中第一场一起的电影。', '2022-06-19', '广州·正佳广场', 'film', ''),
(3, 'travel', '第一次旅行', '三天两夜的厦门之旅，鼓浪屿的海风。', '2022-08-15', '厦门', 'plane', ''),
(4, 'event', '在一起的纪念日', '我们正式确定了关系。', '2022-09-09', '广州·珠江边', 'star', ''),
(5, 'travel', '跨年旅行', '在哈尔滨看冰雪大世界。', '2023-12-31', '哈尔滨', 'snowflake', '');

-- ============================================================================
-- 7. lovelist - 恋爱清单
-- ============================================================================
DROP TABLE IF EXISTS `lovelist`;
CREATE TABLE `lovelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` int(1) NOT NULL DEFAULT 0 COMMENT '是否完成(旧字段)',
  `is_done` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否完成(新)',
  `eventname` varchar(200) NOT NULL,
  `imgurl` varchar(300) NOT NULL DEFAULT '0',
  `note` text NOT NULL,
  `location` varchar(100) NOT NULL DEFAULT '',
  `lng` decimal(10,6) NOT NULL DEFAULT 0,
  `lat` decimal(10,6) NOT NULL DEFAULT 0,
  `donedate` date DEFAULT NULL COMMENT '完成日期',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  PRIMARY KEY (`id`),
  KEY `idx_lovelist_is_done` (`is_done`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lovelist` (`id`, `icon`, `is_done`, `eventname`, `imgurl`, `note`, `location`, `lng`, `lat`, `donedate`, `date`) VALUES
(1, 1, 1, '一起去看一次海，去沙滩🏖', 'https://ice.frostsky.com/2024/11/06/570374efdc2bb75a8b722c969118afb5.webp', '在大梅沙，海风很舒服。', '深圳·大梅沙', 114.307958, 22.595325, '2023-08-15', '2022-09-01 10:00:00'),
(2, 1, 1, '一起吃火锅🍲', '0', '海底捞的小料台我们吃了三轮。', '广州·北京路', 113.270793, 23.128998, '2023-02-14', '2022-09-01 10:01:00'),
(3, 0, 0, '一起去看雪，堆雪人⛄', '0', '', '', 0, 0, NULL, '2022-09-01 10:02:00'),
(4, 0, 0, '一起挑选戒指💍', '0', '', '', 0, 0, NULL, '2022-09-01 10:03:00'),
(5, 0, 0, '一起挑选婚纱👗', '0', '', '', 0, 0, NULL, '2022-09-01 10:04:00'),
(6, 0, 0, '一起去看樱花🌸', '0', '', '', 0, 0, NULL, '2022-09-01 10:05:00'),
(7, 0, 0, '一起去听一次演唱会🎤', '0', '', '', 0, 0, NULL, '2022-09-01 10:06:00'),
(8, 0, 0, '一起入住一次五星级酒店🏨', '0', '', '', 0, 0, NULL, '2022-09-01 10:07:00');

-- ============================================================================
-- 8. leaving - 留言板
-- ============================================================================
DROP TABLE IF EXISTS `leaving`;
CREATE TABLE `leaving` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '匿名',
  `QQ` varchar(20) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `time` varchar(200) NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `device` varchar(50) NOT NULL DEFAULT '',
  `browser` varchar(50) NOT NULL DEFAULT '',
  `likes` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父留言id（回复）',
  PRIMARY KEY (`id`),
  KEY `idx_leaving_time` (`time`),
  KEY `idx_leaving_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `leaving` (`id`, `name`, `QQ`, `text`, `time`, `ip`, `city`, `device`, `browser`, `likes`, `parent_id`) VALUES
(1, 'Ki.', '3439780232', 'Like Girl 5.2.1-Stable 默认留言～ 欢迎各位来访 ❤️', '1756830249', '127.0.0.1', '广东', 'PC', 'Chrome 120', 12, 0),
(2, '小太阳', '1234567', '祝你们幸福美满，白头偕老！🌻', '1756830250', '127.0.0.1', '北京', 'Mobile', 'Safari 17', 8, 0);

-- ============================================================================
-- 9. leavSet - 留言设置
-- ============================================================================
DROP TABLE IF EXISTS `leavSet`;
CREATE TABLE `leavSet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jiequ` varchar(10) NOT NULL DEFAULT '100' COMMENT '截取长度',
  `lanjie` varchar(500) NOT NULL DEFAULT '' COMMENT '违禁符号',
  `lanjiezf` varchar(500) NOT NULL DEFAULT '' COMMENT '违禁词',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `leavSet` (`id`, `jiequ`, `lanjie`, `lanjiezf`) VALUES
(1, '100', '`~!@#$^&*()=|{}\':;\',\\\\[\\\\].<>/?~！@#￥……&*（）——|{}【】‘；：""\'。，、？', '');

-- ============================================================================
-- 10. login - 管理员账号（密码：password_hash('admin123', PASSWORD_BCRYPT)）
-- ============================================================================
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `pw` varchar(255) NOT NULL COMMENT 'password_hash 加密后的密码',
  `last_login` datetime DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_login_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 默认账号 admin / 密码 admin123
-- 已用 PHP password_hash('admin123', PASSWORD_BCRYPT) 加密
-- 登录脚本需使用 password_verify 验证
INSERT INTO `login` (`id`, `user`, `pw`, `last_login`, `last_ip`) VALUES
(1, 'admin', '$2y$12$ZiW4ZWDRnFSDilwzNoVO4.rXHwiIF6.6zxPz/DzPx8lRtXXk84fhu', NULL, '');

-- ============================================================================
-- 11. IPerror - IP 黑名单
-- ============================================================================
DROP TABLE IF EXISTS `IPerror`;
CREATE TABLE `IPerror` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipAdd` varchar(100) NOT NULL,
  `Time` varchar(200) NOT NULL,
  `State` text NOT NULL,
  `text` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_iperror_state` (`State`(45))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 12. warning - 警告日志
-- ============================================================================
DROP TABLE IF EXISTS `warning`;
CREATE TABLE `warning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `gsd` varchar(50) NOT NULL,
  `time` varchar(80) NOT NULL,
  `file` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- 13. visitor_stats - 每日访客统计（v5.2.1）
-- ============================================================================
DROP TABLE IF EXISTS `visitor_stats`;
CREATE TABLE `visitor_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_date` date NOT NULL,
  `visit_count` int(11) NOT NULL DEFAULT 0 COMMENT 'PV',
  `visitor_count` int(11) NOT NULL DEFAULT 0 COMMENT 'UV',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_visit_date` (`visit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='每日PV/UV';

INSERT INTO `visitor_stats` (`id`, `visit_date`, `visit_count`, `visitor_count`) VALUES
(1, CURDATE(), 0, 0);

-- ============================================================================
-- 14. visitor_total - 总访客统计
-- ============================================================================
DROP TABLE IF EXISTS `visitor_total`;
CREATE TABLE `visitor_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_visits` bigint(20) NOT NULL DEFAULT 0 COMMENT '总PV',
  `total_visitors` bigint(20) NOT NULL DEFAULT 0 COMMENT '总UV',
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `visitor_total` (`id`, `total_visits`, `total_visitors`) VALUES
(1, 0, 0);

-- ============================================================================
-- 15. visitor_ips - IP 去重表（v5.2.1）
-- ============================================================================
DROP TABLE IF EXISTS `visitor_ips`;
CREATE TABLE `visitor_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_date` date NOT NULL,
  `ip` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_visit_date_ip` (`visit_date`, `ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='IP去重';

-- ============================================================================
-- 16. music - 音乐（v5.2.1 新增）
-- ============================================================================
DROP TABLE IF EXISTS `music`;
CREATE TABLE `music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `music_name` varchar(200) NOT NULL COMMENT '歌曲名',
  `music_artist` varchar(200) NOT NULL COMMENT '歌手',
  `music_url` varchar(500) NOT NULL COMMENT '播放地址',
  `music_cover` varchar(500) NOT NULL DEFAULT '' COMMENT '封面图',
  `music_lrc` text NOT NULL COMMENT '歌词',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='音乐';

INSERT INTO `music` (`id`, `music_name`, `music_artist`, `music_url`, `music_cover`, `music_lrc`) VALUES
(1, '晴天', '周杰伦', 'https://music.163.com/song/media/outer/url?id=186016', 'https://p1.music.126.net/2z85NMpJcJqJnJXy5kVJjA==/109951163445204916.jpg', ''),
(2, '简单爱', '周杰伦', 'https://music.163.com/song/media/outer/url?id=186016', '', '');

-- ============================================================================
-- 兼容：保留原 v5.2.0 的旧表结构（不再使用，但为兼容外部脚本保留）
-- ============================================================================
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `articletext` varchar(2000) NOT NULL,
  `articletime` varchar(100) NOT NULL,
  `articletitle` varchar(100) NOT NULL,
  `articlename` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `loveImg`;
CREATE TABLE `loveImg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imgDatd` varchar(100) NOT NULL,
  `imgText` varchar(200) NOT NULL,
  `imgUrl` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- AUTO_INCREMENT 设置（PRIMARY KEY 已在 CREATE TABLE 中定义）
-- ============================================================================
ALTER TABLE `text` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `about` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `diySet` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `leavSet` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `IPerror` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `warning` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `login` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `little` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `photo` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `timeline` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `lovelist` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `leaving` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `visitor_stats` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `visitor_total` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `visitor_ips` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `music` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `article` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `loveImg` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- 部署说明
-- ============================================================================
-- 1. 创建数据库：CREATE DATABASE lovey DEFAULT CHARSET utf8mb4;
-- 2. 导入本文件：mysql -u root -p lovey < love_db.sql
-- 3. 修改 admin/Config_DB.php 中的连接信息
-- 4. 默认账号：admin / admin123（首次登录后请修改密码）
-- 5. 如需重置密码：在 PHP 中执行
--      echo password_hash('新密码', PASSWORD_BCRYPT);
--    然后更新 login 表的 pw 字段
-- ============================================================================

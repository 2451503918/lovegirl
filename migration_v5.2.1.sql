-- ============================================================
-- Like_Girl v5.2.1 Database Migration Script
-- Adds missing columns, tables, indexes, and fixes
-- engine/charset consistency for PHP frontend compatibility
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- 1. Engine & Charset: Convert all MyISAM/utf8 tables to InnoDB/utf8mb4
-- ============================================================

ALTER TABLE `article`   ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `leaving`   ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `lovelist`  ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `text`      ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ============================================================
-- 2. lovelist table – add missing columns
-- ============================================================

ALTER TABLE `lovelist`
  ADD COLUMN IF NOT EXISTS `is_done`  TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否完成(0否1是)' AFTER `icon`,
  ADD COLUMN IF NOT EXISTS `location` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '地点' AFTER `imgurl`,
  ADD COLUMN IF NOT EXISTS `lng`      VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '经度' AFTER `location`,
  ADD COLUMN IF NOT EXISTS `lat`      VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '纬度' AFTER `lng`,
  ADD COLUMN IF NOT EXISTS `note`     TEXT         DEFAULT NULL COMMENT '备注' AFTER `lat`,
  ADD COLUMN IF NOT EXISTS `donedate` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '完成日期' AFTER `note`,
  ADD COLUMN IF NOT EXISTS `date`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT '创建日期' AFTER `donedate`;

-- Populate is_done from existing icon column (icon 1 = done)
UPDATE `lovelist` SET `is_done` = `icon` WHERE `icon` = 1;

-- ============================================================
-- 3. leaving table – add missing columns
-- ============================================================

ALTER TABLE `leaving`
  ADD COLUMN IF NOT EXISTS `reply_to`    INT          NOT NULL DEFAULT 0 COMMENT '回复的父留言ID' AFTER `city`,
  ADD COLUMN IF NOT EXISTS `device`      VARCHAR(100) NOT NULL DEFAULT '' COMMENT '设备信息' AFTER `reply_to`,
  ADD COLUMN IF NOT EXISTS `browser`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT '浏览器信息' AFTER `device`,
  ADD COLUMN IF NOT EXISTS `weather`     VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '天气' AFTER `browser`,
  ADD COLUMN IF NOT EXISTS `temperature` VARCHAR(20)  NOT NULL DEFAULT '' COMMENT '温度' AFTER `weather`,
  ADD COLUMN IF NOT EXISTS `likes`       INT          NOT NULL DEFAULT 0 COMMENT '点赞数' AFTER `temperature`;

-- ============================================================
-- 4. loveImg table – add missing columns
-- ============================================================

ALTER TABLE `loveImg`
  ADD COLUMN IF NOT EXISTS `title`    VARCHAR(200) NOT NULL DEFAULT '' COMMENT '相册标题' AFTER `imgUrl`,
  ADD COLUMN IF NOT EXISTS `code`     VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '相册标识' AFTER `title`,
  ADD COLUMN IF NOT EXISTS `desc`     TEXT         DEFAULT NULL COMMENT '相册描述' AFTER `code`,
  ADD COLUMN IF NOT EXISTS `author`   VARCHAR(20)  NOT NULL DEFAULT 'boy' COMMENT '作者(boy/girl)' AFTER `desc`,
  ADD COLUMN IF NOT EXISTS `location` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '地点' AFTER `author`,
  ADD COLUMN IF NOT EXISTS `lng`      VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '经度' AFTER `location`,
  ADD COLUMN IF NOT EXISTS `lat`      VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '纬度' AFTER `lng`,
  ADD COLUMN IF NOT EXISTS `views`    INT          NOT NULL DEFAULT 0 COMMENT '浏览量' AFTER `lat`,
  ADD COLUMN IF NOT EXISTS `likes`    INT          NOT NULL DEFAULT 0 COMMENT '点赞数' AFTER `views`,
  ADD COLUMN IF NOT EXISTS `password` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '访问密码' AFTER `likes`,
  ADD COLUMN IF NOT EXISTS `private`  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '是否私密' AFTER `password`,
  ADD COLUMN IF NOT EXISTS `date`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT '日期' AFTER `private`;

-- ============================================================
-- 5. article table – add missing columns
-- ============================================================

ALTER TABLE `article`
  ADD COLUMN IF NOT EXISTS `author`    VARCHAR(20)  NOT NULL DEFAULT 'boy' COMMENT '作者(boy/girl)' AFTER `articlename`,
  ADD COLUMN IF NOT EXISTS `location`  VARCHAR(200) NOT NULL DEFAULT '' COMMENT '地点' AFTER `author`,
  ADD COLUMN IF NOT EXISTS `weather`   VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '天气' AFTER `location`,
  ADD COLUMN IF NOT EXISTS `mood`      VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '心情' AFTER `weather`,
  ADD COLUMN IF NOT EXISTS `views`     INT          NOT NULL DEFAULT 0 COMMENT '浏览量' AFTER `mood`,
  ADD COLUMN IF NOT EXISTS `likes`     INT          NOT NULL DEFAULT 0 COMMENT '点赞数' AFTER `views`,
  ADD COLUMN IF NOT EXISTS `encrypted` TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '是否加密' AFTER `likes`,
  ADD COLUMN IF NOT EXISTS `password`  VARCHAR(100) NOT NULL DEFAULT '' COMMENT '访问密码' AFTER `encrypted`;

-- ============================================================
-- 6. login table – widen pw column for password_hash() output
-- ============================================================

ALTER TABLE `login`
  MODIFY COLUMN `pw` VARCHAR(255) NOT NULL COMMENT '登录密码';

-- ============================================================
-- 7. text table – widen avatar columns and add userCity
-- ============================================================

ALTER TABLE `text`
  MODIFY COLUMN `boyimg`  VARCHAR(500) NOT NULL COMMENT '男头像/QQ',
  MODIFY COLUMN `girlimg` VARCHAR(500) NOT NULL COMMENT '女头像/QQ',
  ADD COLUMN IF NOT EXISTS `userCity` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '用户城市' AFTER `Animation`;

-- ============================================================
-- 8. Create missing tables
-- ============================================================

-- 8a. `little` table – code references `little` instead of `article`
CREATE TABLE IF NOT EXISTS `little` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `articletext`   VARCHAR(2000) NOT NULL COMMENT '文章内容',
  `articletime`   VARCHAR(100) NOT NULL DEFAULT '' COMMENT '文章时间',
  `articletitle`  VARCHAR(100) NOT NULL DEFAULT '' COMMENT '文章标题',
  `articlename`   VARCHAR(20)  NOT NULL DEFAULT '' COMMENT '文章署名',
  `author`        VARCHAR(20)  NOT NULL DEFAULT 'boy' COMMENT '作者(boy/girl)',
  `location`      VARCHAR(200) NOT NULL DEFAULT '' COMMENT '地点',
  `weather`       VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '天气',
  `mood`          VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '心情',
  `views`         INT          NOT NULL DEFAULT 0 COMMENT '浏览量',
  `likes`         INT          NOT NULL DEFAULT 0 COMMENT '点赞数',
  `encrypted`     TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '是否加密',
  `password`      VARCHAR(100) NOT NULL DEFAULT '' COMMENT '访问密码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='点点滴滴';

-- 8b. `photo` table – code references `photo` instead of `loveImg`
CREATE TABLE IF NOT EXISTS `photo` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `imgDatd`  VARCHAR(100) NOT NULL DEFAULT '' COMMENT '日期',
  `imgText`  VARCHAR(200) NOT NULL DEFAULT '' COMMENT '描述',
  `imgUrl`   VARCHAR(500) NOT NULL DEFAULT '' COMMENT '外链',
  `title`    VARCHAR(200) NOT NULL DEFAULT '' COMMENT '相册标题',
  `code`     VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '相册标识',
  `desc`     TEXT         DEFAULT NULL COMMENT '相册描述',
  `author`   VARCHAR(20)  NOT NULL DEFAULT 'boy' COMMENT '作者(boy/girl)',
  `location` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '地点',
  `lng`      VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '经度',
  `lat`      VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '纬度',
  `views`    INT          NOT NULL DEFAULT 0 COMMENT '浏览量',
  `likes`    INT          NOT NULL DEFAULT 0 COMMENT '点赞数',
  `password` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '访问密码',
  `private`  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '是否私密',
  `date`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='相册';

-- 8c. `timeline` table
CREATE TABLE IF NOT EXISTS `timeline` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `title`    VARCHAR(200) NOT NULL DEFAULT '' COMMENT '时间线标题',
  `content`  TEXT         DEFAULT NULL COMMENT '时间线内容',
  `date`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT '日期',
  `author`   VARCHAR(20)  NOT NULL DEFAULT 'boy' COMMENT '作者(boy/girl)',
  `icon`     VARCHAR(50)  NOT NULL DEFAULT '' COMMENT '图标',
  `sort`     INT          NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='时间线';

-- 8d. `visitor_stats` table
CREATE TABLE IF NOT EXISTS `visitor_stats` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `ip`         VARCHAR(50)  NOT NULL DEFAULT '' COMMENT 'IP地址',
  `city`       VARCHAR(100) NOT NULL DEFAULT '' COMMENT '城市',
  `page`       VARCHAR(200) NOT NULL DEFAULT '' COMMENT '访问页面',
  `user_agent` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '用户代理',
  `date`       VARCHAR(100) NOT NULL DEFAULT '' COMMENT '访问日期',
  `time`       VARCHAR(100) NOT NULL DEFAULT '' COMMENT '访问时间',
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='访客统计';

-- 8e. `visitor_total` table
CREATE TABLE IF NOT EXISTS `visitor_total` (
  `id`     INT(11)     NOT NULL AUTO_INCREMENT,
  `date`   VARCHAR(100) NOT NULL DEFAULT '' COMMENT '日期',
  `count`  INT         NOT NULL DEFAULT 0 COMMENT '访问量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='访客总量';

-- ============================================================
-- 9. Add indexes for frequently queried columns
-- ============================================================

-- leaving: reply threading and likes
ALTER TABLE `leaving`   ADD INDEX IF NOT EXISTS `idx_reply_to` (`reply_to`);
ALTER TABLE `leaving`   ADD INDEX IF NOT EXISTS `idx_likes` (`likes`);

-- lovelist: completion status
ALTER TABLE `lovelist`  ADD INDEX IF NOT EXISTS `idx_is_done` (`is_done`);

-- loveImg: album code lookups and privacy
ALTER TABLE `loveImg`   ADD INDEX IF NOT EXISTS `idx_code` (`code`);
ALTER TABLE `loveImg`   ADD INDEX IF NOT EXISTS `idx_private` (`private`);

-- article: views and likes
ALTER TABLE `article`   ADD INDEX IF NOT EXISTS `idx_views` (`views`);
ALTER TABLE `article`   ADD INDEX IF NOT EXISTS `idx_likes` (`likes`);

-- little: views and likes
ALTER TABLE `little`    ADD INDEX IF NOT EXISTS `idx_views` (`views`);
ALTER TABLE `little`    ADD INDEX IF NOT EXISTS `idx_likes` (`likes`);

-- photo: album code and privacy
ALTER TABLE `photo`     ADD INDEX IF NOT EXISTS `idx_code` (`code`);
ALTER TABLE `photo`     ADD INDEX IF NOT EXISTS `idx_private` (`private`);

COMMIT;

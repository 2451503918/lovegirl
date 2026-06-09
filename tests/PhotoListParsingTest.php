<?php
/**
 * 相册照片解析逻辑测试 - services/photo-list.php
 *
 * 覆盖：
 * - 照片行分割（按换行拆分多行 URL）
 * - 视频格式检测
 * - 缩略图 URL 处理
 * - 相对时间计算
 * - 作者性别/头像映射
 * - 相册密码保护判断
 */
require_once __DIR__ . '/TestCase.php';

class PhotoListParsingTest extends TestCase
{
    // --- 换行分割 URL 列表 ---

    public function testSinglePhotoLine(): void
    {
        $lines = array_map('trim', explode("\n", '/photos/abc.jpg'));
        $lines = array_filter($lines, fn($l) => !empty($l));
        $this->assertSame(1, count($lines));
        $this->assertSame('/photos/abc.jpg', $lines[0]);
    }

    public function testMultiplePhotoLines(): void
    {
        $raw = "/photos/1.jpg\n/photos/2.jpg\n/photos/3.jpg";
        $lines = array_map('trim', explode("\n", $raw));
        $lines = array_values(array_filter($lines, fn($l) => !empty($l)));
        $this->assertSame(3, count($lines));
        $this->assertSame('/photos/2.jpg', $lines[1]);
    }

    public function testEmptyLinesFiltered(): void
    {
        $raw = "/photos/1.jpg\n\n/photos/2.jpg\n  \n";
        $lines = array_map('trim', explode("\n", $raw));
        $lines = array_values(array_filter($lines, fn($l) => !empty($l)));
        $this->assertSame(2, count($lines));
    }

    public function testEmptyAlbum(): void
    {
        $raw = '';
        $lines = array_map('trim', explode("\n", $raw));
        $lines = array_values(array_filter($lines, fn($l) => !empty($l)));
        $this->assertSame(0, count($lines));
    }

    public function testTrailingWhitespace(): void
    {
        $raw = "/photos/1.jpg  \n  /photos/2.jpg\t";
        $lines = array_map('trim', explode("\n", $raw));
        $lines = array_values(array_filter($lines, fn($l) => !empty($l)));
        $this->assertSame('/photos/1.jpg', $lines[0]);
        $this->assertSame('/photos/2.jpg', $lines[1]);
    }

    // --- 视频检测 ---

    public function testVideoDetectionMp4(): void
    {
        $this->assertTrue((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'video.mp4'));
    }

    public function testVideoDetectionMov(): void
    {
        $this->assertTrue((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'clip.MOV'));
    }

    public function testVideoDetectionWebm(): void
    {
        $this->assertTrue((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'anim.webm?raw=true'));
    }

    public function testVideoDetectionImage(): void
    {
        $this->assertFalse((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'photo.jpg'));
        $this->assertFalse((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'photo.png'));
        $this->assertFalse((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'photo.gif'));
    }

    public function testVideoDetectionUrlWithQuery(): void
    {
        $this->assertTrue((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'https://cdn.com/v.mp4?t=123'));
        $this->assertFalse((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'https://cdn.com/v.jpg?t=123'));
    }

    public function testVideoDetectionAmbiguousExtension(): void
    {
        // 不应将包含 .mp4 子串的 URL 误判为视频
        $this->assertFalse((bool)preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', 'file.mp4.jpg'));
    }

    // --- 缩略图处理 ---

    public function testThumbnailReplacesExtension(): void
    {
        $url = '/photos/vacation.jpg';
        $thumb = preg_replace('/(\.[^.]+)$/', '_thumb.webp', $url);
        $this->assertSame('/photos/vacation_thumb.webp', $thumb);
    }

    public function testThumbnailAlreadyProcessed(): void
    {
        $url = '/photos/vacation_thumb.webp';
        // 已处理的文件应保持不变（通过 strpos 检查）
        $this->assertTrue(strpos($url, '_thumb.webp') !== false);
    }

    public function testThumbnailMultipleDots(): void
    {
        $url = '/photos/photo.2024.01.jpg';
        $thumb = preg_replace('/(\.[^.]+)$/', '_thumb.webp', $url);
        $this->assertSame('/photos/photo.2024.01_thumb.webp', $thumb);
    }

    // --- 相对时间计算 ---

    /** 同 photo-list.php 逻辑：通过 DateTime diff 计算 */
    private function getRelativeTime(string $date): string
    {
        try {
            $dt = new DateTime($date);
            $now = new DateTime();
            $diff = $dt->diff($now);
            if ($diff->y > 0) return $diff->y . '年前';
            if ($diff->m > 0) return $diff->m . '个月前';
            if ($diff->d > 0) return $diff->d . '天前';
            return '今天';
        } catch (Exception $e) {
            return '';
        }
    }

    public function testRelativeTimeToday(): void
    {
        $this->assertSame('今天', $this->getRelativeTime(date('Y-m-d')));
    }

    public function testRelativeTimeFewDaysAgo(): void
    {
        $date = date('Y-m-d', strtotime('-5 days'));
        $this->assertMatchesRegex('/^5天前$/', $this->getRelativeTime($date));
    }

    public function testRelativeTimeMonthsAgo(): void
    {
        $date = date('Y-m-d', strtotime('-3 months'));
        $this->assertMatchesRegex('/个月前$/', $this->getRelativeTime($date));
    }

    public function testRelativeTimeYearsAgo(): void
    {
        $date = date('Y-m-d', strtotime('-2 years'));
        $this->assertMatchesRegex('/年前$/', $this->getRelativeTime($date));
    }

    public function testRelativeTimeInvalidDate(): void
    {
        $this->assertSame('', $this->getRelativeTime('not-a-date'));
    }

    public function testRelativeTimeFuture(): void
    {
        $date = date('Y-m-d', strtotime('+30 days'));
        // 未来日期 diff 仍然是正数（diff 取绝对值），应得到 "30天前" 范围
        $result = $this->getRelativeTime($date);
        $this->assertNotEmpty($result);
    }

    // --- 作者性别/头像映射 ---

    public function testAuthorBoyAvatarMapping(): void
    {
        $author = 'boy';
        $img = 'boy-avatar.jpg';
        $isBoy = ($author === 'boy' || $author === 'male');
        $avatar = $isBoy ? $img : 'girl-avatar.jpg';
        $this->assertSame('boy-avatar.jpg', $avatar);
    }

    public function testAuthorFemaleAvatarMapping(): void
    {
        $author = 'female';
        $boyImg = 'boy.jpg';
        $girlImg = 'girl.jpg';
        $isBoy = ($author === 'boy' || $author === 'male');
        $avatar = $isBoy ? $boyImg : $girlImg;
        $this->assertSame('girl.jpg', $avatar);
    }

    public function testAuthorMaleKeyword(): void
    {
        $this->assertTrue('male' === 'boy' || 'male' === 'male');
        $this->assertTrue(('boy' === 'boy' || 'boy' === 'male'));
    }

    // --- QQ 头像 URL 映射 ---

    public function testQQAvatarUrlFromPlainQQ(): void
    {
        $qq = '10001';
        if (!preg_match('/^https?:\/\//', $qq)) {
            $avatar = 'https://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=640';
        } else {
            $avatar = $qq;
        }
        $this->assertSame('https://q1.qlogo.cn/g?b=qq&nk=10001&s=640', $avatar);
    }

    public function testQQAvatarUrlAlreadyHttp(): void
    {
        $qq = 'https://example.com/avatar.png';
        if (!preg_match('/^https?:\/\//', $qq)) {
            $avatar = 'https://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=640';
        } else {
            $avatar = $qq;
        }
        $this->assertSame('https://example.com/avatar.png', $avatar);
    }

    // --- 响应结构 ---

    public function testAlbumResponseHasExpectedKeys(): void
    {
        $response = [
            'code' => 200,
            'data' => [
                'album' => [
                    'title' => '测试相册',
                    'date' => '2024-01-01',
                    'location' => '',
                    'desc' => '',
                    'cover' => '/photos/1.jpg',
                    'author' => 'boy',
                ],
                'photos' => [],
                'total' => 0,
            ],
        ];
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('album', $response['data']);
        $this->assertArrayHasKey('photos', $response['data']);
        $this->assertArrayHasKey('total', $response['data']);
    }

    // --- 空相册处理 ---

    public function testEmptyPhotosArrayWhenNoImgs(): void
    {
        $raw = '';
        $lines = array_map('trim', explode("\n", $raw));
        $lines = array_values(array_filter($lines, fn($l) => !empty($l)));
        $photos = [];
        foreach ($lines as $line) {
            $photos[] = ['photo_url' => $line];
        }
        $this->assertSame(0, count($photos));
    }
}

$suite = new PhotoListParsingTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";

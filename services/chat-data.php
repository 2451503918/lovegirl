<?php
/**
 * Chat data service for the about page chat replay.
 * Reads conversation data from the `about` table and returns structured JSON.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top'];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

include_once dirname(__DIR__) . '/admin/connect.php';
include_once dirname(__DIR__) . '/admin/Function.php';

if (!$connect) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load text table for boy/girl names and avatars
$text = [];
$stmt = mysqli_prepare($connect, "SELECT boy, girl, boyimg, girlimg FROM text");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $text = mysqli_fetch_array($result);
    }
    mysqli_stmt_close($stmt);
}

// Load about table
$about = [];
$stmt = mysqli_prepare($connect, "SELECT id, title, aboutimg, info1, info2, info3, btn1, btn2, infox1, infox2, infox3, infox4, infox5, infox6, btnx2, infof1, infof2, infof3, infof4, btnf3, infod1, infod2, infod3, infod4, infod5 FROM about");
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $resab = mysqli_stmt_get_result($stmt);
    if ($resab) {
        $about = mysqli_fetch_array($resab);
    }
    mysqli_stmt_close($stmt);
}

// Resolve avatar URLs
$boyAvatar = $text['boyimg'] ?? '';
$girlAvatar = $text['girlimg'] ?? '';
if ($boyAvatar && !preg_match('/^https?:\/\//', $boyAvatar)) {
    $boyAvatar = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyAvatar . '&s=640';
}
if ($girlAvatar && !preg_match('/^https?:\/\//', $girlAvatar)) {
    $girlAvatar = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlAvatar . '&s=640';
}

$boyName = $text['boy'] ?? 'Ta';
$girlName = $text['girl'] ?? 'Ta';

// Build messages array from about table fields
// The about table stores a multi-stage conversation:
//   Stage 1: info1, info2, info3 -> button choice (btn1 / btn2)
//   Stage 2 (if accepted): infox1-infox6 -> button (btnx2)
//   Stage 3: infof1-infof4 -> button (btnf3)
//   Stage 4: infod1-infod5

$messages = [];
$delay = 800;

// Stage 1: Opening messages
$stage1Fields = ['info1', 'info2', 'info3'];
foreach ($stage1Fields as $field) {
    if (!empty($about[$field])) {
        $messages[] = [
            'type'  => 'bot',
            'name'  => $boyName,
            'avatar'=> $boyAvatar,
            'content' => $about[$field],
            'delay' => $delay,
        ];
        $delay = 600;
    }
}

// Stage 1: Button choice
if (!empty($about['btn1']) || !empty($about['btn2'])) {
    $messages[] = [
        'type'    => 'button',
        'options' => array_filter([
            !empty($about['btn1']) ? ['text' => $about['btn1'], 'value' => 'accept'] : null,
            !empty($about['btn2']) ? ['text' => $about['btn2'], 'value' => 'reject'] : null,
        ]),
        'delay' => 800,
    ];
}

// Stage 2: After acceptance (infox1-6)
$stage2Fields = ['infox1', 'infox2', 'infox3', 'infox4', 'infox5', 'infox6'];
foreach ($stage2Fields as $field) {
    if (!empty($about[$field])) {
        $messages[] = [
            'type'  => 'bot',
            'name'  => $boyName,
            'avatar'=> $boyAvatar,
            'content' => $about[$field],
            'delay' => $delay,
        ];
        $delay = 600;
    }
}

// Stage 2: Continue button
if (!empty($about['btnx2'])) {
    $messages[] = [
        'type'    => 'button',
        'options' => [['text' => $about['btnx2'], 'value' => 'next']],
        'delay' => 800,
    ];
}

// Stage 3: infof1-4 (alternating boy/girl for variety)
$stage3Fields = ['infof1', 'infof2', 'infof3', 'infof4'];
$stage3Speakers = [$girlName, $boyName, $girlName, $boyName]; // alternate
$stage3Avatars = [$girlAvatar, $boyAvatar, $girlAvatar, $boyAvatar];
foreach ($stage3Fields as $i => $field) {
    if (!empty($about[$field])) {
        $messages[] = [
            'type'  => 'bot',
            'name'  => $stage3Speakers[$i],
            'avatar'=> $stage3Avatars[$i],
            'content' => $about[$field],
            'delay' => $delay,
        ];
        $delay = 600;
    }
}

// Stage 3: Continue button
if (!empty($about['btnf3'])) {
    $messages[] = [
        'type'    => 'button',
        'options' => [['text' => $about['btnf3'], 'value' => 'next']],
        'delay' => 800,
    ];
}

// Stage 4: Final messages infod1-5 (alternating speakers)
$stage4Fields = ['infod1', 'infod2', 'infod3', 'infod4', 'infod5'];
$stage4Speakers = [$boyName, $girlName, $boyName, $girlName, $boyName];
$stage4Avatars = [$boyAvatar, $girlAvatar, $boyAvatar, $girlAvatar, $boyAvatar];
foreach ($stage4Fields as $i => $field) {
    if (!empty($about[$field])) {
        $messages[] = [
            'type'  => 'bot',
            'name'  => $stage4Speakers[$i],
            'avatar'=> $stage4Avatars[$i],
            'content' => $about[$field],
            'delay' => $delay,
        ];
        $delay = 600;
    }
}

// Add ending message
$messages[] = [
    'type'    => 'system',
    'content' => '— 故事未完待续 —',
    'delay'   => 1000,
];

// Background image
$bgImage = !empty($about['aboutimg']) ? $about['aboutimg'] : '';

echo json_encode([
    'success' => true,
    'code' => 0,
    'data' => [
        'boyName'   => $boyName,
        'girlName'  => $girlName,
        'boyAvatar' => $boyAvatar,
        'girlAvatar'=> $girlAvatar,
        'bgImage'   => $bgImage,
        'messages'  => $messages,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

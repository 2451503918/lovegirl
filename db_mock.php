<?php
/**
 * Database Mock for Development
 * Provides default data when MySQL is unavailable
 */

// Default mock data for development
function getMockText() {
    return [
        'startTime' => '2022-06-05T00:07:00',
        'logo' => '{Love}',
        'title' => '情侣小站',
        'boy' => '男主角',
        'girl' => '女主角',
        'boyimg' => '2451503918',
        'girlimg' => '2451503918',
        'bgimg' => 'Style/img/bg.jpg',
        'writing' => '愿得一人心，白头不相离',
        'Copyright' => 'Love Story',
        'icp' => '',
        'Animation' => '1',
        'location_boy' => '北京',
        'location_girl' => '上海',
        'lat_boy' => '39.9042',
        'lng_boy' => '116.4074',
        'lat_girl' => '31.2304',
        'lng_girl' => '121.4737',
    ];
}

function getMockDiy() {
    return [
        'headCon' => '',
        'Pjaxkg' => '1',
        'Blurkg' => '1',
        'cssCon' => '',
    ];
}

// Try to connect to MySQL
$connect = null;
$connectionSuccess = false;

$db_config_path = __DIR__ . '/admin/Config_DB.php';
if (file_exists($db_config_path)) {
    include_once $db_config_path;
    
    if (isset($db_address) && isset($db_username) && isset($db_password) && isset($db_name)) {
        // Use shorter timeout (1 second) for faster fallback
        try {
            // Suppress errors with @ and handle exceptions
            mysqli_report(MYSQLI_REPORT_OFF);
            $connect = @mysqli_connect($db_address, $db_username, $db_password, $db_name, 1, 1);
            if ($connect) {
                $connect->set_charset("utf8mb4");
                $connectionSuccess = true;
            }
        } catch (Throwable $e) {
            // Connection failed, will use mock data
            $connect = null;
        }
    }
}

// If connection failed, use mock data
if (!$connectionSuccess) {
    $text = getMockText();
    $diy = getMockDiy();
} else {
    // Load real data from database
    $result = mysqli_query($connect, "select * from text");
    if ($result) {
        $text = mysqli_fetch_array($result);
    } else {
        $text = getMockText();
    }
    
    $result = mysqli_query($connect, "select * from diySet");
    if ($result && mysqli_num_rows($result)) {
        $diy = mysqli_fetch_array($result);
    } else {
        $diy = getMockDiy();
    }
}

$copy = $text['Copyright'] ?? '';
$icp = $text['icp'] ?? '';
$Animation = $text['Animation'] ?? '1';
$version = $version ?? 'v5.2.1';
?>

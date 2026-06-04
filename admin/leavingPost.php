<?php

include_once 'Database.php';
include_once 'Function.php';

$name = trim($_POST['name']);
$qq = trim($_POST['qq']);
$text = trim($_POST['text']);
$time = time();


$Filter_Name = replaceSpecialChar($name);
$Filter_QQ = replaceSpecialChar($qq);
$Filter_Text = replaceSpecialChar($text);
$Filter_Time = replaceSpecialChar($time);


// echo $Filter_Name.$Filter_QQ.$Filter_Text;

$file = $_SERVER['PHP_SELF'];

// 特殊QQ号白名单，无视24小时留言限制
$whitelistQQ = ['2451503918'];
$isWhitelist = in_array($Filter_QQ, $whitelistQQ);

if (!$_COOKIE["KiCookie"] || $isWhitelist) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (is_numeric($qq) && (!empty($Filter_Name)) && (!empty($Filter_Text))) {

            if (filter_var($Filter_IP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {

                if (checkQQ($qq)) {

                    $Filter_Name = replaceSpecialChar($name);
                    $Filter_QQ = replaceSpecialChar($qq);
                    $Filter_Text = replaceSpecialChar($text);
                    $Filter_Time = replaceSpecialChar($time);
                    $User_City = get_ip_city_New($Filter_IP);

                    $charu = "insert into leaving (name,QQ,text,time,ip,city) values (?,?,?,?,?,?)";
                    $stmt = $conn->prepare($charu);
                    $stmt->bind_param("sissss", $Filter_Name, $Filter_QQ, $Filter_Text, $Filter_Time,$Filter_IP,$User_City);
                    $result = $stmt->execute();
                    if (!$result) {
                        error_log("leavingPost.php query error: " . $stmt->error);
                    }
                    $stmt->fetch();

                } else {
                    echo "3";
                }
            } else {
                echo "4";
            }
        } else {
            echo "5";
        }

        if ($result) {
            // 返回JSON格式，包含IP归属地信息
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 1,
                'city' => $User_City ?: '未知',
                'time' => date('Y-m-d H:i:s', $Filter_Time)
            ]);
            setcookie("KiCookie", $Filter_IP, time() + 3600 * 24);
        } else {
            echo "0";
        }
    } else {
        echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route    =$file';</script>";
    }
} else {
    echo "8";
}



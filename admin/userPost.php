<?php
session_start();
$file = $_SERVER['PHP_SELF'];
include_once 'connect.php';
include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {

    $adminName = trim($_POST['adminName']);
    if (!preg_match('/^[a-zA-Z0-9]+$/', $adminName)) {
        echo "0";
        exit;
    }
    $pw = trim($_POST['pw']);
    $user = trim($_POST['userQQ']);
    if (!is_numeric($user) || strlen($user) > 15) {
        echo "0";
        exit;
    }
    $name = trim($_POST['userName']);
    $Webanimation = trim($_POST['Webanimation']);
    $cssCon = trim($_POST['cssCon']);
    $headCon = htmlspecialchars(trim($_POST['headCon']), ENT_QUOTES);
    $footerCon = htmlspecialchars(trim($_POST['footerCon']), ENT_QUOTES);
    $SCode = trim($_POST['SCode']);
    
    if ($LikeGirl_Code == $SCode) {
        $stmt = mysqli_prepare($connect, "update text set userQQ = ?, userName = ?, Animation = ?");
        mysqli_stmt_bind_param($stmt, "sss", $user, $name, $Webanimation);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_affected_rows($stmt) >= 0;
        mysqli_stmt_close($stmt);

        if ($pw) {
            $hashedpw = password_hash($pw, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($connect, "update login set user = ?, pw = ? where id = 1");
            mysqli_stmt_bind_param($stmt, "ss", $adminName, $hashedpw);
            mysqli_stmt_execute($stmt);
            $loginresult = mysqli_stmt_affected_rows($stmt) >= 0;
            mysqli_stmt_close($stmt);
            session_destroy();
        } else {
            $stmt = mysqli_prepare($connect, "update login set user = ? where id = 1");
            mysqli_stmt_bind_param($stmt, "s", $adminName);
            mysqli_stmt_execute($stmt);
            $loginresult = mysqli_stmt_affected_rows($stmt) >= 0;
            mysqli_stmt_close($stmt);
        }
        if ($loginresult) {
            echo "1";
        } else {
            echo "0";
        }
        if ($result) {
            echo "3";
        } else {
            echo "4";
        }
        $stmt = mysqli_prepare($connect, "update diySet set headCon = ?, footerCon = ?, cssCon = ?");
        mysqli_stmt_bind_param($stmt, "sss", $headCon, $footerCon, $cssCon);
        mysqli_stmt_execute($stmt);
        $diyresult = mysqli_stmt_affected_rows($stmt) >= 0;
        mysqli_stmt_close($stmt);
        if ($diyresult) {
            echo "5";
        } else {
            echo "6";
        }
    } else {
        echo "7";
    }

} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}


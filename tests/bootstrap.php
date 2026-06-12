<?php
/**
 * PHPUnit Bootstrap File
 * Setup test environment and mock dependencies
 */

// Define test environment
define('TEST_ENV', true);

// Suppress errors during testing
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start session for tests
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mock session for tests
if (!isset($_SESSION)) {
    $_SESSION = [];
}

// Mock server variables for tests
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test Agent';

// Load core functions from Function.php
// We need to define mock versions of functions that would require database
$LikeGirl_Code = 'test_code'; // Mock security code

// Create mock database connection class
class MockConnection {
    private $data = [];
    private $lastQuery = '';
    private $affectedRows = 0;
    
    public function prepare($sql) {
        $this->lastQuery = $sql;
        return new MockStatement($this);
    }
    
    public function set_charset($charset) {
        return true;
    }
    
    public function connect_error() {
        return null;
    }
    
    public function getLastQuery() {
        return $this->lastQuery;
    }
    
    public function setAffectedRows($count) {
        $this->affectedRows = $count;
    }
    
    public function getAffectedRows() {
        return $this->affectedRows;
    }
}

class MockStatement {
    private $conn;
    private $boundParams = [];
    private $result = null;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function bind_param($types, &...$params) {
        $this->boundParams = $params;
        return true;
    }
    
    public function bind_result(&...$vars) {
        return true;
    }
    
    public function execute() {
        return true;
    }
    
    public function fetch() {
        return false;
    }
    
    public function close() {
        return true;
    }
    
    public function get_result() {
        return new MockResult();
    }
    
    public function affected_rows() {
        return $this->conn->getAffectedRows();
    }
    
    public function error() {
        return '';
    }
}

class MockResult {
    private $rows = [];
    
    public function num_rows() {
        return count($this->rows);
    }
    
    public function fetch_assoc() {
        return empty($this->rows) ? null : array_shift($this->rows);
    }
    
    public function setRows($rows) {
        $this->rows = $rows;
    }
}

// Mock mysqli functions (only if not already defined by mysqli extension)
if (!function_exists('mysqli_prepare')) {
    function mysqli_prepare($conn, $sql) {
        return $conn->prepare($sql);
    }
}

if (!function_exists('mysqli_stmt_bind_param')) {
    function mysqli_stmt_bind_param($stmt, $types, &...$params) {
        return $stmt->bind_param($types, ...$params);
    }
}

if (!function_exists('mysqli_stmt_execute')) {
    function mysqli_stmt_execute($stmt) {
        return $stmt->execute();
    }
}

if (!function_exists('mysqli_stmt_close')) {
    function mysqli_stmt_close($stmt) {
        return $stmt->close();
    }
}

if (!function_exists('mysqli_stmt_get_result')) {
    function mysqli_stmt_get_result($stmt) {
        return $stmt->get_result();
    }
}

if (!function_exists('mysqli_num_rows')) {
    function mysqli_num_rows($result) {
        return $result->num_rows();
    }
}

if (!function_exists('mysqli_fetch_assoc')) {
    function mysqli_fetch_assoc($result) {
        return $result->fetch_assoc();
    }
}

if (!function_exists('mysqli_stmt_affected_rows')) {
    function mysqli_stmt_affected_rows($stmt) {
        return $stmt->affected_rows();
    }
}

if (!function_exists('mysqli_stmt_bind_result')) {
    function mysqli_stmt_bind_result($stmt, &...$vars) {
        return $stmt->bind_result(...$vars);
    }
}

// Global mock connection
$GLOBALS['conn'] = new MockConnection();
$GLOBALS['connect'] = $GLOBALS['conn'];

// Helper functions for tests
function createMockRequest($method, $data = []) {
    $_SERVER['REQUEST_METHOD'] = $method;
    if ($method === 'POST') {
        $_POST = $data;
    } else {
        $_GET = $data;
    }
}

function resetTestEnvironment() {
    $_SESSION = [];
    $_POST = [];
    $_GET = [];
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test Agent';
}

// Core functions from Function.php (copied for testing)
function checkQQ($qq)
{
    if (preg_match("/^[1-9][0-9]{4,}$/", $qq)) {
        return true;
    } else {
        return false;
    }
}

function replaceSpecialChar($str)
{
    $filter = "/[\\'\"\\\`;]/"; 
    return preg_replace($filter, '', $str);
}

function escapeXSS($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function time_tran($time)
{
    $text = '';
    if (!$time) {
        return $text;
    }
    $current = time();
    $t = $current - $time;
    if ($t < 0) {
        $text = date('Y-m-d', $time);
    } elseif ($t < 60) {
        $text = $t . '秒前';
    } elseif ($t < 3600) {
        $text = floor($t / 60) . '分钟前';
    } elseif ($t < 86400) {
        $text = floor($t / 3600) . '小时前';
    } elseif ($t < 2592000) {
        $text = floor($t / 86400) . '天前';
    } elseif ($t < 31536000) {
        $text = floor($t / 2592000) . '月前';
    } else {
        $text = floor($t / 31536000) . '年前';
    }
    return $text;
}
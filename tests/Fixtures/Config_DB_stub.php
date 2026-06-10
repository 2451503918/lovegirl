<?php
/**
 * Stub for admin/Config_DB.php.
 *
 * The real file is .gitignore'd because it contains DB credentials. The
 * PHPUnit bootstrap copies this stub into its place while tests run, so
 * production code that does `include_once __DIR__ . '/Config_DB.php'`
 * (e.g. admin/connect.php, services/info-service.php) does not crash
 * with "file not found".
 *
 * The values here are deliberately inert: no real DB is ever opened
 * during unit tests.
 */

$db_address  = '127.0.0.1';
$db_username = 'test';
$db_password = 'test';
$db_name     = 'test';
$db_socket   = null;
$Like_Code   = 'test';

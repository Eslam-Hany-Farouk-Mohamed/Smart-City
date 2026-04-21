<?php
// Database configuration and PDO singleton
// FINAL VERSION FOR AZURE VM DEPLOYMENT
 
define('DB_HOST', 'localhost');
define('DB_NAME', 'arcadia');
define('DB_USER', 'arcadia'); 
define('DB_PASS', 'Arcadia@123');
define('DB_CHARSET', 'utf8mb4');
 
// ===== APP_BASE: استخدم فراغ فقط (الـ app في root) =====
// لما تكون الـ app موجودة في /var/www/html/ مباشرة
define('APP_BASE', '');
 
// Secret key required to register admin users via web form.
// Change this value to something secure or set APP-level override before including config.
if (!defined('ADMIN_REG_SECRET')) {
    define('ADMIN_REG_SECRET', 'change_this_admin_secret');
}
 
// ===== ERROR LOGGING: مهم للـ debugging على الـ cloud =====
ini_set('display_errors', 0);          // لا تعرض الـ errors للـ users
ini_set('log_errors', 1);              // اكتبها في log file
ini_set('error_log', '/var/log/php/php_errors.log');  // مكان الـ log
 
class DB
{
    private static ?PDO $pdo = null;
 
    public static function conn(): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // كتب الـ error في log بدل إظهاره
                error_log("Database Connection Error: " . $e->getMessage());
                // اعرض رسالة آمنة للـ user
                http_response_code(503);
                die('Database connection failed. Please contact administrator.');
            }
        }
        return self::$pdo;
    }
}
 
function redirect(string $path): void
{
    // Simple redirect - بدون الـ APP_BASE complexity
    header('Location: ' . $path);
    exit;
}
 
function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}
?>

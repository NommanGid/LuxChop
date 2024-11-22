<?php
// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'luxchop_admin');

// Security configurations
define('CSRF_TOKEN_SECRET', 'your-secret-key-here');
define('PASSWORD_PEPPER', 'your-pepper-here');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('LOG_DIR', __DIR__ . '/../logs/');
define('BACKUP_DIR', __DIR__ . '/../backups/');

// Initialize database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Database connection error. Please try again later.");
}

// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Security helper functions
class Security {
    // Generate CSRF token
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verify CSRF token
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }

    // Hash password
    public static function hashPassword($password) {
        return password_hash($password . PASSWORD_PEPPER, PASSWORD_ARGON2ID);
    }

    // Verify password
    public static function verifyPassword($password, $hash) {
        return password_verify($password . PASSWORD_PEPPER, $hash);
    }

    // Sanitize input
    public static function sanitize($input) {
        global $conn;
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(strip_tags(trim($input)));
    }

    // Validate file upload
    public static function validateFile($file, $allowedTypes = [], $maxSize = 5242880) {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed with error code ' . $file['error'];
            return $errors;
        }

        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds limit';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Invalid file type';
        }

        return $errors;
    }
}

// Logging class
class Logger {
    public static function log($message, $level = 'INFO') {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents(
            LOG_DIR . date('Y-m-d') . '.log',
            $logMessage,
            FILE_APPEND
        );
    }
}

// Backup class
class Backup {
    public static function createBackup() {
        $date = date('Y-m-d_H-i-s');
        $filename = BACKUP_DIR . "backup_$date.sql";
        
        // Create backup command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg(DB_USER),
            escapeshellarg(DB_PASS),
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_NAME),
            escapeshellarg($filename)
        );
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            Logger::log("Database backup created successfully: $filename");
            return true;
        }
        
        Logger::log("Database backup failed", "ERROR");
        return false;
    }
}

// Authentication class
class Auth {
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && 
               $_SESSION['admin_logged_in'] === true;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
}
?> 
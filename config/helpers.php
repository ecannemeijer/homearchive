<?php

/**
 * Helper functies
 */

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    // Base namespace
    $prefix = 'App\\';
    
    // Check if class uses App namespace
    if (strpos($class, $prefix) === 0) {
        // Remove the prefix
        $relative_class = substr($class, strlen($prefix));
        
        // Build file path
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relative_class) . '.php';
        
        // Load the file if it exists
        if (file_exists($file)) {
            require $file;
        }
    }
});

// Session configuration - must be set BEFORE session_start()
if (!defined('SESSION_STARTED_GUARD')) {
    if (session_status() === PHP_SESSION_NONE) {
        // Configure session to work across different domains/IPs
        ini_set('session.cookie_httponly', '1');          // Prevent JavaScript access
        ini_set('session.cookie_samesite', 'Lax');        // CSRF protection
        ini_set('session.use_strict_mode', '1');          // Strict session ID mode
        ini_set('session.cookie_lifetime', '86400');      // 24 hours
        
        // Allow session to work across IP and domain access
        // Empty domain allows the cookie for any subdomain of the current domain
        session_set_cookie_params([
            'lifetime' => 86400,
            'path' => '/',
            'domain' => '',  // Let PHP determine the domain automatically
            'secure' => false,  // Set to true if using HTTPS in production
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        session_start();
    }
    define('SESSION_STARTED_GUARD', true);
}

/**
 * Controleer of gebruiker ingelogd is
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return isset($_SESSION['user_id']);
    }
}

/**
 * Krijg huidige gebruiker ID
 */
if (!function_exists('auth_id')) {
    function auth_id()
    {
        return $_SESSION['user_id'] ?? null;
    }
}

/**
 * Krijg huidge gebruiker
 */
if (!function_exists('auth_user')) {
    function auth_user()
    {
        return $_SESSION['user'] ?? null;
    }
}

/**
 * Logout gebruiker
 */
if (!function_exists('logout')) {
    function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['user_id']);
        session_destroy();
    }
}

/**
 * Redirect naar URL
 */
if (!function_exists('redirect')) {
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Set flash message
 */
if (!function_exists('set_flash')) {
    function set_flash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

/**
 * Get en clear flash message
 */
if (!function_exists('get_flash')) {
    function get_flash()
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}

/**
 * Escape HTML
 */
if (!function_exists('esc_html')) {
    function esc_html($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Escape HTML attribute
 */
if (!function_exists('esc_attr')) {
    function esc_attr($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Format bedrag naar EUR
 */
if (!function_exists('format_price')) {
    function format_price($amount)
    {
        return '€ ' . number_format($amount, 2, ',', '.');
    }
}

/**
 * Format datum
 */
if (!function_exists('format_date')) {
    function format_date($date, $format = 'd-m-Y')
    {
        if (empty($date) || $date === '0000-00-00') {
            return '-';
        }
        return date($format, strtotime($date));
    }
}

/**
 * Get aantal dagen tot datum
 */
if (!function_exists('days_until')) {
    function days_until($date)
    {
        if (empty($date) || $date === '0000-00-00') {
            return null;
        }
        $today = new DateTime();
        $end = new DateTime($date);
        $interval = $today->diff($end);
        
        if ($interval->invert === 1) {
            return -$interval->days;
        }
        return $interval->days;
    }
}

/**
 * Controleer of abonnement bijna afloopt (< 30 dagen)
 */
if (!function_exists('is_expiring_soon')) {
    function is_expiring_soon($end_date, $days = 30)
    {
        $daysUntil = days_until($end_date);
        return $daysUntil !== null && $daysUntil >= 0 && $daysUntil <= $days;
    }
}

/**
 * Genereer veilieg wachtwoord
 */
if (!function_exists('generate_secure_password')) {
    function generate_secure_password($length = 16)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-={}[]|:;<>?,./';
        
        $all = $uppercase . $lowercase . $numbers . $special;
        $password = '';
        
        // Zorg voor minstens 1 van elk type
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Vul rest random
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Shuffle
        $password = str_shuffle($password);
        
        return $password;
    }
}

/**
 * Encrypt wachtwoord (AES-256-CBC)
 */
if (!function_exists('encrypt_password')) {
    function encrypt_password($password, $encryption_key)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($password, 'aes-256-cbc', hash('sha256', $encryption_key, true), OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }
}

/**
 * Decrypt wachtwoord
 */
if (!function_exists('decrypt_password')) {
    function decrypt_password($encrypted_password, $encryption_key)
    {
        $decoded = base64_decode($encrypted_password);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($decoded, 0, $iv_length);
        $encrypted = substr($decoded, $iv_length);
        return openssl_decrypt($encrypted, 'aes-256-cbc', hash('sha256', $encryption_key, true), OPENSSL_RAW_DATA, $iv);
    }
}

/**
 * Valideer email
 */
if (!function_exists('is_valid_email')) {
    function is_valid_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

/**
 * Valideer bestand upload
 */
if (!function_exists('validate_file_upload')) {
    function validate_file_upload($file, $max_size, $allowed_types)
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Geen bestand geüpload'];
        }
        
        if ($file['size'] > $max_size) {
            return ['error' => 'Bestand is te groot (max ' . round($max_size / 1024 / 1024) . 'MB)'];
        }
        
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_type, $allowed_types)) {
            return ['error' => 'Bestandstype niet toegestaan'];
        }
        
        return ['success' => true];
    }
}

/**
 * Genereer unieke bestandsnaam
 */
if (!function_exists('generate_unique_filename')) {
    function generate_unique_filename($original_name)
    {
        $extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $name = pathinfo($original_name, PATHINFO_FILENAME);
        return time() . '_' . uniqid() . '.' . $extension;
    }
}

/**
 * Controleer of bestand is preview-able (PDF of afbeelding)
 */
if (!function_exists('is_previewable_file')) {
    function is_previewable_file($file_type)
    {
        return in_array($file_type, ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
    }
}

/**
 * Zet frequentie naar Nederlands
 */
if (!function_exists('frequency_label')) {
    function frequency_label($frequency)
    {
        $labels = [
            'monthly' => 'Maandelijks',
            'yearly' => 'Jaarlijks'
        ];
        return $labels[$frequency] ?? $frequency;
    }
}

/**
 * Zet type naar Nederlands
 */
if (!function_exists('type_label')) {
    function type_label($type)
    {
        $labels = [
            'subscription' => 'Abonnement',
            'insurance' => 'Verzekering'
        ];
        return $labels[$type] ?? $type;
    }
}

/**
 * Get config value
 */
if (!function_exists('config')) {
    function config($key, $default = null)
    {
        static $config = null;
        
        if ($config === null) {
            // Load .env if not already loaded
            if (empty($_ENV)) {
                $env_file = __DIR__ . '/../.env';
                if (file_exists($env_file)) {
                    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        if (strpos(trim($line), '#')  === 0) continue;
                        if (strpos($line, '=') === false) continue;
                        list($k, $v) = explode('=', $line, 2);
                        $_ENV[trim($k)] = trim($v);
                    }
                }
            }
            
            // Build configuration array
            $config = [
                'db' => [
                    'host' => $_ENV['DB_HOST'] ?? 'localhost',
                    'port' => $_ENV['DB_PORT'] ?? 3306,
                    'database' => $_ENV['DB_NAME'] ?? 'abonnementen',
                    'username' => $_ENV['DB_USER'] ?? 'root',
                    'password' => $_ENV['DB_PASSWORD'] ?? '',
                    'charset' => 'utf8mb4'
                ],
                'app' => [
                    'name' => $_ENV['APP_NAME'] ?? 'Abonnementen Manager',
                    'debug' => $_ENV['APP_DEBUG'] ?? false,
                    'url' => $_ENV['APP_URL'] ?? 'http://localhost'
                ],
                'session' => [
                    'name' => $_ENV['SESSION_NAME'] ?? 'subscription_app'
                ],
                'upload' => [
                    'dir' => $_ENV['UPLOAD_DIR'] ?? 'uploads',
                    'max_size' => $_ENV['MAX_UPLOAD_SIZE'] ?? 5242880,
                    'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'txt']
                ],
                'encryption' => [
                    'key' => $_ENV['ENCRYPTION_KEY'] ?? 'default-key-change-this-32-chars!'
                ]
            ];
        }
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

/**
 * Get database connectie
 */
if (!function_exists('get_db')) {
    function get_db()
    {
        static $db = null;
        
        if ($db === null) {
            $config = config('db');
            
            if (!$config) {
                die('Database configuratie niet gevonden');
            }
            
            try {
                $host = !empty($config['host']) ? $config['host'] : 'localhost';
                $port = !empty($config['port']) ? $config['port'] : 3306;
                $database = !empty($config['database']) ? $config['database'] : 'abonnementen';
                $username = !empty($config['username']) ? $config['username'] : 'root';
                $password = $config['password'] ?? '';
                
                $db = new PDO(
                    'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=utf8mb4',
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Database connectie mislukt: ' . $e->getMessage());
            }
        }
        
        return $db;
    }
}

<?php
declare(strict_types=1);

session_start();

// Sends the same generic message for every failed login, so an attacker
// cannot tell whether the user ID or the password was wrong.
function login_failed(): void
{
    http_response_code(401);
    exit('Invalid user ID or password.');
}

// 1. Only accept POST requests (credentials must never arrive in the URL)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

// 2. Read and validate the input
$uid = trim($_POST['userId'] ?? '');
$upw = $_POST['password'] ?? '';

if ($uid === '' || $upw === '' || strlen($uid) > 50 || strlen($upw) > 128) {
    login_failed();
}

// 3. Connect with PDO. Database credentials come from environment
//    variables instead of being hard-coded in the source file.
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
    getenv('DB_HOST') ?: 'localhost',
    getenv('DB_NAME') ?: 'webapp');

try {
    $pdo = new PDO($dsn, getenv('DB_USER') ?: '', getenv('DB_PASS') ?: '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES   => false,   // use real prepared statements
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 4. Prepared statement: the user ID is sent as data, never as SQL code.
    //    The password is NOT part of the query; only its hash is stored.
    $stmt = $pdo->prepare('SELECT id, pw_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $uid]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log('Login database error: ' . $e->getMessage());   // details go to the log only
    http_response_code(500);
    exit('Something went wrong. Please try again later.');
}

// 5. Check the password against the stored hash
if ($user && password_verify($upw, $user['pw_hash'])) {
    session_regenerate_id(true);          // prevents session fixation
    $_SESSION['user_id'] = $user['id'];
    header('Location: dashboard.php');
    exit;
}

login_failed();

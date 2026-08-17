<?php
require_once __DIR__ . '/config.php';

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'assetcare';
$user = getenv('DB_USER') ?: 'admin';
$pass = getenv('DB_PASS') ?: '123456'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Auto-upgrader schema checks
    try { $pdo->exec("ALTER TABLE users ADD COLUMN mustResetPassword INT DEFAULT 0"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN reset_code VARCHAR(255) DEFAULT NULL"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN reset_expires DATETIME DEFAULT NULL"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN api_token VARCHAR(255) DEFAULT NULL"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE assets ADD COLUMN deliveryCount INT DEFAULT 0"); } catch (PDOException $e) {}

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS slots (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sn VARCHAR(255),
            date_val VARCHAR(255),
            slotNo VARCHAR(255),
            slotName VARCHAR(255),
            totalAssets INT DEFAULT 0,
            returnToIT INT DEFAULT 0,
            eol INT DEFAULT 0,
            pending INT DEFAULT 0,
            remarks TEXT
        )");
    } catch (PDOException $e) {}

    try { $pdo->exec("ALTER TABLE slots ADD COLUMN slotName VARCHAR(255) AFTER slotNo"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE assets ADD INDEX idx_status (status)"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE assets ADD INDEX idx_tag (tag)"); } catch (PDOException $e) {}

    // Seed default admin strictly if zero users exist in the database
    try {
        $checkUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($checkUsers == 0) {
            $initialPassword = bin2hex(random_bytes(8));
            $defaultHash = password_hash($initialPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, passwordHash, role, status, requestDate, lastSeen, mustResetPassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', 'admin@quantanite.com', $defaultHash, 'admin', 'active', '', 0, 1]);
        }
    } catch (PDOException $e) {}

} catch (PDOException $e) {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
?>
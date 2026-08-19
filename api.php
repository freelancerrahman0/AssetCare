<?php
// Start session with secure cookie configurations // B U I L T B Y A B D U R R A H M A N
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

header("Content-Type: application/json");

// CORS Protection
$allowedOrigin = defined('ALLOWED_ORIGIN') ? ALLOWED_ORIGIN : '';
$httpOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (!empty($allowedOrigin) && $httpOrigin === $allowedOrigin) {
    header("Access-Control-Allow-Origin: $httpOrigin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-Token");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/gmail_service.php';

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? [];  // B U I L T B Y A B D U R R A H M A N

function getCurrentUser() {
    global $pdo;
    
    $token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? '';
    if (empty($token)) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }
    }

    if (!empty($token)) {
        $stmt = $pdo->prepare("SELECT username, email, role, status, mustResetPassword FROM users WHERE api_token = ? AND api_token IS NOT NULL");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            return [
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status'],
                'mustResetPassword' => (int)$user['mustResetPassword']
            ];
        }
    }
    return null;
} // B U I L T B Y A B D U R R A H M A N

function requireAuth() {
    $user = getCurrentUser();
    if (!$user) {
        echo json_encode(["success" => false, "message" => "Unauthorized access. Please log in.", "authenticated" => false]);
        exit;
    }
    return $user;
}

function requireRoles(array $allowedRoles) {
    $user = requireAuth();
    if (!in_array($user['role'], $allowedRoles)) {
        echo json_encode(["success" => false, "message" => "Forbidden: You do not have permission to perform this action."]);
        exit;
    }
    return $user;
}

// PUBLIC API ENDPOINTS  // B U I L T B Y A B D U R R A H M A N

if ($action === 'login') {
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Username and password are required."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT username, email, passwordHash, role, status, mustResetPassword FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['passwordHash'])) {
        if ($user['status'] === 'blocked') {
            echo json_encode(["success" => false, "message" => "This account has been blocked by Admin."]);
            exit;
        }
        if ($user['status'] === 'pending') {
            echo json_encode(["success" => false, "message" => "Your account is pending approval."]);
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $upToken = $pdo->prepare("UPDATE users SET api_token = ? WHERE username = ?"); // B U I L T B Y A B D U R R A H M A N
        $upToken->execute([$token, $user['username']]);

        $user['api_token'] = $token;
        unset($user['passwordHash']);
        echo json_encode(["success" => true, "user" => $user]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "Invalid Username or Password."]);
    exit;
}

elseif ($action === 'request_reset_code') {
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');

    if (empty($username) || empty($email)) {
        echo json_encode(["success" => false, "message" => "Username and email are required."]); // B U I L T B Y A B D U R R A H M A N
        exit;
    }

    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = ? AND email = ? AND status = 'active'");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $otp = sprintf("%06d", random_int(100000, 999999));
        $otpHash = password_hash($otp, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', time() + 60);

        $up = $pdo->prepare("UPDATE users SET reset_code = ?, reset_expires = ? WHERE username = ?");
        $up->execute([$otpHash, $expires, $username]);

        $subject = "AssetCare - Password Recovery Code";
        $body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #2563eb;'>Password Recovery Request</h2>
                <p>You have requested to reset your password. Use the following 6-digit security code:</p>
                <div style='background: #f3f4f6; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; border-radius: 8px; margin: 20px 0;'>
                    $otp
                </div>
                <p style='color: #dc2626; font-weight: bold;'>This code will expire in 1 minute.</p> // B U I L T B Y A B D U R R A H M A N
            </div>
        ";

        sendEmailSMTP($email, $subject, $body);
    }

    echo json_encode(["success" => true, "message" => "If the credentials match an active account, a 6-digit verification code has been sent."]);
    exit;
}

elseif ($action === 'verify_reset_code') {
    $username = trim($data['username'] ?? ''); // B U I L T B Y A B D U R R A H M A N
    $code = trim($data['code'] ?? '');

    if (empty($username) || empty($code)) {
        echo json_encode(["success" => false, "message" => "Username and code are required."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT reset_code, reset_expires FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['reset_code'] && $user['reset_expires']) {
        if (strtotime($user['reset_expires']) > time()) {
            if (password_verify($code, $user['reset_code'])) {
                $_SESSION['pwd_reset_auth_user'] = $username; // B U I L T B Y A B D U R R A H M A N
                echo json_encode(["success" => true]);
                exit;
            }
        }
    }

    echo json_encode(["success" => false, "message" => "Invalid or expired security code."]);
    exit;
}

elseif ($action === 'reset_password') {
    $username = trim($data['username'] ?? '');
    $newPass = $data['newPassword'] ?? '';

    $authorizedUser = $_SESSION['pwd_reset_auth_user'] ?? null;
    $currentUser = getCurrentUser();

    $isSelfResetAllowed = ($currentUser && $currentUser['username'] === $username && $currentUser['mustResetPassword'] === 1); // B U I L T B Y A B D U R R A H M A N
    
    if (!$isSelfResetAllowed && ($authorizedUser !== $username)) {
        echo json_encode(["success" => false, "message" => "Unauthorized password reset attempt."]);
        exit;
    }

    if (strlen($newPass) < 8) {
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long."]);
        exit;
    }

    $newHash = password_hash($newPass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET passwordHash = ?, mustResetPassword = 0, reset_code = NULL, reset_expires = NULL WHERE username = ?"); // B U I L T B Y A B D U R R A H M A N
    $stmt->execute([$newHash, $username]);

    unset($_SESSION['pwd_reset_auth_user']);

    echo json_encode(["success" => true]);
    exit;
}

// PROTECTED API ENDPOINTS

requireAuth();

if ($action === 'load') {
    $users = $pdo->query("SELECT username, email, role, status, requestDate, lastSeen, mustResetPassword FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $assets = $pdo->query("SELECT tag, type, brand, model, serial, status, purchaseDate, repairCount, deliveryCount, repairs_json FROM assets")->fetchAll(PDO::FETCH_ASSOC); // B U I L T B Y A B D U R R A H M A N
    $slots = $pdo->query("SELECT id, sn, date_val, slotNo, slotName, totalAssets, returnToIT, eol, pending, remarks FROM slots ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($assets as &$asset) {
        $asset['repairs'] = json_decode($asset['repairs_json'], true) ?: [];
        unset($asset['repairs_json']);
    }
    unset($asset);

    echo json_encode(["success" => true, "users" => $users, "assets" => $assets, "slots" => $slots, "serverTime" => time() * 1000]); // B U I L T B Y A B D U R R A H M A N
    exit;
}

elseif ($action === 'change_password') {
    $userSession = requireAuth();
    $username = $userSession['username'];
    $currentPass = $data['currentPass'] ?? '';
    $newPass = $data['newPass'] ?? '';

    $stmt = $pdo->prepare("SELECT passwordHash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPass, $user['passwordHash'])) {
        echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
        exit; // B U I L T B Y A B D U R R A H M A N
    }

    if (strlen($newPass) < 8) {
        echo json_encode(["success" => false, "message" => "New password must be at least 8 characters long."]);
        exit;
    }

    $newHash = password_hash($newPass, PASSWORD_DEFAULT);
    $up = $pdo->prepare("UPDATE users SET passwordHash = ?, mustResetPassword = 0 WHERE username = ?");
    $up->execute([$newHash, $username]);
    echo json_encode(["success" => true]);
    exit;
}

elseif ($action === 'ping') {
    $userSession = requireAuth();
    $stmt = $pdo->prepare("UPDATE users SET lastSeen = ? WHERE username = ?");
    $stmt->execute([time() * 1000, $userSession['username']]);
    echo json_encode(["success" => true]);
    exit;
}

elseif ($action === 'logout') {
    $userSession = getCurrentUser();
    if ($userSession) {
        $stmt = $pdo->prepare("UPDATE users SET lastSeen = 0, api_token = NULL WHERE username = ?");
        $stmt->execute([$userSession['username']]);
    }
    echo json_encode(["success" => true]);
    exit;
}

// ADMIN ONLY ENDPOINTS

elseif ($action === 'save_user') {
    requireRoles(['admin']);
    
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $mustReset = isset($data['mustResetPassword']) ? intval($data['mustResetPassword']) : 0; // B U I L T BY A B D U R R A H M A N
    $rawPassword = $data['password'] ?? null;

    if (empty($username)) {
        echo json_encode(["success" => false, "message" => "Username is required."]);
        exit;
    }

    if (!empty($rawPassword)) {
        $checkStmt = $pdo->prepare("SELECT username FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        if ($checkStmt->fetch()) {
            echo json_encode(["success" => false, "message" => "This email or user name is already exist."]);
            exit;
        }
        
        $finalHash = password_hash($rawPassword, PASSWORD_DEFAULT);
    } else {
        $stmt = $pdo->prepare("SELECT passwordHash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $finalHash = $stmt->fetchColumn();
        if (!$finalHash) {
            echo json_encode(["success" => false, "message" => "Password required for new account."]);
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, email, passwordHash, role, status, requestDate, lastSeen, mustResetPassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE email=VALUES(email), passwordHash=VALUES(passwordHash), role=VALUES(role), status=VALUES(status), lastSeen=VALUES(lastSeen), mustResetPassword=VALUES(mustResetPassword)");
    $stmt->execute([
        $username, 
        $email, 
        $finalHash, 
        $data['role'] ?? 'Maintenance', 
        $data['status'] ?? 'active', 
        $data['requestDate'] ?? '', 
        $data['lastSeen'] ?? 0,
        $mustReset
    ]);

    if (!empty($rawPassword) && !empty($email)) {
        $portalLink = $data['portalLink'] ?? (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : ''); // B U I LT B Y A B D U R R A H M A N
        $subject = "Welcome to AssetCare - Account Created";
        $body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2 style='color: #2563eb;'>Welcome to AssetCare</h2>
                <p>Hello <strong>$username</strong>,</p>
                <p>An administrator has created an account for you. Please reset your password upon first login.</p>
                <p><strong>Username:</strong> $username</p>
                <p><strong>Temporary Password:</strong> $rawPassword</p>
                <br>
                <a href='$portalLink' style='background: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Login to Portal</a>
            </div>
        ";
        sendEmailSMTP($email, $subject, $body);
    }

    echo json_encode(["success" => true]);
    exit;
}

elseif ($action === 'delete_user') {
    requireRoles(['admin']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?"); // B UI L T B Y A B D U R R A H M A N
    $stmt->execute([$data['username'] ?? '']);
    echo json_encode(["success" => true]);
    exit;
}

// MAINTENANCE & ADMIN ENDPOINTS

elseif ($action === 'save_asset') {
    requireRoles(['admin', 'Maintenance']);
    $repairsJson = json_encode($data['repairs'] ?? []);
    $stmt = $pdo->prepare("INSERT INTO assets (tag, type, brand, model, serial, status, purchaseDate, repairCount, deliveryCount, repairs_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE status=VALUES(status), repairCount=VALUES(repairCount), deliveryCount=VALUES(deliveryCount), repairs_json=VALUES(repairs_json)");
    $stmt->execute([
        $data['tag'] ?? '', 
        $data['type'] ?? '', 
        $data['brand'] ?? '', 
        $data['model'] ?? '', 
        $data['serial'] ?? '', 
        $data['status'] ?? 'N/A', 
        $data['purchaseDate'] ?? '', 
        $data['repairCount'] ?? 0, 
        $data['deliveryCount'] ?? 0, 
        $repairsJson
    ]);
    echo json_encode(["success" => true]);
    exit;
}

elseif ($action === 'delete_asset') {
    requireRoles(['admin', 'Maintenance']);
    $stmt = $pdo->prepare("DELETE FROM assets WHERE tag = ?");
    $stmt->execute([$data['tag'] ?? '']);
    echo json_encode(["success" => true]);
    exit;
}

elseif ($action === 'save_slot') {
    requireRoles(['admin', 'Maintenance']);
    if (isset($data['id']) && $data['id']) {
        $stmt = $pdo->prepare("UPDATE slots SET sn=?, date_val=?, slotNo=?, slotName=?, totalAssets=?, returnToIT=?, eol=?, pending=?, remarks=? WHERE id=?");
        $stmt->execute([$data['sn'], $data['date_val'], $data['slotNo'], $data['slotName'], $data['totalAssets'], $data['returnToIT'], $data['eol'], $data['pending'], $data['remarks'], $data['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO slots (sn, date_val, slotNo, slotName, totalAssets, returnToIT, eol, pending, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['sn'], $data['date_val'], $data['slotNo'], $data['slotName'], $data['totalAssets'], $data['returnToIT'], $data['eol'], $data['pending'], $data['remarks']]);
    }
    echo json_encode(["success" => true]);
    exit;
}

elseif ($action === 'delete_slot') {
    requireRoles(['admin', 'Maintenance']);
    $stmt = $pdo->prepare("DELETE FROM slots WHERE id = ?"); // B U I L T B Y A B D U R R A H M A N
    $stmt->execute([$data['id'] ?? 0]);
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid API action."]);
exit;
?>
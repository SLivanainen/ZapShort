<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$long_url = $_POST['long_url'] ?? '';

if (empty($long_url)) {
    echo json_encode(['success' => false, 'error' => 'URL is required']);
    exit();
}

// Validate URL format
if (!filter_var($long_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL format']);
    exit();
}

// Check if URL already exists in database
try {
    $stmt = $pdo->prepare("SELECT short_code FROM urls WHERE long_url = ?");
    $stmt->execute([$long_url]);
    $existing_code = $stmt->fetchColumn();
    
    if ($existing_code) {
        $short_url = "http://zapshort.wuaze.com/" . $existing_code;
        echo json_encode(['success' => true, 'short_url' => $short_url]);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit();
}

// Generate a unique short code
function generateShortCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$short_code = generateShortCode(5); // Start with 5 characters

// Ensure the code is unique
$max_attempts = 5;
$attempts = 0;

while ($attempts < $max_attempts) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM urls WHERE short_code = ?");
        $stmt->execute([$short_code]);
        $exists = $stmt->fetchColumn();
        
        if (!$exists) {
            break;
        }
        
        // If code exists, try a longer one
        $short_code = generateShortCode(5 + $attempts);
        $attempts++;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error']);
        exit();
    }
}

if ($attempts >= $max_attempts) {
    echo json_encode(['success' => false, 'error' => 'Failed to generate unique short code']);
    exit();
}

// Insert into database
try {
    $stmt = $pdo->prepare("INSERT INTO urls (long_url, short_code, created_at, clicks) VALUES (?, ?, NOW(), 0)");
    $stmt->execute([$long_url, $short_code]);
    
    $short_url = "http://zapshort.wuaze.com/" . $short_code;
    echo json_encode(['success' => true, 'short_url' => $short_url]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>

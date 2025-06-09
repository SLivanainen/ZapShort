<?php
include 'db.php';

$code = $_GET['code'] ?? '';

if (!empty($code)) {
    try {
        $stmt = $pdo->prepare("SELECT long_url FROM urls WHERE short_code = ?");
        $stmt->execute([$code]);
        $url = $stmt->fetchColumn();
        
        if ($url) {
            // Update click count
            $stmt = $pdo->prepare("UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?");
            $stmt->execute([$code]);
            
            header("Location: " . $url);
            exit();
        } else {
            header("Location: index.php?error=invalid_link");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: index.php?error=database_error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

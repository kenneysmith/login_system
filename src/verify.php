<?php
session_start();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/helpers/notifications.php';
require_once __DIR__ . '/helpers/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = json_decode(file_get_contents('php://input'), true);

# Validate JSON data
    if (! isset($jsonData['otpcode'])) {
        echo json_encode([
            'statusCode' => 400,
            'success'    => false,
            'message'    => 'Invalid JSON data',
            'data'       => [],
        ]);
        exit;
    }
    $otpcode = (int) $jsonData['otpcode'];
    // print_r($_SESSION);
    try {
        $user_id  = $_SESSION['user_id'];
        $otp_code = (int) $_SESSION['otp_code'];
        $sql      = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            echo json_encode([
                'statusCode' => 404,
                'success'    => false,
                'message'    => 'User not found',
                'data'       => [],
            ]);
            exit;
        }
        if ($otp_code !== $otpcode) {
            echo json_encode([
                'statusCode' => 401,
                'success'    => false,
                'message'    => 'Invalid OTP code',
                'data'       => [],
            ]);
            exit;
        }

        $sql = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = :user_id");
        $sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $sql->execute();

        if ($sql->rowCount() === 0) {
            echo json_encode([
                'statusCode' => 500,
                'success'    => false,
                'message'    => 'Failed to update user status',
                'data'       => [],
            ]);
        }

        echo json_encode(value: [
            'statusCode' => 201,
            'success'    => true,
            'message'    => 'User verified successfully',
            'data'       => [],
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode([
            'statusCode' => 500,
            'success'    => false,
            'message'    => 'Database error: ' . $e->getMessage(),
            'data'       => [],
        ]);
        exit;

    }
} elseif ($_SERVER['REQUEST_METHOD'] === "PATCH") {} else {
    echo json_encode([
        'statusCode' => 405,
        'success'    => false,
        'message'    => 'Invalid request method',
        'data'       => [],
    ]);
    exit;

}

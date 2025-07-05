<?php

session_start();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/helpers/notifications.php';
require_once __DIR__ . '/helpers/header.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'statusCode' => 405,
        'success'    => false,
        'message'    => 'Invalid request method',
        'data'       => [],
    ]);
    exit;
}

$jsonData = json_decode(file_get_contents('php://input'), true);

# Validate JSON data
if (! isset($jsonData['email']) || ! isset($jsonData['password'])) {
    echo json_encode([
        'statusCode' => 400,
        'success'    => false,
        'message'    => 'Invalid JSON data',
        'data'       => [],
    ]);
    exit;
}

$email    = $jsonData['email'];
$password = $jsonData['password'];

try {
    $sql = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $sql->bindParam(':email', $email);
    $sql->execute();

    if ($sql->rowCount() === 0) {
        echo json_encode([
            'statusCode' => 401,
            'success'    => false,
            'message'    => 'Invalid email or password',
            'data'       => [],
        ]);
        exit;
    }
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    if ($user['status'] !== 'active') {
        echo json_encode([
            'statusCode' => 401,
            'success'    => false,
            'message'    => 'Account not verified',
            'data'       => [],
        ]);
        exit;
    }
    if (! password_verify($password, $user['password'])) {
        echo json_encode([
            'statusCode' => 401,
            'success'    => false,
            'message'    => 'Invalid email or password',
            'data'       => [],
        ]);
        exit;
    }
    $_SESSION['user_id']       = $user['user_id'];
    $_SESSION['user_email']    = $user['email'];
    $_SESSION['user_fullname'] = $user['fullname'];
    $_SESSION['user_gender']   = $user['gender'];
    $_SESSION['user_dob']      = $user['dob'];
    $_SESSION['user_contact']  = $user['contact'];
    $_SESSION['user_address']  = $user['address'];
    $_SESSION['user_status']   = $user['status'];

    echo json_encode([
        'statusCode' => 201,
        'success'    => true,
        'message'    => 'Login successful',
        'data'       => ["user_id" => $user['user_id'], "email" => $user['email'], "fullname" => $user['fullname'], "gender" => $user['gender'], "dob" => $user['dob'], "contact" => $user['contact'], "address" => $user['address'], "status" => $user['status']],
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

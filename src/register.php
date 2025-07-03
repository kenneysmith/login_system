<?php

session_start();

require_once 'database.php';
require_once '../bootstrap.php';
require_once 'helpers/notifications.php';
require_once 'helpers/header.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'statusCode' => 405,
        'success' => false,
        'message' => 'Invalid request method',
        'data' => []
    ]);
    exit;
}

$jsonData = json_decode(file_get_contents('php://input'), true);

# Validate JSON data
if (!isset($jsonData['email']) || !isset($jsonData['password'])) {
    echo json_encode([
        'statusCode' => 400,
        'success' => false,
        'message' => 'Invalid JSON data',
        'data' => []
    ]);
    exit;
}

$email = $jsonData['email'];
$password = $jsonData['password'];
$gender = $jsonData['gender'];
$fullname = $jsonData['fullname'];
$contact = $jsonData['contact'];
$address = $jsonData['address'];
$dob = $jsonData['dob'];

try {
    $pdo->beginTransaction();

    $sql = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $sql->bindParam(':email', $email);
    $sql->execute();
    if ($sql->rowCount() > 0) {
        echo json_encode([
            'statusCode' => 409,
            'success' => false,
            'message' => 'Email already exists',
            'data' => []
        ]);
        exit;
    }
    $sql = $pdo->prepare("SELECT * FROM users WHERE contact = :contact");
    $sql->bindParam(':contact', $contact);  
    $sql->execute();
    if ($sql->rowCount() > 0) {
        echo json_encode([
            'statusCode' => 409,
            'success' => false,
            'message' => 'Contact number already exists',
            'data' => []
        ]);
        exit;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = $pdo->prepare('INSERT INTO users (fullname, email, password, contact, address, gender, dob) VALUES (:fullname, :email, :password, :contact, :address, :gender, :dob)');
    $sql->bindParam(':fullname', $fullname);
    $sql->bindParam(':email', $email);
    $sql->bindParam(':password', $hashedPassword);
    $sql->bindParam(':contact', $contact);
    $sql->bindParam(':address', $address);
    $sql->bindParam(':gender', $gender);
    $sql->bindParam(':dob', $dob);
    $sql->execute();

    $pdo->commit();

    $otpCode = rand(100000, 999999);

    $message = "Your OTP code is: $otpCode. Please use this code to complete your registration.";
   
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['otp_code'] = $otpCode;

    // Send SMS notification
    SendSMS($contact, $message);
    SendEmail($email, 'Registration OTP', $message);
    echo json_encode([
        'statusCode' => 201,
        'success' => true,
        'message' => 'User registered successfully',
        'data' => []
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'statusCode' => 500,
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => []
    ]);
    exit;
}

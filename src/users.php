<?php

require_once 'database.php';

$fullname = "Innocent Bigega";
$email = "bigega23@gmail.com";
$password = "12345678";
$contact = "1234567890";
$address = "123 Main St";
$gender = "male";
$dob = "2000-01-01";
try {
    $pdo->beginTransaction();
    $sql = $pdo->prepare("INSERT INTO users (fullname, email, password, contact, address, gender, dob) 
VALUES (:fullname, :email, :password, :contact, :address, :gender, :dob)");

    $sql->bindParam(':fullname', $fullname);
    $sql->bindParam(':email', $email);
    $sql->bindParam(':password', $password);
    $sql->bindParam(':contact', $contact);
    $sql->bindParam(':address', $address);
    $sql->bindParam(':gender', $gender);
    $sql->bindParam(':dob', $dob);
    $sql->execute();


    if ($sql->rowCount() === 0) {
        json_encode([
            'status' => 'error',
            'message' => 'Failed to create user',
            'data' => []
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'User created successfully',
        'data' => [
            'fullname' => $fullname,
            'email' => $email,
            'contact' => $contact,
            'address' => $address,
            'gender' => $gender,
            'dob' => $dob
        ]
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}

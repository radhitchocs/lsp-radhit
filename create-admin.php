<?php
include('db.php');


$email = 'admin@email.com';
$password = '123';
$password_hashed = password_hash($password,PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT into admin (email,password) values (?,?) ");
    $stmt->bind_param("ss",$email,$password_hashed);

    if ($stmt->execute()) {
        echo "Admin berhasil dibuat dengan email : ". $email . "dan password yang telah di hash" . $password_hashed; 
    } else {
        echo "Admin gagal dibuat;";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

<?php

$localhost = 'localhost';
$user = 'radhitchocs';
$pass = 'password';
$dbname = 'inventoryV2';

$conn = new mysqli($localhost,$user,$pass,$dbname) or die('Gagal Terhubung' . $conn->connect_error);
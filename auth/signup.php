<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../connect.php";
include "../functions.php";

$username = isset($_POST['username']) ? $_POST['username'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$verifyCode = rand(1000, 9999);

// Check If User Exist
$columns = ['user_name', 'user_email'];
$values = [$username, $email];
$userExist = checkRowExist('users', $columns, $values);

if ($username && $email && $password ) {
    if (!$userExist) {
        $data = [
            'user_name'  => $username,
            'user_email' => $email,
            'user_password' => sha1($password),
            'user_verify_code' => $verifyCode
        ];
        insertData('users', $data, "User Registered Seccessfully");

    } else{
        echo errorResponse("User already exist");
    }
} else{
    echo errorResponse("Provide username, email and password");
}
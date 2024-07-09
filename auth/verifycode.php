<?php
include "../connect.php";
include "../functions.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = isset($_POST['email']) ? $_POST['email'] : null;
$verifyCode = isset($_POST['verifycode']) ? $_POST['verifycode'] : null;

// Checking verify code
$validCode = checkVerifyCode('users', $email, $verifyCode);

if($email && $verifyCode){
    if($validCode){
    $data = ['user_approve' => '1'];
    $whereCol = "user_email";
    $whereVal = $email;
    updateData('users', $data, $whereCol, $whereVal, "User Verified Successfully");

    }else{
        echo errorResponse("Incorrect Verify Code");
    }
}else{
    echo errorResponse("Provide email and verify code.");
}
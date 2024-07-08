<?php
include "../connect.php";
include "../functions.php";

$email = isset($_POST['email']) ? $_POST['email'] : null;
$verifyCode = isset($_POST['verifycode']) ? $_POST['verifycode'] : null;

// Checking code

$validCode = checkVerifyCode('users', $email, $verifyCode);

if($email && $verifyCode){
    if($validCode){
    $data = ['user_approve' => '1'];
    $where = "user_email = $email";
    updateData('users', $data, $where, "User Approved");

    }else{
        echo errorResponse("Incorrect Verify Code");
    }
}else{
    echo errorResponse("Provide email and verify code.");
}
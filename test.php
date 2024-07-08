<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "connect.php";
include "functions.php";


$to = 'ahmedmt.dev@gmail.com';
$subject = "Hello";
$msg = "From My PHP";
$headers = "From: noreply@ahmedmt.atwebpages.com";


// if(mail($to, $subject, $msg, $headers)) {
//     echo "Email successfully sent to $to...";
// } else {
//     echo "Email sending failed...";
// }

if(function_exists('mail')) {
    echo "PHP mail() function is enabled";
}
else {
    echo "PHP mail() function is not enabled";
}
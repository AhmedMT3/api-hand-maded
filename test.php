<?php
include "connect.php";
include "functions.php";

$exists =  checkRowExist('users', ['user_name', 'user_email'], ['ahmedjk', 'test@test.com']);

echo $exists ? "Exist" : "Not Exist";
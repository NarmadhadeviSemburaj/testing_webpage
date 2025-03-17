<?php
$plain_password = '123456'; // Set your desired password
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);
echo "Hashed Password: " . $hashed_password;
?>
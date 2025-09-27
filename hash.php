<?php
// naya password aap yaha set kar sakte ho
$newPassword = "admin***";

// bcrypt hash generate karega
$hash = password_hash($newPassword, PASSWORD_BCRYPT);

echo "Your new hash: " . $hash;
?>

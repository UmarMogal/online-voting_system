<?php
session_start();
session_destroy(); // destroy all session data
header("Location:index.php"); // redirect back to login page
exit();
?>

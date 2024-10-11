<?php
session_start();
session_destroy();
header('Location: ../../admin/adminLogin.php');
exit();
?>
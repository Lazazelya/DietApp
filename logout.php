<?php
session_start();
session_unset();
session_destroy();
header("Location: main.php?logout_success=1");
exit();
?>

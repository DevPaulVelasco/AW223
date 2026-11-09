<?php
session_start();
unset($_SESSION['komix_usuario']);
session_regenerate_id(true);
header('Location: login.php');
exit;

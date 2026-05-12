<?php
require_once __DIR__ . '/../includes/auth.php';
start_session();
unset($_SESSION['admin_id']);
session_destroy();
header('Location: login.php');
exit;

<?php
session_name('UserSession');
session_start();
session_destroy();
header('Location: login.php');
exit;

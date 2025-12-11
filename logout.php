<?php
session_start();
session_unset();
session_destroy();

// ANTES: header("Location: login.php");
// AGORA: Manda de volta para o feed
header("Location: index.php"); 
exit;
?>
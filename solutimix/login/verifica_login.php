<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    session_destroy();

    header("Location: ../login/login.php?erro=nao_autorizado");
    exit(); 
}
?>
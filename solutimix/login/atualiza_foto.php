<?php
session_start();
require_once 'conexao.php';
require_once 'verifica_login.php';

if (isset($_FILES['nova_foto']) && $_FILES['nova_foto']['error'] == 0) {
    $id_usuario = $_SESSION['usuario_id'];
    $foto = $_FILES['nova_foto'];
    $diretorio_upload = '../uploads/perfil/';
    
    $nome_arquivo = uniqid() . '_' . basename($foto['name']);
    $caminho_arquivo = $diretorio_upload . $nome_arquivo;

    if (move_uploaded_file($foto['tmp_name'], $caminho_arquivo)) {

        $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
        $stmt->execute([$nome_arquivo, $id_usuario]);

        $_SESSION['usuario_foto'] = $nome_arquivo;
    }
}

header("Location: ../login/perfil.php");
exit();
?>
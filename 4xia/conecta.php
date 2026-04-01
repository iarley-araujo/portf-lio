<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- DADOS DO INFINITYFREE (MYSQL) ---
$host = "sql202.infinityfree.com"; // Peguei da sua foto
$user = "if0_41015383";            // Peguei da sua foto
$pass = "Ic081206";  // <--- TROQUE PELA SENHA DO PAINEL (clique em 'Show' lá no site para ver)
$db   = "if0_41015383_4xia";       // <--- Confirme se você criou o banco '4xia' no painel

// String de Conexão para MySQL
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4;port=3306";

try {
    // Cria a conexão PDO
    $pdo = new PDO($dsn, $user, $pass);

    // Configura para lançar erros se algo der errado
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Se der erro, mostra na tela para a gente arrumar
    die("Erro ao conectar no MySQL: " . $e->getMessage());
}
?>
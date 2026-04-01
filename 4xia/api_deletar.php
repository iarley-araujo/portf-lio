<?php
require 'conecta.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);
$id_ativo = $dados['id'];

// Só apaga se o ativo pertencer ao usuário logado 
$stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = ? AND user_id = ?");
$stmt->execute([$id_ativo, $_SESSION['user_id']]);

echo json_encode(['sucesso' => true]);
?>
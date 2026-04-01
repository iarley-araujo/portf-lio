<?php
// api_ativos.php
require 'conecta.php';
header('Content-Type: application/json');

// Se não tiver logado, retorna lista vazia para não dar erro
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

try {
    // ativos DO USUÁRIO LOGADO
    $stmt = $pdo->prepare("SELECT * FROM portfolio WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $ativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ativos as &$ativo) {
        $ativo['qty'] = (float)$ativo['qty'];
        $ativo['current_price'] = (float)$ativo['current_price'];
        $ativo['avg_price'] = (float)$ativo['avg_price'];
        $ativo['annual_dy'] = (float)$ativo['annual_dy'];
    }

    echo json_encode($ativos);

} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
?>
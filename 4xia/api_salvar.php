<?php
require 'conecta.php';
header('Content-Type: application/json');

// logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não logado']);
    exit;
}

// Recebe os dados do Javascript 
$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados) {
    echo json_encode(['erro' => 'Nenhum dado recebido']);
    exit;
}

try {
  
    $sql = "INSERT INTO portfolio (user_id, ticker, qty, avg_price, current_price, annual_dy, pay_date) 
            VALUES (:user_id, :ticker, :qty, :avg, :cur, :dy, :date)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'], // O PHP pega o ID da sessão automaticamente
        ':ticker'  => $dados['ticker'],
        ':qty'     => $dados['qty'],
        ':avg'     => $dados['avg_price'],
        ':cur'     => $dados['current_price'],
        ':dy'      => $dados['annual_dy'],
        ':date'    => $dados['pay_date']
    ]);

    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no banco: ' . $e->getMessage()]);
}
?>
<?php
require 'conecta.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$userId = $_SESSION['user_id'];
$acao = $_POST['acao'] ?? '';

// --- EDITAR PERFIL ---
if ($acao === 'editar_perfil') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // ATENÇÃO: Verifique se sua tabela se chama 'usuarios', 'users' ou outro nome
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $userId]);
        
        // Atualiza a sessão
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco de dados.']);
    }
}

// --- ALTERAR SENHA ---
elseif ($acao === 'alterar_senha') {
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];
    $confirmaSenha = $_POST['confirma_senha'];

    if ($novaSenha !== $confirmaSenha) {
        echo json_encode(['success' => false, 'message' => 'As novas senhas não conferem.']);
        exit;
    }

    // Busca senha atual (Verifique se a coluna é 'senha' ou 'password')
    $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se você usa password_hash (recomendado)
    if ($user && password_verify($senhaAtual, $user['senha'])) {
        $novaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $update->execute([$novaHash, $userId]);
        echo json_encode(['success' => true]);
    } 
    // Se você NÃO usa hash (senha pura no banco - não recomendado, mas comum em testes)
    elseif ($user && $senhaAtual == $user['senha']) {
        // Se sua senha no banco não é criptografada, use este bloco e comente o de cima
        // $update = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        // $update->execute([$novaSenha, $userId]); // Salva sem hash
        
        // Mas vou assumir que você quer segurança, então vou dar erro se não for hash:
        echo json_encode(['success' => false, 'message' => 'Erro de configuração de segurança.']);
    } 
    else {
        echo json_encode(['success' => false, 'message' => 'Senha atual incorreta.']);
    }
}
?>
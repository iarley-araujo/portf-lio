<?php
session_start();
require_once 'conexao.php';
require_once 'verifica_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['usuario_id'];
    
    // Filtra dados básicos
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $tipo_perfil = filter_input(INPUT_POST, 'tipo_perfil', FILTER_SANITIZE_STRING);
    
    // Endereço
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
    $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
    $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

    // Áreas selecionadas (vem como array)
    $areas_selecionadas = isset($_POST['areas']) ? $_POST['areas'] : [];

    // Senha
    $senha_nova = $_POST['senha_nova'];
    $confirma_senha = $_POST['confirma_senha'];

    try {
        $pdo->beginTransaction(); // Inicia transação para segurança

        // 1. Atualiza Tabela Usuários
        // Monta a query básica
        $sql = "UPDATE usuarios SET 
                nome = ?, email = ?, cpf = ?, tipo_perfil = ?,
                cep = ?, endereco = ?, numero = ?, bairro = ?, cidade = ?, estado = ?
                WHERE id = ?";
        
        $params = [$nome, $email, $cpf, $tipo_perfil, $cep, $endereco, $numero, $bairro, $cidade, $estado, $id_usuario];

        // Se o usuário preencheu senha, muda a query para incluir senha
        if (!empty($senha_nova)) {
            if ($senha_nova === $confirma_senha) {
                $senhaHash = password_hash($senha_nova, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET 
                        nome = ?, email = ?, cpf = ?, tipo_perfil = ?,
                        cep = ?, endereco = ?, numero = ?, bairro = ?, cidade = ?, estado = ?,
                        senha = ? 
                        WHERE id = ?";
                // Remove o ID do final, adiciona senha, e põe ID de volta
                array_pop($params); 
                array_push($params, $senhaHash, $id_usuario);
            } else {
                throw new Exception("As senhas não conferem.");
            }
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // 2. Atualiza Áreas de Interesse
        $stmtDelete = $pdo->prepare("DELETE FROM usuario_areas WHERE usuario_id = ?");
        $stmtDelete->execute([$id_usuario]);

        // Depois insere as novas que vieram marcadas
        if (!empty($areas_selecionadas)) {
            $stmtInsert = $pdo->prepare("INSERT INTO usuario_areas (usuario_id, area_id) VALUES (?, ?)");
            foreach ($areas_selecionadas as $area_id) {
                $stmtInsert->execute([$id_usuario, $area_id]);
            }
        }

        $pdo->commit(); // Salva tudo
        
        // Atualiza sessão se mudou o nome
        $_SESSION['usuario_nome'] = $nome;
        
        header("Location: perfil.php?sucesso=1");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack(); // Cancela se der erro
        echo "<script>alert('Erro ao atualizar: " . $e->getMessage() . "'); window.location.href='configuracoes.php';</script>";
    }
}
?>
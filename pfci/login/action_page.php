<?php
session_start();
require_once "conexao.php";

// ============================================================
// 1. ROTA DE CADASTRO (Novo Usuário)
// ============================================================
// Identifica cadastro se vierem os campos 'nome', 'email', 'senha' e não for login
if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha']) && !isset($_POST['login'])) {
    
    // Filtra dados básicos
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    
    // Data e Perfil
    $data_nascimento = $_POST['data_nascimento']; // Recebe YYYY-MM-DD do input date
    $tipo_perfil = filter_input(INPUT_POST, 'tipo_perfil', FILTER_SANITIZE_STRING);
    
    // Endereço
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
    $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
    $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

    // Áreas de Interesse (Array de IDs)
    $areas = isset($_POST['areas']) ? $_POST['areas'] : [];

    // Verifica se e-mail já existe no banco
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: cadastro.php?erro=ja_cadastrado");
        exit();
    }

    try {
        // Inicia transação: Ou salva tudo (usuário + áreas), ou não salva nada
        $pdo->beginTransaction();

        // 1. Insere na tabela 'usuarios'
        $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        
        $sqlUser = "INSERT INTO usuarios (
            nome, email, senha, cpf, data_nascimento, tipo_perfil, 
            cep, endereco, numero, bairro, cidade, estado, foto_perfil
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'default-user.jpg')";
        
        $stmt = $pdo->prepare($sqlUser);
        $stmt->execute([
            $nome, $email, $senhaHash, $cpf, $data_nascimento, $tipo_perfil,
            $cep, $endereco, $numero, $bairro, $cidade, $estado
        ]);
        
        // Pega o ID do usuário que acabou de ser criado
        $id_novo_usuario = $pdo->lastInsertId();

        // 2. Insere na tabela 'usuario_areas' (Áreas de Interesse)
        if (!empty($areas)) {
            $sqlArea = "INSERT INTO usuario_areas (usuario_id, area_id) VALUES (?, ?)";
            $stmtArea = $pdo->prepare($sqlArea);
            
            foreach ($areas as $area_id) {
                $stmtArea->execute([$id_novo_usuario, $area_id]);
            }
        }

        // Confirma as alterações
        $pdo->commit();
        
        // Redireciona para login com sucesso
        header("Location: login.php?cadastro=sucesso");
        exit();

    } catch (Exception $e) {
        // Se der erro, desfaz tudo
        $pdo->rollBack();
        // Exibe erro para debug (em produção, redirecionar para página de erro genérica)
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}

// ============================================================
// 2. ROTA DE LOGIN (Entrar no sistema)
// ============================================================
if (isset($_POST['email'], $_POST['senha']) && !isset($_POST['nome'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    // Busca usuário pelo email
    $stmt = $pdo->prepare("SELECT id, nome, senha, foto_perfil FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Verifica se usuário existe e se a senha bate
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Cria a sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_foto'] = $usuario['foto_perfil'];
        
        // Redireciona para o início
        header("Location: ../inicio/index.php"); 
        exit();
    } else {
        // Login falhou
        header("Location: login.php?erro=usuario");
        exit();
    }
}

// Se acessar o arquivo diretamente sem dados POST, manda pro login
header("Location: login.php");
exit();
?>
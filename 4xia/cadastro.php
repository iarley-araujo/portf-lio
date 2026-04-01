<?php
require 'conecta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Criptografia
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $senhaHash]);
        
        // Se der certo, já loga o cara direto
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23505) { 
            $erro = "Este e-mail já está cadastrado!";
        } else {
            $erro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - 4xia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f1014] text-gray-300 flex items-center justify-center h-screen">
    <div class="bg-[#1a1b21] p-8 rounded-2xl border border-gray-700 w-full max-w-md shadow-2xl">
        <h1 class="text-3xl font-bold text-white mb-2 text-center">Criar Conta</h1>
        <p class="text-gray-500 text-sm text-center mb-6">Comece a controlar seus investimentos</p>
        
        <?php if(isset($erro)): ?>
            <div class="bg-red-500/20 text-red-400 p-3 rounded-lg mb-4 text-center text-sm border border-red-900">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-4">
            <div>
                <label class="text-xs text-gray-500 uppercase ml-1">Nome Completo</label>
                <input type="text" name="nome" required class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-600 outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-500 uppercase ml-1">E-mail</label>
                <input type="email" name="email" required class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-600 outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-500 uppercase ml-1">Senha</label>
                <input type="password" name="senha" required class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-600 outline-none">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition mt-2">
                CRIAR CONTA GRÁTIS
            </button>
        </form>
        
        <p class="text-center mt-6 text-sm text-gray-500">
            Já tem conta? <a href="login.php" class="text-blue-400 hover:underline">Fazer Login</a>
        </p>
    </div>
</body>
</html>
<?php
require 'conecta.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        // 1. Busca usuário usando PDO (Compatível com Postgres)
        // Usamos Prepared Statements para segurança
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Verifica senha
        // Se você salvou com password_hash, use password_verify
        // Se salvou texto puro, mantenha a comparação direta (mas recomendo mudar)
        if ($user && ($senha == $user['senha'] || password_verify($senha, $user['senha']))) {
            
            // 3. Salva na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['email'] = $user['email'];
            
            // Variáveis de compatibilidade para arquivos antigos
            $_SESSION['id_usuario'] = $user['id'];
            $_SESSION['nome_usuario'] = $user['nome'];

            header("Location: index.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    } catch (PDOException $e) {
        $erro = "Erro no banco: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 4xia (Supabase)</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f1014] text-gray-300 flex items-center justify-center h-screen">
    <div class="bg-[#1a1b21] p-8 rounded-2xl border border-gray-700 w-full max-w-md shadow-2xl">
        <h1 class="text-3xl font-bold text-white mb-6 text-center">Entrar na 4xia</h1>
        
        <?php if(!empty($erro)): ?>
            <div class="bg-red-500/20 text-red-400 p-3 rounded-lg mb-4 text-center text-sm border border-red-900">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-4">
            <div>
                <label class="text-xs text-gray-500 uppercase ml-1">E-mail</label>
                <input type="email" name="email" required class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-600 outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-500 uppercase ml-1">Senha</label>
                <input type="password" name="senha" required class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-600 outline-none">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition mt-2">
                ACESSAR COM SUPABASE
            </button>
        </form>
        
        <p class="text-center mt-6 text-sm text-gray-500">
            Ainda não tem conta? <a href="cadastro.php" class="text-blue-400 hover:underline">Crie agora</a>
        </p>
    </div>
</body>
</html>
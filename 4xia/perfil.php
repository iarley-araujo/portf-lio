<?php
require 'conecta.php';

// VERIFICAÇÃO DE SEGURANÇA: Se não estiver logado, chuta para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Busca dados frescos do usuário
$stmt = $pdo->prepare("SELECT nome, email, data_cadastro FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - 4xia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> </head>
<body class="flex h-screen overflow-hidden text-slate-300 bg-[#0f1014]">

    <aside class="w-20 flex flex-col items-center py-6 border-r border-gray-800 hidden md:flex bg-[#0f1014]">
        <div class="flex flex-col gap-6 w-full px-4">
            <a href="index.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl p-3 flex justify-center"><i class="fa-solid fa-chart-pie"></i></a>
            <a href="detalhes.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl p-3 flex justify-center"><i class="fa-solid fa-search"></i></a>
            <a href="simulador.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl p-3 flex justify-center"><i class="fa-solid fa-calculator"></i></a>
            <a href="perfil.php" class="sidebar-icon text-blue-500 bg-gray-800 rounded-xl p-3 flex justify-center"><i class="fa-solid fa-user"></i></a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <h1 class="text-3xl font-bold text-white mb-8">Meu Perfil</h1>

        <div class="max-w-2xl mx-auto space-y-6">
            
            <div class="card p-8 rounded-3xl flex items-center gap-6 border border-gray-700">
                <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center text-3xl text-white font-bold">
                    <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($user['nome']); ?></h2>
                    <p class="text-gray-400"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="text-xs text-blue-400 bg-blue-900/30 px-2 py-1 rounded mt-2 inline-block">Membro 4xia Pro</span>
                </div>
            </div>

            <div class="card p-6 rounded-3xl border border-gray-800">
                <h3 class="text-lg font-bold text-white mb-4">Dados da Conta</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Data de Cadastro</label>
                        <p class="text-white font-mono"><?php echo date('d/m/Y', strtotime($user['data_cadastro'])); ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase">ID de Usuário</label>
                        <p class="text-white font-mono">#<?php echo $_SESSION['user_id']; ?></p>
                    </div>
                </div>
            </div>

            <a href="logout.php" class="block text-center w-full bg-red-600/20 hover:bg-red-600/40 text-red-500 border border-red-900 py-3 rounded-xl font-bold transition">
                <i class="fa-solid fa-sign-out-alt mr-2"></i> Encerrar Sessão
            </a>
        </div>
    </main>
</body>
</html>
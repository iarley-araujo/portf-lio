<?php
// 1. Inicia Sessão e Conecta
session_start();
require 'conecta.php'; 

// 2. Verificação de Segurança (Aceita os dois tipos de sessão para compatibilidade)
if (!isset($_SESSION['id_usuario']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Pega o ID (dá preferência para o padrão novo, mas aceita o antigo)
$user_id = $_SESSION['user_id'] ?? $_SESSION['id_usuario'];

// 3. Busca dados no Banco (Versão PDO para Supabase)
try {
    $stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        $nome_real = $user_data['nome'];
        $email_real = $user_data['email'];
    } else {
        $nome_real = "Usuário";
        $email_real = "Atualize seu perfil";
    }
} catch (PDOException $e) {
    // Se der erro no banco, não quebra a página inteira
    $nome_real = "Erro";
    $email_real = "Erro de conexão";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invit 4xia - Web</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="flex h-screen overflow-hidden text-slate-300 bg-[#0f1014]">

    <aside class="w-20 flex flex-col items-center py-6 border-r border-gray-800 hidden md:flex bg-[#0f1014] relative z-40">
        <div class="flex flex-col gap-6 w-full px-4">
            <a href="index.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 transition rounded-xl p-3 flex justify-center" title="Dashboard">
                <i class="fa-solid fa-chart-pie"></i>
            </a>
            
            <a href="detalhes.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 transition rounded-xl p-3 flex justify-center" title="Pesquisar Ativo">
                <i class="fa-solid fa-search"></i>
            </a>

            <a href="simulador.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 transition rounded-xl p-3 flex justify-center" title="Simulador de Futuro">
                <i class="fa-solid fa-calculator"></i>
            </a>
        </div>

        <div class="user-profile-container mt-auto pb-5" id="userProfileBtn">
            <div class="avatar-circle w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold cursor-pointer hover:bg-blue-500 transition shadow-lg" onclick="toggleProfileMenu()">
                <span id="userInitials">US</span> 
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8 relative z-0">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Carteira 4xia</h1>
                <p class="text-gray-400 text-sm">Conectado ao Supabase • Dados em Tempo Real</p>
            </div>
            <button id="openModalBtn" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-full font-bold transition shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-plus mr-2"></i> Novo Ativo
            </button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card p-6 rounded-3xl bg-[#1e1e2d]">
                <p class="text-gray-400 text-sm">Patrimônio Investido</p>
                <h2 class="text-2xl font-bold text-white mt-2" id="totalEquity">R$ 0,00</h2>
            </div>
            <div class="card p-6 rounded-3xl bg-[#1e1e2d]">
                <p class="text-gray-400 text-sm">Renda Mensal (Est.)</p>
                <h2 class="text-2xl font-bold text-green-400 mt-2" id="monthlyIncome">R$ 0,00</h2>
            </div>
            <div class="card p-6 rounded-3xl bg-[#1e1e2d]">
                <p class="text-gray-400 text-sm">Renda Anual (Est.)</p>
                <h2 class="text-2xl font-bold text-blue-400 mt-2" id="annualIncome">R$ 0,00</h2>
            </div>
            <div class="card p-6 rounded-3xl bg-gray-800 border border-gray-700">
                <p class="text-gray-400 text-sm">Magic Number Médio</p>
                <h2 class="text-2xl font-bold text-purple-400 mt-2" id="avgMagicNumber">0</h2>
                <p class="text-xs text-gray-500">Cotas para reinvestir sozinho</p>
            </div>
        </div>

        <div class="card rounded-3xl p-6 bg-[#1e1e2d]">
            <h3 class="text-lg font-bold text-white mb-6">Minha Carteira</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-500 text-xs border-b border-gray-800 uppercase">
                            <th class="py-3 px-2">Investimento</th>
                            <th class="py-3 px-2">Cotas</th>
                            <th class="py-3 px-2">Valor Cota</th>
                            <th class="py-3 px-2">DY Anual</th>
                            <th class="py-3 px-2">Valor Investido</th>
                            <th class="py-3 px-2">Renda Mensal</th>
                            <th class="py-3 px-2">Próx. Pagamento</th>
                            <th class="py-3 px-2">Magic Number</th>
                            <th class="py-3 px-2">Ação</th>
                        </tr>
                    </thead>
                    <tbody id="assetTableBody" class="text-sm text-gray-300">
                    </tbody>
                </table>
                <div id="loadingState" class="text-center py-8 text-blue-500 hidden">
                    <i class="fa-solid fa-circle-notch fa-spin"></i> Carregando dados...
                </div>
            </div>
        </div>
    </main>

    <div class="profile-dropdown hidden fixed bottom-5 left-20 w-64 bg-[#1e1e2d] border border-gray-800 rounded-2xl shadow-2xl flex-col overflow-hidden z-[9999]" id="profileDropdown">
        <div class="dropdown-header p-4 bg-[#151520] border-b border-gray-800">
            <span class="user-name block text-white font-bold text-sm"><?php echo $nome_real; ?></span>
            <span class="user-email block text-gray-500 text-xs break-all"><?php echo $email_real; ?></span>
        </div>
        
        <a href="#" onclick="openEditProfile()" class="dropdown-item p-3 text-gray-400 hover:bg-gray-800 hover:text-white flex items-center gap-3 transition">
            <i class="fas fa-user-edit w-5"></i> Editar Perfil
        </a>
        <a href="#" onclick="openChangePass()" class="dropdown-item p-3 text-gray-400 hover:bg-gray-800 hover:text-white flex items-center gap-3 transition">
            <i class="fas fa-key w-5"></i> Alterar Senha
        </a>
        <div class="border-t border-gray-800 my-1"></div>
        <a href="logout.php" class="dropdown-item p-3 text-red-400 hover:bg-red-500/10 flex items-center gap-3 transition">
            <i class="fas fa-sign-out-alt w-5"></i> Sair
        </a>
    </div>

    <div id="modalOverlay" class="fixed inset-0 bg-black/80 hidden flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-[#1a1b21] p-8 rounded-3xl w-full max-w-md border border-gray-700 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-2">Adicionar Ativo</h2>
            <p class="text-xs text-gray-500 mb-6">Digite o código e aperte TAB para buscar dados.</p>
            
            <form id="addAssetForm" class="flex flex-col gap-4">
                <div class="relative">
                    <label class="text-xs text-gray-400 ml-1">Código (Ticker)</label>
                    <input type="text" id="inputTicker" placeholder="Ex: MXRF11" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none uppercase font-bold" required>
                    <div id="loadingTicker" class="absolute right-3 top-9 text-blue-500 hidden"><i class="fa-solid fa-spinner fa-spin"></i></div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-400 ml-1">Quantidade</label>
                        <input type="number" step="any" id="inputQty" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 ml-1">Preço Médio (Seu)</label>
                        <input type="number" step="any" id="inputAvgPrice" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                    </div>
                </div>

                <div class="p-4 bg-gray-800/50 rounded-xl border border-gray-700 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-blue-400 ml-1">Preço Atual (Auto)</label>
                        <input type="number" step="any" id="inputCurrentPrice" class="w-full bg-transparent text-gray-300 font-bold outline-none" readonly placeholder="...">
                    </div>
                    <div>
                        <label class="text-xs text-blue-400 ml-1">DY Anual % (Auto)</label>
                        <input type="number" step="any" id="inputDy" class="w-full bg-transparent text-gray-300 font-bold outline-none" readonly placeholder="...">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-blue-400 ml-1">Próx. Data Pagamento (Auto)</label>
                        <input type="text" id="inputDate" class="w-full bg-transparent text-gray-300 font-bold outline-none" readonly placeholder="...">
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="button" id="closeModalBtn" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl font-bold transition">Cancelar</button>
                    <button type="submit" id="btnSave" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditProfile" class="fixed inset-0 bg-black/80 hidden flex items-center justify-center z-[60] backdrop-blur-sm">
        <div class="bg-[#1a1b21] p-8 rounded-3xl w-full max-w-md border border-gray-700 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6">Editar Perfil</h2>
            
            <form id="formEditProfile" class="flex flex-col gap-4">
                <input type="hidden" name="acao" value="editar_perfil">
                
                <div>
                    <label class="text-xs text-gray-400 ml-1">Seu Nome</label>
                    <input type="text" name="nome" id="editNome" value="<?php echo $nome_real; ?>" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 ml-1">Seu E-mail</label>
                    <input type="email" name="email" id="editEmail" value="<?php echo $email_real; ?>" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="closeProfileModals()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl font-bold transition">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-bold transition">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalChangePass" class="fixed inset-0 bg-black/80 hidden flex items-center justify-center z-[60] backdrop-blur-sm">
        <div class="bg-[#1a1b21] p-8 rounded-3xl w-full max-w-md border border-gray-700 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6">Alterar Senha</h2>
            
            <form id="formChangePass" class="flex flex-col gap-4">
                <input type="hidden" name="acao" value="alterar_senha">

                <div>
                    <label class="text-xs text-gray-400 ml-1">Senha Atual</label>
                    <input type="password" name="senha_atual" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>
                <hr class="border-gray-700 my-2">
                <div>
                    <label class="text-xs text-gray-400 ml-1">Nova Senha</label>
                    <input type="password" name="nova_senha" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="text-xs text-gray-400 ml-1">Confirmar Nova Senha</label>
                    <input type="password" name="confirma_senha" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="closeProfileModals()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl font-bold transition">Cancelar</button>
                    <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-500 text-white py-3 rounded-xl font-bold transition">Trocar Senha</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script> 
    
    <script>
        // --- SCRIPT DO PERFIL ---
        const nomeCompletoUsuario = "<?php echo $nome_real; ?>"; 

        function setInitials(name) {
            if(!name) return;
            const names = name.split(' ');
            let initials = names[0].substring(0, 1).toUpperCase();
            if (names.length > 1) {
                initials += names[names.length - 1].substring(0, 1).toUpperCase();
            }
            document.getElementById('userInitials').innerText = initials;
        }

        setInitials(nomeCompletoUsuario);

        function toggleProfileMenu() {
            const menu = document.getElementById('profileDropdown');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('flex');
                setTimeout(() => menu.classList.add('active'), 10); 
            } else {
                menu.classList.remove('active');
                menu.classList.add('hidden');
                menu.classList.remove('flex');
            }
        }

        document.addEventListener('click', function(event) {
            const btn = document.getElementById('userProfileBtn');
            const menu = document.getElementById('profileDropdown');
            if (btn && !btn.contains(event.target) && menu && !menu.contains(event.target)) {
                menu.classList.remove('active');
                menu.classList.add('hidden');
                menu.classList.remove('flex');
            }
        });

        // Modais
        function openEditProfile() {
            document.getElementById('profileDropdown').classList.add('hidden');
            document.getElementById('modalEditProfile').classList.remove('hidden');
        }

        function openChangePass() {
            document.getElementById('profileDropdown').classList.add('hidden');
            document.getElementById('modalChangePass').classList.remove('hidden');
        }

        function closeProfileModals() {
            document.getElementById('modalEditProfile').classList.add('hidden');
            document.getElementById('modalChangePass').classList.add('hidden');
        }

        // AJAX
        document.getElementById('formEditProfile').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('user_actions.php', { method: 'POST', body: formData });
                const result = await response.json();
                if(result.success) {
                    alert("Perfil atualizado! Recarregando...");
                    location.reload();
                } else {
                    alert("Erro: " + result.message);
                }
            } catch (error) { console.error('Erro:', error); alert("Erro ao conectar com o servidor."); }
        });

        document.getElementById('formChangePass').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('user_actions.php', { method: 'POST', body: formData });
                const result = await response.json();
                if(result.success) {
                    alert("Senha alterada com sucesso!");
                    closeProfileModals();
                    this.reset();
                } else {
                    alert("Erro: " + result.message);
                }
            } catch (error) { console.error('Erro:', error); alert("Erro ao conectar com o servidor."); }
        });
    </script>
</body>
</html>
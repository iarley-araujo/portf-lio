<?php
session_start();
require_once 'conexao.php';
require_once 'verifica_login.php';

$id_usuario = $_SESSION['usuario_id'];

// 1. Busca todos os dados atuais do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

// 2. Busca todas as áreas de medicina disponíveis (para listar as opções)
$stmtAreas = $pdo->query("SELECT * FROM areas_medicina ORDER BY nome_area ASC");
$todas_areas = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);

// 3. Busca as áreas que o usuário JÁ tem selecionadas (para marcar os checkboxes)
$stmtMinhasAreas = $pdo->prepare("SELECT area_id FROM usuario_areas WHERE usuario_id = ?");
$stmtMinhasAreas->execute([$id_usuario]);
$minhas_areas = $stmtMinhasAreas->fetchAll(PDO::FETCH_COLUMN); // Cria um array simples: [1, 3, 5]
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações | PFCI Saude</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="perfil.css">
    <style>
        /* Estilos para o formulário de edição */
        .form-row { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px; }
        .form-group { flex: 1; min-width: 200px; margin-bottom: 5px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; }
        
        .form-control {
            width: 100%; padding: 12px; border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-body); color: var(--text-main);
            font-size: 1rem; outline: none; transition: 0.3s;
        }
        .form-control:focus { border-color: var(--accent); }
        
        .section-title { 
            font-size: 1.2rem; color: var(--text-main); margin: 35px 0 20px 0; 
            border-bottom: 1px solid var(--border); padding-bottom: 10px; font-weight: 600;
        }
        
        /* Grid de Checkboxes */
        .areas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
        }
        .area-option {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border: 1px solid var(--border); border-radius: 8px;
            cursor: pointer; transition: 0.2s; color: var(--text-main);
        }
        .area-option:hover { background: var(--bg-input-hover); }
        .area-option input { transform: scale(1.2); cursor: pointer; accent-color: var(--accent); }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="../inicio/index.php" title="Início"><i class="fas fa-home"></i></a>
        <a href="perfil.php" title="Perfil"><i class="fas fa-user"></i></a>
        <a href="configuracoes.php" title="Configurações"><i class="fas fa-cog active"></i></a>
    </aside>

    <main class="main-content">
        <header>
            <div>
                <h1 class="page-title">Editar Dados</h1>
                <p class="breadcrumb">Atualize suas informações pessoais e preferências</p>
            </div>
            <button id="theme-toggle-btn" class="theme-toggle-btn"><i class="fas fa-sun"></i></button>
        </header>

        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <form action="atualiza_dados.php" method="POST">
                
                <div class="section-title" style="margin-top: 0;">Dados Pessoais</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>CPF</label>
                        <input type="text" name="cpf" id="cpf" class="form-control" value="<?php echo htmlspecialchars($usuario['cpf'] ?? ''); ?>" maxlength="14">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Perfil</label>
                        <select name="tipo_perfil" class="form-control">
                            <option value="Estudante" <?= ($usuario['tipo_perfil'] == 'Estudante') ? 'selected' : '' ?>>Estudante</option>
                            <option value="Professor" <?= ($usuario['tipo_perfil'] == 'Professor') ? 'selected' : '' ?>>Professor</option>
                            <option value="Profissional" <?= ($usuario['tipo_perfil'] == 'Profissional') ? 'selected' : '' ?>>Profissional da Saúde</option>
                        </select>
                    </div>
                </div>

                <div class="section-title">Endereço</div>
                
                <div class="form-row">
                    <div class="form-group" style="max-width: 150px;">
                        <label>CEP</label>
                        <input type="text" name="cep" id="cep" class="form-control" value="<?php echo htmlspecialchars($usuario['cep'] ?? ''); ?>" onblur="pesquisacep(this.value);">
                    </div>
                    <div class="form-group">
                        <label>Rua / Avenida</label>
                        <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?>">
                    </div>
                    <div class="form-group" style="max-width: 100px;">
                        <label>Número</label>
                        <input type="text" name="numero" class="form-control" value="<?php echo htmlspecialchars($usuario['numero'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Bairro</label>
                        <input type="text" name="bairro" id="bairro" class="form-control" value="<?php echo htmlspecialchars($usuario['bairro'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" name="cidade" id="cidade" class="form-control" value="<?php echo htmlspecialchars($usuario['cidade'] ?? ''); ?>">
                    </div>
                    <div class="form-group" style="max-width: 80px;">
                        <label>UF</label>
                        <input type="text" name="estado" id="estado" class="form-control" value="<?php echo htmlspecialchars($usuario['estado'] ?? ''); ?>">
                    </div>
                </div>

                <div class="section-title">Áreas de Interesse</div>
                <p style="margin-bottom: 15px; color: var(--text-muted); font-size: 0.9rem;">Marque as áreas que você tem interesse:</p>
                
                <div class="areas-grid">
                    <?php foreach($todas_areas as $area): ?>
                        <label class="area-option">
                            <input type="checkbox" name="areas[]" value="<?php echo $area['id']; ?>" 
                                <?php echo in_array($area['id'], $minhas_areas) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($area['nome_area']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="section-title">Segurança</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nova Senha (Opcional)</label>
                        <input type="password" name="senha_nova" class="form-control" placeholder="Deixe em branco para manter a atual">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Senha</label>
                        <input type="password" name="confirma_senha" class="form-control">
                    </div>
                </div>

                <div class="actions-container" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="background: var(--accent); color: #fff; border: none;">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="perfil.php" class="btn btn-danger" style="text-decoration: none;">Cancelar</a>
                </div>

            </form>
        </div>
    </main>

    <script>
        // 1. MÁSCARA CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g, '');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})/, '$1-$2');
            v = v.replace(/(-\d{2})\d+?$/, '$1');
            e.target.value = v;
        });

        // 2. MÁSCARA CEP
        document.getElementById('cep').addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g, '');
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
            v = v.replace(/(-\d{3})\d+?$/, '$1');
            e.target.value = v;
        });

        // 3. BUSCA CEP (VIACEP)
        function pesquisacep(valor) {
            var cep = valor.replace(/\D/g, '');
            if (cep != "") {
                var validacep = /^[0-9]{8}$/;
                if(validacep.test(cep)) {
                    document.getElementById('endereco').value="...";
                    document.getElementById('bairro').value="...";
                    document.getElementById('cidade').value="...";
                    document.getElementById('estado').value="...";

                    var script = document.createElement('script');
                    script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
                    document.body.appendChild(script);
                } else { alert("Formato de CEP inválido."); }
            }
        }

        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                document.getElementById('endereco').value=(conteudo.logradouro);
                document.getElementById('bairro').value=(conteudo.bairro);
                document.getElementById('cidade').value=(conteudo.localidade);
                document.getElementById('estado').value=(conteudo.uf);
            } else {
                alert("CEP não encontrado.");
                document.getElementById('endereco').value="";
            }
        }

        // TEMA ESCLURO
        document.addEventListener("DOMContentLoaded", () => {
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const body = document.body;
            const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

            function updateIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.classList.remove('fa-sun'); themeIcon.classList.add('fa-moon');
                    } else {
                        themeIcon.classList.remove('fa-moon'); themeIcon.classList.add('fa-sun');
                    }
                }
            }

            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') { body.classList.add('dark-mode'); updateIcon('dark'); }
            else { updateIcon('light'); }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    body.classList.toggle('dark-mode');
                    const theme = body.classList.contains('dark-mode') ? 'dark' : 'light';
                    localStorage.setItem('theme', theme);
                    updateIcon(theme);
                });
            }
        });
    </script>
</body>
</html>
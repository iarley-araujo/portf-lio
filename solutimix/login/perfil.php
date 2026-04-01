<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'conexao.php';
require_once 'verifica_login.php';

$id_usuario = $_SESSION['usuario_id'];

// 1. BUSCAR TODOS OS DADOS DO USUÁRIO
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: logout.php");
    exit();
}

// 2. BUSCAR AS ÁREAS DE INTERESSE
$stmtAreas = $pdo->prepare("
    SELECT am.nome_area 
    FROM areas_medicina am
    JOIN usuario_areas ua ON am.id = ua.area_id
    WHERE ua.usuario_id = ?
    ORDER BY am.nome_area ASC
");
$stmtAreas->execute([$id_usuario]);
$minhas_areas = $stmtAreas->fetchAll(PDO::FETCH_COLUMN);

// 3. FUNÇÃO PARA CALCULAR IDADE
function calcular_idade($data) {
    if (empty($data)) return "Não informado";
    $nascimento = new DateTime($data);
    $hoje = new DateTime();
    $diferenca = $hoje->diff($nascimento);
    return $diferenca->y . " anos";
}

// 4. LÓGICA DA FOTO
$imagem_padrao = "default-user.jpg"; 
if (!empty($usuario['foto_perfil'])) {
    $caminho_foto_upload = "../uploads/perfil/" . htmlspecialchars($usuario['foto_perfil']);
    $src_imagem = $caminho_foto_upload;
} else {
    $src_imagem = $imagem_padrao; 
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | PFCI Saude</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="perfil.css">
    <style>
        /* Estilos extras para as novas informações */
        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .tag-area {
            background-color: var(--accent);
            color: #fff;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .endereco-box {
            background: rgba(0,0,0,0.02);
            padding: 10px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <a href="../inicio/index.php" title="Início"><i class="fas fa-home"></i></a>
        <a href="perfil.php" title="Meu Perfil"><i class="fas fa-user active"></i></a>
        <a href="configuracoes.php" title="Configurações"><i class="fas fa-cog"></i></a>
    </aside>

    <main class="main-content">
        <header>
            <div>
                <h1 class="page-title">Meu Perfil</h1>
                <p class="breadcrumb">Visualize suas informações completas</p>
            </div>
            
            <button id="theme-toggle-btn" class="theme-toggle-btn" title="Alternar Tema">
                <i class="fas fa-sun"></i>
            </button>
        </header>

        <?php if (isset($_GET['sucesso'])): ?>
            <div style="padding: 15px; background: #d1e7dd; color: #0f5132; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
                Perfil atualizado com sucesso!
            </div>
        <?php endif; ?>

        <div class="profile-grid">
            
            <div class="card left-card">
                <h2 class="user-name"><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                
                <span class="user-badge">
                    <?php echo htmlspecialchars($usuario['tipo_perfil'] ?? 'Usuário'); ?>
                </span>

                <form action="atualiza_foto.php" method="post" enctype="multipart/form-data">
                    <div class="profile-pic-container">
                        <img class="profile-pic" src="<?php echo $src_imagem; ?>" onerror="this.src='default-user.jpg'">
                        
                        <label for="file-upload" class="camera-btn">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input id="file-upload" type="file" name="nova_foto" onchange="this.form.submit()" style="display: none;">
                    </div>
                </form>
                
                <div class="social-icons">
                    <i class="fab fa-instagram" style="color: #e1306c;"></i>
                    <i class="fab fa-linkedin" style="color: #0077b5;"></i>
                </div>
            </div>

            <div class="card details-card">
                <div class="details-header">
                    <h3>Bio & Detalhes</h3>
                    <div class="status-dot" title="Online"></div>
                </div>

                <div class="info-row">
                    <div class="info-group">
                        <label>CPF</label>
                        <div class="value" style="font-size: 0.95rem;">
                            <?php echo !empty($usuario['cpf']) ? htmlspecialchars($usuario['cpf']) : '--'; ?>
                        </div>
                    </div>
                    <div class="info-group">
                        <label>Idade</label>
                        <div class="value">
                            <?php echo calcular_idade($usuario['data_nascimento']); ?>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-group">
                        <label>E-mail</label>
                        <div class="value email-text"><?php echo htmlspecialchars($usuario['email']); ?></div>
                    </div>
                    <div class="info-group">
                        <label>Localização</label>
                        <div class="value">
                            <?php 
                                if (!empty($usuario['cidade'])) {
                                    echo htmlspecialchars($usuario['cidade'] . ' - ' . $usuario['estado']);
                                } else {
                                    echo '<span style="color:var(--text-muted); font-size:0.9rem;">Não informado</span>';
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($usuario['endereco'])): ?>
                <div class="info-row">
                    <div class="info-group" style="width: 100%;">
                        <label>Endereço Completo</label>
                        <div class="endereco-box">
                            <?php 
                                echo htmlspecialchars($usuario['endereco']); 
                                if (!empty($usuario['numero'])) echo ", nº " . htmlspecialchars($usuario['numero']);
                                if (!empty($usuario['bairro'])) echo " - " . htmlspecialchars($usuario['bairro']);
                                if (!empty($usuario['cep'])) echo "<br>CEP: " . htmlspecialchars($usuario['cep']);
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="info-row">
                    <div class="info-group" style="width: 100%;">
                        <label>Áreas de Interesse</label>
                        <div class="tags-container">
                            <?php if (!empty($minhas_areas)): ?>
                                <?php foreach($minhas_areas as $area): ?>
                                    <span class="tag-area"><?php echo htmlspecialchars($area); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">Nenhuma área selecionada.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-group">
                        <label>Membro Desde</label>
                        <div class="value"><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></div>
                    </div>
                </div>

                <div class="actions-container">
                    <a href="../inicio/index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Início
                    </a>
                    
                    <a href="configuracoes.php" class="btn btn-primary" style="background-color: var(--accent); color: #fff;">
                        <i class="fas fa-edit"></i> Editar Dados
                    </a>
                    
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>

            </div>
        </div>
    </main>

    <script>
        // Script do Tema Escuro
        document.addEventListener("DOMContentLoaded", () => {
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const body = document.body;
            const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

            function updateIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.classList.remove('fa-sun');
                        themeIcon.classList.add('fa-moon');
                    } else {
                        themeIcon.classList.remove('fa-moon');
                        themeIcon.classList.add('fa-sun');
                    }
                }
            }

            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                body.classList.add('dark-mode');
                updateIcon('dark');
            } else {
                updateIcon('light');
            }

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
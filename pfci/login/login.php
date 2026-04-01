<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PFCI Saude</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
</head>
<body>
    
    <header class="header-main">
        <div class="logo">
            <a href="../inicio/index.php"> <img src="../imagens/PFCI2.svg" alt="Logo Pfci Saude">
            </a>
        </div>
        <nav class="desktop-nav">
            <a href="../inicio/index.php" class="nav-link">Início</a>
            <a href="../inicio/conteudos.php" class="nav-link">Conteúdos</a>
            <a href="../inicio/ferramentas.php" class="nav-link">Ferramentas</a>
            <a href="../inicio/aulas.php" class="nav-link">Aulas</a>
            <a href="../inicio/atlas.php" class="nav-link">Anatomia 3D</a>
        </nav>

        <div class="header-actions">
            <button id="theme-toggle-btn" class="theme-toggle-btn" title="Alternar Tema">
                <i class="fas fa-sun"></i>
            </button>
        </div>
    </header>

    <div class="background-container">
        <div class="login-container">
            
            <div class="login-header">
                <h2>Bem-vindo</h2>
                <p>Acesse sua conta para continuar</p>
            </div>

            <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger">
                <?php
                if ($_GET['erro'] == 'usuario') echo "Login e/ou senha inválido.";
                elseif ($_GET['erro'] == 'ja_cadastrado') echo "E-mail já cadastrado. Tente fazer login.";
                elseif ($_GET['erro'] == 'nao_autorizado') echo "Você precisa estar logado para acessar esta página.";
                ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
                <div class="alert alert-success">
                    Cadastro realizado com sucesso! Faça seu login.
                </div>
            <?php endif; ?>

            <form action="action_page.php" method="post">
                <div class="input-group">
                    <i class="bi bi-envelope icon"></i>
                    <input type="email" placeholder="Seu e-mail" name="email" required>
                </div>
                <div class="input-group">
                    <i class="bi bi-lock icon"></i>
                    <input type="password" placeholder="Sua senha" id="senha" name="senha" required>
                    <i class="bi bi-eye-fill icon icon-eye" id="btn-senha" onclick="mostrarSenha()"></i>
                </div>
                <button class="btn-login" type="submit">Entrar</button>
            </form>
            
            <div class="additional-links">
                <a href="Recupera.php">Esqueceu a senha?</a>
                <span>|</span>
                <a href="cadastro.php">Criar conta</a>
            </div>

        </div>
    </div>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
    
    <script>
        // Bloco de script principal para carregar o tema e definir funções
        document.addEventListener("DOMContentLoaded", () => {

            // MODO ESCURO
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const body = document.body;
            const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

            // Função para aplicar o tema e salvar
            function applyTheme(theme) {
                if (theme === 'dark') {
                    body.classList.add('dark-mode');
                    if (themeIcon) {
                        themeIcon.classList.remove('fa-sun');
                        themeIcon.classList.add('fa-moon');
                    }
                    localStorage.setItem('theme', 'dark');
                } else {
                    body.classList.remove('dark-mode');
                    if (themeIcon) {
                        themeIcon.classList.remove('fa-moon');
                        themeIcon.classList.add('fa-sun');
                    }
                    localStorage.setItem('theme', 'light');
                }
            }

            // 1. Verificar o tema salvo no localStorage ao carregar a página
            const currentTheme = localStorage.getItem('theme') || 'light';
            applyTheme(currentTheme);

            // 2. Adicionar o evento de clique ao botão
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const newTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
                    applyTheme(newTheme);
                });
            }
        });

        // CÓDIGO DE MOSTRAR SENHA 
        function mostrarSenha() {
            const inputPass = document.getElementById('senha');
            const btnShowPass = document.getElementById('btn-senha');
            if (inputPass.type === 'password') {
                inputPass.type = 'text';
                btnShowPass.classList.remove('bi-eye-fill');
                btnShowPass.classList.add('bi-eye-slash-fill');
            } else {
                inputPass.type = 'password';
                btnShowPass.classList.remove('bi-eye-slash-fill');
                btnShowPass.classList.add('bi-eye-fill');
            }
        }
    </script>
</body>
</html>
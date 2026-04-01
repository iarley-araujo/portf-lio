<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Solutimix</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
</head>
<body>
    
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="header-main-nav">
                    <a href="../inicio/index.php" class="logo-link">SOLUTIMIX</a>
                    <nav class="desktop-nav">
                        <a href="../inicio/index.php" class="nav-link">Início</a>
                        <a href="../inicio/produtos.php" class="nav-link">Produtos</a>
                         <a href="../inicio/sobre.php" class="nav-link">Sobre Nós</a>
                        <a href="../inicio/contato.php" class="nav-link">Contato</a>
                    </nav>
                </div>

                <div class="header-actions">
                    <button id="theme-toggle-btn" class="theme-toggle-btn" title="Alternar Tema">
                        <i class="fas fa-sun"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="background-container">
        <div class="login-wrapper">
            
            <div class="login-container">
                <div class="login-header">
                    <h2>Bem-vindo de volta</h2>
                    <p>Acesse sua conta para gerenciar seus pedidos.</p>
                </div>

                <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    if ($_GET['erro'] == 'usuario') echo "E-mail ou senha incorretos.";
                    elseif ($_GET['erro'] == 'ja_cadastrado') echo "E-mail já cadastrado. Faça login.";
                    elseif ($_GET['erro'] == 'nao_autorizado') echo "Faça login para acessar essa página.";
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Cadastro realizado! Faça seu login.
                    </div>
                <?php endif; ?>

                <form action="action_page.php" method="post">
                    <div class="input-group">
                        <label for="email">E-mail</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" placeholder="seu@email.com" name="email" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="senha">Senha</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" placeholder="Sua senha" id="senha" name="senha" required>
                            <i class="far fa-eye show-pass-icon" id="btn-senha" onclick="mostrarSenha()"></i>
                        </div>
                    </div>

                    <div class="forgot-password">
                        <a href="Recupera.php">Esqueceu a senha?</a>
                    </div>

                    <button class="btn-login" type="submit">Entrar <i class="fas fa-arrow-right"></i></button>
                </form>
                
                <div class="additional-links">
                    <span>Não tem uma conta?</span>
                    <a href="cadastro.php">Cadastre-se</a>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        // --- Scripts de Tema e Interação ---
        document.addEventListener("DOMContentLoaded", () => {
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const body = document.body;
            const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

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

            const currentTheme = localStorage.getItem('theme') || 'light';
            applyTheme(currentTheme);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const newTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
                    applyTheme(newTheme);
                });
            }
        });

        function mostrarSenha() {
            const inputPass = document.getElementById('senha');
            const btnShowPass = document.getElementById('btn-senha');
            if (inputPass.type === 'password') {
                inputPass.type = 'text';
                btnShowPass.classList.remove('fa-eye');
                btnShowPass.classList.add('fa-eye-slash');
            } else {
                inputPass.type = 'password';
                btnShowPass.classList.remove('fa-eye-slash');
                btnShowPass.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
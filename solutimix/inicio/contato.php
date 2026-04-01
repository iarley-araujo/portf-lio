<?php
session_start();

// --- LÓGICA DA FOTO DE PERFIL ---
$caminho_foto_nav = '../login/default-user.jpg'; 
if (isset($_SESSION['usuario_id'])) {
    $pdo_nav = null;
    if (file_exists('../login/conexao.php')) { require_once '../login/conexao.php'; $pdo_nav = $pdo; }
    if ($pdo_nav) {
        $stmt_nav = $pdo_nav->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
        $stmt_nav->execute([$_SESSION['usuario_id']]);
        $user_nav = $stmt_nav->fetch();
        if ($user_nav && !empty($user_nav['foto_perfil']) && file_exists('../uploads/perfil/' . $user_nav['foto_perfil'])) {
            $caminho_foto_nav = '../uploads/perfil/' . $user_nav['foto_perfil'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Solutimix</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* CSS Específico da Página de Contato */
        
        /* Layout em Grid */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr; /* Coluna da esquerda menor, form maior */
            gap: 4rem;
            margin-bottom: 4rem;
        }

        /* Cartão de Informações (Esquerda) */
        .contact-info-card {
            background-color: var(--cor-secundaria); /* Fundo Escuro */
            color: #fff;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--sombra-padrao);
            height: fit-content;
        }

        .contact-info-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .contact-info-item i {
            color: var(--cor-primaria); /* Azul da marca */
            font-size: 1.2rem;
            margin-top: 5px;
        }

        .contact-info-item p {
            margin: 0;
            font-size: 0.95rem;
            color: #e5e7eb;
            line-height: 1.6;
        }

        .contact-socials {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-btn:hover {
            background-color: var(--cor-primaria);
            transform: translateY(-3px);
        }

        /* Formulário (Direita) */
        .contact-form-container {
            background-color: var(--fundo-claro-card);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--sombra-padrao);
            border: 1px solid var(--borda-padrao);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--cor-texto);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: var(--fundo-claro);
            font-family: 'Poppins', sans-serif;
            color: var(--cor-texto);
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--cor-primaria);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background-color: var(--cor-primaria);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background-color: var(--cor-primaria-hover);
        }

        /* Mapa */
        .map-container {
            width: 100%;
            height: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--sombra-padrao);
            margin-bottom: 4rem;
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Dark Mode Ajustes */
        body.dark-mode .contact-form-container {
            background-color: var(--fundo-escuro-card);
            border-color: var(--borda-padrao-dark);
        }
        body.dark-mode .form-group label {
            color: var(--cor-texto-dark);
        }
        body.dark-mode .form-control {
            background-color: var(--fundo-escuro-hover);
            border-color: var(--borda-padrao-dark);
            color: var(--cor-texto-dark);
        }
        body.dark-mode .form-control:focus {
            border-color: var(--cor-primaria);
        }

        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .contact-info-card {
                order: -1; /* Info primeiro no mobile */
            }
        }
    </style>
</head>
<body>

<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="header-main-nav">
                <a href="index.php" class="logo-link" style="text-decoration:none; font-weight:800; font-size:1.5rem; color:var(--cor-secundaria);">SOLUTIMIX</a>
                <nav class="desktop-nav">
                    <a href="index.php" class="nav-link">Início</a>
                    <a href="produtos.php" class="nav-link">Produtos</a>
                    <a href="sobre.php" class="nav-link">Sobre Nós</a>
                    <a href="contato.php" class="nav-link active">Contato</a>
                </nav>
            </div>
           <div class="header-actions">
                    <?php if (isset($_SESSION['usuario_id'])) : ?>
                        <span style="margin-right: 15px; font-weight: 600; color: var(--cor-texto);">Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Cliente'); ?></span>
                        <a href="../login/perfil.php" class="profile-link"><img src="<?php echo $caminho_foto_nav; ?>" class="profile-photo"></a>
                        <a href="../login/logout.php" class="btn-auth">Sair</a>
                    <?php else : ?>
                        <a href="../login/login.php" class="btn-auth"><i class="fas fa-user"></i> Entrar</a>
                        <a href="../login/cadastro.php" class="btn-auth btn-cta">Cadastre-se</a>
                    <?php endif; ?>
                    
                    <button id="theme-toggle-btn" class="theme-toggle-btn" aria-label="Alternar Tema">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <button id="mobile-menu-btn" class="mobile-menu-btn" aria-label="Menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <h3>Solutimix</h3>
        <button class="close-btn" id="mobile-menu-close-btn"><i class="fas fa-times"></i></button>
    </div>
    <nav class="mobile-nav">
        <a href="index.php" class="mobile-nav-link">Início</a>
        <a href="produtos.php" class="mobile-nav-link">Produtos</a>
        <a href="sobre.php" class="mobile-nav-link">Sobre Nós</a>
        <a href="contato.php" class="mobile-nav-link active">Contato</a>
    </nav>
</div>

<main class="page-content">
    
    <section class="page-hero">
        <div class="container">
            <h1 class="hero-title">Fale Conosco</h1>
            <p class="hero-subtitle">Estamos prontos para atender seu negócio. Tire dúvidas, solicite orçamentos ou faça parcerias.</p>
        </div>
    </section>

    <div class="container">
        
        <div class="contact-grid">
            
            <div class="contact-info-card">
                <h3>Informações de Contato</h3>
                
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <p><strong>Endereço:</strong></p>
                        <p>Av. Francisco Morais Ramos, 777<br>Jardim Novo Horizonte, Rio Grande da Serra - SP</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <p><strong>E-mail:</strong></p>
                        <p>contato@solutimix.com.br</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <p><strong>Telefone / WhatsApp:</strong></p>
                        <p>(11) 94470-9622</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <p><strong>Horário de Atendimento:</strong></p>
                        <p>Segunda a Sexta: 08h às 18h<br>Sábado: 08h às 20h</p>
                    </div>
                </div>

                <div class="contact-socials">
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="contact-form-container">
                <form action="#" method="POST"> <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" class="form-control" placeholder="Seu nome ou da sua empresa" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail Profissional</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="exemplo@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto</label>
                        <select id="assunto" name="assunto" class="form-control">
                            <option value="orcamento">Solicitar Orçamento</option>
                            <option value="duvida">Dúvida sobre Produto</option>
                            <option value="posvenda">Pós-venda / Suporte</option>
                            <option value="parceria">Parceria</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" class="form-control" placeholder="Como podemos ajudar você hoje?"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        Enviar Mensagem <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>

        </div>

        <h2 class="section-title" style="font-size: 1.8rem; margin-bottom: 1.5rem;">Nossa Localização</h2>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3652.569300302246!2d-46.38927892376269!3d-23.72707207868669!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce714c7c72f7af%3A0xc3f9479383344f2d!2sAv.%20Francisco%20Morais%20Ramos%2C%20777%20-%20Jardim%20Novo%20Horizonte%2C%20Rio%20Grande%20da%20Serra%20-%20SP%2C%2009450-000!5e0!3m2!1spt-BR!2sbr!4v1733182800000!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </div>
</main>

<footer class="main-footer">
    <div class="container footer-grid">
        <div class="footer-column">
            <h4>Navegação</h4>
            <nav class="footer-nav">
                <a href="index.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="sobre.php">Sobre</a>
            </nav>
        </div>
        <div class="footer-column contact-info">
            <h4>Contato</h4>
            <p><i class="fas fa-envelope footer-icon"></i> contato@solutimix.com.br</p>
            <p><i class="fas fa-phone-alt footer-icon"></i> (11) 94470-9622</p>
        </div>
        <div class="footer-column address-info">
            <h4>Endereço</h4>
            <p><i class="fas fa-map-marker-alt footer-icon"></i> Rio Grande da Serra, SP</p>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p class="copyright-text">© 2025 Solutimix. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
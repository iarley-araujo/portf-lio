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
    <title>Sobre Nós - Solutimix</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* CSS Específico da Página Sobre (Inline para facilitar) */
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-bottom: 4rem;
        }

        .about-text h3 {
            font-size: 1.8rem;
            color: var(--cor-secundaria);
            margin-bottom: 1rem;
        }

        .about-text p {
            font-size: 1rem;
            color: var(--cor-texto-mutado);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .about-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: var(--sombra-padrao);
        }

        /* Valores (Cards) */
        .values-section {
            background-color: var(--fundo-claro-card);
            padding: 4rem 0;
            margin-top: 4rem;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .value-card {
            background: var(--fundo-claro);
            padding: 2.5rem;
            border-radius: 16px;
            text-align: center;
            transition: transform 0.3s;
            border: 1px solid transparent;
        }

        .value-card:hover {
            transform: translateY(-5px);
            border-color: var(--cor-primaria);
            background: #fff;
            box-shadow: var(--sombra-padrao);
        }

        .value-icon {
            font-size: 2.5rem;
            color: var(--cor-primaria);
            margin-bottom: 1rem;
        }

        .value-card h4 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--cor-secundaria);
        }

        /* Dark Mode Ajustes */
        body.dark-mode .about-text h3, 
        body.dark-mode .value-card h4 {
            color: var(--cor-texto-dark);
        }
        body.dark-mode .about-text p {
            color: var(--cor-texto-mutado-dark);
        }
        body.dark-mode .values-section {
            background-color: var(--fundo-escuro-hover);
        }
        body.dark-mode .value-card {
            background-color: var(--fundo-escuro-card);
            border-color: var(--borda-padrao-dark);
        }
        body.dark-mode .value-card:hover {
            background-color: var(--fundo-escuro);
            border-color: var(--cor-primaria);
        }

        @media (max-width: 992px) {
            .about-grid { grid-template-columns: 1fr; gap: 2rem; }
            .about-image { order: -1; }
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
                    <a href="sobre.php" class="nav-link active">Sobre Nós</a>
                    <a href="contato.php" class="nav-link">Contato</a>
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
        <a href="contato.php" class="mobile-nav-link">Contato</a>
    </nav>
</div>

<main class="page-content">
    
    <section class="page-hero">
        <div class="container">
            <h1 class="hero-title">Quem Somos</h1>
            <p class="hero-subtitle">Conheça a história e os valores que movem a Solutimix.</p>
        </div>
    </section>

    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <span class="text-tagline">Nossa História</span>
                <h3>Mais do que uma loja, um parceiro de negócios</h3>
                <p>
                    Fundada com o objetivo de revolucionar o mercado de atacado e varejo online, a <strong>Solutimix</strong> nasceu da necessidade de oferecer produtos de qualidade com agilidade logística e preços competitivos.
                </p>
                <p>
                    Começamos pequenos, focados em eletrônicos, e hoje expandimos para utilidades domésticas e acessórios, atendendo desde o consumidor final até grandes revendedores em todo o Brasil.
                </p>
                <div style="margin-top: 2rem;">
                    <div style="display: flex; gap: 2rem;">
                        <div>
                            <strong style="font-size: 2rem; color: var(--cor-primaria);">5+</strong>
                            <p style="font-size: 0.9rem;">Anos de Mercado</p>
                        </div>
                        <div>
                            <strong style="font-size: 2rem; color: var(--cor-primaria);">10k+</strong>
                            <p style="font-size: 0.9rem;">Clientes Atendidos</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Reunião Solutimix">
            </div>
        </div>
    </div>

    <section class="values-section">
        <div class="container">
            <h2 class="section-title">Nossos Pilares</h2>
            <p class="section-subtitle">O que guia nossas decisões todos os dias.</p>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-bullseye"></i></div>
                    <h4>Missão</h4>
                    <p style="font-size: 0.9rem;">Facilitar o acesso a produtos de tendência e qualidade, impulsionando o negócio dos nossos parceiros revendedores.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-eye"></i></div>
                    <h4>Visão</h4>
                    <p style="font-size: 0.9rem;">Ser referência nacional em distribuição e logística no comércio eletrônico até 2030.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fas fa-handshake"></i></div>
                    <h4>Valores</h4>
                    <p style="font-size: 0.9rem;">Transparência nas negociações, agilidade na entrega e compromisso com a satisfação do cliente.</p>
                </div>
            </div>
        </div>
    </section>

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
                <p class="copyright-text">&copy; 2025 Solutimix. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>


<script src="script.js"></script>
</body>
</html>
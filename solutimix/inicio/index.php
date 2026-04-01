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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Solutimix - Atacado e Varejo</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <style>
        .profile-photo { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--cor-primaria); background-color: #fff; }
        .header-actions .profile-link { display: flex; align-items: center; text-decoration: none; margin-right: 10px; }
        .hero-image-new img { width: 100%; max-width: 500px; border-radius: 20px; box-shadow: var(--sombra-padrao); }
    </style>
</head>
<body>
    
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="header-main-nav">
                    <a href="index.php" class="logo-link" style="text-decoration:none; font-weight:800; font-size:1.5rem; color:var(--cor-secundaria);">
                        SOLUTIMIX
                    </a>
                    <nav class="desktop-nav">
                        <a href="index.php" class="nav-link active">Início</a>
                        <a href="produtos.php" class="nav-link">Produtos</a>
                        <a href="sobre.php" class="nav-link">Sobre Nós</a>
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

    <section class="hero-section-new">
        <div class="container hero-content-new">
            <div class="hero-text-new">
                <span class="hero-tagline">Atacado & Varejo</span>
                <h1 class="hero-title">Abasteça seu negócio com os melhores preços</h1>
                <p class="hero-subtitle">
                    A Solutimix oferece uma variedade incrível de produtos eletrônicos, utilidades e acessórios com condições especiais para revenda.
                </p>
                <div class="hero-buttons">
                    <a href="produtos.php" class="btn-auth btn-cta">Ver Catálogo</a>
                    <a href="contato.php" class="btn-auth">Falar com Vendedor</a>
                </div>
            </div>
            <div class="hero-image-new">
                <img src="https://images.unsplash.com/photo-1586880244406-55983627908c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Estoque Solutimix">
            </div> 
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Por que comprar na Solutimix?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-truck-loading"></i></div>
                    <h3>Entrega Rápida</h3>
                    <p>Logística eficiente para todo o Brasil com rastreio em tempo real.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-tags"></i></div>
                    <h3>Preço de Atacado</h3>
                    <p>Descontos progressivos para grandes quantidades e revendedores.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Garantia Total</h3>
                    <p>Produtos testados e com garantia de troca em caso de defeito.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h3>Suporte Dedicado</h3>
                    <p>Equipe pronta para tirar dúvidas e auxiliar no seu pedido.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="methodology-section">
        <div class="container">
            <div class="methodology-content-wrapper">
                <div class="methodology-image-content">
                    <img src="https://images.unsplash.com/photo-1472851294608-415522f96317?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Produtos" class="methodology-image" />
                </div>
                <div class="methodology-text-content">
                    <span class="text-tagline">Variedade</span>
                    <h2 class="section-title-left">
                        Tudo o que você precisa em <span class="highlight">um só lugar</span>
                    </h2>
                    <p class="section-description-left">
                        Não perca tempo procurando fornecedores diferentes. A Solutimix centraliza as melhores marcas e produtos para facilitar a gestão do seu estoque.
                    </p>
                    <a href="produtos.php" class="btn-primary">
                        Explorar Produtos <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

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
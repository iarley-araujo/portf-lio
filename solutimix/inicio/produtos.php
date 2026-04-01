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
    <title>Solutimix - Produtos</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* --- CSS DA PÁGINA DE PRODUTOS --- */

        /* 1. Barra de Pesquisa e Filtros (Visual Moderno) */
        .courses-filters {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 3rem;
            margin-top: 1rem;
        }

        .search-bar {
            position: relative;
            width: 100%;
            max-width: 600px;
        }

        .search-bar input {
            width: 100%;
            padding: 15px 20px 15px 50px; /* Espaço para o ícone */
            border-radius: 50px;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            font-size: 1rem;
            color: #1f2937;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .search-bar input:focus {
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .search-bar i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.2rem;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .filter-btn {
            background-color: transparent;
            border: 1px solid #e5e7eb;
            padding: 8px 24px;
            border-radius: 50px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .filter-btn:hover {
            border-color: var(--cor-primaria);
            color: var(--cor-primaria);
            background-color: #f3f4f6;
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background-color: var(--cor-primaria);
            color: white;
            border-color: var(--cor-primaria);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }

        /* 2. Grid de Produtos */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding-bottom: 4rem;
        }

        /* 3. Card do Produto */
        .course-card {
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            border-color: var(--cor-primaria);
        }

        .course-image {
            width: 100%;
            height: 220px;
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        
        .course-card:hover .course-image img { transform: scale(1.05); }

        .course-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .course-tag {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--cor-primaria);
            background: rgba(37, 99, 235, 0.1);
            padding: 4px 10px;
            border-radius: 4px;
            align-self: flex-start;
            margin-bottom: 0.8rem;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 0.5rem 0;
            line-height: 1.3;
        }

        .course-excerpt {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            flex-grow: 1;
        }

        .course-meta {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 1rem;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .course-meta .price {
            font-weight: 800;
            color: #111827;
            font-size: 1.1rem;
        }

        .btn-comprar {
            display: block;
            width: 100%;
            text-align: center;
            background: #111827;
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 15px;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-comprar:hover {
            background: var(--cor-primaria);
        }

        /* 4. Modo Escuro */
        body.dark-mode .course-card {
            background-color: #1e293b !important;
            border-color: #334155;
        }
        body.dark-mode .course-title { color: #f8fafc !important; }
        body.dark-mode .course-excerpt, body.dark-mode .course-meta { color: #94a3b8 !important; }
        body.dark-mode .course-meta { border-top-color: #334155; }
        body.dark-mode .course-meta .price { color: #60a5fa !important; }
        body.dark-mode .btn-comprar { background-color: var(--cor-primaria); color: white; }
        body.dark-mode .btn-comprar:hover { background-color: var(--cor-primaria-hover); }
        
        /* Pesquisa Dark */
        body.dark-mode .search-bar input {
            background-color: #1f2937;
            border-color: #374151;
            color: #f3f4f6;
        }
        body.dark-mode .search-bar i { color: #9ca3af; }
        body.dark-mode .filter-btn { border-color: #374151; color: #9ca3af; }
        body.dark-mode .filter-btn:hover { border-color: var(--cor-primaria); color: white; background-color: rgba(37, 99, 235, 0.1); }
        body.dark-mode .filter-btn.active { background-color: var(--cor-primaria); color: white; }
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
                    <a href="produtos.php" class="nav-link active">Produtos</a>
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

<main class="page-content">
    <section class="page-hero">
        <div class="container">
            <h1 class="hero-title">Nosso Catálogo</h1>
            <p class="hero-subtitle">Confira nossas ofertas em eletrônicos, casa e acessórios.</p>
        </div>
    </section>

    <div class="container">
        <div class="courses-filters">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="O que você procura hoje?">
            </div>
            <div class="filter-buttons" id="category-filters">
                <button class="filter-btn active" data-filter="todos">Todos</button>
                <button class="filter-btn" data-filter="eletronicos">Eletrônicos</button>
                <button class="filter-btn" data-filter="casa">Casa</button>
                <button class="filter-btn" data-filter="acessorios">Acessórios</button>
            </div>
        </div> 

        <div class="courses-grid">
            
            <article class="course-card" data-category="eletronicos">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Smartwatch">
                </div>
                <div class="course-content">
                    <span class="course-tag">Eletrônicos</span>
                    <h3 class="course-title">Smartwatch Series 5</h3>
                    <p class="course-excerpt">Relógio inteligente com monitor cardíaco. Caixa com 10 unidades.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 5 un.</span>
                        <span class="price">R$ 450,00</span>
                    </div>
                    <a href="simular_compra.php?id=1" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

            <article class="course-card" data-category="casa">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1583947215259-38e31be8751f?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Kit Cozinha">
                </div>
                <div class="course-content">
                    <span class="course-tag">Casa</span>
                    <h3 class="course-title">Kit Utensílios Premium</h3>
                    <p class="course-excerpt">Conjunto com 5 peças de silicone e cabo de madeira.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 10 un.</span>
                        <span class="price">R$ 35,90</span>
                    </div>
                    <a href="simular_compra.php?id=2" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

            <article class="course-card" data-category="acessorios">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Mochila">
                </div>
                <div class="course-content">
                    <span class="course-tag">Acessórios</span>
                    <h3 class="course-title">Mochila Executiva</h3>
                    <p class="course-excerpt">Mochila impermeável com compartimento para notebook.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 5 un.</span>
                        <span class="price">R$ 89,90</span>
                    </div>
                    <a href="simular_compra.php?id=3" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

             <article class="course-card" data-category="eletronicos">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Fone">
                </div>
                <div class="course-content">
                    <span class="course-tag">Eletrônicos</span>
                    <h3 class="course-title">Fone Bluetooth Pro</h3>
                    <p class="course-excerpt">Cancelamento de ruído e bateria de longa duração.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 20 un.</span>
                        <span class="price">R$ 120,00</span>
                    </div>
                    <a href="simular_compra.php?id=4" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

            <article class="course-card" data-category="eletronicos">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Caixa de Som">
                </div>
                <div class="course-content">
                    <span class="course-tag">Eletrônicos</span>
                    <h3 class="course-title">Caixa de Som Boom</h3>
                    <p class="course-excerpt">Potência de 20W, à prova d'água IPX7. Cores variadas.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 8 un.</span>
                        <span class="price">R$ 180,00</span>
                    </div>
                    <a href="simular_compra.php?id=5" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

            <article class="course-card" data-category="casa">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1584992236310-6eddd724a4c7?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Panelas">
                </div>
                <div class="course-content">
                    <span class="course-tag">Casa</span>
                    <h3 class="course-title">Jogo Panelas Antiaderente</h3>
                    <p class="course-excerpt">Kit com 5 peças vermelhas. Revestimento cerâmico de alta qualidade.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 3 un.</span>
                        <span class="price">R$ 299,90</span>
                    </div>
                    <a href="simular_compra.php?id=6" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

            <article class="course-card" data-category="acessorios">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1602143407151-511c909f2aa3?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Garrafa">
                </div>
                <div class="course-content">
                    <span class="course-tag">Acessórios</span>
                    <h3 class="course-title">Garrafa Térmica Inox</h3>
                    <p class="course-excerpt">Mantém gelado por 24h e quente por 12h. 500ml.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 15 un.</span>
                        <span class="price">R$ 45,00</span>
                    </div>
                    <a href="simular_compra.php?id=7" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

            <article class="course-card" data-category="eletronicos">
                <div class="course-image">
                    <img src="https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Power Bank">
                </div>
                <div class="course-content">
                    <span class="course-tag">Eletrônicos</span>
                    <h3 class="course-title">Power Bank 10000mAh</h3>
                    <p class="course-excerpt">Carregador portátil universal. Carregamento rápido.</p>
                    <div class="course-meta">
                        <span><i class="fas fa-box"></i> Min: 20 un.</span>
                        <span class="price">R$ 55,00</span>
                    </div>
                    <a href="simular_compra.php?id=8" class="btn-comprar">Comprar / Orçar</a>
                </div>
            </article>

        </div> 
    </div> 
</main>

<footer class="main-footer">
    <div class="container">
        <p class="copyright-text">&copy; 2025 Solutimix. Todos os direitos reservados.</p>
    </div>
</footer>

<script src="script.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const filterBtns = document.querySelectorAll(".filter-btn");
    const cards = document.querySelectorAll(".course-card");

    function filterProducts() {
        const term = searchInput.value.toLowerCase();
        const activeCategory = document.querySelector(".filter-btn.active").getAttribute("data-filter");

        cards.forEach(card => {
            const title = card.querySelector(".course-title").textContent.toLowerCase();
            const category = card.getAttribute("data-category");
            
            const matchesSearch = title.includes(term);
            const matchesCategory = activeCategory === "todos" || category === activeCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Evento de Digitação
    searchInput.addEventListener("input", filterProducts);

    // Evento de Clique nos Botões
    filterBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            // Remove active de todos
            filterBtns.forEach(b => b.classList.remove("active"));
            // Adiciona active ao clicado
            btn.classList.add("active");
            filterProducts();
        });
    });
});
</script>

</body>
</html>
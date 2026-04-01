<?php
session_start();

// --- LÓGICA DA FOTO DE PERFIL (Padrão Solutimix) ---
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

// --- SIMULAÇÃO DE BANCO DE DADOS DE PRODUTOS ---
$produtos_db = [
    1 => [
        'nome' => 'Smartwatch Series 5',
        'preco' => 450.00,
        'img' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 5
    ],
    2 => [
        'nome' => 'Kit Utensílios Premium',
        'preco' => 35.90,
        'img' => 'https://images.unsplash.com/photo-1583947215259-38e31be8751f?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 10
    ],
    3 => [
        'nome' => 'Mochila Executiva',
        'preco' => 89.90,
        'img' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 5
    ],
    4 => [
        'nome' => 'Fone Bluetooth Pro',
        'preco' => 120.00,
        'img' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 20
    ],
    5 => [
        'nome' => 'Caixa de Som Boom',
        'preco' => 180.00,
        'img' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 8
    ],
    6 => [
        'nome' => 'Jogo Panelas Antiaderente',
        'preco' => 299.90,
        'img' => 'https://images.unsplash.com/photo-1584992236310-6eddd724a4c7?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 3
    ],
    7 => [
        'nome' => 'Garrafa Térmica Inox',
        'preco' => 45.00,
        'img' => 'https://images.unsplash.com/photo-1602143407151-511c909f2aa3?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 15
    ],
    8 => [
        'nome' => 'Power Bank 10000mAh',
        'preco' => 55.00,
        'img' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80',
        'minimo' => 20
    ]
];
// Pega o ID da URL, se não tiver, usa o produto 1 como padrão para teste
$id_produto = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Verifica se existe
if (!array_key_exists($id_produto, $produtos_db)) {
    // Se não existir, redireciona ou pega o primeiro
    $id_produto = 1;
}

$produto = $produtos_db[$id_produto];
$is_post = ($_SERVER['REQUEST_METHOD'] === 'POST');

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Solutimix</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* CSS ESPECÍFICO DO CHECKOUT */
        
        .checkout-wrapper {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
            margin-bottom: 4rem;
        }

        /* Coluna Esquerda: Formulários */
        .checkout-form-section {
            background-color: var(--fundo-claro-card);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--sombra-padrao);
            border: 1px solid var(--borda-padrao);
        }

        .checkout-title {
            font-size: 1.5rem;
            color: var(--cor-secundaria);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.2rem;
        }
        
        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--cor-texto);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: #fff;
            font-family: 'Poppins', sans-serif;
            color: var(--cor-texto);
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Coluna Direita: Resumo do Pedido */
        .order-summary-card {
            background-color: var(--fundo-claro-card);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--sombra-padrao);
            border: 1px solid var(--borda-padrao);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .product-preview {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .product-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #f3f4f6;
        }

        .product-info h4 {
            font-size: 1rem;
            color: var(--cor-secundaria);
            margin-bottom: 0.2rem;
        }

        .product-info .unit-price {
            font-size: 0.9rem;
            color: var(--cor-texto-mutado);
        }

        /* Controle de Quantidade */
        .qty-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .qty-selector input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        /* Totais */
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: var(--cor-texto-mutado);
            font-size: 0.95rem;
        }

        .summary-row.total {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px dashed #e5e7eb;
            color: var(--cor-secundaria);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .btn-checkout {
            width: 100%;
            background-color: var(--cor-primaria);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-checkout:hover {
            background-color: var(--cor-primaria-hover);
        }

        /* Sucesso */
        .success-box {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--fundo-claro-card);
            border-radius: 16px;
            box-shadow: var(--sombra-padrao);
            max-width: 600px;
            margin: 2rem auto;
        }
        .success-icon { font-size: 4rem; color: #10b981; margin-bottom: 1.5rem; }

        /* Dark Mode */
        body.dark-mode .checkout-form-section,
        body.dark-mode .order-summary-card,
        body.dark-mode .success-box {
            background-color: var(--fundo-escuro-card);
            border-color: var(--borda-padrao-dark);
        }
        body.dark-mode .checkout-title,
        body.dark-mode .product-info h4,
        body.dark-mode .summary-row.total,
        body.dark-mode .form-group label {
            color: var(--cor-texto-dark);
        }
        body.dark-mode .form-control,
        body.dark-mode .qty-selector input {
            background-color: var(--fundo-escuro-hover);
            border-color: var(--borda-padrao-dark);
            color: var(--cor-texto-dark);
        }
        body.dark-mode .checkout-title { border-bottom-color: #334155; }
        body.dark-mode .summary-row.total { border-top-color: #334155; }
        body.dark-mode .product-preview { border-bottom-color: #334155; }

        @media(max-width: 900px) {
            .checkout-wrapper { grid-template-columns: 1fr; }
            .order-summary-card { position: static; order: -1; margin-bottom: 2rem; }
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
                    <a href="produtos.php" class="nav-link active">Produtos</a>
                    <a href="sobre.php" class="nav-link">Sobre Nós</a>
                    <a href="contato.php" class="nav-link">Contato</a>
                </nav>
            </div>
            <div class="header-actions">
                <?php if (isset($_SESSION['usuario_id'])) : ?>
                    <a href="../login/perfil.php" class="profile-link"><img src="<?php echo $caminho_foto_nav; ?>" class="profile-photo"></a>
                    <a href="../login/logout.php" class="btn-auth">Sair</a>
                <?php else : ?>
                    <a href="../login/login.php" class="btn-auth"><i class="fas fa-user"></i> Entrar</a>
                <?php endif; ?>
                <button id="theme-toggle-btn" class="theme-toggle-btn"><i class="fas fa-sun"></i></button>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
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
        <a href="contato.php" class="mobile-nav-link">Contato</a>
    </nav>
</div>

<main class="page-content">
    
    <div class="container">
        
        <?php if ($is_post) : ?>
            <div class="success-box">
                <i class="fas fa-check-circle success-icon"></i>
                <h2 style="margin-bottom: 1rem;">Pedido Realizado!</h2>
                <p style="color: var(--cor-texto-mutado); margin-bottom: 2rem;">
                    Obrigado por comprar na Solutimix. Seu pedido do <strong><?php echo htmlspecialchars($produto['nome']); ?></strong> foi recebido e está em análise.
                </p>
                <p style="font-size: 0.9rem; margin-bottom: 2rem;">
                    Enviamos os detalhes para o seu e-mail.
                </p>
                <a href="produtos.php" class="btn-primary">Voltar ao Catálogo</a>
            </div>

        <?php else : ?>
            <form action="" method="POST" class="checkout-wrapper">
                
                <div class="checkout-form-section">
                    <h3 class="checkout-title"><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h3>
                    
                    <div class="form-row">
                        <div class="form-group" style="max-width: 150px;">
                            <label>CEP</label>
                            <input type="text" class="form-control" placeholder="00000-000" name="cep" required>
                        </div>
                        <div class="form-group">
                            <label>Endereço</label>
                            <input type="text" class="form-control" placeholder="Rua, Avenida..." name="endereco" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="max-width: 100px;">
                            <label>Número</label>
                            <input type="text" class="form-control" name="numero" required>
                        </div>
                        <div class="form-group">
                            <label>Complemento</label>
                            <input type="text" class="form-control" placeholder="Apto, Bloco..." name="complemento">
                        </div>
                    </div>

                    <h3 class="checkout-title" style="margin-top: 2rem;"><i class="far fa-credit-card"></i> Pagamento</h3>
                    
                    <div class="form-group">
                        <label>Nome no Cartão</label>
                        <input type="text" class="form-control" placeholder="Como impresso no cartão" required>
                    </div>
                    <div class="form-group">
                        <label>Número do Cartão</label>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Validade</label>
                            <input type="text" class="form-control" placeholder="MM/AA" required>
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" class="form-control" placeholder="123" required>
                        </div>
                    </div>
                </div>

                <div class="order-summary-card">
                    <h3 style="margin-bottom: 1.5rem; color: var(--cor-secundaria);">Resumo do Pedido</h3>
                    
                    <div class="product-preview">
                        <img src="<?php echo $produto['img']; ?>" class="product-thumb" alt="Produto">
                        <div class="product-info">
                            <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                            <div class="unit-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?> /un</div>
                            
                            <div class="qty-selector">
                                <label style="font-size: 0.85rem;">Qtd:</label>
                                <input type="number" id="qtdInput" name="quantidade" 
                                       value="<?php echo $produto['minimo']; ?>" 
                                       min="<?php echo $produto['minimo']; ?>" 
                                       onchange="atualizarTotal()">
                            </div>
                            <small style="color: var(--cor-primaria); font-size: 0.8rem;">
                                Mínimo: <?php echo $produto['minimo']; ?> unidades
                            </small>
                        </div>
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">R$ 0,00</span>
                    </div>
                    <div class="summary-row">
                        <span>Frete Estimado</span>
                        <span>R$ 45,00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="totalDisplay">R$ 0,00</span>
                    </div>

                    <button type="submit" class="btn-checkout">
                        Finalizar Compra <i class="fas fa-check"></i>
                    </button>
                    
                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="produtos.php" style="color: var(--cor-texto-mutado); font-size: 0.9rem; text-decoration: none;">Continuar Comprando</a>
                    </div>
                </div>

            </form>
        <?php endif; ?>

    </div>
</main>

<footer class="main-footer">
    <div class="container">
        <p class="copyright-text">&copy; 2025 Solutimix. Todos os direitos reservados.</p>
    </div>
</footer>

<script src="script.js"></script>

<script>
    // Script Simples para atualizar preço em tempo real (Sem Backend)
    const precoUnitario = <?php echo $produto['preco']; ?>;
    const frete = 45.00;

    function atualizarTotal() {
        const qtdInput = document.getElementById('qtdInput');
        let qtd = parseInt(qtdInput.value);
        
        // Garante mínimo
        if (qtd < <?php echo $produto['minimo']; ?>) {
            qtd = <?php echo $produto['minimo']; ?>;
            qtdInput.value = qtd;
        }

        const subtotal = qtd * precoUnitario;
        const total = subtotal + frete;

        document.getElementById('subtotalDisplay').innerText = subtotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        document.getElementById('totalDisplay').innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    // Roda ao carregar
    document.addEventListener("DOMContentLoaded", atualizarTotal);
</script>

</body>
</html>
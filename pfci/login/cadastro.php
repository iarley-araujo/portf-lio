<?php
// Inclui a conexão para poder buscar as áreas de medicina no banco
require_once 'conexao.php';

// Busca as áreas disponíveis para listar no formulário
try {
    $stmt = $pdo->query("SELECT * FROM areas_medicina ORDER BY nome_area ASC");
    $areas_medicina = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $areas_medicina = []; // Se der erro, inicia vazio para não quebrar a página
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro Completo - Pfci Saude</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="signup.css?v=<?php echo time(); ?>">
    
    <style>
        .login-container { max-width: 850px; }
        
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px; }
        .form-col { flex: 1; min-width: 220px; }
        
        .section-divider {
            margin: 30px 0 15px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            color: var(--cor-secundaria);
            font-weight: 600;
            text-align: left;
            font-size: 1.1rem;
        }
        
        /* Grid para os checkboxes das áreas */
        .areas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
            text-align: left;
            margin-bottom: 20px;
            max-height: 200px;
            overflow-y: auto; /* Barra de rolagem */
            padding-right: 5px;
        }
        .area-option {
            font-size: 0.9rem;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: #fff;
            transition: 0.2s;
        }
        .area-option:hover { background-color: #f0f4ff; border-color: #bbccee; }
        .area-option input { accent-color: var(--cor-primaria); transform: scale(1.1); }
        
        /* Select */
        select.form-control {
            width: 100%; padding: 14px; background: #f9fafb; border: 1px solid #e5e7eb;
            border-radius: 8px; color: #333; font-size: 1rem; outline: none;
            height: 52px; 
        }
        
        /* Campo de data */
        input[type="date"] { color: #555; }
    </style>
</head>
<body>

    <header class="header-main">
        <div class="logo">
            <a href="../inicio/index.php"> <img src="../imagens/PFCI2.svg" alt="Logo Pfci Saude"> </a>
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
                <h2>Crie sua Conta</h2>
                <p>Preencha todos os campos para personalizar sua experiência.</p>
            </div>

            <form action="action_page.php" method="post" onsubmit="return validarFormulario()">
                <input type="hidden" name="acao" value="cadastro">

                <div class="section-divider">Dados Pessoais</div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-person icon"></i>
                            <input type="text" name="nome" placeholder="Nome completo" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <select name="tipo_perfil" class="form-control" required>
                            <option value="" disabled selected>Qual seu perfil?</option>
                            <option value="Estudante">Estudante</option>
                            <option value="Professor">Professor</option>
                            <option value="Profissional">Profissional da Saúde</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-card-heading icon"></i>
                            <input type="text" name="cpf" id="cpf" placeholder="CPF (000.000.000-00)" maxlength="14">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-calendar-date icon"></i>
                            <input type="date" name="data_nascimento" required title="Data de Nascimento">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col" style="flex: 100%;">
                        <div class="input-group">
                            <i class="bi bi-envelope icon"></i>
                            <input type="email" name="email" placeholder="Seu melhor e-mail" required>
                        </div>
                    </div>
                </div>

                <div class="section-divider">Localização</div>

                <div class="form-row">
                    <div class="form-col" style="max-width: 180px;">
                        <div class="input-group">
                            <i class="bi bi-geo-alt icon"></i>
                            <input type="text" name="cep" id="cep" placeholder="CEP" onblur="pesquisacep(this.value);">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-map icon"></i>
                            <input type="text" name="endereco" id="endereco" placeholder="Rua / Avenida">
                        </div>
                    </div>
                    <div class="form-col" style="max-width: 120px;">
                        <div class="input-group">
                            <i class="bi bi-house icon"></i>
                            <input type="text" name="numero" id="numero" placeholder="Nº">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-signpost icon"></i>
                            <input type="text" name="bairro" id="bairro" placeholder="Bairro">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-building icon"></i>
                            <input type="text" name="cidade" id="cidade" placeholder="Cidade">
                        </div>
                    </div>
                    <div class="form-col" style="max-width: 80px;">
                        <div class="input-group">
                            <i class="bi bi-flag icon"></i>
                            <input type="text" name="estado" id="estado" placeholder="UF">
                        </div>
                    </div>
                </div>

                <div class="section-divider">Áreas de Interesse (Medicina)</div>
                <p style="text-align: left; color: #666; font-size: 0.9rem; margin-bottom: 10px;">Selecione todas as áreas que você gosta:</p>
                
                <div class="areas-grid">
                    <?php if(count($areas_medicina) > 0): ?>
                        <?php foreach ($areas_medicina as $area): ?>
                            <label class="area-option">
                                <input type="checkbox" name="areas[]" value="<?= $area['id'] ?>">
                                <?= htmlspecialchars($area['nome_area']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #999; font-style: italic;">Nenhuma área cadastrada no sistema.</p>
                    <?php endif; ?>
                </div>

                <div class="section-divider">Segurança</div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-lock icon"></i>
                            <input type="password" name="senha" placeholder="Crie uma senha forte" required id="senha">
                            <i class="bi bi-eye-fill icon icon-eye" id="btn-senha" onclick="mostrarSenha('senha', 'btn-senha')"></i>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="input-group">
                            <i class="bi bi-lock icon"></i>
                            <input type="password" name="confirmaSenha" placeholder="Confirme a senha" required id="confirmaSenha">
                            <i class="bi bi-eye-fill icon icon-eye" id="btn-confirmaSenha" onclick="mostrarSenha('confirmaSenha', 'btn-confirmaSenha')"></i>
                        </div>
                    </div>
                </div>
                
                <div id="alertError" class="alert alert-danger" style="display:none;"></div>

                <div class="btn-group">
                    <button type="submit" class="btn-login">Criar Conta</button>
                    <button type="button" class="btn-login btn-cancelar" onclick="window.location.href='login.php'">
                        Cancelar
                    </button>
                </div>

            </form>
            
            <div class="additional-links">
                <a href="login.php">Já tem conta? Faça login</a>
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
    // --- MODO ESCURO ---
    document.addEventListener("DOMContentLoaded", () => {
        const themeToggleBtn = document.getElementById('theme-toggle-btn');
        const body = document.body;
        const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

        function applyTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                if (themeIcon) { themeIcon.classList.remove('fa-sun'); themeIcon.classList.add('fa-moon'); }
            } else {
                body.classList.remove('dark-mode');
                if (themeIcon) { themeIcon.classList.remove('fa-moon'); themeIcon.classList.add('fa-sun'); }
            }
        }
        const currentTheme = localStorage.getItem('theme') || 'light';
        applyTheme(currentTheme);

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const newTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
    });

    // --- MÁSCARAS E CEP ---
    document.getElementById('cpf').addEventListener('input', function(e) {
        var v = e.target.value.replace(/\D/g, '');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})/, '$1-$2');
        v = v.replace(/(-\d{2})\d+?$/, '$1');
        e.target.value = v;
    });

    document.getElementById('cep').addEventListener('input', function(e) {
        var v = e.target.value.replace(/\D/g, '');
        v = v.replace(/(\d{5})(\d)/, '$1-$2');
        v = v.replace(/(-\d{3})\d+?$/, '$1');
        e.target.value = v;
    });

    function pesquisacep(valor) {
        var cep = valor.replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                // Preenche com "..." enquanto carrega
                document.getElementById('endereco').value="...";
                document.getElementById('bairro').value="...";
                document.getElementById('cidade').value="...";
                document.getElementById('estado').value="...";
                
                var script = document.createElement('script');
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
                document.body.appendChild(script);
            } else {
                alert("Formato de CEP inválido.");
            }
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
            document.getElementById('bairro').value="";
            document.getElementById('cidade').value="";
            document.getElementById('estado').value="";
        }
    }

    // --- MOSTRAR SENHA ---
    function mostrarSenha(idInput, idBtn) {
        const inputPass = document.getElementById(idInput);
        const btnShowPass = document.getElementById(idBtn);
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

    // --- VALIDAÇÃO ---
    function exibirAlerta(id, mensagem) {
        const alertElement = document.getElementById(id);
        alertElement.textContent = mensagem;
        alertElement.style.display = 'block';
    }

    function validarFormulario() {
        const senha = document.getElementById("senha").value;
        const confirmaSenha = document.getElementById("confirmaSenha").value;
        const cpf = document.getElementById("cpf").value;

        if (senha.length < 6) {
            exibirAlerta("alertError", "A senha deve ter no mínimo 6 caracteres.");
            return false;
        }
        if (senha !== confirmaSenha) {
            exibirAlerta("alertError", "As senhas não conferem.");
            return false;
        }
        
        if (cpf && cpf.length < 14) {
            exibirAlerta("alertError", "O CPF parece estar incompleto.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
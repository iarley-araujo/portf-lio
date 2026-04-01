<?php
// Adicionado para mostrar erros em vez da tela branca. Ajuda a diagnosticar.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Caminhos corretos para a sua estrutura de pastas
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

include_once "conexao.php"; 
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $token = bin2hex(random_bytes(50));
            $stmt = $pdo->prepare("INSERT INTO tokens_redefinicao (email, token, data_expiracao) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->execute([$email, $token]);
            
            $link = "https://portfolioeducacionalrgs.com/TCC_2025/DS/pfcisaude/login/red.php?token=$token";
            
            $mail = new PHPMailer(true);
            try {
                // Configurações do servidor (Brevo)
                $mail->isSMTP();
                $mail->Host       = 'smtp-relay.brevo.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = '97458c001@smtp-brevo.com'; 
                $mail->Password   = '7a95yJDRgvbXZPOr';         
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';
                
                $mail->setFrom('pfcisaude@gmail.com', 'PFCI Saude');

                $mail->addReplyTo('pfcisaude@gmail.com', 'PFCI Saude');

                $mail->addAddress($email);

                // Conteúdo do e-mail
                $mail->isHTML(true);
                $mail->Subject = 'Redefinicao de Senha - PFCI Saude';
                $mail->Body    = "Ola,<br><br>Clique no link abaixo para redefinir a sua senha:<br><a href='$link'>$link</a><br><br>Este link e valido por 1 hora.";
                $mail->AltBody = "Ola,\n\nCopie e cole o seguinte link no seu navegador para redefinir a sua senha:\n$link\n\nEste link e valido por 1 hora.";

                $mail->send();
                $mensagem = "<div class='alert alert-success'>Foi enviado um link de redefinicao para o seu e-mail.</div>";
            } catch (Exception $e) {
                // mostrar o erro exato em vez da tela branca
                $mensagem = "<div class='alert alert-danger'>Nao foi possivel enviar o e-mail. Erro: {$mail->ErrorInfo}</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-danger'>E-mail nao encontrado no nosso sistema.</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Por favor, insira um e-mail valido.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Senha | PFCI Saude</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Link para o CSS externo -->
  <link rel="stylesheet" href="recupera.css?v=<?php echo time(); ?>">
</head>
<body>
  <header class="header-main">
    <div class="logo">
        <a href="../inicio/index.php"> 
            <img src="../imagens/PFCI2.svg" alt="Logo Pfci Saude">
        </a>
    </div>
    <nav class="desktop-nav">
      <a href="../inicio/index.php" class="nav-link">Início</a>
      <a href="../inicio/conteudos.php" class="nav-link">Conteúdos</a>
      <a href="../inicio/ferramentas.php" class="nav-link">Ferramentas</a>
      <a href="../inicio/aulas.php" class="nav-link">Aulas</a>
      <a href="../inicio/atlas.php" class="nav-link">Anatomia 3D</a>
      
      <!-- Botão de Alternar Tema -->
      <button id="theme-toggle" class="theme-btn" title="Alternar Tema">
         <i class="bi bi-moon-fill" id="theme-icon"></i>
      </button>
    </nav>
  </header>

  <div class="background-container">
    <div class="login-container">
      <div class="login-header">
        <h2>Recuperar Senha</h2>
        <p>Insira o seu e-mail para continuar.</p>
      </div>
      
      <?php if (!empty($mensagem)) { echo $mensagem; } ?>
      
      <form action="Recupera.php" method="POST">
        <div class="input-group">
          <i class="bi bi-envelope icon"></i>
          <input type="email" name="email" class="form-control" placeholder="Seu e-mail" required>
        </div>
        <button type="submit" class="btn-login">Enviar Instruções</button>
      </form>
      <div class="additional-links">
        <a href="login.php">Voltar para o Login</a>
      </div>
    </div>
  </div>

  <!-- Script para funcionamento do Modo Escuro -->
  <script>
    const toggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    // Função para aplicar o tema visualmente
    function aplicarTema(tema) {
        if (tema === 'dark') {
            body.classList.add('dark-mode');
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill'); 
        } else {
            body.classList.remove('dark-mode');
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill'); 
        }
    }

    // 1. Verificar preferência salva
    const temaSalvo = localStorage.getItem('temaPfci');
    if (temaSalvo) {
        aplicarTema(temaSalvo);
    }
    toggleBtn.addEventListener('click', () => {
        if (body.classList.contains('dark-mode')) {
            aplicarTema('light');
            localStorage.setItem('temaPfci', 'light'); 
        } else {
            aplicarTema('dark');
            localStorage.setItem('temaPfci', 'dark'); 
        }
    });
  </script>
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
</body>
</html>
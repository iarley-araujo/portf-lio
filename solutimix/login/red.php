<?php
include_once "conexao.php";
$mensagem = '';
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
$tokenValido = false;
if ($token) {
    $stmt = $pdo->prepare("SELECT email FROM tokens_redefinicao WHERE token = ? AND data_expiracao > NOW()");
    $stmt->execute([$token]);
    if ($stmt->fetch()) {
        $tokenValido = true;
    } else {
        $mensagem = "<div class='alert alert-danger'>Token invalido ou expirado. Por favor, solicite a redefinicao novamente.</div>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'], $_POST['token'])) {
    $senha = $_POST['senha'];
    $tokenPost = $_POST['token'];
    $stmt = $pdo->prepare("SELECT email FROM tokens_redefinicao WHERE token = ? AND data_expiracao > NOW()");
    $stmt->execute([$tokenPost]);
    $resultado = $stmt->fetch();
    if ($resultado) {
        $email = $resultado['email'];
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmtUpdate = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmtUpdate->execute([$senhaHash, $email]);
        $stmtDelete = $pdo->prepare("DELETE FROM tokens_redefinicao WHERE token = ?");
        $stmtDelete->execute([$tokenPost]);
        $mensagem = "<div class='alert alert-success'>Senha redefinida! <a href='login.php'>Clique aqui para fazer login.</a></div>";
        $tokenValido = false;
    } else {
        $mensagem = "<div class='alert alert-danger'>Ocorreu um erro. Tente novamente.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Redefinir Senha | PFCI Saude</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="recupera.css?v=<?php echo time(); ?>">
</head>
<body>
  <header class="header-main">
    <div class="logo">
        <a href="../inicio/index.html"> 
            <img src="../imagens/pfci.svg" alt="Logo Pfci Saude">
        </a>
    </div>
    <nav class="desktop-nav">
      <a href="../inicio/index.html" class="nav-link">Início</a>
      <a href="../inicio/conteudos.html" class="nav-link">Conteúdos</a>
      <a href="../inicio/ferramentas.html" class="nav-link">Ferramentas</a>
      <a href="../inicio/cursos.html" class="nav-link">Cursos</a>
      <a href="../inicio/atlas-3d.html" class="nav-link">Quiz 3D</a>
    </nav>
  </header>

  <div class="background-container">
    <div class="login-container">
      <div class="login-header">
        <h2>Crie uma Nova Senha</h2>
        <p>Escolha uma senha forte para proteger a sua conta.</p>
      </div>

      <?php if (!empty($mensagem)) { echo $mensagem; } ?>

      <?php if ($tokenValido): ?>
        <form action="red.php?token=<?= htmlspecialchars($token) ?>" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="input-group">
                <i class="bi bi-lock icon"></i>
                <input type="password" name="senha" class="form-control" placeholder="Digite a nova senha" required>
            </div>
            <button type="submit" class="btn-login">Redefinir Senha</button>
        </form>
      <?php else: ?>
        <div class="additional-links">
          <a href="login.php">Voltar para o Login</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
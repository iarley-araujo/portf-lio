<?php
// Conexão com o banco de dados
$host = 'localhost';
$usuario = 'u774820395_pfcisaude';
$senha = 'Ai08@1206/';
$banco = 'u774820395_pfcisaude_bd';

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitiza e coleta os dados
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $senha = trim($_POST["senha"]);

    // Valida e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("E-mail inválido.");
    }

    // Verifica se o e-mail já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "E-mail já cadastrado.";
    } else {
        // Criptografa a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere no banco de dados
        $stmt = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $senhaHash);

        if ($stmt->execute()) {
            echo "Cadastro realizado com sucesso! <a href='login.html'>Entrar</a>";
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>

<?php
$host = "localhost";
$banco = "u774820395_pfcisaude_bd";
$usuario = "u774820395_pfcisaude";
$senha = "Ai08@1206/"; 
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$banco;charset=$charset";
$opcoes = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
} catch (\PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
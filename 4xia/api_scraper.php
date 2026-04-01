<?php
// api_scraper.php - (Investidor10)
header('Content-Type: application/json');
error_reporting(0);

$ticker = isset($_GET['ticker']) ? strtolower(trim($_GET['ticker'])) : '';
if (!$ticker) { echo json_encode(['date' => '']); exit; }

function acessarSite($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_ENCODING, ""); 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

// Tenta URL de FIIs
$url = "https://investidor10.com.br/fiis/$ticker/";
$html = acessarSite($url);

// Se não achar, tenta Ações 
if (strpos($html, 'Página não encontrada') !== false || strlen($html) < 500) {
    $url = "https://investidor10.com.br/acoes/$ticker/";
    $html = acessarSite($url);
}

$dataEncontrada = '';

// Padrão: Tabela com id "table-dividends-history" -> datas dd/mm/aaaa
if (preg_match('/id="table-dividends-history".*?<tbody>(.*?)<\/tbody>/s', $html, $tabela)) {
    if (preg_match_all('/(\d{2}\/\d{2}\/\d{4})/', $tabela[1], $datas)) {
        // Pega a data de PAGAMENTO (índice 1 da linha) ou a data COM (índice 0)
        // Geralmente: <td>Data Com</td> <td>Pagamento</td> <td>Valor</td>
        $dataEncontrada = $datas[0][1] ?? $datas[0][0] ?? '';
    }
}

echo json_encode(['date' => $dataEncontrada]);
?>
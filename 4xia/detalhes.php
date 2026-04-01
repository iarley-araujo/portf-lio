<?php
require 'conecta.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invest 4xia - Análise Detalhada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f1014; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .card { background-color: #1a1b21; border: 1px solid #333; }
        /* Loader giratório */
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3b82f6; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-20 flex flex-col items-center py-6 border-r border-gray-800 hidden md:flex bg-[#0f1014]">
        <div class="flex flex-col gap-6 w-full px-4">
    <a href="index.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 transition rounded-xl p-3 flex justify-center" title="Dashboard">
        <i class="fa-solid fa-chart-pie"></i>
    </a>
    
    <a href="detalhes.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 transition rounded-xl p-3 flex justify-center" title="Pesquisar Ativo">
        <i class="fa-solid fa-search"></i>
    </a>

    <a href="simulador.php" class="sidebar-icon text-gray-400 hover:text-white hover:bg-gray-800 transition rounded-xl p-3 flex justify-center" title="Simulador de Futuro">
        <i class="fa-solid fa-calculator"></i>
    </a>
</div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        
        <div class="max-w-3xl mx-auto mb-10">
            <h1 class="text-3xl font-bold text-white mb-4 text-center">Raio-X de ativos 4xia </h1>
            <div class="relative flex items-center">
                <input type="text" id="searchInput" placeholder="Digite o ticker (ex: VALE3, HGLG11, AAPL)..." 
                    class="w-full bg-[#1a1b21] border border-gray-700 rounded-full py-4 px-6 text-white outline-none focus:border-blue-500 text-lg uppercase shadow-lg">
                <button id="btnSearch" class="absolute right-2 bg-blue-600 hover:bg-blue-500 text-white p-3 rounded-full w-12 h-12 flex items-center justify-center transition">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
            <p class="text-center text-gray-500 text-sm mt-2">Pressione Enter para buscar</p>
        </div>

        <div id="loader" class="hidden flex justify-center my-10"><div class="loader"></div></div>

        <div id="resultArea" class="hidden max-w-6xl mx-auto space-y-6">
            
            <div class="card p-6 rounded-3xl flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h2 class="text-4xl font-bold text-white" id="assetSymbol">--</h2>
                    <p class="text-gray-400 text-lg" id="assetName">Nome da Empresa</p>
                </div>
                <div class="text-right">
                    <h2 class="text-4xl font-bold text-green-400" id="assetPrice">R$ 0,00</h2>
                    <p class="text-sm text-gray-400" id="assetSector">Setor: --</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="card p-6 rounded-3xl lg:col-span-2">
                    <h3 class="text-xl font-bold text-white mb-4">Evolução (Últimos 5 Anos)</h3>
                    <div class="h-80 w-full">
                        <canvas id="historyChart"></canvas>
                    </div>
                </div>

                <div class="card p-6 rounded-3xl">
                    <h3 class="text-xl font-bold text-white mb-4">Sobre</h3>
                    <div class="h-80 overflow-y-auto pr-2 text-sm text-gray-300 leading-relaxed" id="assetDescription">
                        </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="card p-4 rounded-2xl text-center">
                    <p class="text-gray-500 text-xs uppercase">Mínima 52 Semanas</p>
                    <p class="text-white font-bold text-lg" id="statLow">--</p>
                </div>
                <div class="card p-4 rounded-2xl text-center">
                    <p class="text-gray-500 text-xs uppercase">Máxima 52 Semanas</p>
                    <p class="text-white font-bold text-lg" id="statHigh">--</p>
                </div>
                <div class="card p-4 rounded-2xl text-center">
                    <p class="text-gray-500 text-xs uppercase">P/L (P/E)</p>
                    <p class="text-white font-bold text-lg" id="statPE">--</p>
                </div>
                <div class="card p-4 rounded-2xl text-center">
                    <p class="text-gray-500 text-xs uppercase">Recomendação</p>
                    <p class="text-blue-400 font-bold text-lg" id="statRec">--</p>
                </div>
            </div>

        </div>
    </main>

    <script src="detalhes.js"></script>
</body>
</html>
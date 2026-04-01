<?php
require 'conecta.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Se não logou, manda vazar
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvestPro - Simulador de Futuro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f1014; color: #e2e8f0; font-family: 'Inter', sans-serif; }
        .card { background-color: #1a1b21; border: 1px solid #333; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <aside class="w-20 flex flex-col items-center py-6 border-r border-gray-800 hidden md:flex bg-[#0f1014]">
        <div class="mb-8 text-3xl text-white"><i class="fa-solid fa-layer-group"></i></div>
        <div class="flex flex-col gap-6 w-full px-4">
            <a href="index.php" class="sidebar-icon text-gray-400 hover:bg-gray-800 rounded-xl p-3 flex justify-center"><i class="fa-solid fa-chart-pie"></i></a>
            <a href="detalhes.php" class="sidebar-icon text-gray-400 hover:bg-gray-800 rounded-xl p-3 flex justify-center"><i class="fa-solid fa-search"></i></a>
            <a href="simulador.php" class="sidebar-icon active bg-gray-800 text-white rounded-xl p-3 flex justify-center"><i class="fa-solid fa-calculator"></i></a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-white">Simulador de Bola de Neve - 4xia</h1>
            <p class="text-gray-400 text-sm">Projeção baseada na rentabilidade real da sua carteira.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="space-y-6">
                <div class="card p-6 rounded-3xl">
                    <h3 class="text-lg font-bold text-white mb-4">Configurar Projeção</h3>
                    
                    <div class="mb-4">
                        <label class="text-xs text-gray-500 uppercase">Patrimônio Atual (Automático)</label>
                        <div class="text-xl font-bold text-white mb-1" id="currentEquity">R$ 0,00</div>
                        <div class="text-xs text-green-400">DY Médio da sua carteira: <span id="currentDy">0%</span></div>
                    </div>

                    <div class="mb-4">
                        <label class="text-xs text-gray-500 uppercase mb-2 block">Aporte Mensal (R$)</label>
                        <input type="number" id="inputMonthly" value="500" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none">
                    </div>

                    <div class="mb-6">
                        <label class="text-xs text-gray-500 uppercase mb-2 block">Tempo (Anos)</label>
                        <input type="number" id="inputYears" value="10" class="w-full bg-[#0f1014] border border-gray-700 rounded-xl p-3 text-white focus:border-blue-500 outline-none">
                        <input type="range" id="rangeYears" min="1" max="30" value="10" class="w-full mt-2 h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
                    </div>

                    <button id="btnSimulate" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-blue-500/20">
                        Calcular Futuro
                    </button>
                </div>

                <div class="card p-6 rounded-3xl bg-gradient-to-br from-gray-900 to-black border border-gray-800">
                    <h3 class="text-lg font-bold text-white mb-4">Resultado em <span id="labelYears">10</span> Anos</h3>
                    
                    <div class="mb-4">
                        <p class="text-gray-500 text-xs">Patrimônio Acumulado</p>
                        <h2 class="text-3xl font-bold text-green-400" id="resultTotal">R$ 0,00</h2>
                    </div>

                    <div class="flex justify-between items-end border-t border-gray-800 pt-4">
                        <div>
                            <p class="text-gray-500 text-xs">Renda Mensal Passiva</p>
                            <h2 class="text-xl font-bold text-blue-400" id="resultMonthly">R$ 0,00</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 text-xs">Total Investido (Bolso)</p>
                            <p class="text-white font-bold" id="resultInvested">R$ 0,00</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-6 rounded-3xl lg:col-span-2 flex flex-col">
                <h3 class="text-lg font-bold text-white mb-6">Curva de Crescimento</h3>
                <div class="flex-1 min-h-[300px]">
                    <canvas id="simulationChart"></canvas>
                </div>
                <p class="text-center text-xs text-gray-500 mt-4">
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span> Apenas Aportes
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full ml-4 mr-1"></span> Aportes + Dividendos (Juros Compostos)
                </p>
            </div>
        </div>
    </main>

    <script src="simulador.js"></script>
</body>
</html>
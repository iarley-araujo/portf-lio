// ================= CONFIGURAÇÕES =================
// Use as mesmas chaves do script.js
const SUPABASE_URL = 'https://myviyuavjybrxdizfoon.supabase.co';
const SUPABASE_KEY = 'sb_publishable_QTIxmv_LlfoZv-1lN3s9NA_KCvFLZIG'; 
// =================================================

let investClient;
let currentPortfolio = { equity: 0, weightedDy: 0 };
let chartInstance = null;

// Conexão Supabase
try {
    if (window.supabase) {
        investClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_KEY);
    }
} catch (e) { console.error("Erro Supabase", e); }

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Carrega dados reais do banco
    await loadRealData();

    // 2. Configura sliders e botões
    const rangeYears = document.getElementById('rangeYears');
    const inputYears = document.getElementById('inputYears');
    const btnSimulate = document.getElementById('btnSimulate');

    // Sincroniza slider com input
    rangeYears.addEventListener('input', (e) => {
        inputYears.value = e.target.value;
        document.getElementById('labelYears').innerText = e.target.value;
    });
    inputYears.addEventListener('input', (e) => {
        rangeYears.value = e.target.value;
        document.getElementById('labelYears').innerText = e.target.value;
    });

    // Botão Calcular
    btnSimulate.addEventListener('click', calculateSimulation);

    // Roda uma simulação inicial
    calculateSimulation();
});

async function loadRealData() {
    if (!investClient) return;

    const { data, error } = await investClient.from('portfolio').select('*');
    if (error || !data) return;

    let totalEquity = 0;
    let totalIncomeAnnual = 0;

    data.forEach(asset => {
        const value = asset.qty * asset.current_price;
        const income = value * (asset.annual_dy / 100);

        totalEquity += value;
        totalIncomeAnnual += income;
    });

    // Calcula DY Ponderado (Média real da carteira)
    // Se a carteira estiver vazia, assume 10% padrão para não quebrar
    const avgDy = totalEquity > 0 ? (totalIncomeAnnual / totalEquity) * 100 : 10;

    currentPortfolio = {
        equity: totalEquity,
        weightedDy: avgDy
    };

    // Atualiza a tela com os dados atuais
    document.getElementById('currentEquity').innerText = formatCurrency(totalEquity);
    document.getElementById('currentDy').innerText = avgDy.toFixed(2) + '% ao ano';
}

function calculateSimulation() {
    const monthlyContribution = parseFloat(document.getElementById('inputMonthly').value) || 0;
    const years = parseInt(document.getElementById('inputYears').value) || 10;
    const months = years * 12;

    // Taxa mensal aproximada (Raiz 12 da taxa anual)
    // Fórmula: (1 + taxa_anual)^(1/12) - 1
    const annualRate = currentPortfolio.weightedDy / 100;
    const monthlyRate = Math.pow(1 + annualRate, 1/12) - 1;

    let patrimonyCompound = currentPortfolio.equity; // Com juros compostos
    let patrimonySimple = currentPortfolio.equity;   // Só dinheiro do bolso (sem reinvestir)
    
    const labels = [];
    const dataCompound = [];
    const dataSimple = [];

    // Loop mês a mês
    for (let i = 1; i <= months; i++) {
        // Cenário 1: Reinvestindo tudo (Juros Compostos)
        const dividends = patrimonyCompound * monthlyRate;
        patrimonyCompound += dividends + monthlyContribution;

        // Cenário 2: Só guardando (Sem rendimento/Gastando dividendos)
        patrimonySimple += monthlyContribution;

        // Salva dados para o gráfico (a cada ano para não poluir)
        if (i % 12 === 0) {
            labels.push(`Ano ${i/12}`);
            dataCompound.push(patrimonyCompound);
            dataSimple.push(patrimonySimple);
        }
    }

    // Atualiza Card de Resultados
    document.getElementById('resultTotal').innerText = formatCurrency(patrimonyCompound);
    document.getElementById('resultInvested').innerText = formatCurrency(patrimonySimple);
    
    // Renda passiva lá no futuro (Patrimônio Final * Taxa Mensal)
    const futureMonthlyIncome = patrimonyCompound * monthlyRate;
    document.getElementById('resultMonthly').innerText = formatCurrency(futureMonthlyIncome);

    renderChart(labels, dataSimple, dataCompound);
}

function renderChart(labels, dataSimple, dataCompound) {
    const ctx = document.getElementById('simulationChart').getContext('2d');

    if (chartInstance) chartInstance.destroy();

    // Gradiente Verde (Juros Compostos)
    const gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
    gradientGreen.addColorStop(0, 'rgba(34, 197, 94, 0.5)');
    gradientGreen.addColorStop(1, 'rgba(34, 197, 94, 0)');

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Com Reinvestimento (Bola de Neve)',
                    data: dataCompound,
                    borderColor: '#22c55e', // Green 500
                    backgroundColor: gradientGreen,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Sem Reinvestimento (Só Aportes)',
                    data: dataSimple,
                    borderColor: '#3b82f6', // Blue 500
                    borderDash: [5, 5], // Linha tracejada
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.raw);
                        }
                    }
                },
                legend: { display: false } // Já temos legenda manual bonita
            },
            scales: {
                y: {
                    grid: { color: '#334155' },
                    ticks: { 
                        color: '#94a3b8',
                        callback: (val) => 'R$ ' + (val/1000).toFixed(0) + 'k' // Formata eixo Y
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });
}

function formatCurrency(val) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
}
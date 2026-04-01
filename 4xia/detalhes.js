
let chartInstance = null;

const searchInput = document.getElementById('searchInput');
const btnSearch = document.getElementById('btnSearch');
const loader = document.getElementById('loader');
const resultArea = document.getElementById('resultArea');

btnSearch.addEventListener('click', () => performSearch());
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') performSearch();
});

async function performSearch() {
    let ticker = searchInput.value.trim().toUpperCase();
    if (!ticker) return;

    if (!ticker.endsWith('.SA') && !ticker.includes('.') && /[0-9]$/.test(ticker)) {
        ticker += '.SA';
    }

    loader.classList.remove('hidden');
    resultArea.classList.add('hidden'); 

    try {
        await fetchAssetDetails(ticker);
    } catch (error) {
        console.error("Erro fatal:", error);
        alert("Não foi possível carregar os dados. Tente outro ativo.");
    } finally {
        loader.classList.add('hidden');
        resultArea.classList.remove('hidden'); 
    }
}

async function fetchAssetDetails(ticker) {
    const proxy = 'https://corsproxy.io/?';
    
    let chartData = null;
    let metaData = null;

    try {
        const urlChart = `https://query1.finance.yahoo.com/v8/finance/chart/${ticker}?range=5y&interval=1mo`;
        const resChart = await fetch(proxy + encodeURIComponent(urlChart));
        const jsonChart = await resChart.json();
        
        if (jsonChart.chart && jsonChart.chart.result) {
            chartData = jsonChart.chart.result[0];
            metaData = chartData.meta; // Guarda dados básicos (preço, moeda, tipo)
        }
    } catch (e) { console.log("Erro ao baixar gráfico:", e); }

    if (!chartData) throw new Error("Gráfico não encontrado no Yahoo Finance.");

    // --- 2. BUSCA OS DETALHES (Perfil) ---
    let profileData = {};
    try {
        const urlProfile = `https://query1.finance.yahoo.com/v10/finance/quoteSummary/${ticker}?modules=assetProfile,price,summaryDetail,financialData`;
        const resProfile = await fetch(proxy + encodeURIComponent(urlProfile));
        const jsonProfile = await resProfile.json();
        
        if (jsonProfile.quoteSummary && jsonProfile.quoteSummary.result) {
            const raw = jsonProfile.quoteSummary.result[0];
            // Junta os módulos encontrados num objeto só
            profileData = { 
                ...raw.assetProfile, 
                ...raw.price, 
                ...raw.summaryDetail, 
                ...raw.financialData 
            };
        }
    } catch (e) { console.log("Detalhes não encontrados, usando dados básicos."); }

    // --- PREENCHIMENTO INTELIGENTE (Plano B) ---
    
    // NOME E SÍMBOLO
    document.getElementById('assetSymbol').innerText = ticker.replace('.SA', '');
    document.getElementById('assetName').innerText = profileData.longName || metaData.symbol || "Ativo B3";
    
    const currentPrice = profileData.regularMarketPrice?.fmt || metaData.regularMarketPrice?.toFixed(2) || "0.00";
    document.getElementById('assetPrice').innerText = `R$ ${currentPrice}`;
    
    let sectorText = profileData.sector || "Setor não informado";
    if (sectorText === "Setor não informado" && ticker.includes('11.SA')) {
        sectorText = "Fundo de Investimento / ETF";
    }
    document.getElementById('assetSector').innerText = sectorText;

    let descText = profileData.longBusinessSummary;
    
    if (!descText) {
        if (ticker.includes('11.SA')) {
            descText = `Este é um Fundo de Investimento ou ETF negociado na B3 (${ticker.replace('.SA','')}). Fundos imobiliários (FIIs) distribuem rendimentos periódicos isentos de IR para pessoas físicas.`;
        } else {
            descText = `Ação negociada na Bolsa de Valores brasileira (B3). Dados detalhados da empresa não foram fornecidos pelo provedor de dados, mas o ativo segue negociado normalmente.`;
        }
    }
    document.getElementById('assetDescription').innerText = descText;

    // ESTATÍSTICAS
    document.getElementById('statLow').innerText = profileData.fiftyTwoWeekLow?.fmt || '--';
    document.getElementById('statHigh').innerText = profileData.fiftyTwoWeekHigh?.fmt || '--';
    document.getElementById('statPE').innerText = profileData.trailingPE?.fmt || 'N/A'; // FIIs não costumam ter P/L no Yahoo
    
    const rec = profileData.recommendationKey || 'Neutra';
    // Traduzindo recomendação do inglês
    const recMap = { 'buy': 'COMPRA', 'strong_buy': 'COMPRA FORTE', 'hold': 'MANTER', 'sell': 'VENDA', 'strong_sell': 'VENDA FORTE' };
    document.getElementById('statRec').innerText = (recMap[rec] || rec).toUpperCase();

    // Renderiza o Gráfico
    renderChart(chartData);
}

function renderChart(data) {
    const ctx = document.getElementById('historyChart').getContext('2d');
    const timestamps = data.timestamp || [];
    const prices = data.indicators.quote[0].close || [];

    // Limpa dados nulos 
    const cleanData = timestamps.map((ts, i) => ({ x: ts, y: prices[i] })).filter(d => d.y !== null);

    const labels = cleanData.map(d => {
        const date = new Date(d.x * 1000);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear().toString().slice(2)}`;
    });
    const values = cleanData.map(d => d.y);

    if (chartInstance) chartInstance.destroy();

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Preço (R$)',
                data: values,
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
}
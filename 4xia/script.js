// ================= CONFIGURAÇÕES =================
// (As chaves do Supabase ficaram apenas de legado, 
// pois agora o PHP gerencia o banco para segurança)
const SUPABASE_URL = 'https://myviyuavjybrxdizfoon.supabase.co';
const SUPABASE_KEY = 'sb_publishable_QTIxmv_LlfoZv-1lN3s9NA_KCvFLZIG'; 

console.log("Iniciando InvestPro (Modo PHP Backend)..."); 

// --- SISTEMA PRINCIPAL ---
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Configura botões do Modal (Abrir/Fechar)
    const btnOpen = document.getElementById('openModalBtn');
    const modal = document.getElementById('modalOverlay');
    const btnClose = document.getElementById('closeModalBtn');

    if (btnOpen) btnOpen.onclick = () => modal.classList.remove('hidden');
    if (btnClose) btnClose.onclick = () => modal.classList.add('hidden');

    // 2. Carrega a tabela assim que a página abre
    loadPortfolio();

    // 3. Evento: Botão Salvar (Enviar formulário)
    const form = document.getElementById('addAssetForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btnSave = document.getElementById('btnSave');
            btnSave.innerText = "Salvando...";
            btnSave.disabled = true;

            const newAsset = {
                ticker: document.getElementById('inputTicker').value.toUpperCase(),
                qty: parseFloat(document.getElementById('inputQty').value),
                avg_price: parseFloat(document.getElementById('inputAvgPrice').value),
                current_price: parseFloat(document.getElementById('inputCurrentPrice').value || 0),
                annual_dy: parseFloat(document.getElementById('inputDy').value || 0),
                pay_date: document.getElementById('inputDate').value
            };

            await addAssetToDb(newAsset);
            
            // Limpa e fecha modal
            e.target.reset();
            modal.classList.add('hidden');
            btnSave.innerText = "Salvar";
            btnSave.disabled = false;
        });
    }

    // 4. Evento: Busca Automática quando sai do campo Ticker (Blur)
    const inputTicker = document.getElementById('inputTicker');
    if (inputTicker) {
        inputTicker.addEventListener('blur', async (e) => {
            const ticker = e.target.value.toUpperCase();
            if (ticker.length < 3) return;
            
            const loader = document.getElementById('loadingTicker');
            if(loader) loader.classList.remove('hidden');
            
            // Chama a nova função HÍBRIDA
            const data = await fetchYahooFinance(ticker);
            
            if(loader) loader.classList.add('hidden');

            // Sempre libera os campos para edição manual (caso a API falhe)
            document.getElementById('inputCurrentPrice').removeAttribute('readonly');
            document.getElementById('inputDy').removeAttribute('readonly');
            document.getElementById('inputDate').removeAttribute('readonly');

            if (data) {
                document.getElementById('inputCurrentPrice').value = data.price;
                document.getElementById('inputDy').value = data.dy;
                document.getElementById('inputDate').value = data.date;
                
                // Pisca a borda da data se ela estiver vazia, para lembrar de preencher
                if(data.date === "") {
                    document.getElementById('inputDate').focus();
                }
            }
        });
    }
});

// ================= FUNÇÕES DE BUSCA (HÍBRIDA) =================

// Esta função pega PREÇO/DY do Yahoo e DATA do Investidor10 (via PHP)
async function fetchYahooFinance(ticker) {
    console.log(`Iniciando busca híbrida para ${ticker}...`);

    // Prepara as URLs
    const symbolYahoo = ticker.endsWith('.SA') ? ticker : `${ticker}.SA`;
    const proxyUrl = 'https://corsproxy.io/?'; 
    const urlYahoo = `https://query1.finance.yahoo.com/v8/finance/chart/${symbolYahoo}?range=1y&interval=1d&events=div`;
    const urlScraper = `api_scraper.php?ticker=${ticker}`;

    try {
        // DISPARA AS DUAS BUSCAS AO MESMO TEMPO (Parallel Fetch)
        const [resYahoo, resScraper] = await Promise.all([
            fetch(proxyUrl + encodeURIComponent(urlYahoo)),
            fetch(urlScraper)
        ]);

        // --- 1. Processa YAHOO (Confiável para Preço e DY) ---
        let price = 0;
        let dy = 0;

        try {
            const jsonYahoo = await resYahoo.json();
            const resultYahoo = jsonYahoo.chart.result[0];
            const meta = resultYahoo.meta;
            
            price = meta.regularMarketPrice || 0;
            
            // Calcula DY (Soma dividendos 12 meses / Preço)
            let totalDividends = 0;
            if (resultYahoo.events && resultYahoo.events.dividends) {
                const divs = Object.values(resultYahoo.events.dividends);
                divs.forEach(d => totalDividends += d.amount);
            }
            dy = price > 0 ? (totalDividends / price) * 100 : 0;
        } catch (e) {
            console.warn("Erro ao ler Yahoo:", e);
        }

        // --- 2. Processa SCRAPER (Especialista em Data) ---
        let dateInvestidor10 = "";
        try {
            const jsonScraper = await resScraper.json();
            dateInvestidor10 = jsonScraper.date || "";
        } catch (e) {
            console.warn("Erro ao ler Scraper:", e);
        }

        console.log("Yahoo (Preço):", price);
        console.log("Investidor10 (Data):", dateInvestidor10);

        // Retorna a combinação dos melhores dados
        return {
            price: price,
            dy: parseFloat(dy.toFixed(2)),
            date: dateInvestidor10
        };

    } catch (err) {
        console.error("Erro fatal na busca híbrida:", err);
        alert("Erro de conexão ao buscar dados. Tente digitar manualmente.");
        return null;
    }
}

// ================= FUNÇÕES DE BANCO DE DADOS (PHP) =================

// Salvar via PHP
async function addAssetToDb(asset) {
    try {
        const response = await fetch('api_salvar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(asset)
        });

        const result = await response.json();

        if (result.erro) {
            alert("Erro do Servidor: " + result.erro);
        } else {
            loadPortfolio(); // Recarrega a tabela
        }
    } catch (e) {
        console.error(e);
        alert("Erro ao conectar com api_salvar.php");
    }
}

// Deletar via PHP
window.deleteAsset = async function(id) {
    if(confirm("Tem certeza que deseja remover este ativo da carteira?")) {
        try {
            await fetch('api_deletar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            loadPortfolio();
        } catch (e) {
            alert("Erro ao tentar deletar.");
        }
    }
}

// Carregar Lista via PHP
async function loadPortfolio() {
    const loadingDiv = document.getElementById('loadingState');
    if(loadingDiv) loadingDiv.classList.remove('hidden');

    try {
        console.log("Buscando carteira no servidor...");
        
        const response = await fetch('api_ativos.php');
        const data = await response.json();

        // Se a API retornar erro (ex: não logado), redireciona
        if (data.erro && data.erro.includes("logado")) {
            window.location.href = 'login.php';
            return;
        }

        renderTable(data);
    } catch (e) {
        console.error("Erro ao carregar carteira:", e);
    } finally {
        if(loadingDiv) loadingDiv.classList.add('hidden');
    }
}

// ================= RENDERIZAÇÃO DA TABELA =================
function renderTable(portfolio) {
    const tbody = document.getElementById('assetTableBody');
    if(!tbody) return;
    tbody.innerHTML = '';

    // Se o portfólio estiver vazio ou não for array, para aqui
    if (!Array.isArray(portfolio)) return;

    let totalEquity = 0;
    let totalMonthly = 0;
    let totalAnnual = 0;
    let magicSum = 0; 
    let magicCount = 0;

    portfolio.forEach(asset => {
        const invested = asset.qty * asset.current_price;
        const annualInc = invested * (asset.annual_dy / 100);
        const monthlyInc = annualInc / 12;
        
        // Cálculo do Magic Number
        const incomePerShare = (asset.current_price * (asset.annual_dy/100)) / 12;
        const magicNumber = incomePerShare > 0 ? Math.ceil(asset.current_price / incomePerShare) : 0;

        if(magicNumber > 0) { magicSum += magicNumber; magicCount++; }
        
        totalEquity += invested;
        totalMonthly += monthlyInc;
        totalAnnual += annualInc;

        const tr = document.createElement('tr');
        tr.className = "border-b border-gray-800 hover:bg-gray-800/50 transition";
        tr.innerHTML = `
            <td class="py-4 px-2 font-bold text-white">${asset.ticker}</td>
            <td class="py-4 px-2">${asset.qty}</td>
            <td class="py-4 px-2">R$ ${asset.current_price.toFixed(2)}</td>
            <td class="py-4 px-2 text-green-400">${asset.annual_dy}%</td>
            <td class="py-4 px-2">R$ ${invested.toFixed(2)}</td>
            <td class="py-4 px-2 text-green-300">R$ ${monthlyInc.toFixed(2)}</td>
            <td class="py-4 px-2 text-gray-400">${asset.pay_date}</td>
            <td class="py-4 px-2">
                <span class="${asset.qty >= magicNumber ? 'text-green-500 font-bold' : 'text-orange-500'}">
                    ${magicNumber} ${asset.qty >= magicNumber ? '<i class="fa-solid fa-check"></i>' : ''}
                </span>
            </td>
            <td class="py-4 px-2">
                <button onclick="deleteAsset(${asset.id})" class="text-red-500 hover:text-red-400 transition">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Atualiza os cards do topo
    document.getElementById('totalEquity').innerText = totalEquity.toLocaleString('pt-BR', {style:'currency', currency:'BRL'});
    document.getElementById('monthlyIncome').innerText = totalMonthly.toLocaleString('pt-BR', {style:'currency', currency:'BRL'});
    document.getElementById('annualIncome').innerText = totalAnnual.toLocaleString('pt-BR', {style:'currency', currency:'BRL'});
    
    if(document.getElementById('avgMagicNumber')) 
        document.getElementById('avgMagicNumber').innerText = magicCount > 0 ? Math.round(magicSum / magicCount) : 0;
}
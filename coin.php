<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap');

    .crypto-widget-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .crypto-widget-header {
        background: #2b2f3a;
        color: white;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .crypto-widget-header h1 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }
    
    .crypto-widget-update-info {
        font-size: 12px;
        color: #9ca3af;
    }
    
    .crypto-widget-tabs-container {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .crypto-widget-tabs {
        display: flex;
        flex: 1;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    
    .crypto-widget-tabs::-webkit-scrollbar {
        height: 4px;
    }
    
    .crypto-widget-tabs::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .crypto-widget-tabs::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 2px;
    }
    
    .crypto-widget-tab {
        padding: 10px 16px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: 'Noto Sans KR', sans-serif;
    }
    
    .crypto-widget-tab:hover {
        background: #e5e7eb;
    }
    
    .crypto-widget-tab.crypto-widget-active {
        color: #2563eb;
        background: white;
        border-bottom: 2px solid #2563eb;
        margin-bottom: -1px;
    }
    
    .crypto-widget-tab-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        border-left: 1px solid #e5e7eb;
    }
    
    .crypto-widget-collapse-toggle, .crypto-widget-settings-toggle {
        background: rgba(0,0,0,0.05);
        color: #6b7280;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .crypto-widget-collapse-toggle:hover, .crypto-widget-settings-toggle:hover {
        background: rgba(0,0,0,0.1);
        color: #374151;
    }
    
    .crypto-widget-settings-toggle.crypto-widget-active {
        background: #2563eb;
        color: white;
    }
    
    .crypto-widget-price-table-container {
        overflow-x: auto;
    }
    
    .crypto-widget-price-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 600px;
        transition: all 0.3s ease;
    }
    
    .crypto-widget-price-table.crypto-widget-collapsed tbody {
        display: none;
    }
    
    .crypto-widget-price-table.crypto-widget-compact th,
    .crypto-widget-price-table.crypto-widget-compact td {
        padding: 6px 8px;
        font-size: 12px;
    }
    
    .crypto-widget-price-table th {
        background: #f8f9fa;
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        font-size: 12px;
        white-space: nowrap;
    }
    
    .crypto-widget-price-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #f3f4f6;
        font-family: 'Noto Sans KR', sans-serif;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    
    .crypto-widget-price-table tr:hover {
        background: #f9fafb;
    }
    
    .crypto-widget-price-table.crypto-widget-highlight-changes .crypto-widget-change-highlight {
        animation: crypto-widget-highlight 1s ease-out;
    }
    
    @keyframes crypto-widget-highlight {
        0% { background-color: #fef3c7; }
        100% { background-color: transparent; }
    }
    
    .crypto-widget-exchange-name {
        font-weight: 500;
        color: #1f2937;
        font-size: 13px;
    }
    
    .crypto-widget-price {
        font-weight: 400;
        color: #1f2937;
        font-size: 13px;
    }
    
    .crypto-widget-currency-unit {
        font-size: 11px;
        color: #6b7280;
        margin-left: 3px;
    }
    
    .crypto-widget-change-rate {
        font-weight: 500;
        font-size: 12px;
    }
    
    /* 커스텀 색상 적용 */
    .crypto-widget-plus {
        color: var(--crypto-widget-plus-color, #ef4444);
    }
    
    .crypto-widget-minus {
        color: var(--crypto-widget-minus-color, #3b82f6);
    }
    
    .crypto-widget-no-data {
        color: #9ca3af;
        text-align: center;
    }
    
    .crypto-widget-loading {
        text-align: center;
        padding: 30px;
        color: #6b7280;
    }
    
    .crypto-widget-error {
        text-align: center;
        padding: 30px;
        color: #ef4444;
    }
    
    .crypto-widget-spinner {
        border: 2px solid #f3f4f6;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: crypto-widget-spin 1s linear infinite;
        margin: 0 auto 15px;
    }
    
    @keyframes crypto-widget-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .crypto-widget-market-info {
        padding: 12px 16px;
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #6b7280;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .crypto-widget-market-stats {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .crypto-widget-market-value {
        font-weight: 500;
        color: #1f2937;
        margin-left: 4px;
    }
    
    /* 사이드 설정 패널 */
    .crypto-widget-settings-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: none;
        backdrop-filter: blur(2px);
    }
    
    .crypto-widget-settings-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 380px;
        height: 100vh;
        background: white;
        box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        z-index: 1001;
        transition: right 0.3s ease;
        overflow-y: auto;
    }
    
    .crypto-widget-settings-sidebar.crypto-widget-open {
        right: 0;
    }
    
    .crypto-widget-settings-header {
        padding: 16px 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .crypto-widget-settings-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .crypto-widget-settings-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #6b7280;
        padding: 4px;
    }
    
    .crypto-widget-settings-content {
        padding: 20px;
    }
    
    .crypto-widget-settings-section {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .crypto-widget-settings-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .crypto-widget-settings-title {
        font-weight: 600;
        margin-bottom: 12px;
        color: #1f2937;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .crypto-widget-column-list, .crypto-widget-exchange-list {
        list-style: none;
        padding: 0;
        max-height: 200px;
        overflow-y: auto;
        margin: 0;
    }
    
    .crypto-widget-column-item, .crypto-widget-exchange-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 6px;
        cursor: move;
        transition: all 0.2s;
        background: #f9fafb;
    }
    
    .crypto-widget-column-item:hover, .crypto-widget-exchange-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    
    .crypto-widget-column-item.crypto-widget-dragging, .crypto-widget-exchange-item.crypto-widget-dragging {
        opacity: 0.5;
    }
    
    .crypto-widget-item-controls {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .crypto-widget-visibility-toggle {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        transition: all 0.2s;
    }
    
    .crypto-widget-visibility-toggle.crypto-widget-active {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    
    .crypto-widget-drag-handle {
        color: #9ca3af;
        font-size: 14px;
        cursor: move;
    }
    
    .crypto-widget-color-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .crypto-widget-color-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .crypto-widget-color-option:hover {
        background: #f9fafb;
    }
    
    .crypto-widget-color-option input[type="radio"] {
        margin: 0;
    }
    
    .crypto-widget-color-preview {
        width: 18px;
        height: 18px;
        border-radius: 3px;
        border: 1px solid #d1d5db;
        flex-shrink: 0;
    }
    
    .crypto-widget-option-label {
        font-size: 12px;
        flex: 1;
    }
    
    .crypto-widget-checkbox-option {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 6px 0;
    }
    
    .crypto-widget-checkbox-option input {
        margin: 0;
    }
    
    .crypto-widget-reset-btn {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        width: 100%;
        margin-top: 16px;
        transition: background 0.2s;
    }
    
    .crypto-widget-reset-btn:hover {
        background: #dc2626;
    }
    
    /* 모바일 최적화 */
    @media (max-width: 768px) {
        .crypto-widget-header {
            padding: 10px 12px;
        }
        
        .crypto-widget-header h1 {
            font-size: 16px;
        }
        
        .crypto-widget-update-info {
            font-size: 10px;
        }
        
        .crypto-widget-tabs {
            padding: 0 8px;
        }
        
        .crypto-widget-tab {
            padding: 8px 12px;
            font-size: 12px;
        }
        
        .crypto-widget-tab-controls {
            padding: 0 8px;
        }
        
        .crypto-widget-collapse-toggle, .crypto-widget-settings-toggle {
            padding: 4px 6px;
            font-size: 10px;
        }
        
        .crypto-widget-price-table {
            font-size: 11px;
            min-width: 500px;
        }
        
        .crypto-widget-price-table th,
        .crypto-widget-price-table td {
            padding: 6px 8px;
        }
        
        .crypto-widget-market-info {
            padding: 8px 12px;
            font-size: 10px;
        }
        
        .crypto-widget-market-stats {
            gap: 8px;
        }
        
        .crypto-widget-settings-sidebar {
            width: 100%;
            right: -100%;
        }
        
        .crypto-widget-currency-unit {
            font-size: 9px;
        }
        
        .crypto-widget-change-rate {
            font-size: 10px;
        }
    }
    
    @media (max-width: 480px) {
        .crypto-widget-market-stats {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        
        .crypto-widget-price-table {
            min-width: 450px;
        }
        
        .crypto-widget-tab-controls {
            flex-direction: column;
            gap: 4px;
            padding: 4px;
        }
    }
</style>

<div class="crypto-widget-container">
    <div class="crypto-widget-header">
        <h1>실시간 코인 시세</h1>
        <div class="crypto-widget-update-info">
            마지막 업데이트: <span id="cryptoWidgetLastUpdate">-</span>
        </div>
    </div>
    
    <div class="crypto-widget-tabs-container">
        <div class="crypto-widget-tabs" id="cryptoWidgetTabs">
            <button class="crypto-widget-tab crypto-widget-active" data-currency="BTC">BTC</button>
            <button class="crypto-widget-tab" data-currency="ETH">ETH</button>
            <button class="crypto-widget-tab" data-currency="XRP">XRP</button>
            <button class="crypto-widget-tab" data-currency="ETC">ETC</button>
            <button class="crypto-widget-tab" data-currency="TRX">TRX</button>
            <button class="crypto-widget-tab" data-currency="BCH">BCH</button>
            <button class="crypto-widget-tab" data-currency="EOS">EOS</button>
            <button class="crypto-widget-tab" data-currency="ADA">ADA</button>
            <button class="crypto-widget-tab" data-currency="SOL">SOL</button>
            <button class="crypto-widget-tab" data-currency="DOGE">DOGE</button>
        </div>
        <div class="crypto-widget-tab-controls">
            <button class="crypto-widget-collapse-toggle" onclick="cryptoWidgetToggleTable()">
                <span id="cryptoWidgetCollapseIcon">📁</span>
            </button>
            <button class="crypto-widget-settings-toggle" onclick="cryptoWidgetToggleSettings()">
                ⚙️
            </button>
        </div>
    </div>
    
    <div id="cryptoWidgetPriceContainer">
        <div class="crypto-widget-loading">
            <div class="crypto-widget-spinner"></div>
            <p>시세 정보를 불러오는 중...</p>
        </div>
    </div>
    
    <div class="crypto-widget-market-info">
        <div class="crypto-widget-market-stats">
            <div>
                <span>전체 시장:</span>
                <span class="crypto-widget-market-value" id="cryptoWidgetTotalMarketCap">-</span>
            </div>
            <div>
                <span>24H 볼륨:</span>
                <span class="crypto-widget-market-value" id="cryptoWidgetTotalVolume">-</span>
            </div>
            <div>
                <span>비트 점유:</span>
                <span class="crypto-widget-market-value" id="cryptoWidgetBtcDominance">-</span>
            </div>
        </div>
        <div>
            <span>환율:</span>
            <span class="crypto-widget-market-value" id="cryptoWidgetExchangeRate">1,369.85</span>
            <span class="crypto-widget-currency-unit">KRW/USD</span>
        </div>
    </div>
</div>

<!-- 설정 사이드바 -->
<div class="crypto-widget-settings-overlay" id="cryptoWidgetSettingsOverlay" onclick="cryptoWidgetCloseSettings()"></div>
<div class="crypto-widget-settings-sidebar" id="cryptoWidgetSettingsSidebar">
    <div class="crypto-widget-settings-header">
        <h3>⚙️ 설정</h3>
        <button class="crypto-widget-settings-close" onclick="cryptoWidgetCloseSettings()">×</button>
    </div>
    <div class="crypto-widget-settings-content">
        <div class="crypto-widget-settings-section">
            <div class="crypto-widget-settings-title">📊 테이블 컬럼 구성</div>
            <ul class="crypto-widget-column-list" id="cryptoWidgetColumnList">
                <!-- 동적으로 생성됨 -->
            </ul>
        </div>
        
        <div class="crypto-widget-settings-section">
            <div class="crypto-widget-settings-title">🏪 거래소 순서</div>
            <ul class="crypto-widget-exchange-list" id="cryptoWidgetExchangeList">
                <!-- 동적으로 생성됨 -->
            </ul>
        </div>
        
        <div class="crypto-widget-settings-section">
            <div class="crypto-widget-settings-title">🎨 색상 테마</div>
            <div class="crypto-widget-color-options">
                <div class="crypto-widget-color-option">
                    <input type="radio" name="cryptoWidgetColorTheme" value="default" id="cryptoWidgetColorDefault" checked>
                    <div class="crypto-widget-color-preview" style="background: linear-gradient(90deg, #ef4444 50%, #3b82f6 50%);"></div>
                    <label for="cryptoWidgetColorDefault" class="crypto-widget-option-label">기본</label>
                </div>
                <div class="crypto-widget-color-option">
                    <input type="radio" name="cryptoWidgetColorTheme" value="green-red" id="cryptoWidgetColorGreenRed">
                    <div class="crypto-widget-color-preview" style="background: linear-gradient(90deg, #10b981 50%, #ef4444 50%);"></div>
                    <label for="cryptoWidgetColorGreenRed" class="crypto-widget-option-label">초록/빨강</label>
                </div>
                <div class="crypto-widget-color-option">
                    <input type="radio" name="cryptoWidgetColorTheme" value="purple-orange" id="cryptoWidgetColorPurpleOrange">
                    <div class="crypto-widget-color-preview" style="background: linear-gradient(90deg, #8b5cf6 50%, #f97316 50%);"></div>
                    <label for="cryptoWidgetColorPurpleOrange" class="crypto-widget-option-label">보라/주황</label>
                </div>
                <div class="crypto-widget-color-option">
                    <input type="radio" name="cryptoWidgetColorTheme" value="blue-gold" id="cryptoWidgetColorBlueGold">
                    <div class="crypto-widget-color-preview" style="background: linear-gradient(90deg, #3b82f6 50%, #f59e0b 50%);"></div>
                    <label for="cryptoWidgetColorBlueGold" class="crypto-widget-option-label">파랑/금색</label>
                </div>
            </div>
        </div>
        
        <div class="crypto-widget-settings-section">
            <div class="crypto-widget-settings-title">⚙️ 표시 옵션</div>
            <div class="crypto-widget-checkbox-option">
                <input type="checkbox" id="cryptoWidgetAutoCollapse">
                <label for="cryptoWidgetAutoCollapse">자동 접기 모드</label>
            </div>
            <div class="crypto-widget-checkbox-option">
                <input type="checkbox" id="cryptoWidgetCompactMode">
                <label for="cryptoWidgetCompactMode">컴팩트 모드</label>
            </div>
            <div class="crypto-widget-checkbox-option">
                <input type="checkbox" id="cryptoWidgetHighlightChanges">
                <label for="cryptoWidgetHighlightChanges">변동 하이라이트</label>
            </div>
            <button class="crypto-widget-reset-btn" onclick="cryptoWidgetResetSettings()">설정 초기화</button>
        </div>
    </div>
</div>

<script>
    (function() {
        let cryptoWidgetCurrentCurrency = 'BTC';
        let cryptoWidgetRefreshInterval;
        let cryptoWidgetIsLoading = false;
        let cryptoWidgetCoinpanData = null;
        let cryptoWidgetLastUpdateTime = null;
        let cryptoWidgetIsTableCollapsed = false;
        let cryptoWidgetPreviousPrices = {};
        
        // 사용자 설정
        let cryptoWidgetUserSettings = {
            columnOrder: ['exchange', 'price_krw', 'price_usd', 'change_24h', 'korea_premium', 'volume_24h'],
            columnVisibility: {
                'exchange': true,
                'price_krw': true,
                'price_usd': true,
                'change_24h': true,
                'korea_premium': true,
                'volume_24h': true
            },
            exchangeOrder: ['bithumb', 'upbit', 'coinone', 'korbit', 'bitflyer', 'binance', 'bitfinex'],
            exchangeVisibility: {
                'bithumb': true,
                'upbit': true,
                'coinone': true,
                'korbit': true,
                'bitflyer': true,
                'binance': true,
                'bitfinex': true
            },
            colorTheme: 'default',
            autoCollapse: false,
            compactMode: false,
            highlightChanges: false
        };

        // 컬럼 정의
        const cryptoWidgetColumnDefinitions = {
            'exchange': { name: '거래소', icon: '🏪' },
            'price_krw': { name: '실시간 시세(KRW)', icon: '💰' },
            'price_usd': { name: '실시간 시세(USD)', icon: '💵' },
            'change_24h': { name: '24시간 변동률', icon: '📈' },
            'korea_premium': { name: '한국 프리미엄', icon: '🇰🇷' },
            'volume_24h': { name: '거래량', icon: '📊' }
        };

        // 숫자 포맷팅
        function cryptoWidgetFormatNumber(num, decimals = 0) {
            if (typeof num !== 'number') return '-';
            return new Intl.NumberFormat('ko-KR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(num);
        }

        // 큰 숫자 포맷팅
        function cryptoWidgetFormatLargeNumber(num) {
            if (num >= 1000000000000) {
                return '$' + (num / 1000000000000).toFixed(2) + 'T';
            } else if (num >= 1000000000) {
                return '$' + (num / 1000000000).toFixed(2) + 'B';
            } else if (num >= 1000000) {
                return '$' + (num / 1000000).toFixed(2) + 'M';
            }
            return '$' + cryptoWidgetFormatNumber(num);
        }

        // API 호출
        async function cryptoWidgetFetchCoinpanData() {
            try {
                const response = await fetch('coinpan_api.php?action=all');
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || 'API 호출 실패');
                }
                
                return result.data;
            } catch (error) {
                console.error('API 호출 실패:', error);
                return null;
            }
        }

        // 시장 정보 업데이트
        function cryptoWidgetUpdateMarketInfo(data) {
            if (data && data.market_info) {
                const market = data.market_info;
                document.getElementById('cryptoWidgetTotalMarketCap').textContent = cryptoWidgetFormatLargeNumber(market.total_market_cap_usd);
                document.getElementById('cryptoWidgetTotalVolume').textContent = cryptoWidgetFormatLargeNumber(market.total_24h_volume_usd);
                document.getElementById('cryptoWidgetBtcDominance').textContent = market.bitcoin_percentage.toFixed(2) + '%';
            }
            
            if (data && data.exchange_rates) {
                document.getElementById('cryptoWidgetExchangeRate').textContent = cryptoWidgetFormatNumber(data.exchange_rates.usd_to_krw, 2);
            }
        }

        // 컬럼 데이터 생성
        function cryptoWidgetGenerateColumnData(columnKey, coinData, symbol, exchangeKey) {
            const currentPrice = coinData.price_krw;
            const previousPrice = cryptoWidgetPreviousPrices[exchangeKey + '_' + symbol];
            const hasChanged = previousPrice && previousPrice !== currentPrice;
            const highlightClass = cryptoWidgetUserSettings.highlightChanges && hasChanged ? 'crypto-widget-change-highlight' : '';
            
            switch (columnKey) {
                case 'exchange':
                    const exchangeNames = {
                        'bithumb': '빗썸', 'upbit': '업비트', 'coinone': '코인원', 'korbit': '코빗',
                        'bitflyer': '플라이어', 'binance': '바이낸스', 'bitfinex': '파이넥스'
                    };
                    return `<td class="crypto-widget-exchange-name ${highlightClass}">${exchangeNames[exchangeKey]}</td>`;
                
                case 'price_krw':
                    return `<td class="crypto-widget-price ${highlightClass}">${cryptoWidgetFormatNumber(coinData.price_krw)}<span class="crypto-widget-currency-unit">KRW</span></td>`;
                
                case 'price_usd':
                    return `<td class="crypto-widget-price ${highlightClass}">${cryptoWidgetFormatNumber(coinData.price_usd, 2)}<span class="crypto-widget-currency-unit">USD</span></td>`;
                
                case 'change_24h':
                    const changeClass = coinData.change_24h_percent >= 0 ? 'crypto-widget-plus' : 'crypto-widget-minus';
                    const changeSymbol = coinData.change_24h_percent >= 0 ? '▲' : '▼';
                    return `<td class="crypto-widget-change-rate ${changeClass} ${highlightClass}">
                        ${changeSymbol} ${cryptoWidgetFormatNumber(Math.abs(coinData.change_24h))}
                        <span style="font-size: 11px;">(${coinData.change_24h_percent.toFixed(2)}%)</span>
                    </td>`;
                
                case 'korea_premium':
                    if (coinData.korea_premium_percent !== 0) {
                        const premiumClass = coinData.korea_premium_percent >= 0 ? 'crypto-widget-plus' : 'crypto-widget-minus';
                        const premiumSign = coinData.korea_premium_percent >= 0 ? '+' : '';
                        return `<td class="crypto-widget-price ${highlightClass}">
                            <span class="${premiumClass}">
                                ${premiumSign}${cryptoWidgetFormatNumber(coinData.korea_premium)}
                                <span style="font-size: 11px;">(${premiumSign}${coinData.korea_premium_percent.toFixed(2)}%)</span>
                            </span>
                        </td>`;
                    }
                    return `<td class="crypto-widget-no-data ${highlightClass}">-</td>`;
                
                case 'volume_24h':
                    return `<td class="crypto-widget-price ${highlightClass}">${cryptoWidgetFormatNumber(coinData.volume_24h, 2)}<span class="crypto-widget-currency-unit">${symbol}</span></td>`;
                
                default:
                    return `<td class="crypto-widget-no-data ${highlightClass}">-</td>`;
            }
        }

        // 가격 표시
        function cryptoWidgetDisplayPrices(symbol) {
            const container = document.getElementById('cryptoWidgetPriceContainer');
            
            if (!cryptoWidgetCoinpanData || !cryptoWidgetCoinpanData.prices) {
                container.innerHTML = '<div class="crypto-widget-error">데이터를 불러올 수 없습니다.</div>';
                return;
            }

            // 이전 가격 저장 (하이라이트용)
            if (cryptoWidgetUserSettings.highlightChanges) {
                cryptoWidgetUserSettings.exchangeOrder.forEach(exchangeKey => {
                    const exchangeData = cryptoWidgetCoinpanData.prices[exchangeKey];
                    const coinData = exchangeData && exchangeData.coins[symbol];
                    if (coinData && coinData.available) {
                        cryptoWidgetPreviousPrices[exchangeKey + '_' + symbol] = coinData.price_krw;
                    }
                });
            }

            // 테이블 헤더 구성
            let headerHtml = '';
            cryptoWidgetUserSettings.columnOrder.forEach(columnKey => {
                if (cryptoWidgetUserSettings.columnVisibility[columnKey] && cryptoWidgetColumnDefinitions[columnKey]) {
                    const column = cryptoWidgetColumnDefinitions[columnKey];
                    headerHtml += `<th>${column.icon} ${column.name}</th>`;
                }
            });

            const tableClasses = [
                'crypto-widget-price-table',
                cryptoWidgetIsTableCollapsed ? 'crypto-widget-collapsed' : '',
                cryptoWidgetUserSettings.compactMode ? 'crypto-widget-compact' : '',
                cryptoWidgetUserSettings.highlightChanges ? 'crypto-widget-highlight-changes' : ''
            ].filter(Boolean).join(' ');

            let html = `
                <div class="crypto-widget-price-table-container">
                    <table class="${tableClasses}">
                        <thead>
                            <tr>${headerHtml}</tr>
                        </thead>
                        <tbody>
            `;

            cryptoWidgetUserSettings.exchangeOrder.forEach(exchangeKey => {
                if (!cryptoWidgetUserSettings.exchangeVisibility[exchangeKey]) return;
                
                const exchangeData = cryptoWidgetCoinpanData.prices[exchangeKey];
                const coinData = exchangeData && exchangeData.coins[symbol];
                
                if (coinData && coinData.available) {
                    let rowHtml = '';
                    cryptoWidgetUserSettings.columnOrder.forEach(columnKey => {
                        if (cryptoWidgetUserSettings.columnVisibility[columnKey] && cryptoWidgetColumnDefinitions[columnKey]) {
                            rowHtml += cryptoWidgetGenerateColumnData(columnKey, coinData, symbol, exchangeKey);
                        }
                    });
                    html += `<tr>${rowHtml}</tr>`;
                } else {
                    // 데이터 없는 경우
                    let emptyCells = '';
                    cryptoWidgetUserSettings.columnOrder.forEach(columnKey => {
                        if (cryptoWidgetUserSettings.columnVisibility[columnKey] && cryptoWidgetColumnDefinitions[columnKey]) {
                            if (columnKey === 'exchange') {
                                const exchangeNames = {
                                    'bithumb': '빗썸', 'upbit': '업비트', 'coinone': '코인원', 'korbit': '코빗',
                                    'bitflyer': '플라이어', 'binance': '바이낸스', 'bitfinex': '파이넥스'
                                };
                                emptyCells += `<td class="crypto-widget-exchange-name">${exchangeNames[exchangeKey]}</td>`;
                            } else {
                                emptyCells += '<td class="crypto-widget-no-data">-</td>';
                            }
                        }
                    });
                    html += `<tr>${emptyCells}</tr>`;
                }
            });

            html += '</tbody></table></div>';
            container.innerHTML = html;
            
            // 접기 아이콘 업데이트
            document.getElementById('cryptoWidgetCollapseIcon').textContent = cryptoWidgetIsTableCollapsed ? '📂' : '📁';
        }

        // 데이터 새로고침
        async function cryptoWidgetRefreshAllPrices() {
            if (cryptoWidgetIsLoading) return;
            
            cryptoWidgetIsLoading = true;
            
            try {
                if (!cryptoWidgetCoinpanData) {
                    document.getElementById('cryptoWidgetPriceContainer').innerHTML = `
                        <div class="crypto-widget-loading">
                            <div class="crypto-widget-spinner"></div>
                            <p>시세 정보를 불러오는 중...</p>
                        </div>
                    `;
                }
                
                cryptoWidgetCoinpanData = await cryptoWidgetFetchCoinpanData();
                
                if (cryptoWidgetCoinpanData) {
                    cryptoWidgetUpdateMarketInfo(cryptoWidgetCoinpanData);
                    cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
                    cryptoWidgetLastUpdateTime = new Date().toLocaleTimeString('ko-KR');
                    document.getElementById('cryptoWidgetLastUpdate').textContent = cryptoWidgetLastUpdateTime;
                } else {
                    document.getElementById('cryptoWidgetPriceContainer').innerHTML = 
                        '<div class="crypto-widget-error">API 연결에 실패했습니다. 백엔드를 확인해주세요.</div>';
                }
            } catch (error) {
                console.error('데이터 새로고침 실패:', error);
                document.getElementById('cryptoWidgetPriceContainer').innerHTML = 
                    '<div class="crypto-widget-error">데이터를 불러올 수 없습니다.</div>';
            } finally {
                cryptoWidgetIsLoading = false;
            }
        }

        // 설정 관련 함수들
        function cryptoWidgetLoadSettings() {
            const saved = localStorage.getItem('cryptoWidgetSettings');
            if (saved) {
                const savedSettings = JSON.parse(saved);
                
                // 저장된 설정에서 유효하지 않은 컬럼 제거
                if (savedSettings.columnOrder) {
                    savedSettings.columnOrder = savedSettings.columnOrder.filter(col => cryptoWidgetColumnDefinitions[col]);
                }
                
                // 누락된 새 컬럼 추가
                Object.keys(cryptoWidgetColumnDefinitions).forEach(col => {
                    if (!savedSettings.columnOrder || !savedSettings.columnOrder.includes(col)) {
                        if (!savedSettings.columnOrder) savedSettings.columnOrder = [];
                        savedSettings.columnOrder.push(col);
                    }
                    if (!savedSettings.columnVisibility || savedSettings.columnVisibility[col] === undefined) {
                        if (!savedSettings.columnVisibility) savedSettings.columnVisibility = {};
                        savedSettings.columnVisibility[col] = true;
                    }
                });
                
                cryptoWidgetUserSettings = { ...cryptoWidgetUserSettings, ...savedSettings };
            }
            cryptoWidgetApplySettings();
        }
        
        function cryptoWidgetSaveSettings() {
            localStorage.setItem('cryptoWidgetSettings', JSON.stringify(cryptoWidgetUserSettings));
        }
        
        function cryptoWidgetApplySettings() {
            // 색상 테마 적용
            const colorThemes = {
                'default': { plus: '#ef4444', minus: '#3b82f6' },
                'green-red': { plus: '#10b981', minus: '#ef4444' },
                'purple-orange': { plus: '#8b5cf6', minus: '#f97316' },
                'blue-gold': { plus: '#3b82f6', minus: '#f59e0b' }
            };
            
            const theme = colorThemes[cryptoWidgetUserSettings.colorTheme];
            document.documentElement.style.setProperty('--crypto-widget-plus-color', theme.plus);
            document.documentElement.style.setProperty('--crypto-widget-minus-color', theme.minus);
            
            // 설정 UI 업데이트
            document.getElementById('cryptoWidgetAutoCollapse').checked = cryptoWidgetUserSettings.autoCollapse;
            document.getElementById('cryptoWidgetCompactMode').checked = cryptoWidgetUserSettings.compactMode;
            document.getElementById('cryptoWidgetHighlightChanges').checked = cryptoWidgetUserSettings.highlightChanges;
            document.querySelector(`input[value="${cryptoWidgetUserSettings.colorTheme}"]`).checked = true;
            
            // 자동 접기 모드
            if (cryptoWidgetUserSettings.autoCollapse) {
                cryptoWidgetIsTableCollapsed = true;
            }
        }

        // 전역 함수들 (onclick에서 사용)
        window.cryptoWidgetToggleTable = function() {
            cryptoWidgetIsTableCollapsed = !cryptoWidgetIsTableCollapsed;
            cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
        };

        window.cryptoWidgetToggleSettings = function() {
            const overlay = document.getElementById('cryptoWidgetSettingsOverlay');
            const sidebar = document.getElementById('cryptoWidgetSettingsSidebar');
            const toggle = document.querySelector('.crypto-widget-settings-toggle');
            
            const isOpen = sidebar.classList.contains('crypto-widget-open');
            
            if (isOpen) {
                cryptoWidgetCloseSettings();
            } else {
                overlay.style.display = 'block';
                sidebar.classList.add('crypto-widget-open');
                toggle.classList.add('crypto-widget-active');
            }
        };

        window.cryptoWidgetCloseSettings = function() {
            const overlay = document.getElementById('cryptoWidgetSettingsOverlay');
            const sidebar = document.getElementById('cryptoWidgetSettingsSidebar');
            const toggle = document.querySelector('.crypto-widget-settings-toggle');
            
            overlay.style.display = 'none';
            sidebar.classList.remove('crypto-widget-open');
            toggle.classList.remove('crypto-widget-active');
        };

        window.cryptoWidgetToggleColumnVisibility = function(columnKey) {
            cryptoWidgetUserSettings.columnVisibility[columnKey] = !cryptoWidgetUserSettings.columnVisibility[columnKey];
            cryptoWidgetUpdateColumnList();
            cryptoWidgetSaveSettings();
            cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
        };

        window.cryptoWidgetToggleExchangeVisibility = function(exchangeKey) {
            cryptoWidgetUserSettings.exchangeVisibility[exchangeKey] = !cryptoWidgetUserSettings.exchangeVisibility[exchangeKey];
            cryptoWidgetUpdateExchangeList();
            cryptoWidgetSaveSettings();
            cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
        };

        window.cryptoWidgetResetSettings = function() {
            if (confirm('모든 설정을 초기화하시겠습니까?')) {
                localStorage.removeItem('cryptoWidgetSettings');
                cryptoWidgetUserSettings = {
                    columnOrder: ['exchange', 'price_krw', 'price_usd', 'change_24h', 'korea_premium', 'volume_24h'],
                    columnVisibility: {
                        'exchange': true, 'price_krw': true, 'price_usd': true, 'change_24h': true,
                        'korea_premium': true, 'volume_24h': true
                    },
                    exchangeOrder: ['bithumb', 'upbit', 'coinone', 'korbit', 'bitflyer', 'binance', 'bitfinex'],
                    exchangeVisibility: {
                        'bithumb': true, 'upbit': true, 'coinone': true, 'korbit': true,
                        'bitflyer': true, 'binance': true, 'bitfinex': true
                    },
                    colorTheme: 'default',
                    autoCollapse: false,
                    compactMode: false,
                    highlightChanges: false
                };
                cryptoWidgetIsTableCollapsed = false;
                cryptoWidgetPreviousPrices = {};
                cryptoWidgetApplySettings();
                cryptoWidgetUpdateColumnList();
                cryptoWidgetUpdateExchangeList();
                cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
            }
        };

        // 설정 패널 초기화
        function cryptoWidgetInitializeSettingsPanel() {
            cryptoWidgetUpdateColumnList();
            cryptoWidgetUpdateExchangeList();
            
            // 색상 테마 변경 이벤트
            document.querySelectorAll('input[name="cryptoWidgetColorTheme"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    cryptoWidgetUserSettings.colorTheme = e.target.value;
                    cryptoWidgetApplySettings();
                    cryptoWidgetSaveSettings();
                });
            });
            
            // 표시 옵션 변경 이벤트
            ['cryptoWidgetAutoCollapse', 'cryptoWidgetCompactMode', 'cryptoWidgetHighlightChanges'].forEach(option => {
                const optionKey = option.replace('cryptoWidget', '');
                const lowerCaseKey = optionKey.charAt(0).toLowerCase() + optionKey.slice(1);
                
                document.getElementById(option).addEventListener('change', (e) => {
                    cryptoWidgetUserSettings[lowerCaseKey] = e.target.checked;
                    if (lowerCaseKey === 'autoCollapse' && e.target.checked) {
                        cryptoWidgetIsTableCollapsed = true;
                    }
                    cryptoWidgetSaveSettings();
                    cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
                });
            });
        }

        // 컬럼 리스트 업데이트
        function cryptoWidgetUpdateColumnList() {
            const list = document.getElementById('cryptoWidgetColumnList');
            list.innerHTML = '';
            
            cryptoWidgetUserSettings.columnOrder.forEach((columnKey) => {
                const column = cryptoWidgetColumnDefinitions[columnKey];
                if (!column) {
                    console.warn(`컬럼 정의를 찾을 수 없습니다: ${columnKey}`);
                    return;
                }
                
                const li = document.createElement('li');
                li.className = 'crypto-widget-column-item';
                li.draggable = true;
                li.dataset.column = columnKey;
                
                li.innerHTML = `
                    <span>${column.icon} ${column.name}</span>
                    <div class="crypto-widget-item-controls">
                        <div class="crypto-widget-visibility-toggle ${cryptoWidgetUserSettings.columnVisibility[columnKey] ? 'crypto-widget-active' : ''}" 
                             onclick="cryptoWidgetToggleColumnVisibility('${columnKey}')">${cryptoWidgetUserSettings.columnVisibility[columnKey] ? '✓' : ''}</div>
                        <span class="crypto-widget-drag-handle">⋮⋮</span>
                    </div>
                `;
                
                cryptoWidgetSetupDragAndDrop(li, columnKey, 'column');
                list.appendChild(li);
            });
        }

        // 거래소 리스트 업데이트
        function cryptoWidgetUpdateExchangeList() {
            const list = document.getElementById('cryptoWidgetExchangeList');
            const exchangeNames = {
                'bithumb': '빗썸', 'upbit': '업비트', 'coinone': '코인원', 'korbit': '코빗',
                'bitflyer': '플라이어', 'binance': '바이낸스', 'bitfinex': '파이넥스'
            };
            
            list.innerHTML = '';
            cryptoWidgetUserSettings.exchangeOrder.forEach((exchangeKey) => {
                const li = document.createElement('li');
                li.className = 'crypto-widget-exchange-item';
                li.draggable = true;
                li.dataset.exchange = exchangeKey;
                
                li.innerHTML = `
                    <span>🏪 ${exchangeNames[exchangeKey]}</span>
                    <div class="crypto-widget-item-controls">
                        <div class="crypto-widget-visibility-toggle ${cryptoWidgetUserSettings.exchangeVisibility[exchangeKey] ? 'crypto-widget-active' : ''}" 
                             onclick="cryptoWidgetToggleExchangeVisibility('${exchangeKey}')">${cryptoWidgetUserSettings.exchangeVisibility[exchangeKey] ? '✓' : ''}</div>
                        <span class="crypto-widget-drag-handle">⋮⋮</span>
                    </div>
                `;
                
                cryptoWidgetSetupDragAndDrop(li, exchangeKey, 'exchange');
                list.appendChild(li);
            });
        }

        // 드래그 앤 드롭 설정
        function cryptoWidgetSetupDragAndDrop(element, key, type) {
            element.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', key);
                element.classList.add('crypto-widget-dragging');
            });
            
            element.addEventListener('dragend', () => {
                element.classList.remove('crypto-widget-dragging');
            });
            
            element.addEventListener('dragover', (e) => {
                e.preventDefault();
            });
            
            element.addEventListener('drop', (e) => {
                e.preventDefault();
                const draggedKey = e.dataTransfer.getData('text/plain');
                const orderArray = type === 'column' ? cryptoWidgetUserSettings.columnOrder : cryptoWidgetUserSettings.exchangeOrder;
                const targetIndex = orderArray.indexOf(key);
                const draggedIndex = orderArray.indexOf(draggedKey);
                
                // 배열 순서 변경
                orderArray.splice(draggedIndex, 1);
                orderArray.splice(targetIndex, 0, draggedKey);
                
                if (type === 'column') {
                    cryptoWidgetUpdateColumnList();
                } else {
                    cryptoWidgetUpdateExchangeList();
                }
                cryptoWidgetSaveSettings();
                cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
            });
        }

        // 탭 변경 처리
        document.getElementById('cryptoWidgetTabs').addEventListener('click', (e) => {
            if (e.target.classList.contains('crypto-widget-tab')) {
                document.querySelectorAll('.crypto-widget-tab').forEach(tab => {
                    tab.classList.remove('crypto-widget-active');
                });
                e.target.classList.add('crypto-widget-active');
                cryptoWidgetCurrentCurrency = e.target.dataset.currency;
                
                if (cryptoWidgetCoinpanData) {
                    cryptoWidgetDisplayPrices(cryptoWidgetCurrentCurrency);
                }
            }
        });

        // 초기화
        cryptoWidgetLoadSettings();
        cryptoWidgetInitializeSettingsPanel();
        cryptoWidgetRefreshAllPrices();
        
        // 1분마다 자동 새로고침
        cryptoWidgetRefreshInterval = setInterval(cryptoWidgetRefreshAllPrices, 60000);

        // 페이지 떠날 때 정리
        window.addEventListener('beforeunload', () => {
            if (cryptoWidgetRefreshInterval) {
                clearInterval(cryptoWidgetRefreshInterval);
            }
        });
    })();
</script>
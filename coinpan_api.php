<?php
/**
 * CoinPan API 프록시 서버
 * CoinPan API를 통한 실시간 암호화폐 시세 조회
 */

// ===================================
// 보안: 외부 접근 차단
// ===================================

// 현재 서버의 도메인 자동 감지
$server_host = $_SERVER['HTTP_HOST'] ?? '';
$server_name = $_SERVER['SERVER_NAME'] ?? '';

// 허용된 도메인 설정 (자동 감지 + 수동 추가)
$allowed_domains = [
    'localhost',
    '127.0.0.1',
    $server_host,
    $server_name,
    // 추가 도메인이 필요한 경우 아래에 추가
    // 'yourdomain.com',
    // 'www.yourdomain.com'
];

// 중복 제거
$allowed_domains = array_unique(array_filter($allowed_domains));

// 현재 요청의 출처 확인
$request_origin = $_SERVER['HTTP_HOST'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';

// 1. 직접 접근 차단 (Referer 체크) - 단, 같은 도메인은 허용
if (empty($referer)) {
    // 같은 서버에서의 요청인지 확인
    $is_same_server = false;
    if (in_array($remote_addr, ['127.0.0.1', '::1']) || $remote_addr === $_SERVER['SERVER_ADDR']) {
        $is_same_server = true;
    }
    
    if (!$is_same_server) {
        http_response_code(403);
        die(json_encode(['error' => 'Direct access forbidden'], JSON_UNESCAPED_UNICODE));
    }
}

// 2. Referer 도메인 체크
if (!empty($referer)) {
    $referer_host = parse_url($referer, PHP_URL_HOST);
    if (!in_array($referer_host, $allowed_domains)) {
        http_response_code(403);
        die(json_encode(['error' => 'Access denied from this domain: ' . $referer_host], JSON_UNESCAPED_UNICODE));
    }
}

// 3. AJAX 요청만 허용 (선택적 - 너무 엄격하면 주석처리)
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    // 개발 중이거나 문제가 있으면 이 부분을 주석처리하세요
    // http_response_code(403);
    // die(json_encode(['error' => 'Only AJAX requests allowed'], JSON_UNESCAPED_UNICODE));
}

// 4. User-Agent 체크 (봇 차단)
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$bot_patterns = [
    'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java'
];

foreach ($bot_patterns as $pattern) {
    if (stripos($user_agent, $pattern) !== false) {
        http_response_code(403);
        die(json_encode(['error' => 'Bot access denied'], JSON_UNESCAPED_UNICODE));
    }
}

// 5. Rate Limiting (IP당 분당 요청 제한)
session_start();
$ip_key = 'api_limit_' . md5($remote_addr);
$current_time = time();
$limit_per_minute = 60; // 분당 최대 60회로 증가 (위젯 사용 고려)

if (!isset($_SESSION[$ip_key])) {
    $_SESSION[$ip_key] = [
        'count' => 1,
        'reset_time' => $current_time + 60
    ];
} else {
    if ($current_time > $_SESSION[$ip_key]['reset_time']) {
        $_SESSION[$ip_key] = [
            'count' => 1,
            'reset_time' => $current_time + 60
        ];
    } else {
        $_SESSION[$ip_key]['count']++;
        
        if ($_SESSION[$ip_key]['count'] > $limit_per_minute) {
            http_response_code(429);
            die(json_encode([
                'error' => 'Too many requests',
                'retry_after' => $_SESSION[$ip_key]['reset_time'] - $current_time
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}

// ===================================
// 원래 코드 시작
// ===================================

header('Content-Type: application/json; charset=utf-8');
// CORS 헤더는 같은 도메인에서만 허용
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array(parse_url($origin, PHP_URL_HOST), $allowed_domains)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
}

// OPTIONS 요청 처리 (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

class CoinPanAPI {
    private $cache_dir = './cache/';
    private $cache_time = 5; // 5초 캐시
    private $debug = false;
    
    public function __construct() {
        // 캐시 디렉토리 생성
        if (!file_exists($this->cache_dir)) {
            mkdir($this->cache_dir, 0777, true);
        }
    }
    
    /**
     * 캐시 확인
     */
    private function getCache($key) {
        $cache_file = $this->cache_dir . $key . '.json';
        if (file_exists($cache_file)) {
            $mtime = filemtime($cache_file);
            if (time() - $mtime < $this->cache_time) {
                return json_decode(file_get_contents($cache_file), true);
            }
        }
        return null;
    }
    
    /**
     * 캐시 저장
     */
    private function setCache($key, $data) {
        $cache_file = $this->cache_dir . $key . '.json';
        file_put_contents($cache_file, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * CoinPan API 호출
     */
    private function fetchCoinPanData() {
        // 현재 타임스탬프 생성
        $timestamp = time();
        $url = "https://api.coinpan.com/default.json?ts={$timestamp}&calibrate=1";
        
        if ($this->debug) {
            error_log("CoinPan API 호출: " . $url);
        }
        
        $ch = curl_init();
        
        // CURL 옵션 설정
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json, text/plain, */*',
                'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Referer: https://coinpan.com/',
                'Origin: https://coinpan.com'
            ],
            CURLOPT_ENCODING => '', // 자동 압축 해제
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        curl_close($ch);
        
        // 디버그 정보
        if ($this->debug) {
            error_log("HTTP Code: {$http_code}");
            error_log("CURL Error: " . $error);
            error_log("Response Length: " . strlen($response));
            error_log("Content Type: " . $info['content_type']);
        }
        
        // 에러 체크
        if ($error) {
            throw new Exception("CURL 에러: " . $error);
        }
        
        if ($http_code !== 200) {
            throw new Exception("HTTP 에러: " . $http_code);
        }
        
        if (empty($response)) {
            throw new Exception("빈 응답");
        }
        
        // JSON 파싱
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON 파싱 오류: " . json_last_error_msg());
        }
        
        return $data;
    }
    
    /**
     * 메인 API 엔드포인트
     */
    public function getData() {
        $cache_key = 'coinpan_data';
        
        try {
            // 캐시 확인
            $cached_data = $this->getCache($cache_key);
            if ($cached_data) {
                return [
                    'success' => true,
                    'data' => $cached_data,
                    'cached' => true,
                    'timestamp' => time()
                ];
            }
            
            // CoinPan API 호출
            $api_data = $this->fetchCoinPanData();
            
            // 데이터 처리 및 정규화
            $processed_data = $this->processData($api_data);
            
            // 캐시 저장
            $this->setCache($cache_key, $processed_data);
            
            return [
                'success' => true,
                'data' => $processed_data,
                'cached' => false,
                'timestamp' => time()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => time()
            ];
        }
    }
    
    /**
     * 데이터 처리 및 정규화
     */
    private function processData($raw_data) {
        $processed = [
            'market_info' => [],
            'exchange_rates' => [],
            'prices' => []
        ];
        
        // 시장 정보 처리
        if (isset($raw_data['coinmarketcap'])) {
            $processed['market_info'] = [
                'total_market_cap_usd' => $raw_data['coinmarketcap']['total_market_cap_usd'] ?? 0,
                'total_24h_volume_usd' => $raw_data['coinmarketcap']['total_24h_volume_usd'] ?? 0,
                'bitcoin_percentage' => $raw_data['coinmarketcap']['bitcoin_percentage_of_market_cap'] ?? 0,
                'timestamp' => $raw_data['coinmarketcap']['timestamp'] ?? time()
            ];
        }
        
        // 환율 정보 처리
        if (isset($raw_data['exchangerates'])) {
            $processed['exchange_rates'] = [
                'usd_to_krw' => $raw_data['exchangerates']['USD2KRW'] ?? 1370,
                'jpy_to_krw' => $raw_data['exchangerates']['JPY2KRW'] ?? 9.4,
                'usd_to_jpy' => $raw_data['exchangerates']['USD2JPY'] ?? 145,
                'timestamp' => time()
            ];
        }
        
        // 가격 정보 처리
        if (isset($raw_data['prices'])) {
            $processed['prices'] = $this->processPrices($raw_data['prices']);
        }
        
        return $processed;
    }
    
    /**
     * 가격 데이터 처리
     */
    private function processPrices($prices_data) {
        $processed_prices = [];
        
        // 지원하는 거래소 목록
        $exchanges = [
            'bithumb' => '빗썸',
            'upbit' => '업비트', 
            'coinone' => '코인원',
            'korbit' => '코빗',
            'bitflyer' => '비트플라이어',
            'binance' => '바이낸스',
            'bitfinex' => '비트파이넥스'
        ];
        
        // 지원하는 코인 목록
        $coins = ['BTC', 'ETH', 'XRP', 'ETC', 'TRX', 'BCH', 'EOS', 'ADA', 'SOL', 'DOGE'];
        
        foreach ($exchanges as $exchange_key => $exchange_name) {
            if (!isset($prices_data[$exchange_key])) continue;
            
            $processed_prices[$exchange_key] = [
                'name' => $exchange_name,
                'coins' => []
            ];
            
            foreach ($coins as $coin) {
                if (!isset($prices_data[$exchange_key][$coin])) continue;
                
                $coin_data = $prices_data[$exchange_key][$coin];
                
                // 데이터 정규화
                $processed_coin = [
                    'symbol' => $coin,
                    'available' => $coin_data['available'] ?? false,
                    'price_krw' => $this->normalizePrice($coin_data['now_price'] ?? 0),
                    'price_usd' => (float)($coin_data['now_price_usd'] ?? 0),
                    'high_24h' => $this->normalizePrice($coin_data['max_price'] ?? 0),
                    'low_24h' => $this->normalizePrice($coin_data['min_price'] ?? 0),
                    'change_24h' => (float)($coin_data['diff_24hr'] ?? 0),
                    'change_24h_percent' => (float)($coin_data['diff_24hr_percent'] ?? 0),
                    'volume_24h' => (float)($coin_data['units_traded'] ?? 0),
                    'korea_premium' => (float)($coin_data['korea_premium'] ?? 0),
                    'korea_premium_percent' => (float)($coin_data['korea_premium_percent'] ?? 0),
                    'last_updated' => time()
                ];
                
                $processed_prices[$exchange_key]['coins'][$coin] = $processed_coin;
            }
        }
        
        return $processed_prices;
    }
    
    /**
     * 가격 데이터 정규화 (문자열/숫자 통일)
     */
    private function normalizePrice($price) {
        if (is_string($price)) {
            return (float)str_replace(',', '', $price);
        }
        return (float)$price;
    }
    
    /**
     * 특정 코인의 가격 정보만 조회
     */
    public function getCoinPrice($symbol) {
        $all_data = $this->getData();
        
        if (!$all_data['success']) {
            return $all_data;
        }
        
        $symbol = strtoupper($symbol);
        $coin_prices = [];
        
        if (isset($all_data['data']['prices'])) {
            foreach ($all_data['data']['prices'] as $exchange_key => $exchange_data) {
                if (isset($exchange_data['coins'][$symbol])) {
                    $coin_prices[$exchange_key] = [
                        'exchange_name' => $exchange_data['name'],
                        'coin_data' => $exchange_data['coins'][$symbol]
                    ];
                }
            }
        }
        
        return [
            'success' => true,
            'symbol' => $symbol,
            'market_info' => $all_data['data']['market_info'] ?? [],
            'exchange_rates' => $all_data['data']['exchange_rates'] ?? [],
            'prices' => $coin_prices,
            'timestamp' => time()
        ];
    }
}

// API 라우팅
try {
    $api = new CoinPanAPI();
    
    $action = $_GET['action'] ?? 'all';
    $symbol = $_GET['symbol'] ?? null;
    
    switch ($action) {
        case 'all':
            $result = $api->getData();
            break;
            
        case 'coin':
            if (!$symbol) {
                throw new Exception('코인 심볼이 필요합니다');
            }
            $result = $api->getCoinPrice($symbol);
            break;
            
        default:
            throw new Exception('잘못된 액션입니다');
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
}
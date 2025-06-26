<?php
/*
 * 파일명: otc_process.php
 * 위치: /otc_process.php
 * 기능: 해외 테더 구매 신청 처리
 * 작성일: 2025-01-24
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    alert('잘못된 접근입니다.', './otc.php');
}

// 로그인 체크
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

// ===================================
// 테이블 생성 (최초 1회)
// ===================================

/* 해외 테더 구매 신청 테이블 */
$sql = "CREATE TABLE IF NOT EXISTS g5_tether_purchase (
    tp_id INT NOT NULL AUTO_INCREMENT,
    tp_quantity DECIMAL(20,8) NOT NULL DEFAULT '0' COMMENT '구매수량',
    tp_price_krw DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '단가(원)',
    tp_total_krw DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '총금액(원)',
    tp_wallet_address VARCHAR(255) NOT NULL DEFAULT '' COMMENT '지갑주소',
    tp_transfer_company VARCHAR(50) NOT NULL DEFAULT '' COMMENT '송금업체',
    tp_name VARCHAR(50) NOT NULL DEFAULT '',
    tp_hp VARCHAR(20) NOT NULL DEFAULT '',
    tp_status TINYINT NOT NULL DEFAULT '0' COMMENT '상태: 0=신청, 1=진행중, 2=완료, 9=취소',
    tp_datetime DATETIME NOT NULL,
    tp_ip VARCHAR(50) NOT NULL DEFAULT '',
    tp_memo TEXT COMMENT '관리자 메모',
    mb_id VARCHAR(50) NOT NULL DEFAULT '',
    tp_process_datetime DATETIME COMMENT '처리일시',
    tp_complete_datetime DATETIME COMMENT '완료일시',
    PRIMARY KEY (tp_id),
    KEY idx_status (tp_status),
    KEY idx_datetime (tp_datetime),
    KEY idx_mb_id (mb_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

// ===================================
// 데이터 검증 및 수집
// ===================================

$quantity = isset($_POST['quantity']) ? (float)$_POST['quantity'] : 0;
$wallet_address = isset($_POST['wallet_address']) ? trim($_POST['wallet_address']) : '';
$transfer_company = isset($_POST['transfer_company']) ? trim($_POST['transfer_company']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$hp = isset($_POST['hp']) ? trim($_POST['hp']) : '';

// 유효성 검사
if($quantity < 1000) {
    alert('최소 구매 수량은 1,000 USDT입니다.');
}

if(!$wallet_address) {
    alert('입금받을 지갑 주소를 입력해주세요.');
}

if(!$transfer_company) {
    alert('이용할 해외송금업체를 선택해주세요.');
}

if(!$name) {
    alert('신청자명을 입력해주세요.');
}

if(!$hp) {
    alert('연락처를 입력해주세요.');
}

// 국내거래소 주소 체크 (간단한 패턴 체크)
$domestic_patterns = array(
    'upbit', 'bithumb', 'coinone', 'korbit'
);

foreach($domestic_patterns as $pattern) {
    if(stripos($wallet_address, $pattern) !== false) {
        alert('국내거래소 주소는 사용할 수 없습니다.\\n해외거래소 주소를 입력해주세요.');
    }
}

// ===================================
// 가격 계산
// ===================================

// USDT 실시간 가격 가져오기
function get_current_usdt_price() {
    $cache_file = G5_DATA_PATH.'/cache/usdt_price.txt';
    $cache_time = 300; // 5분
    
    if(file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        $data = json_decode(file_get_contents($cache_file), true);
        return $data['price'];
    }
    
    // Google Finance 크롤링
    $url = "https://www.google.com/finance/quote/USDT-KRW";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($http_code == 200 && $response) {
        // data-last-price 속성에서 가격 추출
        if(preg_match('/data-last-price="([0-9.]+)"/', $response, $matches)) {
            $price = round((float)$matches[1]);
            file_put_contents($cache_file, json_encode(['price' => $price, 'timestamp' => time()]));
            return $price;
        }
        
        // 대체 패턴
        if(preg_match('/<div[^>]+class="YMlKec fxKbKc"[^>]*>([0-9,]+\.?[0-9]*)<\/div>/', $response, $matches)) {
            $price_str = str_replace(',', '', $matches[1]);
            $price = round((float)$price_str);
            file_put_contents($cache_file, json_encode(['price' => $price, 'timestamp' => time()]));
            return $price;
        }
    }
    
    return 1361; // 기본값 (최근 구글 가격 기준)
}

// 현재 시장가격
$market_price = get_current_usdt_price();

// 조정값 조회
$sql = "SELECT * FROM g5_tether_price ORDER BY tp_id DESC LIMIT 1";
$adjustment = sql_fetch($sql);

if(!$adjustment) {
    $buy_adjustment = 20; // 기본 +20원
} else {
    $buy_adjustment = $adjustment['tp_buy_adjustment'];
}

// 최종 가격 계산
$base_price = $market_price + $buy_adjustment;
$fee_rate = 0.02; // 수수료 2%
$unit_price = round($base_price * (1 + $fee_rate));
$total_price = $quantity * $unit_price;

// ===================================
// DB 저장
// ===================================

$sql = "INSERT INTO g5_tether_purchase SET
        tp_quantity = '$quantity',
        tp_price_krw = '$unit_price',
        tp_total_krw = '$total_price',
        tp_wallet_address = '".sql_real_escape_string($wallet_address)."',
        tp_transfer_company = '".sql_real_escape_string($transfer_company)."',
        tp_name = '".sql_real_escape_string($name)."',
        tp_hp = '".sql_real_escape_string($hp)."',
        tp_status = '0',
        tp_datetime = '".G5_TIME_YMDHIS."',
        tp_ip = '".$_SERVER['REMOTE_ADDR']."',
        mb_id = '{$member['mb_id']}'";

sql_query($sql);
$tp_id = sql_insert_id();

// ===================================
// 관리자 알림 메일 발송
// ===================================

if($config['cf_admin_email']) {
    $subject = "[해외테더구매] 신규 신청 - ".$name;
    
    $content = "해외 테더 구매 신청이 접수되었습니다.\n\n";
    $content .= "==== 신청 정보 ====\n";
    $content .= "신청번호: #".$tp_id."\n";
    $content .= "신청자: ".$name." (".$member['mb_id'].")\n";
    $content .= "연락처: ".$hp."\n";
    $content .= "구매수량: ".number_format($quantity, 2)." USDT\n";
    $content .= "예상단가: ".number_format($unit_price)."원\n";
    $content .= "예상금액: ".number_format($total_price)."원\n";
    $content .= "송금업체: ".$transfer_company."\n";
    $content .= "지갑주소: ".$wallet_address."\n";
    $content .= "신청일시: ".G5_TIME_YMDHIS."\n\n";
    $content .= "관리자 페이지에서 처리해주세요.\n";
    $content .= G5_URL."/adm/tether_list.php";
    
    mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $config['cf_admin_email'], $subject, $content, 1);
}

// ===================================
// 신청 완료 페이지로 이동
// ===================================

// 신청 정보를 세션에 저장
set_session('tp_id', $tp_id);
set_session('tp_quantity', $quantity);
set_session('tp_total', $total_price);

goto_url('./otc_complete.php');
?>
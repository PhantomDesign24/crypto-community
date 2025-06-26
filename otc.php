<?php
/*
 * 파일명: otc.php
 * 위치: /otc.php
 * 기능: 해외 테더 구매 메인 페이지
 * 작성일: 2025-01-23
 * 수정일: 2025-01-24 (전문적인 디자인으로 개선)
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '해외 테더 구매';
$g5['body_script'] = ' id="tether_page"';

/* 로그인 체크 (필수) */
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

// ===================================
// 가격 설정 처리 (관리자만)
// ===================================

if($is_admin && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_price'])) {
    $buy_adjustment = isset($_POST['buy_adjustment']) ? (float)$_POST['buy_adjustment'] : 0;
    $sell_adjustment = isset($_POST['sell_adjustment']) ? (float)$_POST['sell_adjustment'] : 0;
    
    // 가격 테이블이 없으면 생성
    $sql = "CREATE TABLE IF NOT EXISTS g5_tether_price (
        tp_id INT NOT NULL AUTO_INCREMENT,
        tp_buy_adjustment DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '매수 조정값',
        tp_sell_adjustment DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '매도 조정값',
        tp_datetime DATETIME NOT NULL,
        mb_id VARCHAR(50) NOT NULL DEFAULT '',
        PRIMARY KEY (tp_id),
        KEY idx_datetime (tp_datetime)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    sql_query($sql, FALSE);
    
    // 조정값 저장
    $sql = "INSERT INTO g5_tether_price SET
            tp_buy_adjustment = '$buy_adjustment',
            tp_sell_adjustment = '$sell_adjustment',
            tp_datetime = '".G5_TIME_YMDHIS."',
            mb_id = '{$member['mb_id']}'";
    sql_query($sql);
    
    alert('가격 조정값이 저장되었습니다.', './otc.php');
}

// ===================================
// 설정값 처리 (관리자만)
// ===================================

if($is_admin && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_config'])) {
    $min_quantity = isset($_POST['min_quantity']) ? (int)$_POST['min_quantity'] : 1000;
    $contact_type = isset($_POST['contact_type']) ? $_POST['contact_type'] : 'signal';
    $contact_info = isset($_POST['contact_info']) ? trim($_POST['contact_info']) : '';
    $contact_button_text = isset($_POST['contact_button_text']) ? trim($_POST['contact_button_text']) : '상담 신청';
    
    // 설정 테이블이 없으면 생성
    $sql = "CREATE TABLE IF NOT EXISTS g5_tether_config (
        tc_id INT NOT NULL AUTO_INCREMENT,
        tc_min_quantity INT NOT NULL DEFAULT '1000' COMMENT '최소구매수량',
        tc_contact_type VARCHAR(20) NOT NULL DEFAULT 'signal' COMMENT '연락방식',
        tc_contact_info VARCHAR(255) NOT NULL DEFAULT '' COMMENT '연락처정보',
        tc_contact_button_text VARCHAR(100) NOT NULL DEFAULT '상담 신청' COMMENT '버튼텍스트',
        tc_datetime DATETIME NOT NULL,
        PRIMARY KEY (tc_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    sql_query($sql, FALSE);
    
    // 기존 설정 삭제
    sql_query("DELETE FROM g5_tether_config");
    
    // 새 설정 저장
    $sql = "INSERT INTO g5_tether_config SET
            tc_min_quantity = '$min_quantity',
            tc_contact_type = '".sql_real_escape_string($contact_type)."',
            tc_contact_info = '".sql_real_escape_string($contact_info)."',
            tc_contact_button_text = '".sql_real_escape_string($contact_button_text)."',
            tc_datetime = '".G5_TIME_YMDHIS."'";
    sql_query($sql);
    
    alert('설정이 저장되었습니다.', './otc.php');
}

// ===================================
// 설정값 조회
// ===================================

$sql = "SELECT * FROM g5_tether_config ORDER BY tc_id DESC LIMIT 1";
$config_row = sql_fetch($sql);

if(!$config_row) {
    // 기본값 설정
    $min_quantity = 1000;
    $contact_type = 'signal';
    $contact_info = '@tether_service';
    $contact_button_text = '상담 신청';
} else {
    $min_quantity = $config_row['tc_min_quantity'];
    $contact_type = $config_row['tc_contact_type'];
    $contact_info = $config_row['tc_contact_info'];
    $contact_button_text = $config_row['tc_contact_button_text'];
}

// ===================================
// USDT 실시간 가격 가져오기
// ===================================

// ===================================
// USDT 실시간 가격 가져오기
// ===================================

function get_usdt_price() {
    // 캐시 디렉토리 확인 및 생성
    $cache_dir = G5_DATA_PATH.'/cache';
    if(!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
        @chmod($cache_dir, 0755);
    }
    
    // 캐시 확인 (5분간 유지)
    $cache_file = $cache_dir.'/usdt_price.txt';
    $cache_time = 300; // 5분
    
    // 캐시 파일이 존재하고 유효한 경우
    if(file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        $cached_data = @file_get_contents($cache_file);
        if($cached_data) {
            $data = json_decode($cached_data, true);
            if($data && isset($data['price'])) {
                return $data;
            }
        }
    }
    
    $price_data = array(
        'google_price' => 0,
        'upbit_price' => 0,
        'price' => 0,
        'source' => 'Default',
        'timestamp' => time()
    );
    
    // 1. 업비트 API에서 가격 가져오기
    $upbit_url = "https://api.upbit.com/v1/ticker?markets=KRW-USDT";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $upbit_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ));
    $upbit_response = curl_exec($ch);
    $upbit_error = curl_error($ch);
    curl_close($ch);
    
    if($upbit_response && !$upbit_error) {
        $upbit_data = json_decode($upbit_response, true);
        if(isset($upbit_data[0]['trade_price'])) {
            $price_data['upbit_price'] = round($upbit_data[0]['trade_price']);
        }
    }
    
    // 2. CoinGecko API 사용 (Google Finance 대체)
    $url = "https://api.coingecko.com/api/v3/simple/price?ids=tether&vs_currencies=krw";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ));
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if($response && !$error) {
        $data = json_decode($response, true);
        if(isset($data['tether']['krw'])) {
            $price_data['google_price'] = round($data['tether']['krw']);
            $price_data['source'] = 'CoinGecko';
        }
    }
    
    // 3. 기본 가격 설정
    if($price_data['google_price'] > 0) {
        $price_data['price'] = $price_data['google_price'];
    } else if($price_data['upbit_price'] > 0) {
        $price_data['price'] = $price_data['upbit_price'];
        $price_data['source'] = 'Upbit';
    } else {
        // 둘 다 실패한 경우 기본값
        $price_data['price'] = 1361;
        $price_data['upbit_price'] = 1361;
        $price_data['google_price'] = 1361;
        $price_data['source'] = 'Default';
    }
    
    // 캐시 저장
    @file_put_contents($cache_file, json_encode($price_data));
    
    return $price_data;
}

// 실시간 가격 가져오기
$market_data = get_usdt_price();
$base_price = $market_data['price'];
$price_source = $market_data['source'];

// ===================================
// 가격 조정값 조회
// ===================================

$sql = "SELECT * FROM g5_tether_price ORDER BY tp_id DESC LIMIT 1";
$adjustment = sql_fetch($sql);

if(!$adjustment) {
    // 기본 조정값
    $buy_adjustment = 20;  // +20원
    $sell_adjustment = -20; // -20원
} else {
    $buy_adjustment = $adjustment['tp_buy_adjustment'];
    $sell_adjustment = $adjustment['tp_sell_adjustment'];
}

// 최종 가격 계산
$current_buy_price = $base_price + $buy_adjustment;  // 시장가 + 조정값
$current_sell_price = $base_price + $sell_adjustment;  // 시장가 + 조정값

// 수수료 제거 - 최종가격은 조정된 가격 그대로
$final_buy_price = $current_buy_price;

// ===================================
// 내 신청 내역 조회
// ===================================

$sql = "SELECT * FROM g5_tether_purchase 
        WHERE mb_id = '{$member['mb_id']}' 
        ORDER BY tp_id DESC 
        LIMIT 5";
$my_result = sql_query($sql);

include_once('./_head.php');
?>

<!-- ===================================
     해외 테더 구매 페이지 스타일
     =================================== -->
<style>
/* 전체 레이아웃 */
.tether-wrapper {
    background: #f8fafc;
    min-height: 100vh;
    padding: 40px 0;
}

.tether-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* 프로페셔널 헤더 */
.tether-header {
    background: white;
    border-radius: 16px;
    padding: 48px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.tether-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 12px;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #dbeafe;
    color: #1e40af;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
}

.header-desc {
    font-size: 16px;
    color: #64748b;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

/* 메인 콘텐츠 그리드 */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 32px;
    margin: 32px 0;
}

/* 메인 섹션 */
.main-section {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* 정보 카드 */
.info-card {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.info-card h2 {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-card h2 i {
    color: #3b82f6;
}

/* 특징 리스트 */
.feature-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: #dbeafe;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 20px;
    color: #3b82f6;
}

.feature-text h4 {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
}

.feature-text p {
    font-size: 12px;
    color: #64748b;
    margin: 0;
}

/* 준비사항 그리드 */
.preparation-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.prep-card {
    padding: 20px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.prep-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.prep-card h3 i {
    color: #3b82f6;
}

.company-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.company-tag {
    background: white;
    border: 1px solid #cbd5e1;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    color: #475569;
}

/* 주의사항 */
.notice-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    gap: 12px;
}

.notice-box i {
    color: #ef4444;
    font-size: 20px;
    flex-shrink: 0;
}

.notice-content {
    flex: 1;
}

.notice-content h4 {
    font-size: 14px;
    font-weight: 600;
    color: #991b1b;
    margin-bottom: 4px;
}

.notice-content p {
    font-size: 13px;
    color: #7f1d1d;
    margin: 0;
    line-height: 1.5;
}

/* 프로세스 카드 */
.process-timeline {
    display: flex;
    justify-content: space-between;
    position: relative;
}

.process-timeline::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
}

.process-item {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.process-icon {
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-weight: 600;
    color: #94a3b8;
}

.process-item.active .process-icon {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.process-title {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
}

.process-desc {
    font-size: 12px;
    color: #64748b;
}

/* 사이드바 */
.sidebar {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* 실시간 시세 카드 */
.price-card {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 12px;
    padding: 24px;
    color: white;
    text-align: center;
}

.price-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.price-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
}

.price-note {
    font-size: 12px;
    opacity: 0.8;
}

/* CTA 버튼 */
.cta-button {
    width: 100%;
    background: #22c55e;
    color: white;
    padding: 16px;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
}

.cta-button:hover {
    background: #16a34a;
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(34, 197, 94, 0.4);
}

/* 내 신청 내역 */
.my-orders {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.my-orders h3 {
    font-size: 16px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 16px;
}

.order-item {
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
}

.order-item:last-child {
    border-bottom: none;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.order-number {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
}

.order-status {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.order-status.pending {
    background: #fef3c7;
    color: #92400e;
}

.order-status.processing {
    background: #dbeafe;
    color: #1e40af;
}

.order-status.completed {
    background: #d1fae5;
    color: #065f46;
}

.order-info {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #64748b;
}

.empty-orders {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}

.empty-orders i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

/* 모달 스타일 */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    position: relative;
    background: white;
    max-width: 600px;
    margin: 50px auto;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    animation: slideUp 0.3s;
    max-height: 90vh;
    overflow-y: auto;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    padding: 24px 24px 0;
    border-bottom: 1px solid #e2e8f0;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 16px;
}

.modal-close {
    position: absolute;
    top: 24px;
    right: 24px;
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #e2e8f0;
}

.modal-body {
    padding: 24px;
}

/* 폼 스타일 */
.apply-form .form-group {
    margin-bottom: 20px;
}

.apply-form .form-label {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
    display: block;
}

.apply-form .form-label span {
    color: #ef4444;
}

.apply-form .tether-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.2s;
    background: #f8fafc;
}

.apply-form .tether-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.apply-form .tether-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s;
}

.apply-form .tether-select:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.apply-form .form-hint {
    font-size: 12px;
    color: #64748b;
    margin-top: 6px;
}

/* 입력 타입 선택 탭 */
.input-type-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}

.input-type-tab {
    flex: 1;
    padding: 10px;
    background: #f1f5f9;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

.input-type-tab.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.input-type-tab:hover:not(.active) {
    background: #e2e8f0;
}

/* 변환 정보 */
.conversion-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 12px;
    margin-top: 8px;
    font-size: 13px;
    color: #0369a1;
    display: flex;
    align-items: center;
    gap: 8px;
}

.conversion-info i {
    font-size: 16px;
}

.conversion-value {
    font-weight: 600;
    color: #0c4a6e;
}

.input-group {
    display: flex;
    gap: 8px;
}

.input-group-text {
    padding: 12px 16px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
}

/* 계산 결과 */
.calc-result {
    background: #f8fafc;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
}

.calc-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.calc-label {
    color: #64748b;
}

.calc-value {
    font-weight: 600;
    color: #334155;
}

.calc-total {
    border-top: 1px solid #e2e8f0;
    margin-top: 8px;
    padding-top: 8px;
}

.calc-total .calc-value {
    font-size: 18px;
    color: #3b82f6;
}

.modal-footer {
    padding: 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 12px;
}

.btn-primary {
    flex: 1;
    background: #3b82f6;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    flex: 1;
    background: #f1f5f9;
    color: #475569;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

/* 시장가 정보 */
.market-price {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 13px;
}

.market-label {
    opacity: 0.8;
}

.market-value {
    font-weight: 500;
}

.market-diff {
    display: none; /* 시장가 옆 조정값 숨김 */
}

/* 가격 수정 버튼 */
.price-edit-btn {
    width: 100%;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.price-edit-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* 시그널 정보 */
.signal-info {
    background: white;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.signal-info i {
    font-size: 32px;
    color: #22c55e;
}

.signal-text strong {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 4px;
}

.signal-text span {
    font-size: 16px;
    color: #0f172a;
    font-weight: 600;
}

/* 가격 설정 모달 */
.price-modal {
    max-width: 400px;
}

.price-form .form-group {
    margin-bottom: 16px;
}

.price-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 8px;
}

.price-display-label {
    font-size: 14px;
    color: #64748b;
}

.price-display-value {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
}

/* 시장 정보 박스 */
.market-info-box {
    background: #f8fafc;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
}

.market-info-item {
    text-align: center;
}

.info-label {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 4px;
}

.info-value {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
}

/* 조정값 입력 */
.adjustment-input {
    display: flex;
    align-items: center;
    gap: 8px;
}

.adj-prefix {
    padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
}

/* 연락처 버튼 */
.contact-button {
    width: 100%;
    background: #22c55e;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
}

.contact-button:hover {
    background: #16a34a;
    transform: translateY(-1px);
}

/* 설정 버튼 */
.config-edit-btn {
    width: 100%;
    background: #6b7280;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.config-edit-btn:hover {
    background: #4b5563;
}

/* 시세 비교 카드 */
.market-compare-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.market-compare-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.market-compare-card h3 i {
    color: #3b82f6;
}

.market-compare-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.market-item-otc {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.market-item-otc.highlight {
    background: #dbeafe;
    border-color: #3b82f6;
}

.market-source {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #64748b;
}

.market-icon {
    width: 16px;
    height: 16px;
    object-fit: contain;
}

.market-source i {
    font-size: 16px;
    color: #64748b;
}

.market-price-info {
    text-align: right;
}

.market-price-value {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
}

.market-diff-percent {
    font-size: 12px;
    font-weight: 500;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}

.market-diff-percent.up {
    background: #fee2e2;
    color: #dc2626;
}

.market-diff-percent.down {
    background: #d1fae5;
    color: #059669;
}

.market-diff-percent.base {
    background: #e2e8f0;
    color: #64748b;
}

.market-update-time {
    margin-top: 12px;
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

/* 빠른 상담 카드 */
.quick-consult-card {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
}

.quick-consult-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: #15803d;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.quick-consult-card h3 i {
    color: #22c55e;
}

.quick-consult-card p {
    font-size: 14px;
    color: #166534;
    margin-bottom: 16px;
    line-height: 1.5;
}

.consult-button {
    width: 100%;
    background: #22c55e;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.consult-button:hover {
    background: #16a34a;
    transform: translateY(-1px);
}

.signal-info-box {
    background: white;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.signal-info-box i {
    font-size: 32px;
    color: #22c55e;
}

/* 반응형 */
@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        position: static;
    }
    
    .feature-list {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .tether-header {
        padding: 32px 20px;
    }
    
    .tether-header h1 {
        font-size: 24px;
    }
    
    .preparation-grid {
        grid-template-columns: 1fr;
    }
    
    .process-timeline::before {
        display: none;
    }
    
    .process-timeline {
        flex-direction: column;
        gap: 20px;
    }
    
    .modal-content {
        margin: 20px;
    }
}

/* 주문 내역 헤더 */
.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.order-count {
    font-size: 12px;
    color: #94a3b8;
    background: #f1f5f9;
    padding: 4px 12px;
    border-radius: 12px;
}

/* 로딩 */
.order-loading {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.spinner-border {
    width: 24px;
    height: 24px;
    border-width: 2px;
}

/* 페이징 */
.order-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.page-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.page-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
}

.page-btn.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.page-btn.active:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.page-btn i {
    font-size: 12px;
}

/* 주문 상세 정보 */
.order-detail {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed #e2e8f0;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 12px;
}

.detail-label {
    color: #94a3b8;
    font-weight: 500;
}

.detail-value {
    color: #475569;
    font-weight: 500;
}

.detail-value.wallet {
    font-family: monospace;
    font-size: 11px;
    cursor: help;
}

.detail-value.amount {
    color: #3b82f6;
    font-weight: 600;
}

.order-memo {
    margin-top: 8px;
    padding: 8px;
    background: #f8fafc;
    border-radius: 6px;
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
}

.order-memo i {
    margin-right: 4px;
    color: #94a3b8;
}

.order-status.cancelled {
    background: #fef2f2;
    color: #dc2626;
}
</style>

<!-- ===================================
     해외 테더 구매 페이지 콘텐츠
     =================================== -->
<div class="tether-wrapper">
    <div class="tether-container">
        <!-- 프로페셔널 헤더 -->
        <header class="tether-header">
            <div class="header-badge">
                <i class="bi bi-shield-check"></i>
                안전한 해외 테더 구매
            </div>
            <h1>해외 테더 구매 서비스</h1>
            <p class="header-desc">
                국내보다 저렴한 가격으로 해외에서 테더를 구매하세요.<br>
                해외송금 전문업체를 통한 안전하고 신속한 거래를 보장합니다.
            </p>
        </header>
		
		<?php include_once(G5_PATH.'/coin.php');?>
        
        <!-- 메인 콘텐츠 그리드 -->
        <div class="content-grid">
            <!-- 메인 섹션 -->
            <div class="main-section">
                <!-- 주요 특징 -->
                <div class="info-card">
                    <h2><i class="bi bi-star"></i> 서비스 특징</h2>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="feature-text">
                                <h4>현지방문 불필요</h4>
                                <p>해외 현지방문 없이 온라인으로 구매</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="feature-text">
                                <h4>안전한 거래</h4>
                                <p>해외송금 전문업체를 통한 안전거래</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="bi bi-lightning"></i>
                            </div>
                            <div class="feature-text">
                                <h4>빠른 처리</h4>
                                <p>평균 1시간 이내 신속한 입금</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 추가 특징 설명 -->
                    <div style="margin-top: 24px; padding: 20px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #0369a1; margin-bottom: 12px;">
                            <i class="bi bi-info-circle"></i> OTC 장외거래 안내
                        </h4>
                        <ul style="margin: 0; padding-left: 20px; color: #0c4a6e; font-size: 14px; line-height: 1.8;">
                            <li>국내보다 저렴한 테더를 해외에서 현지방문 없이 바로 구매 가능한 OTC 장외거래</li>
                            <li>모든 거래는 신청 후 담당자 1:1 배정을 통해 안전하게 진행됩니다</li>
                            <li>해외송금업체를 통해 해외은행에서 입금 확인 후 즉시 처리됩니다</li>
                        </ul>
                    </div>
                </div>
                
                <!-- 준비사항 -->
                <div class="info-card">
                    <h2><i class="bi bi-check-circle"></i> 구매 준비사항</h2>
                    <div class="preparation-grid">
                        <div class="prep-card">
                            <h3><i class="bi bi-bank"></i> 해외송금업체 계정</h3>
                            <div class="company-tags">
                                <span class="company-tag">한패스</span>
                                <span class="company-tag">센트비</span>
                                <span class="company-tag">유트랜스퍼</span>
                                <span class="company-tag">크로스</span>
                                <span class="company-tag">와이어바알리</span>
                            </div>
                        </div>
                        <div class="prep-card">
                            <h3><i class="bi bi-wallet2"></i> 해외거래소 주소</h3>
                            <div class="company-tags">
                                <span class="company-tag">바이낸스</span>
                                <span class="company-tag">바이비트</span>
                                <span class="company-tag">기타 해외거래소</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="notice-box" style="margin-top: 20px;">
                        <i class="bi bi-exclamation-triangle"></i>
                        <div class="notice-content">
                            <h4>중요 안내</h4>
                            <p>국내거래소(업비트, 빗썸, 코인원, 코빗) 주소는 트래블룰로 인해 입금대기 또는 입금불가 상황이 발생할 수 있습니다. 반드시 해외거래소 주소를 사용해주세요. 국내거래소 주소로의 입금으로 인한 불이익 발생 시 책임지지 않습니다.</p>
                        </div>
                    </div>
                    
                    <!-- 환율 변동 안내 -->
                    <div style="margin-top: 16px; padding: 16px; background: #fefce8; border: 1px solid #fef08a; border-radius: 8px;">
                        <p style="margin: 0; font-size: 13px; color: #713f12; line-height: 1.6;">
                            <i class="bi bi-currency-exchange" style="margin-right: 6px;"></i>
                            거래 중 발생하는 원화-달러 환율 변동으로 인한 이익/손실은 전적으로 구매자 본인의 선택임을 인지하시기 바랍니다.
                        </p>
                    </div>
                </div>
                
                <!-- 거래 프로세스 -->
                <div class="info-card">
                    <h2><i class="bi bi-arrow-right-circle"></i> 거래 진행 과정</h2>
                    <div class="process-timeline">
                        <div class="process-item active">
                            <div class="process-icon">1</div>
                            <div class="process-title">신청서 작성</div>
                            <div class="process-desc">구매 정보 입력</div>
                        </div>
                        <div class="process-item">
                            <div class="process-icon">2</div>
                            <div class="process-title">담당자 배정</div>
                            <div class="process-desc">1:1 상담 진행</div>
                        </div>
                        <div class="process-item">
                            <div class="process-icon">3</div>
                            <div class="process-title">송금 진행</div>
                            <div class="process-desc">해외송금 처리</div>
                        </div>
                        <div class="process-item">
                            <div class="process-icon">4</div>
                            <div class="process-title">테더 수령</div>
                            <div class="process-desc">지갑으로 전송</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 사이드바 -->
            <aside class="sidebar">
                <!-- 실시간 시세 -->
                <div class="price-card">
                    <div class="price-label">해외 테더 매수가</div>
                    <div class="price-value">₩<?php echo number_format($final_buy_price); ?></div>
                    
                    <div class="market-price">
                        <span class="market-label">시장가</span>
                        <span class="market-value">₩<?php echo number_format($market_data['upbit_price']); ?></span>
                    </div>
                    
                    <?php if($is_admin) { ?>
                    <button class="price-edit-btn" onclick="openPriceModal()">
                        <i class="bi bi-pencil"></i> 가격 조정
                    </button>
                    <?php } ?>
                </div>
                
                <!-- 시세 비교 카드 -->
                <div class="market-compare-card">
                    <h3><i class="bi bi-graph-up"></i> 실시간 시세 비교</h3>
                    <div class="market-compare-grid">
                        <?php if($market_data['upbit_price'] > 0) { ?>
                        <div class="market-item-otc">
                            <div class="market-source">
                                <i class="bi bi-currency-exchange"></i>
                                <span>국내거래소 평균 시장가</span>
                            </div>
                            <div class="market-price-info">
                                <div class="market-price-value">₩<?php echo number_format($market_data['upbit_price']); ?></div>
                                <?php 
                                $upbit_diff = ($market_data['google_price'] > 0 && $market_data['upbit_price'] > 0) 
                                    ? (($market_data['upbit_price'] - $market_data['google_price']) / $market_data['google_price']) * 100 
                                    : 0;
                                ?>
                                <div class="market-diff-percent <?php echo $upbit_diff >= 0 ? 'up' : 'down'; ?>">
                                    <?php echo $upbit_diff >= 0 ? '+' : ''; ?><?php echo number_format($upbit_diff, 2); ?>%
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if($market_data['google_price'] > 0) { ?>
                        <div class="market-item-otc">
                            <div class="market-source">
                                <i class="bi bi-globe"></i>
                                <span><?php echo $market_data['source'] == 'CoinGecko' ? 'CoinGecko' : 'Google'; ?></span>
                            </div>
                            <div class="market-price-info">
                                <div class="market-price-value">₩<?php echo number_format($market_data['google_price']); ?></div>
                                <div class="market-diff-percent base">해외 시세</div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <div class="market-item-otc highlight">
                            <div class="market-source">
                                <i class="bi bi-shop"></i>
                                <span>OTC 구매가</span>
                            </div>
                            <div class="market-price-info">
                                <div class="market-price-value">₩<?php echo number_format($final_buy_price); ?></div>
                                <?php 
                                $otc_diff = ($market_data['google_price'] > 0) 
                                    ? (($final_buy_price - $market_data['google_price']) / $market_data['google_price']) * 100 
                                    : 0;
                                ?>
                                <div class="market-diff-percent <?php echo $otc_diff >= 0 ? 'up' : 'down'; ?>">
                                    <?php echo $otc_diff >= 0 ? '+' : ''; ?><?php echo number_format($otc_diff, 2); ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="market-update-time">
                        <i class="bi bi-clock"></i> <?php echo date('m/d H:i', $market_data['timestamp']); ?> 기준
                    </div>
                </div>
                
                <!-- CTA 버튼 -->
                <button class="cta-button" onclick="openModal()">
                    <i class="bi bi-cart-plus"></i>
                    지금 구매 신청하기
                </button>
                
                <!-- 빠른 상담 카드 -->
                <div class="quick-consult-card">
                    <h3><i class="bi bi-headset"></i> 빠른 상담</h3>
                    <p>궁금한 점이 있으신가요?<br>전문 상담원이 도와드립니다.</p>
                    <?php if($contact_type == 'button' && $contact_info) { ?>
                    <button class="consult-button" onclick="window.open('<?php echo $contact_info; ?>', '_blank')">
                        <i class="bi bi-chat-dots"></i> <?php echo $contact_button_text; ?>
                    </button>
                    <?php } else if($contact_type == 'signal' && $contact_info) { ?>
                    <div class="signal-info-box">
                        <i class="bi bi-chat-dots-fill"></i>
                        <div class="signal-text">
                            <strong>시그널 ID</strong>
                            <span><?php echo $contact_info; ?></span>
                        </div>
                    </div>
                    <?php } else { ?>
                    <button class="consult-button" onclick="alert('상담 정보가 설정되지 않았습니다.')">
                        <i class="bi bi-chat-dots"></i> 상담 신청
                    </button>
                    <?php } ?>
                </div>
                
                <!-- 내 신청 내역 -->
                <div class="my-orders">
                    <div class="orders-header">
                        <h3>내 신청 내역</h3>
                        <span class="order-count" id="orderCount"></span>
                    </div>
                    <div id="orderList" class="order-list">
                        <div class="order-loading">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>불러오는 중...</span>
                        </div>
                    </div>
                    <div id="orderPaging"></div>
                </div>                
                <?php if($is_admin) { ?>
                <button class="config-edit-btn" onclick="openConfigModal()">
                    <i class="bi bi-gear"></i> 설정 관리
                </button>
                <?php } ?>
            </aside>
        </div>
    </div>
</div>
<script>
	// 페이지 로드시 첫 페이지 불러오기
document.addEventListener('DOMContentLoaded', function() {
    loadOrders(1);
});

// 주문 내역 불러오기
function loadOrders(page) {
    const orderList = document.getElementById('orderList');
    const orderPaging = document.getElementById('orderPaging');
    const orderCount = document.getElementById('orderCount');
    
    // 로딩 표시
    orderList.innerHTML = `
        <div class="order-loading">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>불러오는 중...</span>
        </div>
    `;
    
    // AJAX 요청
    fetch('<?php echo G5_URL; ?>/otc_orders.php?page=' + page)
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                orderList.innerHTML = '<div class="empty-orders"><i class="bi bi-exclamation-circle"></i><p>' + data.error + '</p></div>';
                return;
            }
            
            // 총 개수 표시
            if(data.total_count > 0) {
                orderCount.textContent = '총 ' + data.total_count + '건';
            } else {
                orderCount.textContent = '';
            }
            
            // 주문 내역 표시
            if(data.orders.length > 0) {
                let html = '';
                data.orders.forEach(order => {
                    html += `
                        <div class="order-item">
                            <div class="order-header">
                                <span class="order-number">#${order.tp_id}</span>
                                <span class="order-status ${order.status_class}">${order.status_text}</span>
                            </div>
                            <div class="order-info">
                                <span>${order.quantity} USDT</span>
                                <span>${order.datetime}</span>
                            </div>
                            <div class="order-detail">
                                <div class="detail-row">
                                    <span class="detail-label">송금업체</span>
                                    <span class="detail-value">${order.transfer_company}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">입금주소</span>
                                    <span class="detail-value wallet" title="${order.wallet_address}">
                                        ${order.masked_wallet}
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">예상금액</span>
                                    <span class="detail-value amount">₩${order.total_krw}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">단가</span>
                                    <span class="detail-value">₩${order.price_krw}</span>
                                </div>
                            </div>
                            ${order.memo ? `
                            <div class="order-memo">
                                <i class="bi bi-chat-left-text"></i> ${order.memo}
                            </div>
                            ` : ''}
                        </div>
                    `;
                });
                orderList.innerHTML = html;
            } else {
                orderList.innerHTML = `
                    <div class="empty-orders">
                        <i class="bi bi-inbox"></i>
                        <p>신청 내역이 없습니다</p>
                    </div>
                `;
            }
            
            // 페이징 표시
            orderPaging.innerHTML = data.paging;
        })
        .catch(error => {
            console.error('Error:', error);
            orderList.innerHTML = `
                <div class="empty-orders">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>데이터를 불러오는 중 오류가 발생했습니다.</p>
                </div>
            `;
        });
}
</script>
<!-- 신청 모달 -->
<div id="applyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">테더 구매 신청</h2>
            <button class="modal-close" onclick="closeModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form class="apply-form" id="tetherForm" method="post" action="./otc_process.php">
            <input type="hidden" name="trade_type" value="buy_tether">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">구매 방식 선택</label>
                    <div class="input-type-tabs">
                        <div class="input-type-tab active" onclick="switchInputType('usdt')">
                            <i class="bi bi-currency-dollar"></i> USDT 수량 입력
                        </div>
                        <div class="input-type-tab" onclick="switchInputType('krw')">
                            <i class="bi bi-cash-coin"></i> 원화 금액 입력
                        </div>
                    </div>
                </div>
                
                <!-- USDT 입력 -->
                <div class="form-group" id="usdtInputGroup">
                    <label class="form-label">구매 수량 <span>*</span></label>
                    <div class="input-group">
                        <input type="number" name="quantity" id="quantity" class="tether-input" 
                               placeholder="최소 1,000 USDT" required min="1000" step="0.01">
                        <span class="input-group-text">USDT</span>
                    </div>
                    <p class="form-hint">최소 구매 수량: 1,000 USDT</p>
                    <div class="conversion-info" id="usdtToKrw" style="display: none;">
                        <i class="bi bi-info-circle"></i>
                        <span>예상 금액: <span class="conversion-value" id="usdtToKrwValue">0</span>원</span>
                    </div>
                </div>
                
                <!-- 원화 입력 -->
                <div class="form-group" id="krwInputGroup" style="display: none;">
                    <label class="form-label">구매 금액 <span>*</span></label>
                    <div class="input-group">
                        <input type="number" id="krwAmount" class="tether-input" 
                               placeholder="원화 금액 입력" min="1000000" step="1000">
                        <span class="input-group-text">원</span>
                    </div>
                    <p class="form-hint">최소 구매 금액: <?php echo number_format($min_quantity * $current_buy_price); ?>원</p>
                    <div class="conversion-info" id="krwToUsdt" style="display: none;">
                        <i class="bi bi-info-circle"></i>
                        <span>구매 가능: <span class="conversion-value" id="krwToUsdtValue">0</span> USDT</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">입금받을 주소 <span>*</span></label>
                    <input type="text" name="wallet_address" class="tether-input" 
                           placeholder="해외거래소 USDT(TRC20) 지갑 주소" required>
                    <p class="form-hint">⚠️ 반드시 해외거래소 주소를 입력해주세요</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">이용 해외송금업체 <span>*</span></label>
                    <input type="text" name="transfer_company" class="tether-input" 
                           placeholder="예: 한패스, 센트비, 유트랜스퍼 등" required>
                    <p class="form-hint">이용하실 해외송금업체명을 입력해주세요</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">신청자명 <span>*</span></label>
                    <input type="text" name="name" class="tether-input" 
                           value="<?php echo $member['mb_name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">연락처 <span>*</span></label>
                    <input type="text" name="hp" class="tether-input" 
                           value="<?php echo $member['mb_hp']; ?>" placeholder="010-0000-0000" required>
                </div>
                
                <!-- 계산 결과 -->
                <div class="calc-result" id="calcResult" style="display: none;">
                    <div class="calc-item">
                        <span class="calc-label">구매 수량</span>
                        <span class="calc-value" id="calcQuantity">0 USDT</span>
                    </div>
                    <div class="calc-item">
                        <span class="calc-label">적용 단가</span>
                        <span class="calc-value" id="calcPrice">0원</span>
                    </div>
                    <div class="calc-item calc-total">
                        <span class="calc-label">예상 총 금액</span>
                        <span class="calc-value" id="calcTotal">0원</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle"></i> 구매 신청
                </button>
                <button type="button" class="btn-secondary" onclick="closeModal()">
                    취소
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 가격 설정 모달 (관리자용) -->
<?php if($is_admin) { ?>
<div id="priceModal" class="modal">
    <div class="modal-content price-modal">
        <div class="modal-header">
            <h2 class="modal-title">테더 가격 조정</h2>
            <button class="modal-close" onclick="closePriceModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form class="price-form" method="post" action="">
            <input type="hidden" name="update_price" value="1">
            
            <div class="modal-body">
                <div class="market-info-box">
                    <div class="market-info-item">
                        <span class="info-label">현재 USDT 시장가</span>
                        <span class="info-value">₩<?php echo number_format($base_price); ?></span>
                    </div>
                    <div class="market-info-item">
                        <span class="info-label">기준</span>
                        <span class="info-value"><?php echo $price_source; ?></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">매수가 조정 (원) <span>*</span></label>
                    <div class="adjustment-input">
                        <span class="adj-prefix">시장가</span>
                        <input type="number" name="buy_adjustment" class="form-control" id="buyAdjustment"
                               value="<?php echo $buy_adjustment; ?>" 
                               placeholder="예: +20 또는 -10" step="1">
                    </div>
                    <p class="form-hint">시장가 대비 얼마나 높게/낮게 판매할지 설정 (예: +20, -10, 0)</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">매도가 조정 (원) <span>*</span></label>
                    <div class="adjustment-input">
                        <span class="adj-prefix">시장가</span>
                        <input type="number" name="sell_adjustment" class="form-control" id="sellAdjustment"
                               value="<?php echo $sell_adjustment; ?>" 
                               placeholder="예: -20 또는 +10" step="1">
                    </div>
                    <p class="form-hint">시장가 대비 얼마나 낮게/높게 매입할지 설정 (예: -20, +10, 0)</p>
                </div>
                
                <div class="calc-result">
                    <h4 style="font-size: 14px; margin-bottom: 12px;">최종 가격 미리보기</h4>
                    <div class="price-display">
                        <span class="price-display-label">매수가</span>
                        <span class="price-display-value" id="previewBuyPrice">
                            ₩<?php echo number_format($current_buy_price); ?>
                        </span>
                    </div>
                    <div class="price-display">
                        <span class="price-display-label">매도가</span>
                        <span class="price-display-value" id="previewSellPrice">
                            ₩<?php echo number_format($current_sell_price); ?>
                        </span>
                    </div>
                    <div class="price-display" style="background: #dbeafe;">
                        <span class="price-display-label">고객 구매가</span>
                        <span class="price-display-value" id="finalBuyPrice" style="color: #1e40af;">
                            ₩<?php echo number_format($final_buy_price); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle"></i> 저장
                </button>
                <button type="button" class="btn-secondary" onclick="closePriceModal()">
                    취소
                </button>
            </div>
        </form>
    </div>
</div>
<?php } ?>

<!-- 설정 모달 (관리자용) -->
<?php if($is_admin) { ?>
<div id="configModal" class="modal">
    <div class="modal-content price-modal">
        <div class="modal-header">
            <h2 class="modal-title">테더 구매 설정</h2>
            <button class="modal-close" onclick="closeConfigModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form class="config-form" method="post" action="">
            <input type="hidden" name="update_config" value="1">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">최소 구매 수량 (USDT) <span>*</span></label>
                    <input type="number" name="min_quantity" class="form-control" 
                           value="<?php echo $min_quantity; ?>" 
                           placeholder="예: 1000" required min="1" step="1">
                    <p class="form-hint">고객이 구매할 수 있는 최소 USDT 수량</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">연락 방식 <span>*</span></label>
                    <select name="contact_type" class="form-control" id="contactType" onchange="toggleContactInfo()">
                        <option value="signal" <?php echo $contact_type == 'signal' ? 'selected' : ''; ?>>시그널 ID 표시</option>
                        <option value="button" <?php echo $contact_type == 'button' ? 'selected' : ''; ?>>상담신청 버튼</option>
                    </select>
                </div>
                
                <div class="form-group" id="contactInfoGroup">
                    <label class="form-label">
                        <span id="contactInfoLabel">
                            <?php echo $contact_type == 'signal' ? '시그널 ID' : '상담 URL'; ?>
                        </span> <span>*</span>
                    </label>
                    <input type="text" name="contact_info" class="form-control" 
                           value="<?php echo $contact_info; ?>" 
                           placeholder="<?php echo $contact_type == 'signal' ? '@signal_id' : 'https://...'; ?>" required>
                    <p class="form-hint" id="contactInfoHint">
                        <?php echo $contact_type == 'signal' ? '표시할 시그널 ID를 입력하세요' : '상담신청 페이지 URL을 입력하세요'; ?>
                    </p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">버튼 텍스트 <span>*</span></label>
                    <input type="text" name="contact_button_text" class="form-control" 
                           value="<?php echo $contact_button_text; ?>" 
                           placeholder="예: 상담 신청" required>
                    <p class="form-hint">버튼에 표시될 텍스트</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle"></i> 저장
                </button>
                <button type="button" class="btn-secondary" onclick="closeConfigModal()">
                    취소
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 상담 모달 (고객용) -->
<div id="contactModal" class="modal">
    <div class="modal-content price-modal">
        <div class="modal-header">
            <h2 class="modal-title">상담 신청</h2>
            <button class="modal-close" onclick="closeContactModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="modal-body" style="text-align: center; padding: 40px;">
            <i class="bi bi-headset" style="font-size: 48px; color: #22c55e; margin-bottom: 20px;"></i>
            <p style="font-size: 16px; margin-bottom: 24px;">
                상담 페이지로 이동하시겠습니까?
            </p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="<?php echo $contact_info; ?>" target="_blank" class="btn-primary" style="text-decoration: none;">
                    <i class="bi bi-box-arrow-up-right"></i> 상담 페이지로 이동
                </a>
                <button type="button" class="btn-secondary" onclick="closeContactModal()">
                    취소
                </button>
            </div>
        </div>
    </div>
</div>
<?php } else if($contact_type == 'button' && $contact_info) { ?>
<!-- 상담 모달 (고객용) -->
<div id="contactModal" class="modal">
    <div class="modal-content price-modal">
        <div class="modal-header">
            <h2 class="modal-title">상담 신청</h2>
            <button class="modal-close" onclick="closeContactModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="modal-body" style="text-align: center; padding: 40px;">
            <i class="bi bi-headset" style="font-size: 48px; color: #22c55e; margin-bottom: 20px;"></i>
            <p style="font-size: 16px; margin-bottom: 24px;">
                상담 페이지로 이동하시겠습니까?
            </p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="<?php echo $contact_info; ?>" target="_blank" class="btn-primary" style="text-decoration: none;">
                    <i class="bi bi-box-arrow-up-right"></i> 상담 페이지로 이동
                </a>
                <button type="button" class="btn-secondary" onclick="closeContactModal()">
                    취소
                </button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
// 모달 제어
function openModal() {
    document.getElementById('applyModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('applyModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('tetherForm').reset();
    document.getElementById('calcResult').style.display = 'none';
}

function openContactModal() {
    document.getElementById('contactModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeContactModal() {
    document.getElementById('contactModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

<?php if($is_admin) { ?>
// 가격 설정 모달
function openPriceModal() {
    const modal = document.getElementById('priceModal');
    if(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closePriceModal() {
    const modal = document.getElementById('priceModal');
    if(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// 설정 모달
function openConfigModal() {
    document.getElementById('configModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeConfigModal() {
    document.getElementById('configModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// 연락 방식 변경시
function toggleContactInfo() {
    const contactType = document.getElementById('contactType').value;
    const label = document.getElementById('contactInfoLabel');
    const hint = document.getElementById('contactInfoHint');
    const input = document.querySelector('input[name="contact_info"]');
    
    if(contactType === 'signal') {
        label.textContent = '시그널 ID';
        hint.textContent = '표시할 시그널 ID를 입력하세요';
        input.placeholder = '@signal_id';
    } else {
        label.textContent = '상담 URL';
        hint.textContent = '상담신청 페이지 URL을 입력하세요';
        input.placeholder = 'https://...';
    }
}

// 조정값 변경시 가격 미리보기
const basePrice = <?php echo $base_price; ?>;

const buyAdjustmentInput = document.getElementById('buyAdjustment');
const sellAdjustmentInput = document.getElementById('sellAdjustment');

if(buyAdjustmentInput) {
    buyAdjustmentInput.addEventListener('input', updatePricePreview);
}
if(sellAdjustmentInput) {
    sellAdjustmentInput.addEventListener('input', updatePricePreview);
}

function updatePricePreview() {
    const buyAdj = parseInt(document.getElementById('buyAdjustment').value) || 0;
    const sellAdj = parseInt(document.getElementById('sellAdjustment').value) || 0;
    
    const buyPrice = basePrice + buyAdj;
    const sellPrice = basePrice + sellAdj;
    const finalBuyPrice = buyPrice; // 수수료 제거
    
    document.getElementById('previewBuyPrice').textContent = '₩' + buyPrice.toLocaleString();
    document.getElementById('previewSellPrice').textContent = '₩' + sellPrice.toLocaleString();
    document.getElementById('finalBuyPrice').textContent = '₩' + finalBuyPrice.toLocaleString();
}
<?php } ?>

// 모달 외부 클릭시 닫기
window.onclick = function(event) {
    const applyModal = document.getElementById('applyModal');
    const priceModal = document.getElementById('priceModal');
    const configModal = document.getElementById('configModal');
    const contactModal = document.getElementById('contactModal');
    
    if (event.target == applyModal) {
        closeModal();
    }
    <?php if($is_admin) { ?>
    if (event.target == priceModal) {
        closePriceModal();
    }
    if (event.target == configModal) {
        closeConfigModal();
    }
    <?php } ?>
    if (event.target == contactModal) {
        closeContactModal();
    }
}

// 수량 입력시 계산
const minQuantity = <?php echo $min_quantity; ?>;
const unitPrice = <?php echo $current_buy_price; ?>;
const quantityInput = document.getElementById('quantity');
const krwAmountInput = document.getElementById('krwAmount');
let currentInputType = 'usdt';

// 입력 타입 전환
function switchInputType(type) {
    currentInputType = type;
    
    // 탭 활성화 상태 변경
    document.querySelectorAll('.input-type-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.closest('.input-type-tab').classList.add('active');
    
    // 입력 그룹 표시/숨김
    if(type === 'usdt') {
        document.getElementById('usdtInputGroup').style.display = 'block';
        document.getElementById('krwInputGroup').style.display = 'none';
        document.getElementById('quantity').required = true;
        document.getElementById('krwAmount').required = false;
    } else {
        document.getElementById('usdtInputGroup').style.display = 'none';
        document.getElementById('krwInputGroup').style.display = 'block';
        document.getElementById('quantity').required = false;
        document.getElementById('krwAmount').required = true;
    }
    
    // 계산 결과 초기화
    document.getElementById('calcResult').style.display = 'none';
}

// USDT 입력시 원화 계산
if(quantityInput) {
    quantityInput.addEventListener('input', function() {
        const quantity = parseFloat(this.value) || 0;
        
        if(quantity > 0) {
            const totalPrice = quantity * unitPrice;
            
            // 변환 정보 표시
            document.getElementById('usdtToKrw').style.display = 'flex';
            document.getElementById('usdtToKrwValue').textContent = totalPrice.toLocaleString();
            
            // 최소 수량 이상일 때 계산 결과 표시
            if(quantity >= minQuantity) {
                updateCalcResult(quantity, totalPrice);
            } else {
                document.getElementById('calcResult').style.display = 'none';
            }
        } else {
            document.getElementById('usdtToKrw').style.display = 'none';
            document.getElementById('calcResult').style.display = 'none';
        }
    });
}

// 원화 입력시 USDT 계산
if(krwAmountInput) {
    krwAmountInput.addEventListener('input', function() {
        const krwAmount = parseFloat(this.value) || 0;
        
        if(krwAmount > 0) {
            const usdtQuantity = krwAmount / unitPrice;
            
            // 변환 정보 표시 (소수점 2자리까지)
            document.getElementById('krwToUsdt').style.display = 'flex';
            document.getElementById('krwToUsdtValue').textContent = usdtQuantity.toFixed(2);
            
            // USDT 수량을 실제 quantity 입력값에 설정
            document.getElementById('quantity').value = usdtQuantity.toFixed(2);
            
            // 최소 금액 이상일 때 계산 결과 표시
            const minKrwAmount = minQuantity * unitPrice;
            if(krwAmount >= minKrwAmount) {
                updateCalcResult(usdtQuantity, krwAmount);
            } else {
                document.getElementById('calcResult').style.display = 'none';
            }
        } else {
            document.getElementById('krwToUsdt').style.display = 'none';
            document.getElementById('calcResult').style.display = 'none';
            document.getElementById('quantity').value = '';
        }
    });
}

// 계산 결과 업데이트
function updateCalcResult(quantity, totalPrice) {
    document.getElementById('calcQuantity').textContent = quantity.toFixed(2) + ' USDT';
    document.getElementById('calcPrice').textContent = unitPrice.toLocaleString() + '원';
    document.getElementById('calcTotal').textContent = totalPrice.toLocaleString() + '원';
    document.getElementById('calcResult').style.display = 'block';
}

// 폼 제출
const tetherForm = document.getElementById('tetherForm');
if(tetherForm) {
    tetherForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let quantity;
        if(currentInputType === 'usdt') {
            quantity = parseFloat(document.getElementById('quantity').value);
        } else {
            // 원화 입력의 경우 계산된 USDT 수량 사용
            const krwAmount = parseFloat(document.getElementById('krwAmount').value);
            quantity = krwAmount / unitPrice;
            document.getElementById('quantity').value = quantity.toFixed(2);
        }
        
        if(quantity < minQuantity) {
            alert('최소 구매 수량은 ' + minQuantity.toLocaleString() + ' USDT입니다.');
            return false;
        }
        
        if(confirm('입력하신 정보로 테더 구매를 신청하시겠습니까?\n\n구매 수량: ' + quantity.toFixed(2) + ' USDT\n예상 금액: ' + (quantity * unitPrice).toLocaleString() + '원')) {
            this.submit();
        }
    });
}
</script>

<?php
include_once('./_tail.php');
?>
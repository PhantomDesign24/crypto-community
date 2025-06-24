<?php
/*
 * 파일명: otc.php
 * 위치: /otc.php
 * 기능: OTC 장외거래 메인 페이지
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = 'OTC 장외거래';
$g5['body_script'] = ' id="otc_page"';

/* 페이지 설정 */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows = 15; // 한 페이지에 보여줄 게시글 수

/* 검색 설정 */
$sfl = isset($_GET['sfl']) ? trim($_GET['sfl']) : '';
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';
$sca = isset($_GET['sca']) ? trim($_GET['sca']) : '';

// ===================================
// 데이터베이스 테이블 생성 (최초 1회)
// ===================================

/* OTC 게시판 테이블 */
$sql = "CREATE TABLE IF NOT EXISTS g5_otc (
    ot_id INT NOT NULL AUTO_INCREMENT,
    ot_type VARCHAR(10) NOT NULL DEFAULT '' COMMENT '거래타입: buy/sell',
    ot_category VARCHAR(50) NOT NULL DEFAULT '' COMMENT '카테고리',
    ot_subject VARCHAR(255) NOT NULL DEFAULT '',
    ot_content TEXT NOT NULL,
    ot_crypto_type VARCHAR(20) NOT NULL DEFAULT '' COMMENT '암호화폐 종류',
    ot_quantity DECIMAL(20,8) NOT NULL DEFAULT '0' COMMENT '수량',
    ot_price_krw DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '단가(원)',
    ot_total_krw DECIMAL(20,2) NOT NULL DEFAULT '0' COMMENT '총금액(원)',
    ot_bank_name VARCHAR(50) NOT NULL DEFAULT '' COMMENT '은행명',
    ot_bank_account VARCHAR(100) NOT NULL DEFAULT '' COMMENT '계좌번호',
    ot_wallet_address VARCHAR(255) NOT NULL DEFAULT '' COMMENT '지갑주소',
    ot_name VARCHAR(50) NOT NULL DEFAULT '',
    ot_hp VARCHAR(20) NOT NULL DEFAULT '' COMMENT '연락처',
    ot_password VARCHAR(255) NOT NULL DEFAULT '',
    ot_datetime DATETIME NOT NULL,
    ot_ip VARCHAR(50) NOT NULL DEFAULT '',
    ot_hit INT NOT NULL DEFAULT '0',
    ot_comment INT NOT NULL DEFAULT '0',
    ot_status TINYINT NOT NULL DEFAULT '0' COMMENT '상태: 0=거래중, 1=완료',
    mb_id VARCHAR(50) NOT NULL DEFAULT '',
    PRIMARY KEY (ot_id),
    KEY idx_type (ot_type),
    KEY idx_status (ot_status),
    KEY idx_datetime (ot_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

/* OTC 파일 업로드 테이블 */
$sql = "CREATE TABLE IF NOT EXISTS g5_otc_file (
    ot_id INT NOT NULL,
    bf_no INT NOT NULL,
    bf_source VARCHAR(255) NOT NULL DEFAULT '',
    bf_file VARCHAR(255) NOT NULL DEFAULT '',
    bf_filesize INT NOT NULL DEFAULT '0',
    bf_download INT NOT NULL DEFAULT '0',
    bf_datetime DATETIME NOT NULL,
    PRIMARY KEY (ot_id, bf_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

/* OTC 시세 테이블 */
$sql = "CREATE TABLE IF NOT EXISTS g5_otc_price (
    op_id INT NOT NULL AUTO_INCREMENT,
    op_buy_price DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'USDT 매수가',
    op_sell_price DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'USDT 매도가',
    op_datetime DATETIME NOT NULL COMMENT '수정일시',
    mb_id VARCHAR(20) NOT NULL DEFAULT '' COMMENT '수정자 ID',
    PRIMARY KEY (op_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

// ===================================
// OTC 시세 정보 가져오기
// ===================================

$otc_price = sql_fetch("SELECT * FROM g5_otc_price ORDER BY op_id DESC LIMIT 1");
if (!$otc_price) {
    // 기본값 설정
    $otc_price = array(
        'op_buy_price' => 1450,
        'op_sell_price' => 1430
    );
}

// ===================================
// 시세 업데이트 처리 (관리자)
// ===================================

if($is_admin && isset($_POST['price_update']) && $_POST['price_update'] == '1') {
    $new_buy_price = isset($_POST['new_buy_price']) ? (float)$_POST['new_buy_price'] : 0;
    $new_sell_price = isset($_POST['new_sell_price']) ? (float)$_POST['new_sell_price'] : 0;
    
    if($new_buy_price <= 0 || $new_sell_price <= 0) {
        alert('매수가와 매도가는 0보다 커야 합니다.');
    }
    
    if($new_buy_price <= $new_sell_price) {
        alert('매수가는 매도가보다 높아야 합니다.');
    }
    
    // 시세 저장
    $sql = "INSERT INTO g5_otc_price SET
            op_buy_price = '$new_buy_price',
            op_sell_price = '$new_sell_price',
            op_datetime = '".G5_TIME_YMDHIS."',
            mb_id = '{$member['mb_id']}'";
    
    sql_query($sql);
    
    alert('OTC 시세가 업데이트되었습니다.', './otc.php');
}

// ===================================
// 글 삭제 처리
// ===================================

if(isset($_GET['mode']) && $_GET['mode'] == 'delete' && isset($_GET['ot_id'])) {
    $ot_id = (int)$_GET['ot_id'];
    
    // 게시글 정보 조회
    $sql = "SELECT * FROM g5_otc WHERE ot_id = '$ot_id'";
    $post = sql_fetch($sql);
    
    if($post) {
        // 권한 확인 (관리자이거나 본인글)
        if($is_admin || ($is_member && $post['mb_id'] == $member['mb_id'])) {
            // 첨부파일 삭제
            $sql = "SELECT * FROM g5_otc_file WHERE ot_id = '$ot_id'";
            $result = sql_query($sql);
            while($row = sql_fetch_array($result)) {
                @unlink(G5_DATA_PATH.'/otc/'.$row['bf_file']);
            }
            sql_query("DELETE FROM g5_otc_file WHERE ot_id = '$ot_id'");
            sql_query("DELETE FROM g5_otc WHERE ot_id = '$ot_id'");
            alert('게시글이 삭제되었습니다.', './otc.php?page='.$page);
        } else if(!$is_member && isset($_POST['del_password'])) {
            // 비회원 비밀번호 확인
            if(password_verify($_POST['del_password'], $post['ot_password'])) {
                sql_query("DELETE FROM g5_otc WHERE ot_id = '$ot_id'");
                alert('게시글이 삭제되었습니다.', './otc.php?page='.$page);
            } else {
                alert('비밀번호가 일치하지 않습니다.');
            }
        }
    }
}

include_once('./_head.php');

// ===================================
// 카테고리 설정
// ===================================

$categories = array(
    '매수요청' => array('icon' => 'bi-cart-plus-fill', 'color' => '#dc2626', 'type' => 'buy'),
    '매도요청' => array('icon' => 'bi-cart-dash-fill', 'color' => '#2563eb', 'type' => 'sell'),
    '거래완료' => array('icon' => 'bi-check-circle-fill', 'color' => '#059669', 'type' => 'complete')
);

// ===================================
// 게시글 목록 조회
// ===================================

/* 검색 SQL */
$sql_search = "";
if ($stx) {
    $sql_search .= " AND ( ";
    switch ($sfl) {
        case 'subject':
            $sql_search .= " ot_subject LIKE '%{$stx}%' ";
            break;
        case 'content':
            $sql_search .= " ot_content LIKE '%{$stx}%' ";
            break;
        case 'name':
            $sql_search .= " ot_name LIKE '%{$stx}%' ";
            break;
        default:
            $sql_search .= " (ot_subject LIKE '%{$stx}%' OR ot_content LIKE '%{$stx}%') ";
    }
    $sql_search .= " ) ";
}

/* 카테고리 필터 */
if($sca) {
    if($sca == '거래완료') {
        $sql_search .= " AND ot_status = '1' ";
    } else {
        $sql_search .= " AND ot_category = '".sql_real_escape_string($sca)."' AND ot_status = '0' ";
    }
}

/* 전체 게시글 수 */
$sql = "SELECT COUNT(*) as cnt FROM g5_otc WHERE 1=1 {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page = ceil($total_count / $rows);

/* 시작 위치 */
$from_record = ($page - 1) * $rows;

/* 게시글 목록 가져오기 */
$sql = "SELECT * FROM g5_otc 
        WHERE 1=1 {$sql_search}
        ORDER BY ot_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

$list = array();
$num = $total_count - (($page - 1) * $rows);

while($row = sql_fetch_array($result)) {
    $row['num'] = $num--;
    $list[] = $row;
}
?>

<style>
/* OTC 페이지 전체 */
#otc_wrap {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

/* 페이지 헤더 */
.otc-header {
    background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    color: white;
    position: relative;
    overflow: hidden;
}

.otc-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: rotate(45deg);
}

.otc-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    position: relative;
}

.otc-header p {
    font-size: 16px;
    opacity: 0.9;
    position: relative;
}

/* OTC 가격 정보 */
.otc-price-info {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 30px;
}

.price-item {
    flex: 1;
    text-align: center;
    padding: 20px;
    border-radius: 12px;
    background: #f8fafc;
    transition: all 0.3s;
}

.price-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.price-item.buy {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.price-item.sell {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
}

.price-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    font-weight: 500;
}

.price-value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 4px;
}

.price-item.buy .price-value {
    color: #dc2626;
}

.price-item.sell .price-value {
    color: #2563eb;
}

.price-unit {
    font-size: 12px;
    color: #9ca3af;
}

/* 사이트 직거래 버튼 */
.direct-trade-btn {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(245, 158, 11, 0.3);
    transition: all 0.3s;
    text-decoration: none;
    cursor: pointer;
    border: none;
}

.direct-trade-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

/* 메인 컨테이너 */
.otc-main-container {
    display: flex;
    gap: 30px;
}

/* 게시판 영역 */
.otc-board-section {
    flex: 1;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

/* 카테고리 */
#otc_cate {
    background: #f9fafb;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

#otc_cate ul {
    display: flex;
    gap: 10px;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
}

#otc_cate li {
    list-style: none;
}

#otc_cate a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s;
}

#otc_cate a:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#otc_cate a.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 게시판 정보 */
.otc-board-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 2px solid #1f2937;
}

.board-info-left {
    font-size: 14px;
    color: #6b7280;
}

.board-info-right {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* 검색 */
#otc_search {
    display: flex;
    align-items: center;
    gap: 5px;
}

#otc_search select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

#otc_search input {
    padding: 8px 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    width: 200px;
}

#otc_search button {
    padding: 8px 14px;
    background: #374151;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
}

#otc_search button:hover {
    background: #1f2937;
}

/* 버튼 */
.btn_bo_user {
    display: flex;
    gap: 8px;
}

.btn_b02 {
    padding: 8px 16px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn_b02:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

/* 게시판 목록 */
#otc_list table {
    width: 100%;
    border-collapse: collapse;
}

#otc_list th {
    padding: 16px 8px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    text-align: center;
}

#otc_list td {
    padding: 16px 8px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #1f2937;
    text-align: center;
}

#otc_list tr:hover td {
    background: #f9fafb;
}

/* 거래 타입 배지 */
.trade-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.trade-type-badge.buy {
    background: #fee2e2;
    color: #dc2626;
}

.trade-type-badge.sell {
    background: #dbeafe;
    color: #2563eb;
}

.trade-type-badge.complete {
    background: #d1fae5;
    color: #059669;
}

/* 암호화폐 정보 */
.crypto-info {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.crypto-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

/* 제목 */
.td_subject {
    text-align: left !important;
    padding-left: 16px !important;
}

.otc_tit a {
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
}

.otc_tit a:hover {
    color: #3b82f6;
    text-decoration: underline;
}

/* 금액 */
.amount-text {
    font-weight: 600;
    color: #1f2937;
}

/* 상태 */
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.trading {
    background: #fef3c7;
    color: #f59e0b;
}

.status-badge.complete {
    background: #d1fae5;
    color: #059669;
}

/* 빈 테이블 */
.empty_table {
    padding: 80px 20px !important;
    text-align: center;
    color: #9ca3af;
}

.empty_table i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

/* 페이지네이션 */
.pg_wrap {
    padding: 30px;
    text-align: center;
    border-top: 1px solid #e5e7eb;
}

.pg {
    display: inline-flex;
    gap: 4px;
}

.pg a,
.pg strong {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #6b7280;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.pg a:hover {
    background: #f3f4f6;
    color: #374151;
    border-color: #d1d5db;
}

.pg strong {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    font-weight: 600;
}

/* 사이드 정보 */
.otc-side-section {
    width: 320px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* 안내사항 */
.otc-notice-box {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.notice-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.notice-header i {
    color: #f59e0b;
}

.notice-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notice-item {
    display: flex;
    gap: 8px;
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
}

.notice-item i {
    color: #3b82f6;
    margin-top: 2px;
    flex-shrink: 0;
}

/* 시세 변경 폼 (관리자) */
.price-edit-form {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
    display: none;
}

.price-edit-header {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.price-edit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.price-input-group {
    position: relative;
}

.price-input-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    display: block;
}

.price-input-wrapper {
    position: relative;
}

.price-input-wrapper input {
    width: 100%;
    padding: 12px 16px 12px 36px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
}

.price-input-wrapper input:focus {
    outline: none;
    border-color: #3b82f6;
}

.price-input-wrapper .currency {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-weight: 600;
}

.price-edit-buttons {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn-price-update {
    padding: 10px 20px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-price-cancel {
    padding: 10px 20px;
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-price-edit {
    background: #6366f1;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* 직거래 모달 */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow-y: auto;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border-radius: 16px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    animation: modalFadeIn 0.3s;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 24px;
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    border-radius: 16px 16px 0 0;
    position: relative;
}

.modal-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
}

.close {
    position: absolute;
    right: 24px;
    top: 24px;
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
}

.close:hover {
    transform: rotate(90deg);
}

.modal-body {
    padding: 30px;
}

/* 거래 타입 선택 */
.trade-type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 30px;
}

.trade-type-option {
    position: relative;
}

.trade-type-option input[type="radio"] {
    display: none;
}

.trade-type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 24px;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
}

.trade-type-label:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.trade-type-option input[type="radio"]:checked + .trade-type-label {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.trade-type-icon {
    font-size: 32px;
}

.trade-type-text {
    font-size: 16px;
    font-weight: 600;
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

/* 입력 그룹 */
.input-group {
    display: flex;
    align-items: center;
}

.input-group-text {
    padding: 12px 16px;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-right: none;
    border-radius: 8px 0 0 8px;
    color: #6b7280;
}

.input-group .form-control {
    border-radius: 0 8px 8px 0;
}

/* 안내 메시지 */
.alert {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    gap: 12px;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
}

.alert i {
    font-size: 20px;
    flex-shrink: 0;
}

/* 모달 버튼 */
.modal-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.btn-submit {
    flex: 1;
    padding: 14px 24px;
    background: #f59e0b;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    background: #f97316;
    transform: translateY(-1px);
}

.btn-cancel {
    flex: 1;
    padding: 14px 24px;
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #e5e7eb;
}

/* 반응형 */
@media (max-width: 1200px) {
    .otc-main-container {
        flex-direction: column;
    }
    
    .otc-side-section {
        width: 100%;
    }
}

@media (max-width: 768px) {
    #otc_wrap {
        padding: 0 15px;
    }
    
    .otc-header {
        padding: 30px 20px;
    }
    
    .otc-header h1 {
        font-size: 24px;
    }
    
    .otc-price-info {
        flex-direction: column;
        gap: 16px;
    }
    
    .price-value {
        font-size: 24px;
    }
    
    .otc-board-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    #otc_search input {
        width: 150px;
    }
    
    .td_type,
    .td_crypto,
    .td_status,
    .td_hit {
        display: none;
    }
    
    #otc_list th:nth-child(2),
    #otc_list th:nth-child(3),
    #otc_list th:nth-child(6),
    #otc_list th:nth-child(7) {
        display: none;
    }
    
    .modal-content {
        margin: 10% auto;
        width: 95%;
    }
    
    .trade-type-selector {
        grid-template-columns: 1fr;
    }
}
</style>

<div id="otc_wrap">
    <!-- OTC 헤더 -->
    <div class="otc-header">
        <h1><i class="bi bi-currency-exchange"></i> OTC 장외거래</h1>
        <p>안전하고 신뢰할 수 있는 P2P 암호화폐 거래 플랫폼</p>
    </div>
    
    <!-- 코인 시세 -->
    <?php include_once(G5_PATH.'/coin.php');?>
    
    <!-- OTC 가격 정보 -->
    <div class="otc-price-info">
        <div class="price-item buy">
            <div class="price-label">USDT 매수가</div>
            <div class="price-value">₩<?php echo number_format($otc_price['op_buy_price']); ?></div>
            <div class="price-unit">1 USDT 당 가격</div>
        </div>
        
        <div class="price-item sell">
            <div class="price-label">USDT 매도가</div>
            <div class="price-value">₩<?php echo number_format($otc_price['op_sell_price']); ?></div>
            <div class="price-unit">1 USDT 당 가격</div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <button onclick="openDirectTradeModal()" class="direct-trade-btn">
                <i class="bi bi-lightning-charge-fill"></i> 사이트 직거래
            </button>
            <?php if($is_admin) { ?>
            <button onclick="togglePriceEdit()" class="btn-price-edit">
                <i class="bi bi-gear"></i> 시세 변경
            </button>
            <?php } ?>
        </div>
    </div>
    
    <?php if($is_admin) { ?>
    <!-- 시세 변경 폼 (관리자) -->
    <div class="price-edit-form" id="priceEditForm">
        <form method="post" onsubmit="return confirmPriceUpdate(this);">
            <input type="hidden" name="price_update" value="1">
            
            <div class="price-edit-header">
                <span><i class="bi bi-currency-exchange"></i> USDT 시세 변경</span>
                <button type="button" onclick="togglePriceEdit()" style="background: none; border: none; font-size: 24px; cursor: pointer;">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            
            <div class="price-edit-grid">
                <div class="price-input-group">
                    <label class="price-input-label">새 매수가격</label>
                    <div class="price-input-wrapper">
                        <span class="currency">₩</span>
                        <input type="number" name="new_buy_price" value="<?php echo $otc_price['op_buy_price']; ?>" 
                               min="0" step="0.01" required>
                    </div>
                </div>
                
                <div class="price-input-group">
                    <label class="price-input-label">새 매도가격</label>
                    <div class="price-input-wrapper">
                        <span class="currency">₩</span>
                        <input type="number" name="new_sell_price" value="<?php echo $otc_price['op_sell_price']; ?>" 
                               min="0" step="0.01" required>
                    </div>
                </div>
            </div>
            
            <div style="padding: 12px; background: #fef3c7; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 14px; color: #92400e;">
                    <i class="bi bi-info-circle"></i> 
                    매수가는 매도가보다 높게 설정해야 합니다. 
                    변경된 시세는 즉시 적용됩니다.
                </p>
            </div>
            
            <div class="price-edit-buttons">
                <button type="button" onclick="togglePriceEdit()" class="btn-price-cancel">취소</button>
                <button type="submit" class="btn-price-update">시세 변경</button>
            </div>
        </form>
    </div>
    <?php } ?>
    
    <!-- 메인 컨테이너 -->
    <div class="otc-main-container">
        <!-- 게시판 영역 -->
        <div class="otc-board-section">
            <!-- 카테고리 -->
            <nav id="otc_cate">
                <ul>
                    <li>
                        <a href="?sca=" class="<?php echo !$sca ? 'active' : ''; ?>">
                            <i class="bi bi-grid-3x3-gap"></i> 전체
                        </a>
                    </li>
                    <?php foreach($categories as $cate_name => $cate_info) { ?>
                    <li>
                        <a href="?sca=<?php echo urlencode($cate_name); ?>" 
                           class="<?php echo $sca == $cate_name ? 'active' : ''; ?>"
                           style="<?php echo $sca == $cate_name ? 'background: '.$cate_info['color'].'; border-color: '.$cate_info['color'].';' : ''; ?>">
                            <i class="bi <?php echo $cate_info['icon']; ?>"></i> <?php echo $cate_name; ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>
            
            <!-- 게시판 정보 -->
            <div class="otc-board-info">
                <div class="board-info-left">
                    <span>Total <?php echo number_format($total_count); ?>건</span>
                    <span> · </span>
                    <span><?php echo $page; ?> 페이지</span>
                </div>
                <div class="board-info-right">
                    <!-- 검색 -->
                    <form id="otc_search" method="get">
                        <input type="hidden" name="sca" value="<?php echo $sca; ?>">
                        <select name="sfl">
                            <option value="subject" <?php echo $sfl == 'subject' ? 'selected' : ''; ?>>제목</option>
                            <option value="content" <?php echo $sfl == 'content' ? 'selected' : ''; ?>>내용</option>
                            <option value="name" <?php echo $sfl == 'name' ? 'selected' : ''; ?>>작성자</option>
                        </select>
                        <input type="text" name="stx" value="<?php echo $stx; ?>" placeholder="검색어">
                        <button type="submit"><i class="bi bi-search"></i></button>
                    </form>
                    
                    <!-- 버튼 -->
                    <div class="btn_bo_user">
                        <a href="./otc_write.php" class="btn_b02">
                            <i class="bi bi-pencil"></i> 거래 등록
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- 게시판 목록 -->
            <div id="otc_list">
                <table>
                    <thead>
                        <tr>
                            <th scope="col" class="td_num">번호</th>
                            <th scope="col" class="td_type">거래타입</th>
                            <th scope="col" class="td_crypto">암호화폐</th>
                            <th scope="col">제목</th>
                            <th scope="col" class="td_amount">거래금액</th>
                            <th scope="col" class="td_name">작성자</th>
                            <th scope="col" class="td_date">날짜</th>
                            <th scope="col" class="td_status">상태</th>
                            <th scope="col" class="td_hit">조회</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row) { ?>
                        <tr>
                            <td class="td_num"><?php echo $row['num']; ?></td>
                            <td class="td_type">
                                <span class="trade-type-badge <?php echo $row['ot_type']; ?>">
                                    <?php echo $row['ot_type'] == 'buy' ? '매수' : '매도'; ?>
                                </span>
                            </td>
                            <td class="td_crypto">
                                <div class="crypto-info">
                                    <span class="crypto-icon"><?php echo substr($row['ot_crypto_type'], 0, 1); ?></span>
                                    <span><?php echo $row['ot_crypto_type']; ?></span>
                                </div>
                            </td>
                            <td class="td_subject">
                                <div class="otc_tit">
                                    <a href="./otc_view.php?ot_id=<?php echo $row['ot_id']; ?>&page=<?php echo $page; ?>">
                                        <?php echo get_text($row['ot_subject']); ?>
                                    </a>
                                    <?php if($row['ot_comment'] > 0) { ?>
                                    <span class="cnt_cmt">[<?php echo $row['ot_comment']; ?>]</span>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="td_amount">
                                <span class="amount-text">₩<?php echo number_format($row['ot_total_krw']); ?></span>
                            </td>
                            <td class="td_name"><?php echo get_text($row['ot_name']); ?></td>
                            <td class="td_date"><?php echo substr($row['ot_datetime'], 5, 5); ?></td>
                            <td class="td_status">
                                <span class="status-badge <?php echo $row['ot_status'] ? 'complete' : 'trading'; ?>">
                                    <?php echo $row['ot_status'] ? '완료' : '거래중'; ?>
                                </span>
                            </td>
                            <td class="td_hit"><?php echo number_format($row['ot_hit']); ?></td>
                        </tr>
                        <?php } ?>
                        
                        <?php if(count($list) == 0) { ?>
                        <tr>
                            <td colspan="9" class="empty_table">
                                <i class="bi bi-inbox"></i>
                                <p>등록된 거래가 없습니다.</p>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 페이지네이션 -->
            <div class="pg_wrap">
                <span class="pg">
                    <?php if($page > 1) { ?>
                    <a href="?page=1<?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_start">처음</a>
                    <a href="?page=<?php echo $page-1; ?><?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_prev">이전</a>
                    <?php } ?>
                    
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_page, $page + 2);
                    
                    for($i = $start_page; $i <= $end_page; $i++) {
                        if($i == $page) {
                            echo '<strong class="pg_current">'.$i.'</strong>';
                        } else {
                            echo '<a href="?page='.$i.($sca ? '&sca='.$sca : '').($stx ? '&sfl='.$sfl.'&stx='.$stx : '').'" class="pg_page">'.$i.'</a>';
                        }
                    }
                    ?>
                    
                    <?php if($page < $total_page) { ?>
                    <a href="?page=<?php echo $page+1; ?><?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_next">다음</a>
                    <a href="?page=<?php echo $total_page; ?><?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_end">맨끝</a>
                    <?php } ?>
                </span>
            </div>
        </div>
        
        <!-- 사이드 섹션 -->
        <div class="otc-side-section">
            <!-- 안내사항 -->
            <div class="otc-notice-box">
                <div class="notice-header">
                    <i class="bi bi-exclamation-triangle"></i> 거래 시 주의사항
                </div>
                <div class="notice-list">
                    <div class="notice-item">
                        <i class="bi bi-check-circle"></i>
                        <span>최소 거래 금액은 1,000만원 이상입니다.</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-check-circle"></i>
                        <span>거래 전 상대방의 신원을 반드시 확인하세요.</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-check-circle"></i>
                        <span>입금 확인 후 코인을 전송하세요.</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-check-circle"></i>
                        <span>거래 내역은 반드시 스크린샷으로 보관하세요.</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-check-circle"></i>
                        <span>사기 거래 발견 시 즉시 신고해주세요.</span>
                    </div>
                </div>
            </div>
            
            <!-- 거래 가이드 -->
            <div class="otc-notice-box">
                <div class="notice-header">
                    <i class="bi bi-book"></i> 거래 프로세스
                </div>
                <div class="notice-list">
                    <div class="notice-item">
                        <i class="bi bi-1-circle-fill"></i>
                        <span>거래 게시글 작성 또는 기존 게시글 확인</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-2-circle-fill"></i>
                        <span>거래 상대방과 연락하여 거래 조건 협의</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-3-circle-fill"></i>
                        <span>신원 확인 및 거래 계약서 작성</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-4-circle-fill"></i>
                        <span>입금 확인 후 코인 전송</span>
                    </div>
                    <div class="notice-item">
                        <i class="bi bi-5-circle-fill"></i>
                        <span>거래 완료 확인 및 평가</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 직거래 모달 -->
<div id="directTradeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="bi bi-lightning-charge-fill"></i> 사이트 직거래</h2>
            <span class="close" onclick="closeDirectTradeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="directTradeForm" method="post" action="./otc_direct_process.php" enctype="multipart/form-data">
                <!-- 거래 타입 선택 -->
                <div class="trade-type-selector">
                    <div class="trade-type-option">
                        <input type="radio" name="trade_type" value="buy" id="trade_buy" required>
                        <label for="trade_buy" class="trade-type-label">
                            <i class="bi bi-cart-plus-fill trade-type-icon"></i>
                            <span class="trade-type-text">USDT 매수</span>
                        </label>
                    </div>
                    <div class="trade-type-option">
                        <input type="radio" name="trade_type" value="sell" id="trade_sell" required>
                        <label for="trade_sell" class="trade-type-label">
                            <i class="bi bi-cart-dash-fill trade-type-icon"></i>
                            <span class="trade-type-text">USDT 매도</span>
                        </label>
                    </div>
                </div>
                
                <!-- 안내 메시지 -->
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>사이트 직거래 안내</strong><br>
                        최소 거래 금액은 1,000만원이며, 실시간 시세가 적용됩니다.
                    </div>
                </div>
                
                <!-- 매수 폼 -->
                <div id="buyForm" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">신청자 이름</label>
                        <input type="text" name="buy_name" class="form-control" placeholder="실명을 입력하세요">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">매수 수량</label>
                        <div class="input-group">
                            <input type="number" name="buy_quantity" class="form-control" placeholder="0" min="0" step="0.01">
                            <span class="input-group-text">USDT</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">입금받을 USDT 주소</label>
                        <input type="text" name="buy_wallet" class="form-control" placeholder="USDT 지갑 주소를 입력하세요">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">신분증 사진 + 자필 확인서</label>
                        <input type="file" name="buy_verification" class="form-control" accept="image/*">
                        <small class="text-muted">
                            "본인은 크립토허브에서 개인적인 용도로 000 USDT를 원화 0000원에 구매함" 
                            수기 자필 A4 용지와 함께 촬영한 셀카 1장
                        </small>
                    </div>
                </div>
                
                <!-- 매도 폼 -->
                <div id="sellForm" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">신청자 이름</label>
                        <input type="text" name="sell_name" class="form-control" placeholder="실명을 입력하세요">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">매도 수량</label>
                        <div class="input-group">
                            <input type="number" name="sell_quantity" class="form-control" placeholder="0" min="0" step="0.01">
                            <span class="input-group-text">USDT</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">입금받을 은행</label>
                        <select name="sell_bank" class="form-select">
                            <option value="">은행 선택</option>
                            <option value="KB국민은행">KB국민은행</option>
                            <option value="신한은행">신한은행</option>
                            <option value="우리은행">우리은행</option>
                            <option value="하나은행">하나은행</option>
                            <option value="기업은행">기업은행</option>
                            <option value="농협은행">농협은행</option>
                            <option value="SC제일은행">SC제일은행</option>
                            <option value="한국씨티은행">한국씨티은행</option>
                            <option value="카카오뱅크">카카오뱅크</option>
                            <option value="케이뱅크">케이뱅크</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">계좌번호</label>
                        <input type="text" name="sell_account" class="form-control" placeholder="계좌번호를 입력하세요">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">연락처</label>
                        <input type="tel" name="sell_phone" class="form-control" placeholder="010-0000-0000">
                    </div>
                </div>
                
                <!-- 버튼 -->
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeDirectTradeModal()">취소</button>
                    <button type="submit" class="btn-submit">거래 신청</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// 시세 변경 폼 토글
function togglePriceEdit() {
    const form = document.getElementById('priceEditForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// 시세 변경 확인
function confirmPriceUpdate(f) {
    const buyPrice = parseFloat(f.new_buy_price.value);
    const sellPrice = parseFloat(f.new_sell_price.value);
    
    if(buyPrice <= 0 || sellPrice <= 0) {
        alert('매수가와 매도가는 0보다 커야 합니다.');
        return false;
    }
    
    if(buyPrice <= sellPrice) {
        alert('매수가는 매도가보다 높아야 합니다.');
        return false;
    }
    
    const spread = buyPrice - sellPrice;
    const spreadRate = (spread / sellPrice) * 100;
    
    return confirm('USDT 시세를 변경하시겠습니까?\n\n' +
                   '매수가: ₩' + buyPrice.toLocaleString() + '\n' +
                   '매도가: ₩' + sellPrice.toLocaleString() + '\n' +
                   '스프레드: ' + spreadRate.toFixed(2) + '%');
}

// 직거래 모달 열기
function openDirectTradeModal() {
    document.getElementById('directTradeModal').style.display = 'block';
}

// 직거래 모달 닫기
function closeDirectTradeModal() {
    document.getElementById('directTradeModal').style.display = 'none';
    document.getElementById('directTradeForm').reset();
    document.getElementById('buyForm').style.display = 'none';
    document.getElementById('sellForm').style.display = 'none';
}

// 거래 타입 선택 시 폼 전환
document.querySelectorAll('input[name="trade_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'buy') {
            document.getElementById('buyForm').style.display = 'block';
            document.getElementById('sellForm').style.display = 'none';
        } else {
            document.getElementById('buyForm').style.display = 'none';
            document.getElementById('sellForm').style.display = 'block';
        }
    });
});

// 모달 외부 클릭 시 닫기
window.onclick = function(event) {
    if (event.target == document.getElementById('directTradeModal')) {
        closeDirectTradeModal();
    }
}

// 삭제 확인
function confirmDelete(ot_id) {
    if(confirm('정말 삭제하시겠습니까?')) {
        location.href = '?mode=delete&ot_id=' + ot_id + '&page=<?php echo $page; ?>';
    }
}
</script>

<?php
include_once('./_tail.php');
?>
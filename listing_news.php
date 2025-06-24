<?php
/*
 * 파일명: listing_news.php
 * 위치: /
 * 기능: 신규상장소식 CRUD (한 페이지 관리)
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '신규상장소식';

/* 모드 설정 */
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';
$ln_id = isset($_GET['ln_id']) ? (int)$_GET['ln_id'] : 0;
$exchange = isset($_GET['exchange']) ? $_GET['exchange'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows = 20;

// ===================================
// 데이터베이스 테이블 생성
// ===================================

$sql = "CREATE TABLE IF NOT EXISTS g5_listing_news (
    ln_id INT NOT NULL AUTO_INCREMENT,
    ln_exchange VARCHAR(50) NOT NULL,
    ln_symbol VARCHAR(50) NOT NULL,
    ln_name_kr VARCHAR(100) NOT NULL,
    ln_name_en VARCHAR(100) NOT NULL,
    ln_date DATE NOT NULL,
    ln_time TIME,
    ln_type VARCHAR(50) DEFAULT 'KRW',
    ln_notice_url VARCHAR(255),
    ln_logo VARCHAR(255),
    ln_description TEXT,
    ln_datetime DATETIME NOT NULL,
    PRIMARY KEY (ln_id),
    KEY idx_exchange (ln_exchange),
    KEY idx_date (ln_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

// 기존 테이블에 ln_logo 컬럼이 없으면 추가
$sql = "SHOW COLUMNS FROM g5_listing_news LIKE 'ln_logo'";
$result = sql_query($sql);
if(sql_num_rows($result) == 0) {
    sql_query("ALTER TABLE g5_listing_news ADD ln_logo VARCHAR(255) AFTER ln_notice_url", FALSE);
}

// ===================================
// CRUD 처리
// ===================================

// 삭제 처리
if($mode == 'delete' && $ln_id && ($is_admin || $member['mb_grade'] >= 2)) {
    // 로고 파일 삭제
    $del_data = sql_fetch("SELECT ln_logo FROM g5_listing_news WHERE ln_id = '$ln_id'");
    if($del_data['ln_logo'] && file_exists(G5_DATA_PATH.'/listing/'.$del_data['ln_logo'])) {
        @unlink(G5_DATA_PATH.'/listing/'.$del_data['ln_logo']);
    }
    
    sql_query("DELETE FROM g5_listing_news WHERE ln_id = '$ln_id'");
    alert('삭제되었습니다.', './listing_news.php?exchange='.$exchange.'&page='.$page);
}

// 저장 처리
if($_SERVER['REQUEST_METHOD'] == 'POST' && ($is_admin || $member['mb_grade'] >= 2)) {
    $ln_exchange = isset($_POST['ln_exchange']) ? trim($_POST['ln_exchange']) : '';
    $ln_symbol = isset($_POST['ln_symbol']) ? strtoupper(trim($_POST['ln_symbol'])) : '';
    $ln_name_kr = isset($_POST['ln_name_kr']) ? trim($_POST['ln_name_kr']) : '';
    $ln_name_en = isset($_POST['ln_name_en']) ? trim($_POST['ln_name_en']) : '';
    $ln_date = isset($_POST['ln_date']) ? trim($_POST['ln_date']) : '';
    $ln_time = isset($_POST['ln_time']) ? trim($_POST['ln_time']) : '';
    $ln_type = isset($_POST['ln_type']) ? trim($_POST['ln_type']) : 'KRW';
    $ln_notice_url = isset($_POST['ln_notice_url']) ? trim($_POST['ln_notice_url']) : '';
    $ln_description = isset($_POST['ln_description']) ? trim($_POST['ln_description']) : '';
    
    if(!$ln_exchange || !$ln_symbol || !$ln_name_kr || !$ln_date) {
        alert('필수 항목을 모두 입력해주세요.');
    }
    
    // 파일 업로드 처리
    $ln_logo = '';
    if($mode == 'edit' && $ln_id) {
        // 기존 로고 정보 가져오기
        $old_data = sql_fetch("SELECT ln_logo FROM g5_listing_news WHERE ln_id = '$ln_id'");
        $ln_logo = isset($old_data['ln_logo']) ? $old_data['ln_logo'] : '';
    }
    
    if(isset($_FILES['ln_logo']) && $_FILES['ln_logo']['name'] && $_FILES['ln_logo']['error'] == 0) {
        // 업로드 디렉토리
        $upload_dir = G5_DATA_PATH.'/listing';
        if(!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0707);
            @chmod($upload_dir, 0707);
        }
        
        $filename = $_FILES['ln_logo']['name'];
        $tmp_name = $_FILES['ln_logo']['tmp_name'];
        
        // 확장자 확인
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'))) {
            alert('이미지 파일만 업로드 가능합니다.');
        }
        
        // 파일명 생성 (특수문자 제거)
        $safe_symbol = preg_replace('/[^a-zA-Z0-9]/', '', $ln_symbol);
        $new_filename = $safe_symbol.'_'.time().'.'.$ext;
        $dest_path = $upload_dir.'/'.$new_filename;
        
        if(move_uploaded_file($tmp_name, $dest_path)) {
            // 기존 파일 삭제
            if($mode == 'edit' && $ln_logo && file_exists($upload_dir.'/'.$ln_logo)) {
                @unlink($upload_dir.'/'.$ln_logo);
            }
            $ln_logo = $new_filename;
            @chmod($dest_path, 0606);
        }
    }
    
    // 로고 삭제 체크
    if(isset($_POST['ln_logo_del']) && $_POST['ln_logo_del'] == '1' && $mode == 'edit') {
        if($ln_logo && file_exists(G5_DATA_PATH.'/listing/'.$ln_logo)) {
            @unlink(G5_DATA_PATH.'/listing/'.$ln_logo);
        }
        $ln_logo = '';
    }
    
    if($mode == 'edit' && $ln_id) {
        // 수정
        $sql = "UPDATE g5_listing_news SET
                ln_exchange = '".sql_real_escape_string($ln_exchange)."',
                ln_symbol = '".sql_real_escape_string($ln_symbol)."',
                ln_name_kr = '".sql_real_escape_string($ln_name_kr)."',
                ln_name_en = '".sql_real_escape_string($ln_name_en)."',
                ln_date = '$ln_date',
                ln_time = '$ln_time',
                ln_type = '$ln_type',
                ln_notice_url = '".sql_real_escape_string($ln_notice_url)."',
                ln_logo = '$ln_logo',
                ln_description = '".sql_real_escape_string($ln_description)."'
                WHERE ln_id = '$ln_id'";
    } else {
        // 등록
        $sql = "INSERT INTO g5_listing_news SET
                ln_exchange = '".sql_real_escape_string($ln_exchange)."',
                ln_symbol = '".sql_real_escape_string($ln_symbol)."',
                ln_name_kr = '".sql_real_escape_string($ln_name_kr)."',
                ln_name_en = '".sql_real_escape_string($ln_name_en)."',
                ln_date = '$ln_date',
                ln_time = '$ln_time',
                ln_type = '$ln_type',
                ln_notice_url = '".sql_real_escape_string($ln_notice_url)."',
                ln_logo = '$ln_logo',
                ln_description = '".sql_real_escape_string($ln_description)."',
                ln_datetime = NOW()";
    }
    
    // SQL 실행
    $sql_result = sql_query($sql, false);
    
    // 에러 확인
    if(!$sql_result) {
        alert('저장 중 오류가 발생했습니다.');
    }
    
    alert('저장되었습니다.', './listing_news.php?exchange='.$exchange.'&page='.$page);
}

// 수정 모드일 때 데이터 가져오기
$ln = array();
if($mode == 'edit' && $ln_id) {
    $ln = sql_fetch("SELECT * FROM g5_listing_news WHERE ln_id = '$ln_id'");
    if(!$ln['ln_id']) {
        alert('존재하지 않는 데이터입니다.', './listing_news.php');
    }
}

include_once('./_head.php');

// ===================================
// 거래소 정보
// ===================================

$exchanges = array(
    'upbit' => array('name' => '업비트', 'color' => '#0062DF'),
    'bithumb' => array('name' => '빗썸', 'color' => '#F7931A'),
    'coinone' => array('name' => '코인원', 'color' => '#0090D0'),
    'korbit' => array('name' => '코빗', 'color' => '#4B3BFF'),
    'bybit' => array('name' => '바이비트', 'color' => '#FFD748'),
    'okx' => array('name' => 'OKX', 'color' => '#000000')
);

// ===================================
// 목록 조회
// ===================================

$sql_search = "";
if($exchange != 'all') {
    $sql_search = " WHERE ln_exchange = '".sql_real_escape_string($exchange)."' ";
}

$sql = "SELECT COUNT(*) as cnt FROM g5_listing_news {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page = ceil($total_count / $rows);
$from_record = ($page - 1) * $rows;

$sql = "SELECT * FROM g5_listing_news 
        {$sql_search}
        ORDER BY ln_date DESC, ln_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);
?>

<style>
/* 페이지 컨테이너 */
.listing-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 페이지 헤더 */
.page-header {
    text-align: center;
    margin-bottom: 50px;
}

.page-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.page-header h1 i {
    color: #3b82f6;
}

.page-header p {
    font-size: 18px;
    color: #6b7280;
}

/* 관리 버튼 */
.admin-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
    gap: 10px;
}

.btn-write {
    padding: 10px 20px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-write:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* 거래소 필터 */
.exchange-filter {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.exchange-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-decoration: none;
    color: #374151;
    font-weight: 500;
    transition: all 0.3s;
}

.exchange-btn:hover {
    transform: translateY(-2px);
    border-color: #3b82f6;
}

.exchange-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 입력 폼 */
.write-form {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 40px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.required {
    color: #ef4444;
}

.form-control,
.form-select {
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
    min-height: 80px;
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

/* 상장 목록 */
.listing-grid {
    display: grid;
    gap: 16px;
}

.listing-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.3s;
    position: relative;
    display: flex;
    align-items: center;
    gap: 20px;
}

.listing-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

/* 거래소 색상 표시 */
.exchange-indicator {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--exchange-color);
    border-radius: 12px 0 0 12px;
}

/* 코인 정보 */
.coin-logo {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    color: #6b7280;
    flex-shrink: 0;
}

.coin-logo img { width:100%; }

.coin-info {
    flex: 1;
}

.coin-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.coin-name {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.coin-symbol {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.exchange-badge {
    padding: 4px 10px;
    background: var(--exchange-color);
    color: white;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.coin-description {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 12px;
    line-height: 1.5;
}

/* 메타 정보 */
.coin-meta {
    display: flex;
    gap: 20px;
    font-size: 13px;
    color: #6b7280;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-item i {
    color: #9ca3af;
}

.market-type {
    padding: 2px 8px;
    background: #e0e7ff;
    color: #3730a3;
    border-radius: 4px;
    font-weight: 500;
}

.market-type.btc {
    background: #fef3c7;
    color: #d97706;
}

/* NEW 뱃지 */
.new-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 10px;
    background: #ef4444;
    color: white;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* 액션 버튼 */
.card-actions {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

.btn-edit {
    background: #f3f4f6;
    color: #374151;
}

.btn-edit:hover {
    background: #e5e7eb;
}

.btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-delete:hover {
    background: #fecaca;
}

/* 데이터 없음 */
.no-data {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.no-data i {
    font-size: 64px;
    color: #e5e7eb;
    margin-bottom: 20px;
}

/* 반응형 */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 28px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .listing-card {
        flex-direction: column;
        text-align: center;
    }
    
    .coin-meta {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .card-actions {
        margin-top: 12px;
    }
}
</style>

<div class="listing-container">
    <!-- 페이지 헤더 -->
    <div class="page-header">
        <h1><i class="bi bi-megaphone"></i> 신규상장소식</h1>
        <p>국내외 주요 거래소의 신규 상장 코인 정보를 확인하세요</p>
    </div>
    
    <?php if($is_admin || $member['mb_grade'] >= 2) { ?>
    <!-- 관리자 액션 -->
    <div class="admin-actions">
        <?php if($mode == 'list') { ?>
        <a href="?mode=write" class="btn-write">
            <i class="bi bi-plus-lg"></i> 상장소식 등록
        </a>
        <?php } else { ?>
        <a href="./listing_news.php" class="btn-write">
            <i class="bi bi-list"></i> 목록으로
        </a>
        <?php } ?>
    </div>
    <?php } ?>
    
    <?php if($mode == 'write' || $mode == 'edit') { ?>
    <!-- 입력 폼 -->
    <form method="post" class="write-form" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="<?php echo $mode; ?>">
        <input type="hidden" name="ln_id" value="<?php echo $ln_id; ?>">
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">거래소 <span class="required">*</span></label>
                <select name="ln_exchange" class="form-select" required>
                    <option value="">선택하세요</option>
                    <?php foreach($exchanges as $key => $ex) { ?>
                    <option value="<?php echo $key; ?>" <?php echo ($ln['ln_exchange'] ?? '') == $key ? 'selected' : ''; ?>>
                        <?php echo $ex['name']; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">심볼 <span class="required">*</span></label>
                <input type="text" name="ln_symbol" class="form-control" required
                       value="<?php echo $ln['ln_symbol'] ?? ''; ?>"
                       placeholder="BTC" style="text-transform: uppercase;">
            </div>
            
            <div class="form-group">
                <label class="form-label">한글명 <span class="required">*</span></label>
                <input type="text" name="ln_name_kr" class="form-control" required
                       value="<?php echo $ln['ln_name_kr'] ?? ''; ?>"
                       placeholder="비트코인">
            </div>
            
            <div class="form-group">
                <label class="form-label">영문명</label>
                <input type="text" name="ln_name_en" class="form-control"
                       value="<?php echo $ln['ln_name_en'] ?? ''; ?>"
                       placeholder="Bitcoin">
            </div>
            
            <div class="form-group">
                <label class="form-label">상장일 <span class="required">*</span></label>
                <input type="date" name="ln_date" class="form-control" required
                       value="<?php echo $ln['ln_date'] ?? date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">상장시간</label>
                <input type="time" name="ln_time" class="form-control"
                       value="<?php echo $ln['ln_time'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">마켓</label>
                <select name="ln_type" class="form-select">
                    <option value="KRW" <?php echo ($ln['ln_type'] ?? 'KRW') == 'KRW' ? 'selected' : ''; ?>>KRW</option>
                    <option value="BTC" <?php echo ($ln['ln_type'] ?? '') == 'BTC' ? 'selected' : ''; ?>>BTC</option>
                    <option value="USDT" <?php echo ($ln['ln_type'] ?? '') == 'USDT' ? 'selected' : ''; ?>>USDT</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">공지 URL</label>
                <input type="url" name="ln_notice_url" class="form-control"
                       value="<?php echo $ln['ln_notice_url'] ?? ''; ?>"
                       placeholder="https://...">
            </div>
            
            <div class="form-group">
                <label class="form-label">코인 로고</label>
                <input type="file" name="ln_logo" class="form-control" accept="image/*">
                <?php if($mode == 'edit' && $ln['ln_logo']) { ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo G5_DATA_URL.'/listing/'.$ln['ln_logo']; ?>" 
                         style="height: 40px; width: 40px; object-fit: contain; border-radius: 50%; background: #f3f4f6; padding: 4px;">
                    <label style="margin-left: 10px;">
                        <input type="checkbox" name="ln_logo_del" value="1"> 삭제
                    </label>
                </div>
                <?php } ?>
                <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                    JPG, PNG, GIF, WebP, SVG 형식 지원
                </p>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">설명</label>
            <textarea name="ln_description" class="form-control form-textarea"
                      placeholder="간단한 설명을 입력하세요"><?php echo $ln['ln_description'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="button" onclick="history.back();" class="btn btn-secondary">
                <i class="bi bi-x"></i> 취소
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> 저장
            </button>
        </div>
    </form>
    <?php } else { ?>
    
    <!-- 거래소 필터 -->
    <div class="exchange-filter">
        <a href="?exchange=all" class="exchange-btn <?php echo $exchange == 'all' ? 'active' : ''; ?>">
            <i class="bi bi-grid-3x3-gap"></i> 전체
        </a>
        <?php foreach($exchanges as $key => $ex) { ?>
        <a href="?exchange=<?php echo $key; ?>" 
           class="exchange-btn <?php echo $exchange == $key ? 'active' : ''; ?>">
            <?php echo $ex['name']; ?>
        </a>
        <?php } ?>
    </div>
    
    <!-- 상장 목록 -->
    <div class="listing-grid">
        <?php 
        while($row = sql_fetch_array($result)) { 
            $is_new = (strtotime($row['ln_date']) >= strtotime('-2 days'));
            $exchange_info = $exchanges[$row['ln_exchange']] ?? array('name' => $row['ln_exchange'], 'color' => '#6b7280');
        ?>
        <div class="listing-card" style="--exchange-color: <?php echo $exchange_info['color']; ?>;">
            <div class="exchange-indicator"></div>
            
            <?php if($is_new) { ?>
            <span class="new-badge">NEW</span>
            <?php } ?>
            
            <div class="coin-logo">
                <?php if($row['ln_logo'] && file_exists(G5_DATA_PATH.'/listing/'.$row['ln_logo'])) { ?>
                    <img src="<?php echo G5_DATA_URL.'/listing/'.$row['ln_logo']; ?>" alt="<?php echo $row['ln_symbol']; ?>">
                <?php } else { ?>
                    <?php echo substr($row['ln_symbol'], 0, 3); ?>
                <?php } ?>
            </div>
            
            <div class="coin-info">
                <div class="coin-header">
                    <span class="coin-name"><?php echo $row['ln_name_kr']; ?></span>
                    <span class="coin-symbol"><?php echo $row['ln_symbol']; ?></span>
                    <span class="exchange-badge" style="background: <?php echo $exchange_info['color']; ?>;">
                        <?php echo $exchange_info['name']; ?>
                    </span>
                </div>
                
                <?php if($row['ln_description']) { ?>
                <p class="coin-description"><?php echo nl2br($row['ln_description']); ?></p>
                <?php } ?>
                
                <div class="coin-meta">
                    <div class="meta-item">
                        <i class="bi bi-calendar-event"></i>
                        <?php echo date('Y.m.d', strtotime($row['ln_date'])); ?>
                        <?php if($row['ln_time']) { ?>
                        <?php echo date('H:i', strtotime($row['ln_time'])); ?>
                        <?php } ?>
                    </div>
                    <div class="meta-item">
                        <span class="market-type <?php echo strtolower($row['ln_type']); ?>">
                            <?php echo $row['ln_type']; ?>
                        </span>
                    </div>
                    <?php if($row['ln_notice_url']) { ?>
                    <div class="meta-item">
                        <a href="<?php echo $row['ln_notice_url']; ?>" target="_blank" style="color: #3b82f6;">
                            <i class="bi bi-box-arrow-up-right"></i> 공지
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            
            <?php if($is_admin || $member['mb_grade'] >= 2) { ?>
            <div class="card-actions">
                <a href="?mode=edit&ln_id=<?php echo $row['ln_id']; ?>" class="btn btn-sm btn-edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="?mode=delete&ln_id=<?php echo $row['ln_id']; ?>&exchange=<?php echo $exchange; ?>&page=<?php echo $page; ?>" 
                   onclick="return confirm('삭제하시겠습니까?');" class="btn btn-sm btn-delete">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        
        <?php if(sql_num_rows($result) == 0) { ?>
        <div class="no-data">
            <i class="bi bi-inbox"></i>
            <h3>상장 소식이 없습니다</h3>
            <p>아직 등록된 신규 상장 정보가 없습니다.</p>
        </div>
        <?php } ?>
    </div>
    
    <!-- 페이지네이션 -->
    <?php if($total_page > 1) { ?>
    <div class="pagination" style="text-align: center; margin-top: 40px;">
        <?php echo get_paging(10, $page, $total_page, '?exchange='.$exchange.'&page='); ?>
    </div>
    <?php } ?>
    
    <?php } ?>
</div>

<?php
include_once('./_tail.php');
?>
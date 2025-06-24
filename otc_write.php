<?php
/*
 * 파일명: otc_write.php
 * 위치: /otc_write.php
 * 기능: OTC 거래 글쓰기/수정
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'write';
$ot_id = isset($_GET['ot_id']) ? (int)$_GET['ot_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

/* 페이지 제목 */
$g5['title'] = $mode == 'edit' ? '거래 수정' : '거래 등록';

// ===================================
// 수정 모드일 경우 데이터 조회
// ===================================

$ot = array();
if($mode == 'edit' && $ot_id) {
    $sql = "SELECT * FROM g5_otc WHERE ot_id = '$ot_id'";
    $ot = sql_fetch($sql);
    
    if(!$ot['ot_id']) {
        alert('존재하지 않는 게시글입니다.', './otc.php');
    }
    
    // 권한 확인 (관리자이거나 본인글)
    if(!$is_admin && !($is_member && $ot['mb_id'] == $member['mb_id'])) {
        alert('수정 권한이 없습니다.', './otc.php');
    }
}

// ===================================
// OTC 시세 정보 가져오기
// ===================================

$otc_price = sql_fetch("SELECT * FROM g5_otc_price ORDER BY op_id DESC LIMIT 1");
if (!$otc_price) {
    $otc_price = array(
        'op_buy_price' => 1450,
        'op_sell_price' => 1430
    );
}

// ===================================
// 폼 전송 처리
// ===================================

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ot_type = isset($_POST['ot_type']) ? trim($_POST['ot_type']) : '';
    $ot_category = $ot_type == 'buy' ? '매수요청' : '매도요청';
    $ot_subject = isset($_POST['ot_subject']) ? trim($_POST['ot_subject']) : '';
    $ot_content = isset($_POST['ot_content']) ? trim($_POST['ot_content']) : '';
    $ot_crypto_type = isset($_POST['ot_crypto_type']) ? trim($_POST['ot_crypto_type']) : '';
    $ot_quantity = isset($_POST['ot_quantity']) ? (float)$_POST['ot_quantity'] : 0;
    $ot_price_krw = isset($_POST['ot_price_krw']) ? (float)$_POST['ot_price_krw'] : 0;
    $ot_total_krw = $ot_quantity * $ot_price_krw;
    
    // 거래 타입별 추가 정보
    if($ot_type == 'buy') {
        $ot_wallet_address = isset($_POST['ot_wallet_address']) ? trim($_POST['ot_wallet_address']) : '';
        $ot_bank_name = '';
        $ot_bank_account = '';
    } else {
        $ot_wallet_address = '';
        $ot_bank_name = isset($_POST['ot_bank_name']) ? trim($_POST['ot_bank_name']) : '';
        $ot_bank_account = isset($_POST['ot_bank_account']) ? trim($_POST['ot_bank_account']) : '';
    }
    
    $ot_name = isset($_POST['ot_name']) ? trim($_POST['ot_name']) : '';
    $ot_hp = isset($_POST['ot_hp']) ? trim($_POST['ot_hp']) : '';
    $ot_password = isset($_POST['ot_password']) ? trim($_POST['ot_password']) : '';
    
    // 유효성 검사
    if(!$ot_type) alert('거래 타입을 선택해주세요.');
    if(!$ot_subject) alert('제목을 입력해주세요.');
    if(!$ot_content) alert('내용을 입력해주세요.');
    if(!$ot_crypto_type) alert('암호화폐를 선택해주세요.');
    if($ot_quantity <= 0) alert('수량을 입력해주세요.');
    if($ot_price_krw <= 0) alert('단가를 입력해주세요.');
    
    // 최소 거래 금액 체크 (5만원)
    if($ot_total_krw < 50000) {
        alert('최소 거래 금액은 5만원 이상입니다.\\n현재 거래금액: '.number_format($ot_total_krw).'원');
    }
    
    // 회원/비회원 처리
    if($is_member) {
        $ot_name = $member['mb_nick'] ? $member['mb_nick'] : $member['mb_name'];
        $mb_id = $member['mb_id'];
    } else {
        if(!$ot_name) alert('이름을 입력해주세요.');
        if(!$ot_password && $mode == 'write') alert('비밀번호를 입력해주세요.');
        $mb_id = '';
    }
    
    if(!$ot_hp) alert('연락처를 입력해주세요.');
    
    if($mode == 'edit') {
        // 수정
        $sql = "UPDATE g5_otc SET
                ot_type = '".sql_real_escape_string($ot_type)."',
                ot_category = '".sql_real_escape_string($ot_category)."',
                ot_subject = '".sql_real_escape_string($ot_subject)."',
                ot_content = '".sql_real_escape_string($ot_content)."',
                ot_crypto_type = '".sql_real_escape_string($ot_crypto_type)."',
                ot_quantity = '$ot_quantity',
                ot_price_krw = '$ot_price_krw',
                ot_total_krw = '$ot_total_krw',
                ot_bank_name = '".sql_real_escape_string($ot_bank_name)."',
                ot_bank_account = '".sql_real_escape_string($ot_bank_account)."',
                ot_wallet_address = '".sql_real_escape_string($ot_wallet_address)."',
                ot_name = '".sql_real_escape_string($ot_name)."',
                ot_hp = '".sql_real_escape_string($ot_hp)."'
                WHERE ot_id = '$ot_id'";
        
        sql_query($sql);
        
    } else {
        // 새글 작성
        $ot_password_hash = '';
        if(!$is_member && $ot_password) {
            $ot_password_hash = password_hash($ot_password, PASSWORD_DEFAULT);
        }
        
        $sql = "INSERT INTO g5_otc SET
                ot_type = '".sql_real_escape_string($ot_type)."',
                ot_category = '".sql_real_escape_string($ot_category)."',
                ot_subject = '".sql_real_escape_string($ot_subject)."',
                ot_content = '".sql_real_escape_string($ot_content)."',
                ot_crypto_type = '".sql_real_escape_string($ot_crypto_type)."',
                ot_quantity = '$ot_quantity',
                ot_price_krw = '$ot_price_krw',
                ot_total_krw = '$ot_total_krw',
                ot_bank_name = '".sql_real_escape_string($ot_bank_name)."',
                ot_bank_account = '".sql_real_escape_string($ot_bank_account)."',
                ot_wallet_address = '".sql_real_escape_string($ot_wallet_address)."',
                ot_name = '".sql_real_escape_string($ot_name)."',
                ot_hp = '".sql_real_escape_string($ot_hp)."',
                ot_password = '$ot_password_hash',
                ot_datetime = '".G5_TIME_YMDHIS."',
                ot_ip = '".$_SERVER['REMOTE_ADDR']."',
                mb_id = '$mb_id'";
        
        sql_query($sql);
        $ot_id = sql_insert_id();
    }
    
    // ===================================
    // 파일 업로드 처리
    // ===================================
    
    $file_upload_msg = '';
    $upload_max_filesize = ini_get('upload_max_filesize');
    
    if(!preg_match("/([0-9]+)M/i", $upload_max_filesize, $match))
        $upload_max_filesize = 10 * 1048576; // 10MB
    else
        $upload_max_filesize = $match[1] * 1048576;
    
    // 파일 삭제
    if(isset($_POST['bf_file_del'])) {
        foreach($_POST['bf_file_del'] as $bf_no => $val) {
            if($val == 1) {
                $sql = "SELECT * FROM g5_otc_file WHERE ot_id = '$ot_id' AND bf_no = '$bf_no'";
                $file = sql_fetch($sql);
                if($file['bf_file']) {
                    @unlink(G5_DATA_PATH.'/otc/'.$file['bf_file']);
                }
                sql_query("DELETE FROM g5_otc_file WHERE ot_id = '$ot_id' AND bf_no = '$bf_no'");
            }
        }
    }
    
    // 파일 업로드
    if(isset($_FILES['bf_file']['name'])) {
        // 디렉토리 생성
        @mkdir(G5_DATA_PATH.'/otc', G5_DIR_PERMISSION);
        @chmod(G5_DATA_PATH.'/otc', G5_DIR_PERMISSION);
        
        for($i=0; $i<count($_FILES['bf_file']['name']); $i++) {
            if($_FILES['bf_file']['name'][$i] && is_uploaded_file($_FILES['bf_file']['tmp_name'][$i])) {
                
                // 파일 크기 체크
                if($_FILES['bf_file']['size'][$i] > $upload_max_filesize) {
                    $file_upload_msg .= '파일 '.$_FILES['bf_file']['name'][$i].'의 용량이 너무 큽니다.\\n';
                    continue;
                }
                
                // 파일명 생성
                $dest_file = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr(md5(uniqid(time())),0,8).'_'.str_replace('%', '', urlencode($_FILES['bf_file']['name'][$i]));
                $dest_file = preg_replace("/\s+/", "", $dest_file);
                
                // 파일 업로드
                $dest_path = G5_DATA_PATH.'/otc/'.$dest_file;
                move_uploaded_file($_FILES['bf_file']['tmp_name'][$i], $dest_path);
                chmod($dest_path, G5_FILE_PERMISSION);
                
                // DB 저장
                $sql = "SELECT MAX(bf_no) as max_no FROM g5_otc_file WHERE ot_id = '$ot_id'";
                $row = sql_fetch($sql);
                $bf_no = $row['max_no'] + 1;
                
                $sql = "INSERT INTO g5_otc_file SET
                        ot_id = '$ot_id',
                        bf_no = '$bf_no',
                        bf_source = '".sql_real_escape_string($_FILES['bf_file']['name'][$i])."',
                        bf_file = '$dest_file',
                        bf_filesize = '".$_FILES['bf_file']['size'][$i]."',
                        bf_datetime = '".G5_TIME_YMDHIS."'";
                sql_query($sql);
            }
        }
    }
    
    if($file_upload_msg)
        alert($file_upload_msg, './otc_view.php?ot_id='.$ot_id.'&page='.$page);
    else
        alert('거래가 '.($mode == 'edit' ? '수정' : '등록').'되었습니다.', './otc_view.php?ot_id='.$ot_id.'&page='.$page);
}

include_once('./_head.php');

// 스마트에디터 사용
include_once(G5_EDITOR_LIB);

// 암호화폐 목록
$crypto_list = array(
    'USDT' => 'Tether (USDT)',
    'BTC' => 'Bitcoin (BTC)',
    'ETH' => 'Ethereum (ETH)',
    'XRP' => 'Ripple (XRP)',
    'ADA' => 'Cardano (ADA)',
    'SOL' => 'Solana (SOL)',
    'DOGE' => 'Dogecoin (DOGE)'
);
?>

<style>
/* 글쓰기 폼 */
.otc-write-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 헤더 */
.otc-write-header {
    background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
    border-radius: 16px 16px 0 0;
    padding: 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-top: 20px;
}
.sound_only, .cke_sc { display:none; }

.otc-write-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: rotate(45deg);
}

.otc-write-header h2 {
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin: 0;
    position: relative;
}

.otc-write-header p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px;
    margin-top: 8px;
    position: relative;
}

/* 폼 래퍼 */
.otc-write-form {
    background: white;
    border-radius: 0 0 16px 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.form-body {
    padding: 40px;
}

/* 거래 타입 선택 */
.trade-type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
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
    gap: 12px;
    padding: 30px;
    background: #f9fafb;
    border: 3px solid #e5e7eb;
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

.trade-type-option input[type="radio"]:checked + .trade-type-label .trade-type-icon {
    color: white;
}

.trade-type-icon {
    font-size: 48px;
    color: #6b7280;
}

.trade-type-title {
    font-size: 20px;
    font-weight: 700;
}

.trade-type-desc {
    font-size: 14px;
    opacity: 0.9;
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 28px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 10px;
}

.form-label i {
    color: #6b7280;
    font-size: 16px;
}

.required {
    color: #ef4444;
    font-weight: 400;
}

/* 입력 요소 */
.form-control {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
    background: #f9fafb;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-select {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    background: #f9fafb;
    cursor: pointer;
}

/* 입력 그룹 */
.input-group {
    display: flex;
    align-items: stretch;
}

.input-group-text {
    padding: 14px 18px;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    border-right: none;
    border-radius: 10px 0 0 10px;
    color: #6b7280;
    font-weight: 500;
}

.input-group .form-control {
    border-radius: 0 10px 10px 0;
}

/* 금액 계산 표시 */
.amount-display {
    background: #fef3c7;
    border: 2px solid #fde68a;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    text-align: center;
}

.amount-label {
    font-size: 14px;
    color: #92400e;
    margin-bottom: 8px;
}

.amount-value {
    font-size: 28px;
    font-weight: 700;
    color: #f59e0b;
}

/* 에디터 영역 */
.editor-wrapper {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

/* 파일 업로드 */
.file-upload-area {
    border: 2px dashed #e5e7eb;
    border-radius: 10px;
    padding: 24px;
    text-align: center;
    background: #f9fafb;
}

.file-input-wrapper {
    margin-top: 16px;
}

.file-input-wrapper input[type="file"] {
    display: none;
}

.file-input-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

/* 조건별 입력 영역 */
.conditional-fields {
    background: #f8fafc;
    border-radius: 10px;
    padding: 24px;
    margin-bottom: 24px;
}

.field-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 24px 0;
}

/* 연락처 정보 */
.contact-info {
    background: #eff6ff;
    border: 2px solid #bfdbfe;
    border-radius: 10px;
    padding: 24px;
    margin-bottom: 24px;
}

/* 도움말 */
.form-help {
    font-size: 13px;
    color: #6b7280;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-help i {
    font-size: 14px;
}

/* 경고 메시지 */
.alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 24px;
    display: flex;
    gap: 12px;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
}

.alert i {
    font-size: 20px;
    flex-shrink: 0;
}

/* 액션 버튼 */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 32px 40px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.action-buttons {
    display: flex;
    gap: 12px;
}

.btn {
    padding: 14px 28px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: white;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

/* 반응형 */
@media (max-width: 768px) {
    .otc-write-container {
        padding: 0 15px;
    }
    
    .otc-write-header {
        padding: 30px 20px;
    }
    
    .form-body {
        padding: 24px 20px;
    }
    
    .trade-type-selector {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 16px;
        padding: 24px 20px;
    }
    
    .action-buttons {
        width: 100%;
        flex-direction: column-reverse;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="otc-write-container">
    <?php include_once(G5_PATH.'/coin.php');?>
    
    <form method="post" class="otc-write-form" enctype="multipart/form-data" onsubmit="return fotc_submit(this);">
        <input type="hidden" name="mode" value="<?php echo $mode; ?>">
        <input type="hidden" name="ot_id" value="<?php echo $ot_id; ?>">
        <input type="hidden" name="page" value="<?php echo $page; ?>">
        
        <!-- 헤더 -->
        <div class="otc-write-header">
            <h2><?php echo $mode == 'edit' ? '거래 수정' : '거래 등록'; ?></h2>
            <p>안전한 P2P 거래를 위해 정확한 정보를 입력해주세요</p>
        </div>
        
        <div class="form-body">
            <!-- 거래 타입 선택 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-cart"></i> 거래 타입 선택 <span class="required">*</span>
                </label>
                <div class="trade-type-selector">
                    <div class="trade-type-option">
                        <input type="radio" name="ot_type" value="buy" id="type_buy" 
                               <?php echo ($ot['ot_type'] ?? '') == 'buy' ? 'checked' : ''; ?> required>
                        <label for="type_buy" class="trade-type-label">
                            <i class="bi bi-cart-plus-fill trade-type-icon"></i>
                            <div class="trade-type-title">매수</div>
                            <div class="trade-type-desc">암호화폐를 구매합니다</div>
                        </label>
                    </div>
                    <div class="trade-type-option">
                        <input type="radio" name="ot_type" value="sell" id="type_sell" 
                               <?php echo ($ot['ot_type'] ?? '') == 'sell' ? 'checked' : ''; ?> required>
                        <label for="type_sell" class="trade-type-label">
                            <i class="bi bi-cart-dash-fill trade-type-icon"></i>
                            <div class="trade-type-title">매도</div>
                            <div class="trade-type-desc">암호화폐를 판매합니다</div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- 경고 메시지 -->
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                <div>
                    <strong>최소 거래 금액은 5만원입니다.</strong><br>
                    거래 전 반드시 상대방의 신원을 확인하고 안전한 거래를 진행하세요.
                </div>
            </div>
            
            <!-- 제목 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-type"></i> 제목 <span class="required">*</span>
                </label>
                <input type="text" name="ot_subject" class="form-control" required 
                       value="<?php echo get_text($ot['ot_subject'] ?? ''); ?>" 
                       placeholder="예: USDT 1만개 매수합니다"
                       maxlength="200">
            </div>
            
            <!-- 암호화폐 선택 및 수량 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-currency-bitcoin"></i> 암호화폐 정보 <span class="required">*</span>
                </label>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                    <select name="ot_crypto_type" class="form-select" required>
                        <option value="">암호화폐 선택</option>
                        <?php foreach($crypto_list as $symbol => $name) { ?>
                        <option value="<?php echo $symbol; ?>" 
                                <?php echo ($ot['ot_crypto_type'] ?? '') == $symbol ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                        <?php } ?>
                    </select>
                    <div class="input-group">
                        <input type="number" name="ot_quantity" class="form-control" required
                               value="<?php echo $ot['ot_quantity'] ?? ''; ?>"
                               placeholder="0" min="0" step="0.00000001" id="quantity">
                        <span class="input-group-text">개</span>
                    </div>
                </div>
            </div>
            
            <!-- 단가 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-cash-coin"></i> 단가 (KRW) <span class="required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text">₩</span>
                    <input type="number" name="ot_price_krw" class="form-control" required
                           value="<?php echo $ot['ot_price_krw'] ?? ''; ?>"
                           placeholder="0" min="0" step="0.01" id="price">
                </div>
                <p class="form-help">
                    <i class="bi bi-info-circle"></i> 
                    현재 USDT 시세 - 매수: ₩<?php echo number_format($otc_price['op_buy_price']); ?> / 
                    매도: ₩<?php echo number_format($otc_price['op_sell_price']); ?>
                </p>
            </div>
            
            <!-- 총 거래금액 표시 -->
            <div class="amount-display">
                <div class="amount-label">총 거래금액</div>
                <div class="amount-value" id="totalAmount">₩0</div>
            </div>
            
            <!-- 거래 타입별 추가 정보 -->
            <div class="conditional-fields">
                <!-- 매수 시 -->
                <div id="buyFields" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-wallet2"></i> 입금받을 지갑 주소 <span class="required">*</span>
                        </label>
                        <input type="text" name="ot_wallet_address" class="form-control"
                               value="<?php echo get_text($ot['ot_wallet_address'] ?? ''); ?>"
                               placeholder="암호화폐를 받을 지갑 주소를 입력하세요">
                        <p class="form-help">
                            <i class="bi bi-shield-check"></i> 지갑 주소를 정확히 입력해주세요. 잘못된 주소로 전송 시 복구가 불가능합니다.
                        </p>
                    </div>
                </div>
                
                <!-- 매도 시 -->
                <div id="sellFields" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-bank"></i> 입금받을 은행 정보 <span class="required">*</span>
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                            <select name="ot_bank_name" class="form-select">
                                <option value="">은행 선택</option>
                                <option value="KB국민은행" <?php echo ($ot['ot_bank_name'] ?? '') == 'KB국민은행' ? 'selected' : ''; ?>>KB국민은행</option>
                                <option value="신한은행" <?php echo ($ot['ot_bank_name'] ?? '') == '신한은행' ? 'selected' : ''; ?>>신한은행</option>
                                <option value="우리은행" <?php echo ($ot['ot_bank_name'] ?? '') == '우리은행' ? 'selected' : ''; ?>>우리은행</option>
                                <option value="하나은행" <?php echo ($ot['ot_bank_name'] ?? '') == '하나은행' ? 'selected' : ''; ?>>하나은행</option>
                                <option value="기업은행" <?php echo ($ot['ot_bank_name'] ?? '') == '기업은행' ? 'selected' : ''; ?>>기업은행</option>
                                <option value="농협은행" <?php echo ($ot['ot_bank_name'] ?? '') == '농협은행' ? 'selected' : ''; ?>>농협은행</option>
                                <option value="SC제일은행" <?php echo ($ot['ot_bank_name'] ?? '') == 'SC제일은행' ? 'selected' : ''; ?>>SC제일은행</option>
                                <option value="카카오뱅크" <?php echo ($ot['ot_bank_name'] ?? '') == '카카오뱅크' ? 'selected' : ''; ?>>카카오뱅크</option>
                                <option value="케이뱅크" <?php echo ($ot['ot_bank_name'] ?? '') == '케이뱅크' ? 'selected' : ''; ?>>케이뱅크</option>
                            </select>
                            <input type="text" name="ot_bank_account" class="form-control"
                                   value="<?php echo get_text($ot['ot_bank_account'] ?? ''); ?>"
                                   placeholder="계좌번호 (- 제외)">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 내용 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-text-paragraph"></i> 상세 내용 <span class="required">*</span>
                </label>
                <div class="editor-wrapper">
                    <?php echo editor_html('ot_content', get_text(html_purifier($ot['ot_content'] ?? ''), 0)); ?>
                </div>
            </div>
            
            <!-- 파일 첨부 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-paperclip"></i> 파일 첨부
                </label>
                <div class="file-upload-area">
                    <p>거래 관련 서류나 스크린샷을 첨부할 수 있습니다</p>
                    <div class="file-input-wrapper">
                        <input type="file" name="bf_file[]" id="file1">
                        <label for="file1" class="file-input-label">
                            <i class="bi bi-folder-plus"></i> 파일 선택
                        </label>
                    </div>
                    
                    <?php if($mode == 'edit' && $ot_id) { 
                        // 기존 첨부파일 표시
                        $sql = "SELECT * FROM g5_otc_file WHERE ot_id = '$ot_id' ORDER BY bf_no";
                        $file_result = sql_query($sql);
                        while($file = sql_fetch_array($file_result)) {
                    ?>
                    <div style="margin-top: 12px; text-align: left;">
                        <label>
                            <input type="checkbox" name="bf_file_del[<?php echo $file['bf_no']; ?>]" value="1">
                            삭제 - <?php echo $file['bf_source']; ?> (<?php echo get_filesize($file['bf_filesize']); ?>)
                        </label>
                    </div>
                    <?php } } ?>
                </div>
            </div>
            
            <!-- 연락처 정보 -->
            <div class="contact-info">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">
                        <i class="bi bi-person"></i> 작성자 정보 <span class="required">*</span>
                    </label>
                    <?php if($is_member) { ?>
                        <input type="text" class="form-control" value="<?php echo $member['mb_nick'] ? $member['mb_nick'] : $member['mb_name']; ?>" readonly>
                    <?php } else { ?>
                        <input type="text" name="ot_name" class="form-control" required
                               value="<?php echo get_text($ot['ot_name'] ?? ''); ?>"
                               placeholder="이름">
                    <?php } ?>
                </div>
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">
                        <i class="bi bi-telephone"></i> 연락처 <span class="required">*</span>
                    </label>
                    <input type="tel" name="ot_hp" class="form-control" required
                           value="<?php echo get_text($ot['ot_hp'] ?? ''); ?>"
                           placeholder="010-0000-0000">
                </div>
                
                <?php if(!$is_member && $mode == 'write') { ?>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">
                        <i class="bi bi-lock"></i> 비밀번호 <span class="required">*</span>
                    </label>
                    <input type="password" name="ot_password" class="form-control" required
                           placeholder="수정/삭제 시 필요합니다">
                </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- 액션 버튼 -->
        <div class="form-actions">
            <div class="action-left">
                <i class="bi bi-shield-check"></i> 거래 시 주의사항을 반드시 확인해주세요
            </div>
            <div class="action-buttons">
                <a href="./otc.php?page=<?php echo $page; ?>" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> 취소
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?php echo $mode == 'edit' ? '수정 완료' : '등록 완료'; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// 거래 타입 선택 시 필드 전환
function toggleFields() {
    const buyChecked = document.getElementById('type_buy').checked;
    const sellChecked = document.getElementById('type_sell').checked;
    
    document.getElementById('buyFields').style.display = buyChecked ? 'block' : 'none';
    document.getElementById('sellFields').style.display = sellChecked ? 'block' : 'none';
    
    // 필수 속성 조정
    if(buyChecked) {
        document.querySelector('input[name="ot_wallet_address"]').setAttribute('required', '');
        document.querySelector('select[name="ot_bank_name"]').removeAttribute('required');
        document.querySelector('input[name="ot_bank_account"]').removeAttribute('required');
    } else if(sellChecked) {
        document.querySelector('input[name="ot_wallet_address"]').removeAttribute('required');
        document.querySelector('select[name="ot_bank_name"]').setAttribute('required', '');
        document.querySelector('input[name="ot_bank_account"]').setAttribute('required', '');
    }
}

// 이벤트 리스너
document.getElementById('type_buy').addEventListener('change', toggleFields);
document.getElementById('type_sell').addEventListener('change', toggleFields);

// 총 거래금액 계산
function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('price').value) || 0;
    const total = quantity * price;
    
    document.getElementById('totalAmount').textContent = '₩' + total.toLocaleString();
    
    // 최소 금액 체크
    if(total > 0 && total < 10000000) {
        document.getElementById('totalAmount').style.color = '#ef4444';
        document.getElementById('totalAmount').innerHTML += '<br><small style="font-size:14px;">최소 거래금액 미달</small>';
    } else {
        document.getElementById('totalAmount').style.color = '#f59e0b';
    }
}

document.getElementById('quantity').addEventListener('input', calculateTotal);
document.getElementById('price').addEventListener('input', calculateTotal);

// 페이지 로드 시 초기화
window.onload = function() {
    toggleFields();
    calculateTotal();
};

// 폼 제출 검증
function fotc_submit(f) {
    <?php echo get_editor_js('ot_content'); ?>
    
    if(!f.ot_type.value) {
        alert('거래 타입을 선택해주세요.');
        return false;
    }
    
    if(!f.ot_subject.value) {
        alert('제목을 입력해주세요.');
        f.ot_subject.focus();
        return false;
    }
    
    const quantity = parseFloat(f.ot_quantity.value);
    const price = parseFloat(f.ot_price_krw.value);
    const total = quantity * price;
    
    if(total < 10000000) {
        alert('최소 거래 금액은 5만원입니다.\n현재 거래금액: ' + total.toLocaleString() + '원');
        return false;
    }
    
    return true;
}
</script>

<?php
include_once('./_tail.php');
?>
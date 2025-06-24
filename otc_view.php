<?php
/*
 * 파일명: otc_view.php
 * 위치: /otc_view.php
 * 기능: OTC 거래 상세보기
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

$ot_id = isset($_GET['ot_id']) ? (int)$_GET['ot_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if(!$ot_id) {
    alert('잘못된 접근입니다.', './otc.php');
}

// ===================================
// 게시글 조회
// ===================================

$sql = "SELECT * FROM g5_otc WHERE ot_id = '$ot_id'";
$view = sql_fetch($sql);

if(!$view['ot_id']) {
    alert('존재하지 않는 게시글입니다.', './otc.php');
}

// 조회수 증가
sql_query("UPDATE g5_otc SET ot_hit = ot_hit + 1 WHERE ot_id = '$ot_id'");
$view['ot_hit']++;

/* 페이지 제목 */
$g5['title'] = $view['ot_subject'];

// ===================================
// 이전글, 다음글
// ===================================

$sql = "SELECT ot_id, ot_subject, ot_name, ot_datetime, ot_type FROM g5_otc 
        WHERE ot_id < '$ot_id' 
        ORDER BY ot_id DESC 
        LIMIT 1";
$prev = sql_fetch($sql);

$sql = "SELECT ot_id, ot_subject, ot_name, ot_datetime, ot_type FROM g5_otc 
        WHERE ot_id > '$ot_id' 
        ORDER BY ot_id ASC 
        LIMIT 1";
$next = sql_fetch($sql);

// ===================================
// OTC 시세 정보
// ===================================

$otc_price = sql_fetch("SELECT * FROM g5_otc_price ORDER BY op_id DESC LIMIT 1");
if (!$otc_price) {
    $otc_price = array(
        'op_buy_price' => 1450,
        'op_sell_price' => 1430
    );
}

include_once('./_head.php');

// 프로필 색상
$profile_colors = array('#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#6366f1');
$profile_color = $profile_colors[ord(substr($view['ot_name'], 0, 1)) % count($profile_colors)];
?>

<style>
/* 상세보기 컨테이너 */
.otc-view-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 글 헤더 */
.otc-view-header {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
    margin-top: 20px;
}

.view-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
}

.view-type-badge.buy {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
}

.view-type-badge.sell {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.view-subject {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 24px;
    line-height: 1.4;
}

.view-meta {
    display: flex;
    align-items: center;
    gap: 30px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: <?php echo $profile_color; ?>;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 18px;
}

.author-details {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.post-date {
    font-size: 14px;
    color: #6b7280;
}

.view-stats {
    display: flex;
    gap: 20px;
    margin-left: auto;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #6b7280;
}

/* 거래 정보 박스 */
.trade-info-box {
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.trade-info-box::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
    transform: rotate(45deg);
}

.trade-info-header {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
}

.trade-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    position: relative;
}

.info-item {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.info-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-value {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.info-value.highlight {
    color: #f59e0b;
    font-size: 24px;
}

/* 글 내용 */
.view-body {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
}

.view-content {
    padding: 40px;
}

.view-content-text {
    font-size: 16px;
    line-height: 1.8;
    color: #374151;
    min-height: 200px;
}

/* 연락처 정보 */
.contact-section {
    background: #eff6ff;
    padding: 30px 40px;
    border-top: 1px solid #bfdbfe;
}

.contact-header {
    font-size: 16px;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.contact-icon {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3b82f6;
    font-size: 18px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.contact-text {
    display: flex;
    flex-direction: column;
}

.contact-label {
    font-size: 12px;
    color: #6b7280;
}

.contact-value {
    font-size: 16px;
    font-weight: 500;
    color: #1f2937;
}

/* 첨부파일 */
.view-files {
    background: #f9fafb;
    padding: 24px 40px;
    border-bottom: 1px solid #e5e7eb;
}

.file-header {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.file-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 10px;
}

.file-link {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #374151;
    text-decoration: none;
    width: 100%;
}

.file-link:hover {
    color: #3b82f6;
}

/* 버튼 영역 */
.view-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 40px;
    background: #f9fafb;
}

.btn-group {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
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
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.btn-danger:hover {
    background: #fecaca;
}

/* 이전글 다음글 */
.view-nav {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.nav-item {
    display: flex;
    padding: 24px 30px;
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.2s;
    position: relative;
}

.nav-item:last-child {
    border-bottom: none;
}

.nav-item:hover {
    background: #f9fafb;
}

.nav-label {
    width: 100px;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.nav-link {
    color: #1f2937;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
}

.nav-link:hover {
    color: #3b82f6;
}

.nav-meta {
    font-size: 13px;
    color: #9ca3af;
}

/* 상태 배지 */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
}

.status-badge.trading {
    background: #fef3c7;
    color: #f59e0b;
}

.status-badge.complete {
    background: #d1fae5;
    color: #059669;
}

/* 경고 메시지 */
.warning-box {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    display: flex;
    gap: 16px;
}

.warning-icon {
    color: #f59e0b;
    font-size: 24px;
    flex-shrink: 0;
}

.warning-text {
    font-size: 14px;
    color: #92400e;
    line-height: 1.6;
}

/* 반응형 */
@media (max-width: 768px) {
    .otc-view-container {
        padding: 0 15px;
    }
    
    .otc-view-header {
        padding: 30px 20px;
    }
    
    .view-subject {
        font-size: 22px;
    }
    
    .view-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .view-stats {
        margin-left: 0;
    }
    
    .trade-info-grid {
        grid-template-columns: 1fr;
    }
    
    .view-content {
        padding: 20px;
    }
    
    .view-actions {
        flex-direction: column;
        gap: 12px;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn {
        flex: 1;
        justify-content: center;
    }
}
</style>

<div class="otc-view-container">
    <?php include_once(G5_PATH.'/coin.php');?>
    
    <!-- 경고 메시지 -->
    <div class="warning-box">
        <i class="bi bi-exclamation-triangle warning-icon"></i>
        <div class="warning-text">
            <strong>거래 시 주의사항</strong><br>
            입금 전 반드시 상대방의 신원을 확인하고, 거래 내역은 스크린샷으로 보관하세요.
            사기 거래 의심 시 즉시 신고해주시기 바랍니다.
        </div>
    </div>
    
    <!-- 글 헤더 -->
    <div class="otc-view-header">
        <span class="view-type-badge <?php echo $view['ot_type']; ?>">
            <i class="bi <?php echo $view['ot_type'] == 'buy' ? 'bi-cart-plus-fill' : 'bi-cart-dash-fill'; ?>"></i>
            <?php echo $view['ot_type'] == 'buy' ? '매수' : '매도'; ?>
        </span>
        
        <h1 class="view-subject">
            <?php echo get_text($view['ot_subject']); ?>
        </h1>
        
        <div class="view-meta">
            <div class="author-info">
                <div class="author-avatar">
                    <?php echo strtoupper(mb_substr($view['ot_name'], 0, 1)); ?>
                </div>
                <div class="author-details">
                    <div class="author-name"><?php echo get_text($view['ot_name']); ?></div>
                    <div class="post-date"><?php echo date('Y년 m월 d일 H:i', strtotime($view['ot_datetime'])); ?></div>
                </div>
            </div>
            
            <div class="view-stats">
                <div class="stat-item">
                    <i class="bi bi-eye"></i>
                    <span><?php echo number_format($view['ot_hit']); ?></span>
                </div>
                <div class="stat-item">
                    <span class="status-badge <?php echo $view['ot_status'] ? 'complete' : 'trading'; ?>">
                        <i class="bi <?php echo $view['ot_status'] ? 'bi-check-circle' : 'bi-clock'; ?>"></i>
                        <?php echo $view['ot_status'] ? '거래완료' : '거래중'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 거래 정보 -->
    <div class="trade-info-box">
        <h3 class="trade-info-header">
            <i class="bi bi-receipt"></i> 거래 정보
        </h3>
        <div class="trade-info-grid">
            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-currency-bitcoin"></i> 암호화폐
                </div>
                <div class="info-value"><?php echo $view['ot_crypto_type']; ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-stack"></i> 수량
                </div>
                <div class="info-value"><?php echo number_format($view['ot_quantity'], 8); ?> 개</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-tag"></i> 단가
                </div>
                <div class="info-value">₩<?php echo number_format($view['ot_price_krw']); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">
                    <i class="bi bi-calculator"></i> 총 거래금액
                </div>
                <div class="info-value highlight">₩<?php echo number_format($view['ot_total_krw']); ?></div>
            </div>
        </div>
    </div>
    
    <!-- 글 본문 -->
    <div class="view-body">
        <!-- 글 내용 -->
        <div class="view-content">
            <div class="view-content-text">
                <?php echo conv_content($view['ot_content'], 1); ?>
            </div>
        </div>
        
        <!-- 연락처 정보 (로그인 시에만 표시) -->
        <?php if($is_member || $is_admin) { ?>
        <div class="contact-section">
            <h4 class="contact-header">
                <i class="bi bi-person-lines-fill"></i> 거래자 연락처
            </h4>
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div class="contact-text">
                        <span class="contact-label">연락처</span>
                        <span class="contact-value"><?php echo $view['ot_hp']; ?></span>
                    </div>
                </div>
                
                <?php if($view['ot_type'] == 'buy' && $view['ot_wallet_address']) { ?>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="contact-text">
                        <span class="contact-label">지갑 주소</span>
                        <span class="contact-value" style="word-break: break-all;"><?php echo $view['ot_wallet_address']; ?></span>
                    </div>
                </div>
                <?php } ?>
                
                <?php if($view['ot_type'] == 'sell' && $view['ot_bank_name']) { ?>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="bi bi-bank"></i>
                    </div>
                    <div class="contact-text">
                        <span class="contact-label">입금 계좌</span>
                        <span class="contact-value"><?php echo $view['ot_bank_name']; ?> <?php echo $view['ot_bank_account']; ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } else { ?>
        <div class="contact-section" style="text-align: center;">
            <p style="color: #6b7280; margin: 0;">
                <i class="bi bi-lock"></i> 연락처 정보는 로그인 후 확인 가능합니다.
                <a href="<?php echo G5_BBS_URL; ?>/login.php?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" style="color: #3b82f6; text-decoration: none; font-weight: 500;">로그인</a>
            </p>
        </div>
        <?php } ?>
        
        <!-- 첨부파일 -->
        <?php
        $sql = "SELECT * FROM g5_otc_file WHERE ot_id = '$ot_id' ORDER BY bf_no";
        $file_result = sql_query($sql);
        $file_count = sql_num_rows($file_result);
        
        if($file_count > 0) {
        ?>
        <div class="view-files">
            <div class="file-header">
                <i class="bi bi-paperclip"></i> 첨부파일 (<?php echo $file_count; ?>)
            </div>
            <?php while($file = sql_fetch_array($file_result)) { ?>
            <div class="file-item">
                <a href="./otc_download.php?ot_id=<?php echo $ot_id; ?>&bf_no=<?php echo $file['bf_no']; ?>" class="file-link">
                    <i class="bi bi-file-earmark-arrow-down"></i>
                    <span><?php echo $file['bf_source']; ?></span>
                    <span style="margin-left: auto; color: #9ca3af; font-size: 13px;">
                        <?php echo get_filesize($file['bf_filesize']); ?> · 
                        다운로드 <?php echo number_format($file['bf_download']); ?>회
                    </span>
                </a>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        
        <!-- 버튼 영역 -->
        <div class="view-actions">
            <div class="btn-group">
                <a href="./otc.php?page=<?php echo $page; ?>" class="btn btn-secondary">
                    <i class="bi bi-list"></i> 목록
                </a>
                <?php if(!$view['ot_status']) { ?>
                <button onclick="completeTransaction()" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> 거래완료
                </button>
                <?php } ?>
            </div>
            
            <?php if($is_admin || ($is_member && $view['mb_id'] == $member['mb_id'])) { ?>
            <div class="btn-group">
                <a href="./otc_write.php?mode=edit&ot_id=<?php echo $ot_id; ?>&page=<?php echo $page; ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> 수정
                </a>
                <a href="javascript:confirmDelete();" class="btn btn-danger">
                    <i class="bi bi-trash"></i> 삭제
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
    
    <!-- 이전글 다음글 -->
    <div class="view-nav">
        <?php if($prev['ot_id']) { ?>
        <a href="./otc_view.php?ot_id=<?php echo $prev['ot_id']; ?>&page=<?php echo $page; ?>" class="nav-item">
            <div class="nav-label">
                <i class="bi bi-chevron-up"></i> 이전글
            </div>
            <div class="nav-content">
                <div class="nav-link">
                    <span class="trade-type-badge <?php echo $prev['ot_type']; ?>" style="font-size: 12px; padding: 2px 8px;">
                        <?php echo $prev['ot_type'] == 'buy' ? '매수' : '매도'; ?>
                    </span>
                    <?php echo get_text($prev['ot_subject']); ?>
                </div>
                <div class="nav-meta"><?php echo $prev['ot_name']; ?> · <?php echo date('Y.m.d', strtotime($prev['ot_datetime'])); ?></div>
            </div>
        </a>
        <?php } ?>
        
        <?php if($next['ot_id']) { ?>
        <a href="./otc_view.php?ot_id=<?php echo $next['ot_id']; ?>&page=<?php echo $page; ?>" class="nav-item">
            <div class="nav-label">
                <i class="bi bi-chevron-down"></i> 다음글
            </div>
            <div class="nav-content">
                <div class="nav-link">
                    <span class="trade-type-badge <?php echo $next['ot_type']; ?>" style="font-size: 12px; padding: 2px 8px;">
                        <?php echo $next['ot_type'] == 'buy' ? '매수' : '매도'; ?>
                    </span>
                    <?php echo get_text($next['ot_subject']); ?>
                </div>
                <div class="nav-meta"><?php echo $next['ot_name']; ?> · <?php echo date('Y.m.d', strtotime($next['ot_datetime'])); ?></div>
            </div>
        </a>
        <?php } ?>
    </div>
    
    <!-- 시세 정보 -->
    <div class="otc-notice-box" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
        <div class="notice-header" style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">
            <i class="bi bi-graph-up" style="color: #3b82f6;"></i> 현재 USDT 시세
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="text-align: center; padding: 16px; background: #fee2e2; border-radius: 8px;">
                <div style="font-size: 14px; color: #dc2626; margin-bottom: 4px;">매수가</div>
                <div style="font-size: 24px; font-weight: 700; color: #dc2626;">₩<?php echo number_format($otc_price['op_buy_price']); ?></div>
            </div>
            <div style="text-align: center; padding: 16px; background: #dbeafe; border-radius: 8px;">
                <div style="font-size: 14px; color: #2563eb; margin-bottom: 4px;">매도가</div>
                <div style="font-size: 24px; font-weight: 700; color: #2563eb;">₩<?php echo number_format($otc_price['op_sell_price']); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if(confirm('정말 삭제하시겠습니까?\n삭제된 글은 복구할 수 없습니다.')) {
        location.href = './otc.php?mode=delete&ot_id=<?php echo $ot_id; ?>&page=<?php echo $page; ?>';
    }
}

function completeTransaction() {
    if(confirm('거래를 완료 처리하시겠습니까?\n완료 후에는 변경할 수 없습니다.')) {
        // AJAX로 거래 완료 처리
        const formData = new FormData();
        formData.append('ot_id', '<?php echo $ot_id; ?>');
        formData.append('action', 'complete');
        
        fetch('./otc_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('거래가 완료 처리되었습니다.');
                location.reload();
            } else {
                alert(data.message || '처리 중 오류가 발생했습니다.');
            }
        })
        .catch(error => {
            alert('처리 중 오류가 발생했습니다.');
        });
    }
}
</script>

<?php
include_once('./_tail.php');
?>
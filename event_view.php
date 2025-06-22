<?php
/*
 * 파일명: event_view.php
 * 위치: /event_view.php
 * 기능: 이벤트 상세보기 및 신청
 * 작성일: 2025-01-11
 */

// 이미 common.php는 event.php에서 include되어 있음
if(!defined('_GNUBOARD_')) exit;

// 이벤트 정보 가져오기
$sql = "SELECT * FROM g5_event WHERE ev_id = '{$ev_id}'";
$event = sql_fetch($sql);

if(!$event['ev_id']) {
    alert('존재하지 않는 이벤트입니다.', G5_URL.'/event.php');
}

// 조회수 증가
sql_query("UPDATE g5_event SET ev_hit = ev_hit + 1 WHERE ev_id = '{$ev_id}'");

// 회원 신청 여부 확인
$is_applied = false;
$my_apply = null;
if($member['mb_id']) {
    $sql = "SELECT * FROM g5_event_apply WHERE ev_id = '{$ev_id}' AND mb_id = '{$member['mb_id']}'";
    $my_apply = sql_fetch($sql);
    $is_applied = $my_apply ? true : false;
}

$g5['title'] = $event['ev_subject'] . ' - 에어드랍 이벤트';
include_once(G5_PATH.'/head.php');

// 남은 기간 계산
$now = time();
$start_time = strtotime($event['ev_start_date']);
$end_time = strtotime($event['ev_end_date']);
$remaining_days = floor(($end_time - $now) / 86400);
$is_ongoing = ($now >= $start_time && $now <= $end_time && $event['ev_status'] == 'ongoing');
?>

<!-- ===================================
     이벤트 상세 페이지
     =================================== -->
<div class="event-view-page">
    <div class="container">
        <!-- 뒤로가기 버튼 -->
        <div class="mb-4">
            <a href="<?php echo G5_URL; ?>/event.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> 목록으로
            </a>
        </div>
        
        <div class="row">
            <!-- 왼쪽: 이벤트 내용 -->
            <div class="col-lg-8">
                <div class="event-detail-card">
                    <!-- 이벤트 헤더 -->
                    <div class="event-detail-header">
                        <div class="event-status-badges">
                            <?php if($event['ev_recommend']) { ?>
                            <span class="badge bg-warning">
                                <i class="bi bi-star-fill"></i> 추천
                            </span>
                            <?php } ?>
                            
                            <?php if($event['ev_status'] == 'ongoing') { ?>
                                <span class="badge bg-success">진행중</span>
                            <?php } else if($event['ev_status'] == 'scheduled') { ?>
                                <span class="badge bg-info">진행예정</span>
                            <?php } else { ?>
                                <span class="badge bg-secondary">종료</span>
                            <?php } ?>
                        </div>
                        
                        <h1 class="event-detail-title"><?php echo $event['ev_subject']; ?></h1>
                        
                        <div class="event-detail-meta">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <?php echo date('Y.m.d H:i', strtotime($event['ev_start_date'])); ?> ~ 
                                <?php echo date('Y.m.d H:i', strtotime($event['ev_end_date'])); ?>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-eye"></i>
                                조회 <?php echo number_format($event['ev_hit']); ?>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                참여 <?php echo number_format($event['ev_apply_count']); ?>명
                            </div>
                        </div>
                    </div>
                    
                    <!-- 이벤트 이미지 -->
                    <?php if($event['ev_image']) { ?>
                    <div class="event-detail-image">
                        <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $event['ev_image']; ?>" 
                             alt="<?php echo $event['ev_subject']; ?>">
                    </div>
                    <?php } ?>
                    
                    <!-- 이벤트 내용 -->
                    <div class="event-detail-content">
                        <?php echo conv_content($event['ev_content'], 2); ?>
                    </div>
                </div>
                
                <!-- 참여자 목록 (최근 10명) -->
                <div class="recent-participants mt-4">
                    <h4 class="mb-3">
                        <i class="bi bi-people-fill"></i> 최근 참여자
                    </h4>
                    <div class="participants-list">
                        <?php
                        $sql = "SELECT ea.*, m.mb_nick 
                                FROM g5_event_apply ea 
                                LEFT JOIN g5_member m ON ea.mb_id = m.mb_id 
                                WHERE ea.ev_id = '{$ev_id}' 
                                ORDER BY ea.ea_id DESC 
                                LIMIT 10";
                        $result = sql_query($sql);
                        
                        if(sql_num_rows($result) > 0) {
                            while($row = sql_fetch_array($result)) {
                                $nick = $row['mb_nick'] ? $row['mb_nick'] : '탈퇴회원';
                                $wallet = substr($row['ea_wallet_address'], 0, 6) . '...' . substr($row['ea_wallet_address'], -4);
                        ?>
                        <div class="participant-item">
                            <div class="participant-info">
                                <i class="bi bi-person-circle"></i>
                                <span class="nick"><?php echo $nick; ?></span>
                                <span class="wallet"><?php echo $wallet; ?></span>
                            </div>
                            <div class="participant-status">
                                <?php if($row['ea_status'] == 'paid') { ?>
                                    <span class="badge bg-success">지급완료</span>
                                <?php } else { ?>
                                    <span class="badge bg-warning">대기중</span>
                                <?php } ?>
                            </div>
                        </div>
                        <?php 
                            }
                        } else {
                        ?>
                        <p class="text-muted text-center py-3">아직 참여자가 없습니다.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <!-- 오른쪽: 신청 폼 -->
            <div class="col-lg-4">
<!-- 코인 정보 박스 부분을 다음으로 교체 -->
<div class="airdrop-info-section">
    <!-- 메인 에어드랍 정보 -->
    <div class="airdrop-main-card">
        <div class="airdrop-header">
            <h5 class="airdrop-title">
                <i class="bi bi-gift"></i> 에어드랍 리워드
            </h5>
            <?php if($event['ev_recommend']) { ?>
            <span class="recommend-tag">
                <i class="bi bi-star-fill"></i> HOT
            </span>
            <?php } ?>
        </div>
        
        <div class="airdrop-amount-display">
            <div class="coin-badge">
                <span class="coin-symbol"><?php echo $event['ev_coin_symbol']; ?></span>
            </div>
            <div class="amount-info">
                <div class="amount-value">
                    <?php echo number_format($event['ev_coin_amount']); ?>
                    <span class="amount-unit"><?php echo $event['ev_coin_symbol']; ?></span>
                </div>
                <div class="coin-name"><?php echo $event['ev_coin_name']; ?></div>
            </div>
        </div>
    </div>
    
    <!-- 이벤트 정보 카드들 -->
    <div class="event-info-grid">
        <div class="info-item">
            <i class="bi bi-calendar-check"></i>
            <div class="info-detail">
                <span class="info-label">시작일</span>
                <span class="info-value"><?php echo date('Y.m.d', strtotime($event['ev_start_date'])); ?></span>
            </div>
        </div>
        
        <div class="info-item">
            <i class="bi bi-calendar-x"></i>
            <div class="info-detail">
                <span class="info-label">종료일</span>
                <span class="info-value"><?php echo date('Y.m.d', strtotime($event['ev_end_date'])); ?></span>
            </div>
        </div>
        
        <div class="info-item">
            <i class="bi bi-people"></i>
            <div class="info-detail">
                <span class="info-label">참여자</span>
                <span class="info-value"><?php echo number_format($event['ev_apply_count']); ?>명</span>
            </div>
        </div>
        
        <div class="info-item">
            <i class="bi bi-shield-check"></i>
            <div class="info-detail">
                <span class="info-label">인증</span>
                <span class="info-value">필수</span>
            </div>
        </div>
    </div>
    
    <!-- 남은 시간 표시 (진행중일 때만) -->
    <?php if($is_ongoing) { ?>
    <div class="time-status">
        <div class="status-content">
            <i class="bi bi-clock-history"></i>
            <span class="status-text">
                마감까지 <strong><?php echo $remaining_days; ?>일</strong> 남음
            </span>
        </div>
        <div class="status-badge">진행중</div>
    </div>
    <?php } else if($event['ev_status'] == 'scheduled') { ?>
    <div class="time-status scheduled">
        <div class="status-content">
            <i class="bi bi-hourglass-split"></i>
            <span class="status-text">
                <strong><?php echo abs($remaining_days); ?>일</strong> 후 시작
            </span>
        </div>
        <div class="status-badge">예정</div>
    </div>
    <?php } else { ?>
    <div class="time-status ended">
        <div class="status-content">
            <i class="bi bi-check-circle"></i>
            <span class="status-text">이벤트가 종료되었습니다</span>
        </div>
        <div class="status-badge">종료</div>
    </div>
    <?php } ?>
</div>

<style>
/* 에어드랍 정보 섹션 */
.airdrop-info-section {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
}

/* 메인 에어드랍 카드 */
.airdrop-main-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    position: relative;
}

.airdrop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.airdrop-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.airdrop-title i {
    color: #3b82f6;
}

.recommend-tag {
    background: #fef3c7;
    color: #d97706;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* 에어드랍 금액 표시 */
.airdrop-amount-display {
    display: flex;
    align-items: center;
    gap: 16px;
}

.coin-badge {
    width: 60px;
    height: 60px;
    background: #3b82f6;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.coin-symbol {
    color: white;
    font-size: 24px;
    font-weight: 700;
}

.amount-info {
    flex: 1;
}

.amount-value {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
}

.amount-unit {
    font-size: 20px;
    color: #6b7280;
    margin-left: 8px;
}

.coin-name {
    font-size: 14px;
    color: #6b7280;
}

/* 이벤트 정보 그리드 */
.event-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 20px;
}

.info-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item i {
    font-size: 20px;
    color: #6b7280;
}

.info-detail {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.info-label {
    font-size: 12px;
    color: #9ca3af;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

/* 시간 상태 표시 */
.time-status {
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.time-status.scheduled {
    background: #fef3c7;
    border-color: #fde68a;
}

.time-status.ended {
    background: #f3f4f6;
    border-color: #e5e7eb;
}

.status-content {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #1e40af;
}

.time-status.scheduled .status-content {
    color: #d97706;
}

.time-status.ended .status-content {
    color: #6b7280;
}

.status-text {
    font-size: 14px;
}

.status-text strong {
    font-weight: 700;
}

.status-badge {
    background: #3b82f6;
    color: white;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
}

.time-status.scheduled .status-badge {
    background: #f59e0b;
}

.time-status.ended .status-badge {
    background: #6b7280;
}

/* 반응형 */
@media (max-width: 768px) {
    .airdrop-info-section {
        padding: 16px;
    }
    
    .airdrop-main-card {
        padding: 16px;
    }
    
    .amount-value {
        font-size: 24px;
    }
    
    .coin-badge {
        width: 50px;
        height: 50px;
    }
    
    .coin-symbol {
        font-size: 20px;
    }
    
    .event-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>            </div>
        </div>
    </div>
</div>

<style>
/* 이벤트 상세 페이지 */
.event-view-page {
    padding: 40px 0;
    background: #f9fafb;
    min-height: calc(100vh - 200px);
}

/* 상세 카드 */
.event-detail-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

/* 헤더 */
.event-detail-header {
    margin-bottom: 32px;
}

.event-status-badges {
    margin-bottom: 16px;
}

.event-status-badges .badge {
    margin-right: 8px;
}

.event-detail-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 16px;
    line-height: 1.4;
}

.event-detail-meta {
    display: flex;
    gap: 24px;
    color: #6b7280;
    font-size: 14px;
}

.event-detail-meta .meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* 이미지 */
.event-detail-image {
    margin-bottom: 32px;
    text-align: center;
}

.event-detail-image img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
}

/* 내용 */
.event-detail-content {
    font-size: 16px;
    line-height: 1.8;
    color: #374151;
}

.event-detail-content img {
    max-width: 100%;
    height: auto;
}

/* 신청 카드 */
.event-apply-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    top: 100px;
}

/* 코인 정보 */
.coin-info-box {
    background: #f3f4f6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
}

.coin-details .detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}

.coin-details .detail-row.highlight {
    background: #fef3c7;
    margin: 0 -12px;
    padding: 12px;
    border-radius: 8px;
}

.coin-details .label {
    color: #6b7280;
    font-size: 14px;
}

.coin-details .value {
    font-weight: 600;
}

/* 남은 시간 */
.time-remaining {
    background: #dbeafe;
    color: #1e40af;
    padding: 16px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 24px;
}

/* 로그인 필요 */
.apply-login-required {
    text-align: center;
    padding: 32px 0;
    color: #6b7280;
}

.apply-login-required i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    color: #9ca3af;
}

/* 이미 신청 */
.already-applied {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 24px;
}

.applied-badge {
    text-align: center;
    color: #16a34a;
    margin-bottom: 20px;
}

.applied-badge i {
    font-size: 48px;
    margin-bottom: 8px;
    display: block;
}

.applied-info {
    font-size: 14px;
}

.applied-info code {
    font-size: 12px;
    word-break: break-all;
}

/* 참여자 목록 */
.recent-participants {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.participants-list {
    max-height: 400px;
    overflow-y: auto;
}

.participant-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.participant-item:last-child {
    border-bottom: none;
}

.participant-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.participant-info .nick {
    font-weight: 500;
}

.participant-info .wallet {
    color: #9ca3af;
    font-size: 13px;
    font-family: monospace;
}

/* 진행 불가 */
.event-not-available {
    text-align: center;
    padding: 32px 0;
    color: #6b7280;
}

.event-not-available i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    color: #9ca3af;
}

/* 반응형 */
@media (max-width: 992px) {
    .event-apply-card {
        position: static !important;
        margin-top: 24px;
    }
}
</style>

<script>
// 이벤트 신청 처리
document.getElementById('eventApplyForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // 파일 개수 체크
    const files = this.querySelector('input[type="file"]').files;
    if(files.length > 5) {
        alert('파일은 최대 5개까지 업로드 가능합니다.');
        return;
    }
    
    // Ajax로 신청 처리
    fetch('<?php echo G5_URL; ?>/ajax/event_apply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('이벤트 신청이 완료되었습니다.');
            location.reload();
        } else {
            alert(data.message || '신청 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('신청 중 오류가 발생했습니다.');
    });
});
</script>

<?php
include_once(G5_PATH.'/tail.php');
?>
<?php
/*
 * 파일명: event_view.php
 * 위치: /event_view.php
 * 기능: 이벤트 상세보기 및 신청
 * 작성일: 2025-01-11
 * 수정일: 2025-01-26
 */

include_once('./_common.php');


// 이벤트 정보 가져오기
$sql = "SELECT * FROM g5_event WHERE ev_id = '{$ev_id}' ";
$event_2 = sql_fetch($sql);


if(!$event_2['ev_id']) {
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
$g5['title'] = $event_2['ev_subject'] . ' - 에어드랍 이벤트';
include_once(G5_PATH.'/head.php');

// 한국 시간대 설정
date_default_timezone_set('Asia/Seoul');

// 날짜 기반 상태 자동 판단
$now = time();
$start_time = strtotime($event_2['ev_start_date']);
$end_time = strtotime($event_2['ev_end_date']);

// 상태 변수
$status = '';
$status_text = '';
$status_class = '';
$can_apply = false;
$remaining_days = 0;
$days_until_start = 0;

if($now < $start_time) {
    // 시작 전
    $status = 'scheduled';
    $status_text = '진행예정';
    $status_class = 'info';
    $days_until_start = ceil(($start_time - $now) / 86400);
} else if($now >= $start_time && $now <= $end_time) {
    // 진행 중
    $status = 'ongoing';
    $status_text = '진행중';
    $status_class = 'success';
    $can_apply = true;
    $remaining_days = ceil(($end_time - $now) / 86400);
} else {
    // 종료
    $status = 'ended';
    $status_text = '종료';
    $status_class = 'secondary';
}

// 관리자 권한 확인
$is_admin = $is_admin || $member['mb_level'] >= 10;
?>

<!-- ===================================
     이벤트 상세 페이지
     =================================== -->
<div class="event-view-page">
    <div class="container">
        <!-- 뒤로가기 및 관리 버튼 -->
        <div class="mb-4 d-flex justify-content-between">
            <a href="<?php echo G5_URL; ?>/event.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> 목록으로
            </a>
            <?php if($is_admin) { ?>
            <div>
                <a href="<?php echo G5_URL; ?>/event_write.php?w=u&ev_id=<?php echo $ev_id; ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> 수정
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteEvent('<?php echo $ev_id; ?>')">
                    <i class="bi bi-trash"></i> 삭제
                </button>
            </div>
            <?php } ?>
        </div>
        
        <div class="row">
            <!-- 왼쪽: 이벤트 내용 -->
            <div class="col-lg-8">
                <div class="event-detail-card">
                    <!-- 이벤트 헤더 -->
                    <div class="event-detail-header">
                        <div class="event-status-badges">
                            <?php if($event_2['ev_recommend']) { ?>
                            <span class="badge bg-warning">
                                <i class="bi bi-star-fill"></i> 추천
                            </span>
                            <?php } ?>
                            
                            <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </div>
                        
                        <h1 class="event-detail-title"><?php echo $event_2['ev_subject']; ?></h1>
                        
                        <div class="event-detail-meta">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <?php echo date('Y.m.d H:i', strtotime($event_2['ev_start_date'])); ?> ~ 
                                <?php echo date('Y.m.d H:i', strtotime($event_2['ev_end_date'])); ?>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-eye"></i>
                                조회 <?php echo number_format($event_2['ev_hit']); ?>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                참여 <?php echo number_format($event_2['ev_apply_count']); ?>명
                            </div>
                        </div>
                    </div>
                    
                    <!-- 이벤트 이미지 -->
                    <?php if($event_2['ev_image']) { ?>
                    <div class="event-detail-image">
                        <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $event_2['ev_image']; ?>" 
                             alt="<?php echo $event_2['ev_subject']; ?>">
                    </div>
                    <?php } ?>
                    
                    <!-- 이벤트 내용 -->
                    <div class="event-detail-content">
                        <?php echo conv_content($event_2['ev_content'], 2); ?>
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
                                // 닉네임 마스킹 처리
                                if($row['mb_nick']) {
                                    $nick_len = mb_strlen($row['mb_nick'], 'utf-8');
                                    if($nick_len <= 2) {
                                        $masked_nick = mb_substr($row['mb_nick'], 0, 1, 'utf-8') . '*';
                                    } else {
                                        $masked_nick = mb_substr($row['mb_nick'], 0, 1, 'utf-8') . 
                                                      str_repeat('*', $nick_len - 2) . 
                                                      mb_substr($row['mb_nick'], -1, 1, 'utf-8');
                                    }
                                } else {
                                    $masked_nick = '탈퇴회원';
                                }
                                
                                // 지갑주소 마스킹 (앞 6자리와 뒤 4자리만 표시)
                                $wallet = substr($row['ea_wallet_address'], 0, 6) . '...' . substr($row['ea_wallet_address'], -4);
                        ?>
                        <div class="participant-item">
                            <div class="participant-info">
                                <i class="bi bi-person-circle"></i>
                                <span class="nick"><?php echo $masked_nick; ?></span>
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
                <!-- 코인 정보 박스 부분 -->
                <div class="airdrop-info-section">
                    <!-- 메인 에어드랍 정보 -->
                    <div class="airdrop-main-card">
                        <div class="airdrop-header">
                            <h5 class="airdrop-title">
                                <i class="bi bi-gift"></i> 에어드랍 리워드
                            </h5>
                            <?php if($event_2['ev_recommend']) { ?>
                            <span class="recommend-tag">
                                <i class="bi bi-star-fill"></i> HOT
                            </span>
                            <?php } ?>
                        </div>
                        
                        <div class="airdrop-amount-display">
                            <div class="coin-badge">
                                <span class="coin-symbol"><?php echo $event_2['ev_coin_symbol']; ?></span>
                            </div>
                            <div class="amount-info">
                                <div class="amount-value">
                                    <?php echo number_format($event_2['ev_coin_amount']); ?>
                                    <span class="amount-unit"><?php echo $event_2['ev_coin_symbol']; ?></span>
                                </div>
                                <div class="coin-name"><?php echo $event_2['ev_coin_name']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 이벤트 정보 카드들 -->
                    <div class="event-info-grid">
                        <div class="info-item">
                            <i class="bi bi-calendar-check"></i>
                            <div class="info-detail">
                                <span class="info-label">시작일</span>
                                <span class="info-value"><?php echo date('Y.m.d', strtotime($event_2['ev_start_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-calendar-x"></i>
                            <div class="info-detail">
                                <span class="info-label">종료일</span>
                                <span class="info-value"><?php echo date('Y.m.d', strtotime($event_2['ev_end_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-people"></i>
                            <div class="info-detail">
                                <span class="info-label">참여자</span>
                                <span class="info-value"><?php echo number_format($event_2['ev_apply_count']); ?>명</span>
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
                    
                    <!-- 남은 시간 표시 -->
                    <?php if($status == 'ongoing') { ?>
                    <div class="time-status">
                        <div class="status-content">
                            <i class="bi bi-clock-history"></i>
                            <span class="status-text">
                                마감까지 <strong><?php echo $remaining_days; ?>일</strong> 남음
                            </span>
                        </div>
                        <div class="status-badge">진행중</div>
                    </div>
                    <?php } else if($status == 'scheduled') { ?>
                    <div class="time-status scheduled">
                        <div class="status-content">
                            <i class="bi bi-hourglass-split"></i>
                            <span class="status-text">
                                <strong><?php echo $days_until_start; ?>일</strong> 후 시작
                            </span>
                        </div>
                        <div class="status-badge">예정</div>
                    </div>
                    <?php } else if($status == 'ended') { ?>
                    <div class="time-status ended">
                        <div class="status-content">
                            <i class="bi bi-check-circle"></i>
                            <span class="status-text">이벤트가 종료되었습니다</span>
                        </div>
                        <div class="status-badge">종료</div>
                    </div>
                    <?php } ?>
                </div>

                <!-- 신청 폼 -->
                <div class="event-apply-card mt-3">
                    <?php if(!$member['mb_id']) { ?>
                        <!-- 로그인 필요 -->
                        <div class="apply-login-required">
                            <i class="bi bi-lock"></i>
                            <p>이벤트 참여는 로그인 후 가능합니다.</p>
                            <a href="<?php echo G5_BBS_URL; ?>/login.php?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               class="btn btn-primary w-100">
                                로그인하기
                            </a>
                        </div>
                    <?php } else if($is_applied) { ?>
                        <!-- 이미 신청함 -->
                        <div class="already-applied">
                            <div class="applied-badge">
                                <i class="bi bi-check-circle-fill"></i>
                                <h5>이미 참여하셨습니다</h5>
                            </div>
                            <div class="applied-info">
                                <p class="mb-2"><strong>신청일시:</strong> <?php echo $my_apply['ea_datetime']; ?></p>
                                <p class="mb-2"><strong>지갑주소:</strong></p>
                                <code><?php echo $my_apply['ea_wallet_address']; ?></code>
                                <p class="mt-2 mb-0">
                                    <strong>상태:</strong> 
                                    <?php if($my_apply['ea_status'] == 'paid') { ?>
                                        <span class="badge bg-success">지급완료</span>
                                    <?php } else { ?>
                                        <span class="badge bg-warning">대기중</span>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                    <?php } else if($can_apply) { ?>
                        <!-- 신청 가능 -->
                        <h5 class="mb-3">이벤트 참여하기</h5>
                        <form id="eventApplyForm">
                            <input type="hidden" name="ev_id" value="<?php echo $ev_id; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">지갑 주소 <span class="text-danger">*</span></label>
                                <input type="text" name="wallet_address" class="form-control" 
                                       placeholder="0x..." required>
                                <small class="text-muted">에어드랍을 받을 지갑 주소를 입력하세요.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">인증 파일 업로드 <span class="text-danger">*</span></label>
                                <input type="file" name="proof_files[]" class="form-control" 
                                       multiple accept="image/*" required>
                                <small class="text-muted">최대 5개까지 업로드 가능합니다.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">참여 메모 (선택)</label>
                                <textarea name="memo" class="form-control" rows="3" 
                                          placeholder="참여 인증 내용을 간단히 적어주세요."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> 이벤트 참여하기
                            </button>
                        </form>
                    <?php } else { ?>
                        <!-- 진행 불가 -->
                        <div class="event-not-available">
                            <i class="bi bi-calendar-x"></i>
                            <p>현재 참여 가능한 기간이 아닙니다.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
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
    position: sticky;
    top: 100px;
}

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
    
    .event-detail-meta {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<script>
// 이벤트 삭제
function deleteEvent(ev_id) {
    if(!confirm('정말 이 이벤트를 삭제하시겠습니까?\n삭제된 이벤트는 복구할 수 없습니다.')) {
        return;
    }
    
    fetch('<?php echo G5_URL; ?>/ajax/event_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ev_id=' + ev_id
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('이벤트가 삭제되었습니다.');
            location.href = '<?php echo G5_URL; ?>/event.php';
        } else {
            alert(data.message || '삭제 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('삭제 중 오류가 발생했습니다.');
    });
}

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
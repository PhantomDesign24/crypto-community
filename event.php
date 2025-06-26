<?php
/*
 * 파일명: event.php
 * 위치: /event.php
 * 기능: 이벤트 메인 페이지 (사용자/관리자 통합)
 * 작성일: 2025-01-11
 * 수정일: 2025-01-26
 */

include_once('./_common.php');

// 한국 시간대 설정
date_default_timezone_set('Asia/Seoul');

$g5['title'] = '에어드랍 이벤트';
include_once(G5_PATH.'/head.php');

// 상태별 탭
$status = isset($_GET['status']) ? $_GET['status'] : 'ongoing';
if(!in_array($status, ['ongoing', 'scheduled', 'ended'])) {
    $status = 'ongoing';
}

// 페이징
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows = 12; // 한 페이지에 보여줄 이벤트 수
$from_record = ($page - 1) * $rows;

// 현재 시간
$current_datetime = date('Y-m-d H:i:s');

// 상태별 조건 설정 (날짜 기반)
$where = "";
if($status == 'ongoing') {
    // 진행중: 시작일 <= 현재 <= 종료일
    $where = "WHERE ev_start_date <= '{$current_datetime}' AND ev_end_date >= '{$current_datetime}'";
} else if($status == 'scheduled') {
    // 예정: 시작일 > 현재
    $where = "WHERE ev_start_date > '{$current_datetime}'";
} else if($status == 'ended') {
    // 종료: 종료일 < 현재
    $where = "WHERE ev_end_date < '{$current_datetime}'";
}

// 전체 이벤트 수
$sql = "SELECT COUNT(*) as cnt FROM g5_event {$where}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page = ceil($total_count / $rows);

// 이벤트 목록
$sql = "SELECT * FROM g5_event 
        {$where}
        ORDER BY ev_recommend DESC, ev_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);
?>

<!-- ===================================
     이벤트 페이지 헤더
     =================================== -->
<div class="event-page-header">
    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-gift-fill"></i>
            에어드랍 이벤트
        </h1>
        <p class="page-desc">다양한 코인 에어드랍 이벤트에 참여하고 무료 코인을 받아가세요!</p>
        
        <?php if($is_admin) { ?>
        <div class="admin-buttons mt-3">
            <a href="<?php echo G5_URL; ?>/event_write.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> 새 이벤트 작성
            </a>
            <a href="<?php echo G5_URL; ?>/event_apply_list.php" class="btn btn-info">
                <i class="bi bi-list-check"></i> 신청 관리
            </a>
        </div>
        <?php } ?>
    </div>
</div>

<!-- ===================================
     이벤트 페이지 콘텐츠
     =================================== -->
<div class="event-page-content">
    <div class="container">
        <!-- 상태 탭 -->
        <ul class="nav nav-tabs event-tabs mb-5">
            <li class="nav-item">
                <a class="nav-link <?php echo $status == 'ongoing' ? 'active' : ''; ?>" 
                   href="<?php echo G5_URL; ?>/event.php?status=ongoing">
                    <i class="bi bi-play-circle"></i> 진행중
                    <span class="badge bg-danger ms-1"><?php echo get_event_count_by_date('ongoing'); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $status == 'scheduled' ? 'active' : ''; ?>" 
                   href="<?php echo G5_URL; ?>/event.php?status=scheduled">
                    <i class="bi bi-clock"></i> 진행예정
                    <span class="badge bg-info ms-1"><?php echo get_event_count_by_date('scheduled'); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $status == 'ended' ? 'active' : ''; ?>" 
                   href="<?php echo G5_URL; ?>/event.php?status=ended">
                    <i class="bi bi-check-circle"></i> 진행종료
                    <span class="badge bg-secondary ms-1"><?php echo get_event_count_by_date('ended'); ?></span>
                </a>
            </li>
        </ul>
        
        <!-- 이벤트 그리드 -->
        <div class="event-grid mb-5">
            <?php 
            if($total_count > 0) {
                while($event = sql_fetch_array($result)) {
                    // 날짜 계산
                    $now = time();
                    $start_time = strtotime($event['ev_start_date']);
                    $end_time = strtotime($event['ev_end_date']);
                    
                    // 실제 상태 판단
                    if($now < $start_time) {
                        // 아직 시작 안함
                        $real_status = 'scheduled';
                        $days_left = ceil(($start_time - $now) / 86400);
                    } else if($now >= $start_time && $now <= $end_time) {
                        // 진행 중
                        $real_status = 'ongoing';
                        $days_left = ceil(($end_time - $now) / 86400);
                    } else {
                        // 종료됨
                        $real_status = 'ended';
                        $days_left = 0;
                    }
            ?>
            <div class="event-card" onclick="location.href='<?php echo G5_URL; ?>/event_view.php?ev_id=<?php echo $event['ev_id']; ?>'">
                <div class="event-card-inner">
                    <?php if($is_admin) { ?>
                    <!-- 관리자 메뉴 -->
                    <div class="admin-menu">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" onclick="event.stopPropagation();" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo G5_URL; ?>/event_write.php?w=u&ev_id=<?php echo $event['ev_id']; ?>">
                                    <i class="bi bi-pencil"></i> 수정
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo G5_URL; ?>/event_apply_list.php?ev_id=<?php echo $event['ev_id']; ?>">
                                    <i class="bi bi-people"></i> 신청자 관리
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteEvent(<?php echo $event['ev_id']; ?>); return false;">
                                    <i class="bi bi-trash"></i> 삭제
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <!-- 추천 배지 -->
                    <?php if($event['ev_recommend']) { ?>
                    <div class="recommend-badge">
                        <i class="bi bi-star-fill"></i> 추천
                    </div>
                    <?php } ?>
                    
                    <!-- 이벤트 이미지 -->
                    <div class="event-image">
                        <?php if($event['ev_image']) { ?>
                            <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $event['ev_image']; ?>" 
                                 alt="<?php echo $event['ev_subject']; ?>">
                        <?php } else { ?>
                            <div class="event-no-image">
                                <i class="bi bi-gift"></i>
                                <p><?php echo $event['ev_coin_symbol']; ?></p>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <!-- 이벤트 정보 -->
                    <div class="event-info">
                        <div class="event-coin-badge">
                            <span class="coin-symbol"><?php echo $event['ev_coin_symbol']; ?></span>
                            <span class="coin-amount"><?php echo $event['ev_coin_amount']; ?></span>
                        </div>
                        
                        <h3 class="event-title"><?php echo $event['ev_subject']; ?></h3>
                        <p class="event-summary"><?php echo $event['ev_summary']; ?></p>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="bi bi-calendar-range"></i>
                                <?php echo date('m/d', strtotime($event['ev_start_date'])); ?> ~ 
                                <?php echo date('m/d', strtotime($event['ev_end_date'])); ?>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                <?php echo number_format($event['ev_apply_count']); ?>명 참여
                            </div>
                        </div>
                        
                        <?php if($real_status == 'scheduled') { ?>
                        <div class="event-action">
                            <button class="btn btn-info btn-sm w-100" disabled>
                                <i class="bi bi-clock"></i> D-<?php echo $days_left; ?>
                            </button>
                        </div>
                        <?php } else if($real_status == 'ongoing') { ?>
                        <div class="event-action">
                            <button class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-cursor-fill"></i> 참여하기
                            </button>
                        </div>
                        <?php } else { ?>
                        <div class="event-action">
                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                <i class="bi bi-x-circle"></i> 종료됨
                            </button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
            ?>
            <div class="no-events">
                <i class="bi bi-inbox"></i>
                <p>현재 <?php echo get_status_text($status); ?> 이벤트가 없습니다.</p>
            </div>
            <?php } ?>
        </div>
        
        <!-- 페이징 -->
        <?php if($total_page > 1) { ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $start_page = max(1, $page - 5);
                $end_page = min($total_page, $page + 5);
                
                if($page > 1) {
                ?>
                <li class="page-item">
                    <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $page-1; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php } ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++) { ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php } ?>
                
                <?php if($page < $total_page) { ?>
                <li class="page-item">
                    <a class="page-link" href="?status=<?php echo $status; ?>&page=<?php echo $page+1; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </nav>
        <?php } ?>
        
        <!-- 참여자 현황 -->
        <div class="participants-section mt-5">
            <h3 class="section-title mb-4">
                <i class="bi bi-people-fill"></i> 최근 참여자 현황
            </h3>
            
            <div class="table-responsive">
                <table class="table table-hover participants-table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>참여자</th>
                            <th>이벤트명</th>
                            <th>코인</th>
                            <th>수량</th>
                            <th>참여일시</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 최근 참여자 목록 조회
                        $participants_sql = "SELECT a.*, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount, m.mb_nick, m.mb_id 
                                           FROM g5_event_apply a 
                                           JOIN g5_event e ON a.ev_id = e.ev_id 
                                           JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
                                           ORDER BY a.ea_datetime DESC 
                                           LIMIT 30";
                        $participants_result = sql_query($participants_sql);
                        $num = 1;
                        
                        while($participant = sql_fetch_array($participants_result)) {
                            // 닉네임 마스킹
                            $nick_len = mb_strlen($participant['mb_nick'], 'utf-8');
                            if($nick_len <= 2) {
                                $masked_nick = mb_substr($participant['mb_nick'], 0, 1, 'utf-8') . '*';
                            } else {
                                $masked_nick = mb_substr($participant['mb_nick'], 0, 1, 'utf-8') . 
                                              str_repeat('*', $nick_len - 2) . 
                                              mb_substr($participant['mb_nick'], -1, 1, 'utf-8');
                            }
                            
                            // 아이디 마스킹
                            $id_length = strlen($participant['mb_id']);
                            if($id_length <= 3) {
                                $masked_id = str_repeat('*', $id_length);
                            } else {
                                $masked_id = substr($participant['mb_id'], 0, 2) . str_repeat('*', $id_length - 4) . substr($participant['mb_id'], -2);
                            }
                        ?>
                        <tr class="participant-row" data-status="<?php echo $participant['ea_status']; ?>">
                            <td class="text-center"><?php echo $num++; ?></td>
                            <td>
                                <div class="participant-info">
                                    <span class="participant-nick"><?php echo $masked_nick; ?></span>
                                    <small class="text-muted">(<?php echo $masked_id; ?>)</small>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo G5_URL; ?>/event_view.php?ev_id=<?php echo $participant['ev_id']; ?>" 
                                   class="event-link">
                                    <?php echo $participant['ev_subject']; ?>
                                </a>
                            </td>
                            <td>
                                <span class="coin-symbol-small"><?php echo $participant['ev_coin_symbol']; ?></span>
                            </td>
                            <td class="text-success fw-bold">
                                <?php echo $participant['ev_coin_amount']; ?>
                            </td>
                            <td class="text-muted">
                                <?php echo date('m-d H:i', strtotime($participant['ea_datetime'])); ?>
                            </td>
                            <td>
                                <?php if($participant['ea_status'] == 'applied') { ?>
                                    <span class="badge bg-warning">신청완료</span>
                                <?php } else if($participant['ea_status'] == 'paid') { ?>
                                    <span class="badge bg-success">지급완료</span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">대기중</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <?php if($num == 1) { ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                아직 참여자가 없습니다.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 실시간 효과를 위한 업데이트 표시 -->
            <div class="update-notice text-center mt-3">
                <small class="text-muted">
                    <i class="bi bi-arrow-clockwise"></i> 
                    실시간 업데이트 중 
                    <span class="update-dot"></span>
                </small>
            </div>
        </div>
    </div>
</div>

<style>
/* 페이지 헤더 */
.event-page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    margin-bottom: 40px;
}

.page-title {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 10px;
}

.page-desc {
    font-size: 18px;
    opacity: 0.9;
}

/* 관리자 버튼 */
.admin-buttons {
    display: flex;
    gap: 10px;
}

/* 이벤트 탭 */
.event-tabs .nav-link {
    color: #6b7280;
    font-weight: 500;
    padding: 12px 24px;
    border: none;
    border-bottom: 3px solid transparent;
}

.event-tabs .nav-link:hover {
    color: #3b82f6;
}

.event-tabs .nav-link.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
    background: none;
}

/* 이벤트 그리드 */
.event-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}

/* 이벤트 카드 */
.event-card {
    cursor: pointer;
    position: relative;
}

.event-card-inner {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.event-card:hover .event-card-inner {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

/* 관리자 메뉴 */
.admin-menu {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

/* 추천 배지 */
.recommend-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #fbbf24;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
}

/* 이벤트 이미지 */
.event-image {
    width: 100%;
    height: 180px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-no-image {
    text-align: center;
    color: #9ca3af;
}

.event-no-image i {
    font-size: 48px;
    margin-bottom: 8px;
}

.event-no-image p {
    font-size: 20px;
    font-weight: 600;
    margin: 0;
}

/* 이벤트 정보 */
.event-info {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.event-coin-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.coin-symbol {
    background: #dbeafe;
    color: #1e40af;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 13px;
    font-weight: 600;
}

.coin-amount {
    color: #16a34a;
    font-weight: 600;
    font-size: 14px;
}

.event-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.event-summary {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 16px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.event-meta {
    font-size: 13px;
    color: #9ca3af;
    margin-bottom: 16px;
}

.meta-item {
    margin-bottom: 4px;
}

/* 참여자 현황 테이블 */
.participants-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.participants-table {
    margin: 0;
}

.participants-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.participants-table td {
    vertical-align: middle;
    padding: 16px 12px;
    border-bottom: 1px solid #f3f4f6;
}

.participants-table tbody tr:last-child td {
    border-bottom: none;
}

/* 참여자 정보 */
.participant-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.participant-nick {
    font-weight: 500;
    color: #1f2937;
}

/* 이벤트 링크 */
.event-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.event-link:hover {
    text-decoration: underline;
}

/* 코인 심볼 */
.coin-symbol-small {
    background: #dbeafe;
    color: #1e40af;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* 행 애니메이션 */
.participant-row {
    transition: all 0.3s ease;
}

.participant-row:hover {
    background-color: #f9fafb;
}

/* 새로운 참여자 하이라이트 */
.participant-row.new-entry {
    animation: highlight 2s ease;
}

@keyframes highlight {
    0% {
        background-color: #fef3c7;
    }
    100% {
        background-color: transparent;
    }
}

/* 지급완료 행 스타일 */
.participant-row[data-status="paid"] {
    background-color: #f0fdf4;
}

/* 업데이트 표시 */
.update-notice {
    font-size: 12px;
}

.update-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    margin-left: 4px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* 빈 상태 */
.no-events {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: #9ca3af;
}

.no-events i {
    font-size: 64px;
    margin-bottom: 16px;
}

/* 반응형 */
@media (max-width: 1200px) {
    .event-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .event-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .participants-section {
        padding: 16px;
    }
    
    .participants-table {
        font-size: 13px;
    }
    
    .participants-table th,
    .participants-table td {
        padding: 10px 8px;
    }
    
    /* 모바일에서 일부 컬럼 숨기기 */
    .participants-table th:nth-child(4),
    .participants-table td:nth-child(4) {
        display: none;
    }
    
    .participant-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }
    
    .participant-info small {
        font-size: 11px;
    }
}

@media (max-width: 576px) {
    .event-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 28px;
    }
    
    .page-desc {
        font-size: 16px;
    }
    
    .event-tabs .nav-link {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .admin-buttons {
        flex-direction: column;
    }
}
</style>

<script>
// 이벤트 삭제
function deleteEvent(ev_id) {
    if(!confirm('정말 이 이벤트를 삭제하시겠습니까?')) {
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
            location.reload();
        } else {
            alert(data.message || '삭제 중 오류가 발생했습니다.');
        }
    });
}

// 실시간 업데이트 효과 (선택사항)
setInterval(function() {
    // 실제로는 AJAX로 새로운 데이터를 가져와서 업데이트
    // 여기서는 시각적 효과만 구현
    const dot = document.querySelector('.update-dot');
    if(dot) {
        dot.style.backgroundColor = '#' + Math.floor(Math.random()*16777215).toString(16);
    }
}, 3000);

// 새로운 참여자 하이라이트 (페이지 로드 시)
document.addEventListener('DOMContentLoaded', function() {
    const firstRow = document.querySelector('.participant-row');
    if(firstRow) {
        firstRow.classList.add('new-entry');
    }
});
</script>

<?php
// 헬퍼 함수들 - 날짜 기반으로 수정
function get_event_count_by_date($status) {
    $current_datetime = date('Y-m-d H:i:s');
    
    if($status == 'ongoing') {
        $where = "WHERE ev_start_date <= '{$current_datetime}' AND ev_end_date >= '{$current_datetime}'";
    } else if($status == 'scheduled') {
        $where = "WHERE ev_start_date > '{$current_datetime}'";
    } else if($status == 'ended') {
        $where = "WHERE ev_end_date < '{$current_datetime}'";
    } else {
        $where = "";
    }
    
    $sql = "SELECT COUNT(*) as cnt FROM g5_event {$where}";
    $row = sql_fetch($sql);
    return $row['cnt'];
}

function get_status_text($status) {
    switch($status) {
        case 'ongoing': return '진행중인';
        case 'scheduled': return '진행 예정인';
        case 'ended': return '종료된';
        default: return '';
    }
}

// 테이블 존재 확인 함수
function sql_table_exists($table_name) {
    $sql = "SHOW TABLES LIKE '{$table_name}'";
    $result = sql_query($sql);
    return sql_num_rows($result) > 0;
}

include_once(G5_PATH.'/tail.php');
?>
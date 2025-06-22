<?php
/*
 * 파일명: get_events.php
 * 위치: /ajax/get_events.php
 * 기능: 이벤트 목록 Ajax
 * 작성일: 2025-01-11
 */

include_once('../common.php');

header('Content-Type: application/json');

$status = isset($_GET['status']) ? $_GET['status'] : 'ongoing';
if(!in_array($status, ['ongoing', 'scheduled', 'ended'])) {
    $status = 'ongoing';
}

// 이벤트 목록 가져오기
$sql = "SELECT * FROM g5_event 
        WHERE ev_status = '{$status}'
        AND ev_recommend = 1
        ORDER BY ev_id DESC 
        LIMIT 6";
$result = sql_query($sql);

$html = '';
while($row = sql_fetch_array($result)) {
    $remaining_days = floor((strtotime($row['ev_end_date']) - time()) / 86400);
    
    ob_start();
    ?>
    <div class="event-card" data-event-id="<?php echo $row['ev_id']; ?>">
        <div class="event-card-inner">
            <!-- 상태 배지 -->
            <div class="event-badge">
                <?php if($row['ev_status'] == 'ongoing') { ?>
                    <span class="badge bg-success">진행중</span>
                <?php } else if($row['ev_status'] == 'scheduled') { ?>
                    <span class="badge bg-info">진행예정</span>
                <?php } else { ?>
                    <span class="badge bg-secondary">종료</span>
                <?php } ?>
            </div>
            
            <!-- 이벤트 이미지 -->
            <div class="event-image">
                <?php if($row['ev_image']) { ?>
                    <img src="<?php echo G5_DATA_URL; ?>/event/<?php echo $row['ev_image']; ?>" alt="<?php echo $row['ev_subject']; ?>">
                <?php } else { ?>
                    <div class="event-no-image">
                        <i class="bi bi-gift"></i>
                    </div>
                <?php } ?>
            </div>
            
            <!-- 이벤트 내용 -->
            <div class="event-content">
                <div class="event-coin-info">
                    <span class="coin-symbol"><?php echo $row['ev_coin_symbol']; ?></span>
                    <span class="coin-amount"><?php echo $row['ev_coin_amount']; ?></span>
                </div>
                <h4 class="event-title"><?php echo $row['ev_subject']; ?></h4>
                <p class="event-summary"><?php echo $row['ev_summary']; ?></p>
                
                <div class="event-meta">
                    <div class="meta-item">
                        <i class="bi bi-calendar-event"></i>
                        <span>D-<?php echo $remaining_days; ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-people"></i>
                        <span><?php echo number_format($row['ev_apply_count']); ?>명 참여</span>
                    </div>
                </div>
                
                <button class="btn btn-primary btn-sm w-100 mt-3" onclick="viewEvent(<?php echo $row['ev_id']; ?>)">
                    <i class="bi bi-arrow-right-circle"></i> 참여하기
                </button>
            </div>
        </div>
    </div>
    <?php
    $html .= ob_get_clean();
}

if(!$html) {
    $html = '<div class="text-center col-12 py-5"><p class="text-muted">현재 '.($status == 'ongoing' ? '진행중인' : ($status == 'scheduled' ? '예정된' : '종료된')).' 이벤트가 없습니다.</p></div>';
}

echo json_encode(['success' => true, 'html' => $html]);
?>
<?php
/*
 * 파일명: event_list.php
 * 위치: /sub_admin/event_list.php
 * 기능: 하부조직 이벤트 관리
 * 작성일: 2025-01-11
 * 수정일: 2025-01-24 (삭제 기능 추가)
 */

define('_GNUBOARD_', true);
$sub_menu = "300";
include_once('./_common.php');

$g5['title'] = '이벤트 관리';

include_once('./header.php');

// 상태별 탭
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// 검색
$sfl = isset($_GET['sfl']) ? $_GET['sfl'] : '';
$stx = isset($_GET['stx']) ? $_GET['stx'] : '';

$sql_search = " WHERE 1=1 ";

// 최고관리자는 모든 신청 내역을 보고, 일반 관리자는 하위 회원의 신청만 보기
$managed_members = array();
if(!$is_admin) { // 최고관리자가 아닌 경우
    $managed_members = get_managed_members($member['mb_id']);
    if($managed_members && count($managed_members) > 0) {
        $sql_search .= " AND ea.mb_id IN ('".implode("','", $managed_members)."') ";
    } else {
        // 관리하는 회원이 없어도 자신의 신청은 볼 수 있게
        $sql_search .= " AND ea.mb_id = '{$member['mb_id']}' ";
    }
}

if($status && $status != 'all') {
    if($status == 'applied') {
        $sql_search .= " AND ea.ea_status = 'applied' ";
    } else if($status == 'paid') {
        $sql_search .= " AND ea.ea_status = 'paid' ";
    }
}

if($stx) {
    $sql_search .= " AND {$sfl} LIKE '%{$stx}%' ";
}

// 전체 신청 수
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_event_apply ea 
        LEFT JOIN g5_member m ON ea.mb_id = m.mb_id 
        LEFT JOIN g5_event e ON ea.ev_id = e.ev_id
        {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 페이징
$rows = 20;
$total_page = ceil($total_count / $rows);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$from_record = ($page - 1) * $rows;

// 이벤트 신청 목록
$sql = "SELECT ea.*, m.mb_nick, m.mb_name, m.mb_recommend, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount
        FROM g5_event_apply ea 
        LEFT JOIN g5_member m ON ea.mb_id = m.mb_id 
        LEFT JOIN g5_event e ON ea.ev_id = e.ev_id
        {$sql_search}
        ORDER BY ea.ea_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);
?>

<!-- 페이지 컨테이너 -->
<div class="ev-container">
    <!-- 통계 카드 -->
    <div class="ev-stats-grid">
        <div class="ev-stat-card">
            <div class="ev-stat-icon bg-primary">
                <i class="bi bi-gift"></i>
            </div>
            <div class="ev-stat-content">
                <div class="ev-stat-value"><?php echo number_format($total_count); ?></div>
                <div class="ev-stat-label">
                    <?php echo $is_admin ? '전체 신청' : '하위 회원 신청'; ?>
                </div>
            </div>
        </div>
        
        <div class="ev-stat-card">
            <div class="ev-stat-icon bg-warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="ev-stat-content">
                <?php
                if($is_admin) {
                    $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply ea WHERE ea.ea_status = 'applied'");
                } else {
                    $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply ea WHERE ea.ea_status = 'applied' ".($managed_members ? "AND ea.mb_id IN ('".implode("','", $managed_members)."')" : "AND 1=0"));
                }
                ?>
                <div class="ev-stat-value"><?php echo number_format($cnt['cnt']); ?></div>
                <div class="ev-stat-label">대기중</div>
            </div>
        </div>
        
        <div class="ev-stat-card">
            <div class="ev-stat-icon bg-success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="ev-stat-content">
                <?php
                if($is_admin) {
                    $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply ea WHERE ea.ea_status = 'paid'");
                } else {
                    $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply ea WHERE ea.ea_status = 'paid' ".($managed_members ? "AND ea.mb_id IN ('".implode("','", $managed_members)."')" : "AND 1=0"));
                }
                ?>
                <div class="ev-stat-value"><?php echo number_format($cnt['cnt']); ?></div>
                <div class="ev-stat-label">지급완료</div>
            </div>
        </div>
        
        <div class="ev-stat-card">
            <div class="ev-stat-icon bg-info">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div class="ev-stat-content">
                <?php
                $cnt = sql_fetch("SELECT COUNT(DISTINCT e.ev_id) as cnt FROM g5_event e WHERE e.ev_status = 'ongoing'");
                ?>
                <div class="ev-stat-value"><?php echo number_format($cnt['cnt']); ?></div>
                <div class="ev-stat-label">진행 이벤트</div>
            </div>
        </div>
    </div>
    
    <!-- 검색 영역 -->
    <div class="ev-search-box">
        <form method="get" class="ev-search-form">
            <div class="ev-form-row">
                <div class="ev-form-group">
                    <select name="status" class="ev-form-select">
                        <option value="all">전체 상태</option>
                        <option value="applied" <?php echo $status == 'applied' ? 'selected' : ''; ?>>신청완료</option>
                        <option value="paid" <?php echo $status == 'paid' ? 'selected' : ''; ?>>지급완료</option>
                    </select>
                </div>
                <div class="ev-form-group">
                    <select name="sfl" class="ev-form-select">
                        <option value="m.mb_id" <?php echo $sfl == 'm.mb_id' ? 'selected' : ''; ?>>회원ID</option>
                        <option value="m.mb_name" <?php echo $sfl == 'm.mb_name' ? 'selected' : ''; ?>>이름</option>
                        <option value="m.mb_nick" <?php echo $sfl == 'm.mb_nick' ? 'selected' : ''; ?>>닉네임</option>
                        <option value="e.ev_subject" <?php echo $sfl == 'e.ev_subject' ? 'selected' : ''; ?>>이벤트명</option>
                        <?php if($is_admin) { ?>
                        <option value="m.mb_recommend" <?php echo $sfl == 'm.mb_recommend' ? 'selected' : ''; ?>>추천인</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="ev-form-group flex-grow-1">
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="ev-form-input" placeholder="검색어를 입력하세요">
                </div>
                <div class="ev-form-group">
                    <button type="submit" class="ev-btn ev-btn-primary">
                        <i class="bi bi-search"></i> 검색
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 선택 삭제 폼 시작 -->
    <form name="flist" method="post" action="./event_list_update.php" onsubmit="return flist_submit(this);">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="status" value="<?php echo $status ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="">

    <?php if($is_admin) { // 최고관리자만 선택 삭제 가능 ?>
    <div class="ev-batch-actions">
        <button type="submit" name="act_button" value="선택삭제" class="ev-btn ev-btn-danger" onclick="document.pressed=this.value">
            <i class="bi bi-trash"></i> 선택삭제
        </button>
    </div>
    <?php } ?>
    
    <!-- 이벤트 신청 목록 테이블 -->
    <div class="ev-table-responsive">
        <table class="ev-table">
            <thead>
                <tr>
                    <?php if($is_admin) { ?>
                    <th class="ev-check-all">
                        <input type="checkbox" id="chkall">
                    </th>
                    <?php } ?>
                    <th class="ev-number">번호</th>
                    <th class="ev-event">이벤트명</th>
                    <th class="ev-user">신청자</th>
                    <th class="ev-recommender">추천인</th>
                    <th class="ev-reward">보상</th>
                    <th class="ev-date">신청일시</th>
                    <th class="ev-status">상태</th>
                    <th class="ev-action">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $list_num = $total_count - ($page - 1) * $rows;
                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    // 상태 표시
                    if($row['ea_status'] == 'applied') {
                        $status_html = '<span class="ev-badge ev-badge-warning"><i class="bi bi-clock"></i> 승인 대기</span>';
                    } else if($row['ea_status'] == 'paid') {
                        $status_html = '<span class="ev-badge ev-badge-success"><i class="bi bi-check-circle"></i> 지급완료</span>';
                    } else {
                        $status_html = '<span class="ev-badge ev-badge-secondary">미정</span>';
                    }
                ?>
                <tr>
                    <?php if($is_admin) { ?>
                    <td class="ev-check">
                        <input type="hidden" name="ea_id[<?php echo $i ?>]" value="<?php echo $row['ea_id'] ?>">
                        <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
                    </td>
                    <?php } ?>
                    <td class="ev-number"><?php echo $list_num; ?></td>
                    <td class="ev-event">
                        <a href="<?php echo G5_BBS_URL; ?>/board.php?bo_table=event&wr_id=<?php echo $row['ev_id']; ?>" target="_blank" class="ev-event-link">
                            <?php echo $row['ev_subject']; ?>
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </td>
                    <td class="ev-user">
                        <div class="ev-user-info">
                            <span class="ev-user-name"><?php echo $row['mb_name']; ?></span>
                            <span class="ev-user-id">(<?php echo $row['mb_id']; ?>)</span>
                        </div>
                    </td>
                    <td class="ev-recommender"><?php echo $row['mb_recommend'] ? $row['mb_recommend'] : '-'; ?></td>
                    <td class="ev-reward">
                        <?php if($row['ev_coin_amount']) { ?>
                            <span class="ev-coin-amount">
                                <i class="bi bi-coin"></i>
                                <?php echo number_format($row['ev_coin_amount']); ?> 
                                <span class="ev-coin-symbol"><?php echo $row['ev_coin_symbol']; ?></span>
                            </span>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                    <td class="ev-date"><?php echo substr($row['ea_datetime'], 0, 16); ?></td>
                    <td class="ev-status">
                        <?php if($is_admin) { // 최고관리자만 상태 변경 가능 ?>
                        <select class="ev-status-select" data-ea-id="<?php echo $row['ea_id']; ?>" onchange="changeStatus(this)">
                            <option value="applied" <?php echo $row['ea_status'] == 'applied' ? 'selected' : ''; ?>>승인 대기</option>
                            <option value="paid" <?php echo $row['ea_status'] == 'paid' ? 'selected' : ''; ?>>지급완료</option>
                        </select>
                        <?php } else { ?>
                        <?php echo $status_html; ?>
                        <?php } ?>
                    </td>
                    <td class="ev-action">
                        <div class="ev-action-buttons">
                            <?php if($row['ea_status'] == 'applied') { ?>
                                <a href="./event_pay.php?ea_id=<?php echo $row['ea_id']; ?>&<?php echo $qstr; ?>" 
                                   class="ev-btn ev-btn-success"
                                   onclick="return confirm('이벤트 보상을 지급하시겠습니까?');">
                                    <i class="bi bi-check"></i> 승인
                                </a>
                            <?php } ?>
                            
                            <?php if($is_admin) { // 최고관리자만 개별 삭제 가능 ?>
                                <a href="./event_delete.php?ea_id=<?php echo $row['ea_id']; ?>&<?php echo $qstr; ?>" 
                                   class="ev-btn ev-btn-danger"
                                   onclick="return confirm('정말 삭제하시겠습니까?\n\n삭제하면 복구할 수 없습니다.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php
                    $list_num--;
                }
                
                if ($i == 0) {
                ?>
                <tr>
                    <td colspan="<?php echo $is_admin ? '9' : '8'; ?>" class="text-center ev-empty">
                        <i class="bi bi-inbox"></i>
                        <p>신청 내역이 없습니다.</p>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    </form>
    
    <!-- 페이징 -->
    <?php if($total_page > 1) { ?>
    <div class="ev-pagination">
        <?php
        $qstr .= "&status={$status}";
        echo get_paging(10, $page, $total_page, './event_list.php?'.$qstr.'&page=');
        ?>
    </div>
    <?php } ?>
</div>

<!-- 최고관리자용 추가 정보 -->
<?php if($is_admin) { ?>
<div class="ev-admin-info">
    <div class="ev-info-card">
        <h4><i class="bi bi-info-circle"></i> 최고관리자 안내</h4>
        <ul>
            <li>모든 회원의 이벤트 신청 내역을 확인할 수 있습니다.</li>
            <li>추천인별로 검색하여 특정 하부조직의 신청 현황을 확인할 수 있습니다.</li>
            <li>지급 완료 처리 시 전광판에 자동으로 표시됩니다.</li>
            <li>삭제 기능은 최고관리자만 사용 가능하며, 승인 완료된 건은 삭제할 수 없습니다.</li>
        </ul>
    </div>
</div>
<?php } ?>

<script>
// 전체 선택
$(function() {
    $("#chkall").click(function() {
        var checked = $(this).is(":checked");
        $("input[name='chk[]']").prop("checked", checked);
    });
});

function flist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?\n\n삭제하면 복구할 수 없습니다.")) {
            return false;
        }
    }

    return true;
}

// 상태 변경 함수
function changeStatus(select) {
    var ea_id = $(select).data('ea-id');
    var status = $(select).val();
    var originalValue = $(select).data('original-value');
    
    if(!originalValue) {
        $(select).data('original-value', $(select).find('option:selected').val());
    }
    
    if(!confirm('상태를 변경하시겠습니까?')) {
        $(select).val($(select).data('original-value'));
        return false;
    }
    
    $.ajax({
        url: './event_status_update.php',
        type: 'POST',
        data: {
            ea_id: ea_id,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('상태가 변경되었습니다.');
                $(select).data('original-value', status);
                
                // 지급완료로 변경된 경우 승인 버튼 숨기기
                if(status == 'paid') {
                    $(select).closest('tr').find('.ev-btn-success').remove();
                }
            } else {
                alert(response.message || '상태 변경 중 오류가 발생했습니다.');
                $(select).val($(select).data('original-value'));
            }
        },
        error: function() {
            alert('상태 변경 중 오류가 발생했습니다.');
            $(select).val($(select).data('original-value'));
        }
    });
}
</script>

<style>
/* ===================================
 * 이벤트 관리 페이지 스타일 (ev- 접두사)
 * =================================== */

/* 페이지 컨테이너 */
.ev-container {
    max-width: 100%;
    margin: 0 auto;
}

/* 통계 그리드 */
.ev-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* 통계 카드 */
.ev-stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 16px;
}

.ev-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.ev-stat-icon.bg-primary {
    background: #e0e7ff;
    color: #4f46e5;
}

.ev-stat-icon.bg-warning {
    background: #fef3c7;
    color: #f59e0b;
}

.ev-stat-icon.bg-success {
    background: #d1fae5;
    color: #10b981;
}

.ev-stat-icon.bg-info {
    background: #dbeafe;
    color: #3b82f6;
}

.ev-stat-content {
    flex: 1;
}

.ev-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}

.ev-stat-label {
    font-size: 14px;
    color: #6b7280;
}

/* 검색 영역 */
.ev-search-box {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.ev-search-form {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.ev-form-row {
    display: flex;
    gap: 12px;
    width: 100%;
    align-items: center;
}

.ev-form-group {
    display: flex;
    flex-direction: column;
}

.ev-form-group.flex-grow-1 {
    flex: 1;
}

.ev-form-select,
.ev-form-input {
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.2s;
}

.ev-form-select:focus,
.ev-form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 버튼 스타일 */
.ev-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.ev-btn i {
    font-size: 16px;
}

.ev-btn-primary {
    background: #3b82f6;
    color: white;
}

.ev-btn-primary:hover {
    background: #2563eb;
}

.ev-btn-success {
    background: #10b981;
    color: white;
    padding: 6px 12px;
    font-size: 12px;
}

.ev-btn-success:hover {
    background: #059669;
    color: white;
}

.ev-btn-danger {
    background: #ef4444;
    color: white;
    padding: 6px 12px;
    font-size: 12px;
}

.ev-btn-danger:hover {
    background: #dc2626;
    color: white;
}

/* 선택 삭제 버튼 영역 */
.ev-batch-actions {
    margin-bottom: 15px;
}

/* 테이블 반응형 */
.ev-table-responsive {
    overflow-x: auto;
    margin-bottom: 20px;
}

/* 테이블 스타일 */
.ev-table {
    width: 100%;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.ev-table thead th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    padding: 16px 12px;
    text-align: center;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.ev-table tbody td {
    padding: 16px 12px;
    text-align: center;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.ev-table tbody tr:hover {
    background: #f9fafb;
}

/* 체크박스 열 */
.ev-check-all {
    width: 40px;
}

.ev-check {
    width: 40px;
}

/* 열 너비 */
.ev-number {
    width: 60px;
}

.ev-event {
    min-width: 200px;
}

.ev-user {
    min-width: 120px;
}

.ev-recommender {
    min-width: 100px;
}

.ev-reward {
    min-width: 120px;
}

.ev-date {
    min-width: 140px;
}

.ev-status {
    min-width: 100px;
}

.ev-action {
    min-width: 120px;
}

/* 이벤트명 링크 */
.ev-event-link {
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.ev-event-link:hover {
    color: #3b82f6;
}

.ev-event-link i {
    font-size: 12px;
    opacity: 0.6;
}

/* 사용자 정보 */
.ev-user-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.ev-user-name {
    font-weight: 500;
    color: #374151;
}

.ev-user-id {
    font-size: 12px;
    color: #9ca3af;
}

/* 코인 표시 */
.ev-coin-amount {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #3b82f6;
    font-weight: 600;
}

.ev-coin-symbol {
    font-size: 12px;
    color: #6b7280;
}

/* 상태 선택 박스 */
.ev-status-select {
    padding: 6px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    background: white;
    cursor: pointer;
    min-width: 100px;
}

.ev-status-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 상태 배지 */
.ev-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.ev-badge i {
    font-size: 14px;
}

.ev-badge-warning {
    background: #fef3c7;
    color: #d97706;
}

.ev-badge-success {
    background: #d1fae5;
    color: #059669;
}

.ev-badge-secondary {
    background: #e5e7eb;
    color: #6b7280;
}

/* 액션 버튼 */
.ev-action-buttons {
    display: flex;
    gap: 4px;
    justify-content: center;
}

/* 빈 상태 */
.ev-empty {
    padding: 60px 20px !important;
    text-align: center;
    color: #9ca3af;
}

.ev-empty i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.ev-empty p {
    margin: 0;
    font-size: 16px;
}

/* 페이지네이션 */
.ev-pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

/* 관리자 정보 카드 */
.ev-admin-info {
    margin-top: 30px;
}

.ev-info-card {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 20px;
}

.ev-info-card h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 12px;
}

.ev-info-card ul {
    margin: 0;
    padding-left: 20px;
}

.ev-info-card li {
    color: #3b82f6;
    margin-bottom: 8px;
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .ev-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .ev-stat-card {
        padding: 16px;
    }
    
    .ev-stat-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .ev-stat-value {
        font-size: 20px;
    }
    
    .ev-stat-label {
        font-size: 12px;
    }
    
    .ev-search-form {
        flex-direction: column;
    }
    
    .ev-form-row {
        flex-direction: column;
        width: 100%;
    }
    
    .ev-form-group {
        width: 100%;
    }
    
    .ev-table {
        font-size: 12px;
    }
    
    .ev-table thead th,
    .ev-table tbody td {
        padding: 12px 8px;
    }
    
    .ev-btn {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .ev-btn i {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .ev-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// 담당자가 관리하는 회원 목록 가져오기
function get_managed_members($manager_id) {
    $members = array();
    
    // 1단계 추천인 (직접 추천)
    $sql = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
            WHERE mb_recommend = '{$manager_id}'";
    $result = sql_query($sql);
    while($row = sql_fetch_array($result)) {
        $members[] = $row['mb_id'];
        
        // 2단계 추천인 (추천인의 추천인)
        $sql2 = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
                 WHERE mb_recommend = '{$row['mb_id']}'";
        $result2 = sql_query($sql2);
        while($row2 = sql_fetch_array($result2)) {
            $members[] = $row2['mb_id'];
        }
    }
    
    return array_unique($members);
}

include_once('./footer.php');
?>
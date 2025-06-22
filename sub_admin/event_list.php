<?php
/*
 * 파일명: event_list.php
 * 위치: /sub_admin/event_list.php
 * 기능: 하부조직 이벤트 관리
 * 작성일: 2025-01-11
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

// 담당자인 경우 자신이 관리하는 회원들의 이벤트 신청만 보기
$managed_members = array();
if($member['mb_grade'] < 10) { // 최고관리자가 아닌 경우
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
$sql = "SELECT ea.*, m.mb_nick, m.mb_name, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount
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
                <div class="ev-stat-label">전체 신청</div>
            </div>
        </div>
        
        <div class="ev-stat-card">
            <div class="ev-stat-icon bg-warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="ev-stat-content">
                <?php
                $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply ea WHERE ea.ea_status = 'applied' ".($managed_members ? "AND ea.mb_id IN ('".implode("','", $managed_members)."')" : "AND 1=0"));
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
                $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply ea WHERE ea.ea_status = 'paid' ".($managed_members ? "AND ea.mb_id IN ('".implode("','", $managed_members)."')" : "AND 1=0"));
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
                        <option value="m.mb_nick" <?php echo $sfl == 'm.mb_nick' ? 'selected' : ''; ?>>닉네임</option>
                        <option value="m.mb_id" <?php echo $sfl == 'm.mb_id' ? 'selected' : ''; ?>>아이디</option>
                        <option value="e.ev_subject" <?php echo $sfl == 'e.ev_subject' ? 'selected' : ''; ?>>이벤트명</option>
                    </select>
                </div>
                
                <div class="ev-form-group" style="flex: 1;">
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="ev-form-control" placeholder="검색어를 입력하세요">
                </div>
                
                <div class="ev-form-group">
                    <button type="submit" class="ev-btn ev-btn-primary">
                        <i class="bi bi-search"></i> 검색
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 이벤트 신청 목록 -->
    <div class="ev-table-wrap">
        <table class="ev-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>상태</th>
                    <th>이벤트명</th>
                    <th>신청자</th>
                    <th>지갑주소</th>
                    <th>신청일시</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $num = $total_count - $from_record;
                while($row = sql_fetch_array($result)) {
                ?>
                <tr>
                    <td class="text-center"><?php echo $num--; ?></td>
                    <td class="text-center">
                        <?php if($row['ea_status'] == 'paid') { ?>
                            <span class="ev-badge ev-badge-success">지급완료</span>
                        <?php } else { ?>
                            <span class="ev-badge ev-badge-warning">대기중</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="<?php echo G5_URL; ?>/event.php?ev_id=<?php echo $row['ev_id']; ?>" target="_blank" class="ev-link">
                            <?php echo $row['ev_subject']; ?>
                        </a>
                        <span class="ev-text-muted">(<?php echo $row['ev_coin_symbol']; ?> <?php echo $row['ev_coin_amount']; ?>)</span>
                    </td>
                    <td>
                        <strong><?php echo $row['mb_nick']; ?></strong>
                        <span class="ev-text-muted">(<?php echo $row['mb_id']; ?>)</span>
                    </td>
                    <td>
                        <code class="ev-code"><?php echo $row['ea_wallet_address']; ?></code>
                        <button class="ev-btn ev-btn-sm ev-btn-outline" onclick="copyToClipboard('<?php echo $row['ea_wallet_address']; ?>')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </td>
                    <td class="text-center"><?php echo date('Y-m-d H:i', strtotime($row['ea_datetime'])); ?></td>
                    <td class="text-center">
                        <?php if($row['ea_status'] == 'applied') { ?>
                        <button onclick="paymentComplete(<?php echo $row['ea_id']; ?>)" class="ev-btn ev-btn-sm ev-btn-success">
                            <i class="bi bi-check"></i> 지급완료
                        </button>
                        <button onclick="editApply(<?php echo $row['ea_id']; ?>, '<?php echo $row['ea_wallet_address']; ?>')" class="ev-btn ev-btn-sm ev-btn-primary">
                            <i class="bi bi-pencil"></i> 수정
                        </button>
                        <?php } else { ?>
                        <button onclick="cancelPayment(<?php echo $row['ea_id']; ?>)" class="ev-btn ev-btn-sm ev-btn-warning">
                            <i class="bi bi-arrow-counterclockwise"></i> 취소
                        </button>
                        <button class="ev-btn ev-btn-sm ev-btn-secondary" disabled>
                            <i class="bi bi-check-all"></i> 지급됨
                        </button>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                
                <?php if($total_count == 0) { ?>
                <tr>
                    <td colspan="7" class="text-center ev-empty">
                        <i class="bi bi-inbox"></i>
                        <p>신청 내역이 없습니다.</p>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- 페이징 -->
    <?php if($total_page > 1) { ?>
    <div class="ev-pagination">
        <?php
        $pagelist = get_paging(10, $page, $total_page, '?'.http_build_query($_GET).'&page=');
        echo $pagelist;
        ?>
    </div>
    <?php } ?>
</div>

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
    background: #eff6ff;
    color: #3b82f6;
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
    background: #e0f2fe;
    color: #0ea5e9;
}

.ev-stat-content {
    flex: 1;
}

.ev-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.ev-stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-top: 4px;
}

/* 검색 박스 */
.ev-search-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.ev-search-form {
    margin: 0;
}

.ev-form-row {
    display: flex;
    gap: 12px;
    align-items: center;
}

.ev-form-group {
    display: flex;
    align-items: center;
}

.ev-form-select,
.ev-form-control {
    padding: 10px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    transition: all 0.2s;
}

.ev-form-select:focus,
.ev-form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 버튼 스타일 */
.ev-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
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
}

.ev-btn-success:hover {
    background: #059669;
}

.ev-btn-warning {
    background: #f59e0b;
    color: white;
}

.ev-btn-warning:hover {
    background: #d97706;
}

.ev-btn-secondary {
    background: #6b7280;
    color: white;
}

.ev-btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

.ev-btn-outline {
    background: transparent;
    border: 1px solid #e5e7eb;
    color: #6b7280;
}

.ev-btn-outline:hover {
    background: #f3f4f6;
}

/* 테이블 */
.ev-table-wrap {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.ev-table {
    width: 100%;
    border-collapse: collapse;
}

.ev-table th {
    background: #f9fafb;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.ev-table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
}

.ev-table tbody tr:hover {
    background: #f9fafb;
}

.ev-table tbody tr:last-child td {
    border-bottom: none;
}

/* 배지 */
.ev-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.ev-badge-success {
    background: #d1fae5;
    color: #065f46;
}

.ev-badge-warning {
    background: #fef3c7;
    color: #92400e;
}

/* 코드 */
.ev-code {
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 6px;
    font-family: monospace;
    font-size: 13px;
    color: #374151;
}

/* 링크 */
.ev-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.ev-link:hover {
    text-decoration: underline;
}

/* 텍스트 */
.ev-text-muted {
    color: #9ca3af;
    font-size: 13px;
}

/* 빈 상태 */
.ev-empty {
    padding: 60px 20px !important;
    color: #9ca3af;
    text-align: center;
}

.ev-empty i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

/* 페이징 */
.ev-pagination {
    margin-top: 20px;
    text-align: center;
}

.ev-pagination .pg_wrap {
    display: inline-flex;
    gap: 4px;
}

.ev-pagination .pg_page,
.ev-pagination .pg_current {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    text-decoration: none;
}

.ev-pagination .pg_page {
    background: white;
    border: 1px solid #e5e7eb;
    color: #374151;
}

.ev-pagination .pg_page:hover {
    background: #f3f4f6;
}

.ev-pagination .pg_current {
    background: #3b82f6;
    color: white;
    font-weight: 600;
}

/* 텍스트 정렬 */
.text-center {
    text-align: center;
}

/* 반응형 */
@media (max-width: 768px) {
    .ev-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .ev-form-row {
        flex-wrap: wrap;
    }
    
    .ev-form-group {
        width: 100%;
    }
    
    .ev-table {
        font-size: 14px;
    }
    
    .ev-table th,
    .ev-table td {
        padding: 12px 8px;
    }
    
    .ev-code {
        font-size: 11px;
        display: block;
        margin-bottom: 4px;
    }
    
    .ev-btn-sm {
        font-size: 12px;
        padding: 4px 8px;
    }
}
</style>

<script>
// 클립보드 복사
function copyToClipboard(text) {
    // 구형 브라우저 대응
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            alert('지갑 주소가 복사되었습니다.');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

// 폴백 복사 함수
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        alert('지갑 주소가 복사되었습니다.');
    } catch (err) {
        alert('복사에 실패했습니다. 수동으로 복사해주세요.');
    }
    
    document.body.removeChild(textArea);
}

// 지급 완료 처리
function paymentComplete(ea_id) {
    if(!confirm('지급 완료 처리하시겠습니까?')) {
        return;
    }
    
    fetch('<?php echo G5_URL; ?>/sub_admin/ajax/event_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ea_id=' + ea_id + '&action=pay'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            alert('지급 완료 처리되었습니다.');
            location.reload();
        } else {
            alert(data.message || '처리 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('처리 중 오류가 발생했습니다.');
    });
}

// 지급 취소 처리
function cancelPayment(ea_id) {
    if(!confirm('지급을 취소하고 대기 상태로 변경하시겠습니까?')) {
        return;
    }
    
    fetch('<?php echo G5_URL; ?>/sub_admin/ajax/event_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ea_id=' + ea_id + '&action=cancel'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            alert('대기 상태로 변경되었습니다.');
            location.reload();
        } else {
            alert(data.message || '처리 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('처리 중 오류가 발생했습니다.');
    });
}

// 신청 정보 수정
function editApply(ea_id, current_address) {
    const new_address = prompt('새 지갑 주소를 입력하세요:', current_address);
    
    if(new_address && new_address !== current_address) {
        if(!confirm('지갑 주소를 변경하시겠습니까?')) {
            return;
        }
        
        fetch('<?php echo G5_URL; ?>/sub_admin/ajax/event_edit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'ea_id=' + ea_id + '&wallet_address=' + encodeURIComponent(new_address)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                alert('수정되었습니다.');
                location.reload();
            } else {
                alert(data.message || '수정 중 오류가 발생했습니다.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('수정 중 오류가 발생했습니다.');
        });
    }
}
</script>

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
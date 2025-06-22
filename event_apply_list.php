<?php
/*
 * 파일명: event_apply_list.php
 * 위치: /event_apply_list.php
 * 기능: 이벤트 신청자 관리 (관리자/담당자)
 * 작성일: 2025-01-11
 */

if(!defined('_GNUBOARD_')) exit;

// 권한 체크
if(!$is_admin && !$is_event_manager) {
    alert('접근 권한이 없습니다.', G5_URL);
}

$g5['title'] = '이벤트 신청자 관리';

// 이벤트 ID
$ev_id = isset($_GET['ev_id']) ? (int)$_GET['ev_id'] : 0;

// 이벤트 정보
$event = null;
if($ev_id) {
    $event = sql_fetch("SELECT * FROM g5_event WHERE ev_id = '{$ev_id}'");
}

// 검색 조건
$sfl = isset($_GET['sfl']) ? $_GET['sfl'] : '';
$stx = isset($_GET['stx']) ? $_GET['stx'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$sql_search = " WHERE 1=1 ";
if($ev_id) {
    $sql_search .= " AND ea.ev_id = '{$ev_id}' ";
}
if($stx) {
    $sql_search .= " AND {$sfl} LIKE '%{$stx}%' ";
}
if($status) {
    $sql_search .= " AND ea.ea_status = '{$status}' ";
}

// 담당자인 경우 자신이 관리하는 회원만
if(!$is_admin && $is_event_manager) {
    // 담당자가 관리하는 회원 목록 가져오기 (구현 필요)
    $managed_members = get_managed_members($member['mb_id']);
    if($managed_members) {
        $sql_search .= " AND ea.mb_id IN ('".implode("','", $managed_members)."') ";
    } else {
        $sql_search .= " AND 1=0 "; // 관리하는 회원이 없으면 결과 없음
    }
}

// 전체 신청 수
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_event_apply ea 
        LEFT JOIN g5_member m ON ea.mb_id = m.mb_id 
        {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 페이징
$rows = 30;
$total_page = ceil($total_count / $rows);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$from_record = ($page - 1) * $rows;

// 신청 목록
$sql = "SELECT ea.*, m.mb_nick, m.mb_name, m.mb_email, m.mb_hp, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount
        FROM g5_event_apply ea 
        LEFT JOIN g5_member m ON ea.mb_id = m.mb_id 
        LEFT JOIN g5_event e ON ea.ev_id = e.ev_id
        {$sql_search}
        ORDER BY ea.ea_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

include_once(G5_PATH.'/head.php');
?>

<!-- ===================================
     신청자 관리 페이지
     =================================== -->
<div class="apply-list-page">
    <div class="container-fluid">
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><?php echo $g5['title']; ?></h2>
                    <?php if($event) { ?>
                    <p class="text-muted mb-0">
                        <i class="bi bi-gift"></i> <?php echo $event['ev_subject']; ?> 
                        (<?php echo $event['ev_coin_symbol']; ?> <?php echo $event['ev_coin_amount']; ?>)
                    </p>
                    <?php } ?>
                </div>
                <div>
                    <a href="<?php echo G5_URL; ?>/event.php" class="btn btn-secondary">
                        <i class="bi bi-list"></i> 이벤트 목록
                    </a>
                </div>
            </div>
        </div>
        
        <!-- 검색 폼 -->
        <div class="search-box mb-4">
            <form method="get" class="row g-3">
                <?php if($ev_id) { ?>
                <input type="hidden" name="ev_id" value="<?php echo $ev_id; ?>">
                <?php } ?>
                <input type="hidden" name="admin" value="apply_list">
                
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">전체 상태</option>
                        <option value="applied" <?php echo $status == 'applied' ? 'selected' : ''; ?>>신청완료</option>
                        <option value="paid" <?php echo $status == 'paid' ? 'selected' : ''; ?>>지급완료</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select name="sfl" class="form-select">
                        <option value="m.mb_nick" <?php echo $sfl == 'm.mb_nick' ? 'selected' : ''; ?>>닉네임</option>
                        <option value="m.mb_id" <?php echo $sfl == 'm.mb_id' ? 'selected' : ''; ?>>아이디</option>
                        <option value="ea.ea_wallet_address" <?php echo $sfl == 'ea.ea_wallet_address' ? 'selected' : ''; ?>>지갑주소</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <input type="text" name="stx" value="<?php echo $stx; ?>" class="form-control" placeholder="검색어">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> 검색
                    </button>
                </div>
            </form>
        </div>
        
        <!-- 통계 -->
        <div class="stats-box mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-content">
                            <h4><?php echo number_format($total_count); ?></h4>
                            <p>전체 신청자</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-content">
                            <?php
                            $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply WHERE ea_status = 'applied' ".($ev_id ? "AND ev_id = '{$ev_id}'" : ""));
                            ?>
                            <h4><?php echo number_format($cnt['cnt']); ?></h4>
                            <p>대기중</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="stat-content">
                            <?php
                            $cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply WHERE ea_status = 'paid' ".($ev_id ? "AND ev_id = '{$ev_id}'" : ""));
                            ?>
                            <h4><?php echo number_format($cnt['cnt']); ?></h4>
                            <p>지급완료</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="stat-content">
                            <?php
                            $cnt = sql_fetch("SELECT COUNT(DISTINCT ev_id) as cnt FROM g5_event_apply");
                            ?>
                            <h4><?php echo number_format($cnt['cnt']); ?></h4>
                            <p>진행 이벤트</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 신청자 목록 -->
        <div class="apply-list-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="chkall"></th>
                            <th>번호</th>
                            <th>상태</th>
                            <th>이벤트</th>
                            <th>신청자</th>
                            <th>지갑주소</th>
                            <th>신청일시</th>
                            <th>파일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $num = $total_count - $from_record;
                        while($row = sql_fetch_array($result)) {
                            // 파일 개수 확인
                            $file_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM g5_event_apply_file WHERE ea_id = '{$row['ea_id']}'");
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="chk[]" value="<?php echo $row['ea_id']; ?>">
                            </td>
                            <td><?php echo $num--; ?></td>
                            <td>
                                <?php if($row['ea_status'] == 'paid') { ?>
                                    <span class="badge bg-success">지급완료</span>
                                <?php } else { ?>
                                    <span class="badge bg-warning">대기중</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="<?php echo G5_URL; ?>/event.php?ev_id=<?php echo $row['ev_id']; ?>" target="_blank">
                                    <?php echo $row['ev_coin_symbol']; ?> <?php echo $row['ev_coin_amount']; ?>
                                </a>
                            </td>
                            <td>
                                <div class="user-info">
                                    <strong><?php echo $row['mb_nick']; ?></strong>
                                    <small class="text-muted">(<?php echo $row['mb_id']; ?>)</small>
                                </div>
                            </td>
                            <td>
                                <code><?php echo $row['ea_wallet_address']; ?></code>
                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="copyToClipboard('<?php echo $row['ea_wallet_address']; ?>')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['ea_datetime'])); ?></td>
                            <td>
                                <?php if($file_cnt['cnt'] > 0) { ?>
                                <button class="btn btn-sm btn-info" onclick="viewFiles(<?php echo $row['ea_id']; ?>)">
                                    <i class="bi bi-image"></i> <?php echo $file_cnt['cnt']; ?>개
                                </button>
                                <?php } else { ?>
                                <span class="text-muted">-</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($row['ea_status'] == 'applied') { ?>
                                <a href="<?php echo G5_URL; ?>/event.php?admin=payment_complete&ea_id=<?php echo $row['ea_id']; ?>" 
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('지급 완료 처리하시겠습니까?');">
                                    <i class="bi bi-check"></i> 지급완료
                                </a>
                                <?php } else { ?>
                                <button class="btn btn-sm btn-secondary" disabled>
                                    <i class="bi bi-check-all"></i> 처리됨
                                </button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <?php if($total_count == 0) { ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                신청 내역이 없습니다.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- 일괄 처리 -->
        <?php if($total_count > 0) { ?>
        <div class="bulk-actions mt-3">
            <button type="button" class="btn btn-success" onclick="bulkPayment()">
                <i class="bi bi-check-all"></i> 선택 지급완료
            </button>
            <button type="button" class="btn btn-primary" onclick="exportExcel()">
                <i class="bi bi-file-earmark-excel"></i> 엑셀 다운로드
            </button>
        </div>
        <?php } ?>
        
        <!-- 페이징 -->
        <?php if($total_page > 1) { ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php
                $pagelist = get_paging(10, $page, $total_page, '?'.http_build_query($_GET).'&page=');
                echo $pagelist;
                ?>
            </ul>
        </nav>
        <?php } ?>
    </div>
</div>

<!-- 파일 보기 모달 -->
<div class="modal fade" id="fileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">업로드 파일</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fileModalBody">
                <!-- 파일 목록이 여기에 표시됩니다 -->
            </div>
        </div>
    </div>
</div>

<style>
/* 신청자 관리 페이지 */
.apply-list-page {
    padding: 20px 0;
    background: #f9fafb;
    min-height: calc(100vh - 200px);
}

.page-header {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

/* 검색 박스 */
.search-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

/* 통계 박스 */
.stats-box {
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 16px;
    height: 100%;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.stat-content h4 {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
}

.stat-content p {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

/* 테이블 */
.apply-list-table {
    background: white;
    border-radius: 12px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

.apply-list-table table {
    margin: 0;
}

.apply-list-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.user-info {
    line-height: 1.2;
}

.user-info small {
    display: block;
}

/* 일괄 처리 */
.bulk-actions {
    display: flex;
    gap: 10px;
}

/* 반응형 */
@media (max-width: 768px) {
    .apply-list-table {
        font-size: 14px;
    }
    
    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }
}
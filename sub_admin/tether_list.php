<?php
/*
 * 파일명: tether_list.php
 * 위치: /sub_admin/tether_list.php
 * 기능: 테더 구매 신청 목록 관리
 * 작성일: 2025-01-26
 */

include_once('./_common.php');
include_once('./header.php');

// 권한 체크
if($member['mb_grade'] < 2) {
    alert('접근 권한이 없습니다.', G5_URL);
}

// 검색 조건
$sfl = isset($_GET['sfl']) ? $_GET['sfl'] : '';
$stx = isset($_GET['stx']) ? $_GET['stx'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sdate = isset($_GET['sdate']) ? $_GET['sdate'] : date('Y-m-d', strtotime('-7 days'));
$edate = isset($_GET['edate']) ? $_GET['edate'] : date('Y-m-d');

$sql_search = "";
if($stx) {
    $sql_search .= " AND ";
    switch($sfl) {
        case 'mb_id':
            $sql_search .= " tp.mb_id LIKE '%{$stx}%' ";
            break;
        case 'tp_name':
            $sql_search .= " tp.tp_name LIKE '%{$stx}%' ";
            break;
        case 'tp_hp':
            $sql_search .= " tp.tp_hp LIKE '%{$stx}%' ";
            break;
        case 'tp_wallet_address':
            $sql_search .= " tp.tp_wallet_address LIKE '%{$stx}%' ";
            break;
        default:
            $sql_search .= " (tp.mb_id LIKE '%{$stx}%' OR tp.tp_name LIKE '%{$stx}%' OR tp.tp_hp LIKE '%{$stx}%') ";
            break;
    }
}

if($status !== '') {
    $sql_search .= " AND tp.tp_status = '{$status}' ";
}

if($sdate) {
    $sql_search .= " AND DATE(tp.tp_datetime) >= '{$sdate}' ";
}

if($edate) {
    $sql_search .= " AND DATE(tp.tp_datetime) <= '{$edate}' ";
}

// 페이지
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows = 20;
$from_record = ($page - 1) * $rows;

// 전체 카운트
$sql = "SELECT COUNT(*) as cnt 
        FROM g5_tether_purchase tp
        LEFT JOIN {$g5['member_table']} m ON tp.mb_id = m.mb_id
        WHERE 1 {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page = ceil($total_count / $rows);

// 목록 조회
$sql = "SELECT tp.*, m.mb_name, m.mb_nick, m.mb_email, m.mb_hp as member_hp
        FROM g5_tether_purchase tp
        LEFT JOIN {$g5['member_table']} m ON tp.mb_id = m.mb_id
        WHERE 1 {$sql_search}
        ORDER BY tp.tp_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

// 통계
$sql_stats = "SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN tp_status = '0' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN tp_status = '1' THEN 1 ELSE 0 END) as processing_count,
                SUM(CASE WHEN tp_status = '2' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN tp_status = '9' THEN 1 ELSE 0 END) as cancelled_count,
                SUM(tp_quantity) as total_quantity,
                SUM(tp_total_krw) as total_amount
              FROM g5_tether_purchase
              WHERE 1 {$sql_search}";
$stats = sql_fetch($sql_stats);
?>

<div class="sa-content">
    <!-- 통계 카드 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary text-white">
                <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo number_format($stats['pending_count']); ?></div>
                    <div class="stat-label">신청대기</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-info text-white">
                <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo number_format($stats['processing_count']); ?></div>
                    <div class="stat-label">진행중</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success text-white">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo number_format($stats['completed_count']); ?></div>
                    <div class="stat-label">완료</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-warning text-white">
                <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo number_format($stats['total_quantity'] ?? 0, 2); ?></div>
                    <div class="stat-label">총 USDT</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 검색 폼 -->
    <div class="sa-card mb-4">
        <div class="card-body">
            <form method="get" class="search-form">
                <div class="row g-3">
                    <div class="col-md-2">
                        <select name="sfl" class="form-select">
                            <option value="">전체검색</option>
                            <option value="mb_id" <?php echo $sfl == 'mb_id' ? 'selected' : ''; ?>>회원ID</option>
                            <option value="tp_name" <?php echo $sfl == 'tp_name' ? 'selected' : ''; ?>>신청자명</option>
                            <option value="tp_hp" <?php echo $sfl == 'tp_hp' ? 'selected' : ''; ?>>연락처</option>
                            <option value="tp_wallet_address" <?php echo $sfl == 'tp_wallet_address' ? 'selected' : ''; ?>>지갑주소</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="stx" value="<?php echo $stx; ?>" class="form-control" placeholder="검색어">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">전체상태</option>
                            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>신청완료</option>
                            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>진행중</option>
                            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>완료</option>
                            <option value="9" <?php echo $status === '9' ? 'selected' : ''; ?>>취소</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="sdate" value="<?php echo $sdate; ?>" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="edate" value="<?php echo $edate; ?>" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> 검색
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 신청 목록 -->
    <div class="sa-card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul"></i> 테더 구매 신청 목록
                <span class="badge bg-secondary ms-2"><?php echo number_format($total_count); ?>건</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th width="100">상태</th>
                            <th>신청자</th>
                            <th>연락처</th>
                            <th>송금업체</th>
                            <th>수량</th>
                            <th>단가</th>
                            <th>총액</th>
                            <th>신청일시</th>
                            <th width="120">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 0;
                        while($row = sql_fetch_array($result)) { 
                            $bg = ($i % 2) ? 'bg-light' : '';
                            
                            $status_class = '';
                            $status_text = '';
                            switch($row['tp_status']) {
                                case '0':
                                    $status_class = 'warning';
                                    $status_text = '신청완료';
                                    break;
                                case '1':
                                    $status_class = 'info';
                                    $status_text = '진행중';
                                    break;
                                case '2':
                                    $status_class = 'success';
                                    $status_text = '완료';
                                    break;
                                case '9':
                                    $status_class = 'danger';
                                    $status_text = '취소';
                                    break;
                            }
                            
                            // 지갑주소 마스킹
                            $masked_wallet = strlen($row['tp_wallet_address']) > 20 
                                ? substr($row['tp_wallet_address'], 0, 10) . '...' . substr($row['tp_wallet_address'], -6)
                                : $row['tp_wallet_address'];
                        ?>
                        <tr class="<?php echo $bg; ?>">
                            <td class="text-center"><?php echo $row['tp_id']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td>
                                <?php echo $row['tp_name']; ?>
                                <small class="text-muted d-block"><?php echo $row['mb_id']; ?></small>
                            </td>
                            <td><?php echo $row['tp_hp']; ?></td>
                            <td><?php echo $row['tp_transfer_company']; ?></td>
                            <td class="text-end"><?php echo number_format($row['tp_quantity'], 2); ?> USDT</td>
                            <td class="text-end">₩<?php echo number_format($row['tp_price_krw']); ?></td>
                            <td class="text-end fw-bold">₩<?php echo number_format($row['tp_total_krw']); ?></td>
                            <td class="text-center">
                                <?php echo date('m-d H:i', strtotime($row['tp_datetime'])); ?>
                            </td>
                            <td class="text-center">
                                <a href="./tether_view.php?tp_id=<?php echo $row['tp_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> 상세
                                </a>
                            </td>
                        </tr>
                        <?php 
                            $i++;
                        } 
                        
                        if($i == 0) {
                            echo '<tr><td colspan="10" class="text-center py-5 text-muted">신청 내역이 없습니다.</td></tr>';
                        }
                        ?>
                    </tbody>
                    <?php if($total_count > 0) { ?>
                    <tfoot>
                        <tr class="table-secondary fw-bold">
                            <td colspan="5" class="text-end">합계</td>
                            <td class="text-end"><?php echo number_format($stats['total_quantity'] ?? 0, 2); ?> USDT</td>
                            <td></td>
                            <td class="text-end">₩<?php echo number_format($stats['total_amount'] ?? 0); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>

    <!-- 페이징 -->
    <?php if($total_page > 1) { ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php
            $start_page = max(1, $page - 5);
            $end_page = min($total_page, $page + 5);
            
            if($page > 1) {
            ?>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page-1])); ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <?php } ?>
            
            <?php for($i = $start_page; $i <= $end_page; $i++) { ?>
            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php } ?>
            
            <?php if($page < $total_page) { ?>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page+1])); ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
    <?php } ?>
</div>

<style>
/* 통계 카드 */
.stat-card {
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    height: 100%;
}

.stat-icon {
    font-size: 40px;
    opacity: 0.8;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
    margin-top: 5px;
}

/* 검색 폼 */
.search-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

/* 테이블 */
.table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    font-size: 14px;
}

/* 반응형 */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 15px;
    }
    
    .table {
        font-size: 12px;
    }
    
    .btn-sm {
        font-size: 12px;
        padding: 0.25rem 0.5rem;
    }
}
</style>

<?php
include_once('./footer.php');
?>
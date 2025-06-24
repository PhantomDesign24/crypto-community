<?php
/*
 * 파일명: consultation_list.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 상담신청 목록 확인
 * 작성일: 2025-01-23
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// ===================================
// 페이지 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '상담신청 관리';

/* 페이지당 목록 수 */
$rows = 20;

/* 페이지 번호 */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

/* 검색 조건 */
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';
$sfl = isset($_GET['sfl']) ? trim($_GET['sfl']) : '';
$sst = isset($_GET['sst']) ? trim($_GET['sst']) : 'cs_datetime';
$sod = isset($_GET['sod']) ? trim($_GET['sod']) : 'desc';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// 헤더 포함 (권한 체크 포함)
include_once('./header.php');

// ===================================
// 상담신청 목록 조회
// ===================================

/* 검색 SQL */
$sql_search = " WHERE 1=1 ";

// 최고관리자가 아닌 경우 자신의 하위 회원의 상담신청만 조회
if (!$is_admin) {
    // 하위 회원 ID 목록 가져오기
    $sub_members = array();
    $sql = "SELECT mb_id FROM {$g5['member_table']} WHERE mb_recommend = '{$member['mb_id']}'";
    $result = sql_query($sql);
    while($row = sql_fetch_array($result)) {
        $sub_members[] = "'".$row['mb_id']."'";
    }
    
    if (count($sub_members) > 0) {
        $sql_search .= " AND mb_id IN (".implode(',', $sub_members).")";
    } else {
        $sql_search .= " AND 1=0 "; // 하위 회원이 없으면 조회 안됨
    }
}

// 상태 필터
if ($status !== '') {
    $sql_search .= " AND cs_status = '{$status}' ";
}

// 검색어
if ($stx) {
    switch ($sfl) {
        case 'cs_name':
            $sql_search .= " AND cs_name LIKE '%{$stx}%' ";
            break;
        case 'cs_hp':
            $sql_search .= " AND cs_hp LIKE '%{$stx}%' ";
            break;
        case 'cs_subject':
            $sql_search .= " AND cs_subject LIKE '%{$stx}%' ";
            break;
        case 'cs_content':
            $sql_search .= " AND cs_content LIKE '%{$stx}%' ";
            break;
        default:
            $sql_search .= " AND (cs_name LIKE '%{$stx}%' OR cs_hp LIKE '%{$stx}%' OR cs_subject LIKE '%{$stx}%') ";
    }
}

/* 전체 건수 */
$sql = "SELECT COUNT(*) as cnt FROM g5_consultation {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

/* 페이지 계산 */
$total_page = ceil($total_count / $rows);
$from_record = ($page - 1) * $rows;

/* 정렬 */
$sql_order = " ORDER BY {$sst} {$sod} ";

/* 상담신청 목록 */
$sql = "SELECT * FROM g5_consultation 
        {$sql_search}
        {$sql_order}
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

// 상태별 건수
$status_count = array();
$sql = "SELECT cs_status, COUNT(*) as cnt FROM g5_consultation {$sql_search} GROUP BY cs_status";
$status_result = sql_query($sql);
while($row = sql_fetch_array($status_result)) {
    $status_count[$row['cs_status']] = $row['cnt'];
}
?>

<style>
/* 상담신청 목록 스타일 */
.consultation-list-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* 상태 탭 */
.status-tabs {
    display: flex;
    gap: 8px;
    padding: 20px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    flex-wrap: wrap;
}

.status-tab {
    padding: 8px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
    position: relative;
}

.status-tab:hover {
    background: #f3f4f6;
    color: #374151;
}

.status-tab.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.status-count {
    display: inline-block;
    padding: 2px 6px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    font-size: 12px;
    margin-left: 4px;
}

/* 검색 영역 */
.search-area {
    padding: 20px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.search-form {
    display: flex;
    gap: 8px;
    align-items: center;
}

.search-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    color: #374151;
}

.search-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.search-button {
    padding: 8px 16px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
}

.search-button:hover {
    background: #2563eb;
}

/* 테이블 스타일 */
.consultation-table {
    width: 100%;
}

.consultation-table th {
    padding: 12px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 500;
    font-size: 14px;
    color: #374151;
    text-align: left;
}

.consultation-table td {
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #1f2937;
}

.consultation-table tr:hover td {
    background: #f9fafb;
}

/* 상태 뱃지 */
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.status-접수 {
    background: #fee2e2;
    color: #dc2626;
}

.status-badge.status-진행중 {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.status-완료 {
    background: #dbeafe;
    color: #2563eb;
}

/* 제목 링크 */
.subject-link {
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
}

.subject-link:hover {
    color: #3b82f6;
    text-decoration: underline;
}

/* 페이지네이션 */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    padding: 20px;
}

.pagination a,
.pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    color: #6b7280;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.pagination a:hover {
    background: #f3f4f6;
    color: #374151;
}

.pagination .current {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 데이터 없음 */
.no-data {
    padding: 60px 20px;
    text-align: center;
    color: #9ca3af;
}

.no-data i {
    font-size: 48px;
    margin-bottom: 16px;
}

/* 반응형 */
@media (max-width: 768px) {
    .consultation-table {
        font-size: 12px;
    }
    
    .consultation-table th,
    .consultation-table td {
        padding: 8px;
    }
    
    .hide-mobile {
        display: none;
    }
    
    .search-form {
        flex-wrap: wrap;
    }
    
    .search-input {
        width: 100%;
    }
}
</style>

<!-- 상담신청 목록 -->
<div class="consultation-list-container">
    <!-- 상태 탭 -->
    <div class="status-tabs">
        <a href="?status=" class="status-tab <?php echo $status === '' ? 'active' : ''; ?>">
            전체 <span class="status-count"><?php echo number_format($total_count); ?></span>
        </a>
        <a href="?status=접수" class="status-tab <?php echo $status === '접수' ? 'active' : ''; ?>">
            접수 <span class="status-count"><?php echo number_format($status_count['접수'] ?? 0); ?></span>
        </a>
        <a href="?status=진행중" class="status-tab <?php echo $status === '진행중' ? 'active' : ''; ?>">
            진행중 <span class="status-count"><?php echo number_format($status_count['진행중'] ?? 0); ?></span>
        </a>
        <a href="?status=완료" class="status-tab <?php echo $status === '완료' ? 'active' : ''; ?>">
            완료 <span class="status-count"><?php echo number_format($status_count['완료'] ?? 0); ?></span>
        </a>
    </div>
    
    <!-- 검색 영역 -->
    <div class="search-area">
        <form method="get" class="search-form">
            <input type="hidden" name="status" value="<?php echo $status; ?>">
            <select name="sfl" class="search-select">
                <option value="">전체</option>
                <option value="cs_name" <?php echo $sfl == 'cs_name' ? 'selected' : ''; ?>>이름</option>
                <option value="cs_hp" <?php echo $sfl == 'cs_hp' ? 'selected' : ''; ?>>연락처</option>
                <option value="cs_subject" <?php echo $sfl == 'cs_subject' ? 'selected' : ''; ?>>제목</option>
                <option value="cs_content" <?php echo $sfl == 'cs_content' ? 'selected' : ''; ?>>내용</option>
            </select>
            <input type="text" name="stx" value="<?php echo $stx; ?>" class="search-input" placeholder="검색어를 입력하세요">
            <button type="submit" class="search-button">
                <i class="bi bi-search"></i> 검색
            </button>
        </form>
    </div>
    
    <!-- 목록 테이블 -->
    <table class="consultation-table">
        <thead>
            <tr>
                <th>번호</th>
                <th>이름</th>
                <th class="hide-mobile">연락처</th>
                <th>제목</th>
                <th>상태</th>
                <th class="hide-mobile">신청일</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $num = $total_count - (($page - 1) * $rows);
            for ($i=0; $row=sql_fetch_array($result); $i++) { 
                // 회원 정보 가져오기
                $mb_info = '';
                if ($row['mb_id']) {
                    $mb = get_member($row['mb_id']);
                    if ($mb['mb_recommend'] == $member['mb_id']) {
                        $mb_info = ' ('.$mb['mb_id'].')';
                    }
                }
            ?>
            <tr>
                <td><?php echo $num--; ?></td>
                <td><?php echo get_text($row['cs_name']).$mb_info; ?></td>
                <td class="hide-mobile"><?php echo get_text($row['cs_hp']); ?></td>
                <td>
                    <a href="./consultation_view.php?cs_id=<?php echo $row['cs_id']; ?>" class="subject-link">
                        <?php echo get_text($row['cs_subject']); ?>
                    </a>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $row['cs_status']; ?>">
                        <?php echo $row['cs_status']; ?>
                    </span>
                </td>
                <td class="hide-mobile"><?php echo substr($row['cs_datetime'], 0, 10); ?></td>
            </tr>
            <?php } ?>
            
            <?php if ($i == 0) { ?>
            <tr>
                <td colspan="6" class="no-data">
                    <i class="bi bi-inbox"></i>
                    <p>등록된 상담신청이 없습니다.</p>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <!-- 페이지네이션 -->
    <?php if ($total_page > 1) { ?>
    <div class="pagination">
        <?php echo get_paging(10, $page, $total_page, '?status='.$status.'&amp;sfl='.$sfl.'&amp;stx='.$stx.'&amp;page='); ?>
    </div>
    <?php } ?>
</div>

<?php
include_once('./footer.php');
?>
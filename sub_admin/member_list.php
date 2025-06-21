<?php
/*
 * 파일명: member_list.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 하위 회원 목록
 * 작성일: 2025-01-23
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// ===================================
// 페이지 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '회원 목록';

/* 페이지당 목록 수 */
$rows = 20;

/* 페이지 번호 */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

/* 검색 조건 */
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';
$sfl = isset($_GET['sfl']) ? trim($_GET['sfl']) : '';

// 헤더 포함 (권한 체크 포함)
include_once('./header.php');

// ===================================
// 회원 목록 조회
// ===================================

/* 검색 SQL */
$sql_search = " WHERE mb_recommend = '{$member['mb_id']}' ";

if ($stx) {
    switch ($sfl) {
        case 'mb_id':
            $sql_search .= " AND mb_id LIKE '%{$stx}%' ";
            break;
        case 'mb_name':
            $sql_search .= " AND mb_name LIKE '%{$stx}%' ";
            break;
        case 'mb_nick':
            $sql_search .= " AND mb_nick LIKE '%{$stx}%' ";
            break;
        case 'mb_email':
            $sql_search .= " AND mb_email LIKE '%{$stx}%' ";
            break;
        case 'mb_hp':
            $sql_search .= " AND mb_hp LIKE '%{$stx}%' ";
            break;
        default:
            $sql_search .= " AND (mb_id LIKE '%{$stx}%' OR mb_name LIKE '%{$stx}%' OR mb_nick LIKE '%{$stx}%') ";
    }
}

/* 전체 회원 수 */
$sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

/* 페이지 계산 */
$total_page = ceil($total_count / $rows);
$from_record = ($page - 1) * $rows;

/* 회원 목록 */
$sql = "SELECT * FROM {$g5['member_table']} 
        {$sql_search}
        ORDER BY mb_datetime DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);
?>

<style>
/* 회원 목록 스타일 */
.member-list-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* 검색 영역 */
.search-area {
    padding: 20px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.search-form {
    display: flex;
    gap: 12px;
    align-items: center;
    max-width: 500px;
}

.search-select {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
}

.search-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.btn-search {
    padding: 10px 20px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-search:hover {
    background: #2563eb;
}

/* 테이블 */
.member-table {
    width: 100%;
    border-collapse: collapse;
}

.member-table th {
    background: #f9fafb;
    padding: 12px;
    font-size: 14px;
    font-weight: 600;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.member-table td {
    padding: 12px;
    font-size: 14px;
    border-bottom: 1px solid #f3f4f6;
}

.member-table tr:hover {
    background: #f9fafb;
}

/* 회원 정보 */
.member-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.member-avatar {
    width: 36px;
    height: 36px;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.member-details h4 {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
}

.member-details p {
    font-size: 12px;
    color: #6b7280;
}

/* 등급 뱃지 */
.grade-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.grade-1 {
    background: #f3f4f6;
    color: #4b5563;
}

.grade-2 {
    background: #dbeafe;
    color: #1e40af;
}

.grade-3 {
    background: #ede9fe;
    color: #5b21b6;
}

/* 액션 버튼 */
.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 6px;
    font-size: 12px;
    color: #374151;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-action:hover {
    background: #f9fafb;
}

.btn-view {
    color: #3b82f6;
    border-color: #bfdbfe;
}

.btn-edit {
    color: #10b981;
    border-color: #a7f3d0;
}

/* 페이지네이션 */
.pagination {
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
}

.page-link:hover {
    background: #f9fafb;
}

.page-link.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 결과 없음 */
.no-result {
    padding: 60px 20px;
    text-align: center;
    color: #6b7280;
}
</style>

<!-- 회원 목록 -->
<div class="member-list-container">
    <!-- 검색 영역 -->
    <div class="search-area">
        <form method="get" action="" class="search-form">
            <select name="sfl" class="search-select">
                <option value="">전체</option>
                <option value="mb_id" <?php echo ($sfl == 'mb_id') ? 'selected' : ''; ?>>아이디</option>
                <option value="mb_name" <?php echo ($sfl == 'mb_name') ? 'selected' : ''; ?>>이름</option>
                <option value="mb_nick" <?php echo ($sfl == 'mb_nick') ? 'selected' : ''; ?>>닉네임</option>
                <option value="mb_email" <?php echo ($sfl == 'mb_email') ? 'selected' : ''; ?>>이메일</option>
                <option value="mb_hp" <?php echo ($sfl == 'mb_hp') ? 'selected' : ''; ?>>휴대폰</option>
            </select>
            <input type="text" name="stx" value="<?php echo $stx; ?>" class="search-input" placeholder="검색어를 입력하세요">
            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> 검색
            </button>
        </form>
    </div>
    
    <!-- 테이블 -->
    <?php if ($total_count > 0) { ?>
    <table class="member-table">
        <thead>
            <tr>
                <th>회원정보</th>
                <th>등급</th>
                <th>포인트</th>
                <th>가입일</th>
                <th>최근접속</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = sql_fetch_array($result)) { ?>
            <tr>
                <td>
                    <div class="member-info">
                        <div class="member-avatar">
                            <?php echo mb_substr($row['mb_name'], 0, 1); ?>
                        </div>
                        <div class="member-details">
                            <h4><?php echo get_text($row['mb_name']); ?> (<?php echo $row['mb_id']; ?>)</h4>
                            <p><?php echo $row['mb_email']; ?></p>
                        </div>
                    </div>
                </td>
                <td>
                    <?php
                    $grade_class = 'grade-' . $row['mb_grade'];
                    $grade_text = '';
                    switch($row['mb_grade']) {
                        case 1: $grade_text = '일반'; break;
                        case 2: $grade_text = '파트너'; break;
                        case 3: $grade_text = '매니저'; break;
                    }
                    ?>
                    <span class="grade-badge <?php echo $grade_class; ?>"><?php echo $grade_text; ?></span>
                </td>
                <td><?php echo number_format($row['mb_point']); ?>P</td>
                <td><?php echo date('Y.m.d', strtotime($row['mb_datetime'])); ?></td>
                <td><?php echo $row['mb_today_login'] ? date('m/d H:i', strtotime($row['mb_today_login'])) : '-'; ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="./member_view.php?mb_id=<?php echo $row['mb_id']; ?>" class="btn-action btn-view">
                            <i class="bi bi-eye"></i> 보기
                        </a>
                        <a href="./member_edit.php?mb_id=<?php echo $row['mb_id']; ?>" class="btn-action btn-edit">
                            <i class="bi bi-pencil"></i> 수정
                        </a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <!-- 페이지네이션 -->
    <?php if ($total_page > 1) { ?>
    <div class="pagination">
        <?php
        $page_block = 10;
        $start_page = (ceil($page / $page_block) - 1) * $page_block + 1;
        $end_page = $start_page + $page_block - 1;
        if ($end_page > $total_page) $end_page = $total_page;
        
        if ($page > 1) {
            echo '<a href="?page=1&sfl='.$sfl.'&stx='.$stx.'" class="page-link"><i class="bi bi-chevron-double-left"></i></a>';
            echo '<a href="?page='.($page-1).'&sfl='.$sfl.'&stx='.$stx.'" class="page-link"><i class="bi bi-chevron-left"></i></a>';
        }
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a href="?page='.$i.'&sfl='.$sfl.'&stx='.$stx.'" class="page-link '.$active.'">'.$i.'</a>';
        }
        
        if ($page < $total_page) {
            echo '<a href="?page='.($page+1).'&sfl='.$sfl.'&stx='.$stx.'" class="page-link"><i class="bi bi-chevron-right"></i></a>';
            echo '<a href="?page='.$total_page.'&sfl='.$sfl.'&stx='.$stx.'" class="page-link"><i class="bi bi-chevron-double-right"></i></a>';
        }
        ?>
    </div>
    <?php } ?>
    
    <?php } else { ?>
    <div class="no-result">
        <i class="bi bi-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
        <p>등록된 회원이 없습니다.</p>
    </div>
    <?php } ?>
</div>

<?php
// 푸터 포함
include_once('./footer.php');
?>
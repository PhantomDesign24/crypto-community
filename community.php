<?php
/*
 * 파일명: community.php
 * 위치: /
 * 기능: 커뮤니티 페이지 (그누보드 게시판 스타일, 독립 운영)
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '커뮤니티';

/* 페이지 설정 */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rows = 15; // 한 페이지에 보여줄 게시글 수

/* 검색 설정 */
$sfl = isset($_GET['sfl']) ? trim($_GET['sfl']) : '';
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';
$sca = isset($_GET['sca']) ? trim($_GET['sca']) : '';

// ===================================
// 데이터베이스 테이블 생성 (최초 1회)
// ===================================

$sql = "CREATE TABLE IF NOT EXISTS g5_community (
    cm_id INT NOT NULL AUTO_INCREMENT,
    cm_category VARCHAR(50) NOT NULL DEFAULT '',
    cm_subject VARCHAR(255) NOT NULL DEFAULT '',
    cm_content TEXT NOT NULL,
    cm_name VARCHAR(50) NOT NULL DEFAULT '',
    cm_password VARCHAR(255) NOT NULL DEFAULT '',
    cm_datetime DATETIME NOT NULL,
    cm_ip VARCHAR(50) NOT NULL DEFAULT '',
    cm_hit INT NOT NULL DEFAULT '0',
    cm_comment INT NOT NULL DEFAULT '0',
    cm_is_notice TINYINT NOT NULL DEFAULT '0',
    mb_id VARCHAR(50) NOT NULL DEFAULT '',
    PRIMARY KEY (cm_id),
    KEY idx_category (cm_category),
    KEY idx_datetime (cm_datetime),
    KEY idx_notice (cm_is_notice)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

// 파일 업로드 테이블
$sql = "CREATE TABLE IF NOT EXISTS g5_community_file (
    cm_id INT NOT NULL,
    bf_no INT NOT NULL,
    bf_source VARCHAR(255) NOT NULL DEFAULT '',
    bf_file VARCHAR(255) NOT NULL DEFAULT '',
    bf_filesize INT NOT NULL DEFAULT '0',
    bf_download INT NOT NULL DEFAULT '0',
    bf_datetime DATETIME NOT NULL,
    PRIMARY KEY (cm_id, bf_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
sql_query($sql, FALSE);

// ===================================
// 글 삭제 처리
// ===================================

if(isset($_GET['mode']) && $_GET['mode'] == 'delete' && isset($_GET['cm_id'])) {
    $cm_id = (int)$_GET['cm_id'];
    
    // 게시글 정보 조회
    $sql = "SELECT * FROM g5_community WHERE cm_id = '$cm_id'";
    $post = sql_fetch($sql);
    
    if($post) {
        // 권한 확인 (관리자이거나 본인글)
        if($is_admin || ($is_member && $post['mb_id'] == $member['mb_id'])) {
            sql_query("DELETE FROM g5_community WHERE cm_id = '$cm_id'");
            alert('게시글이 삭제되었습니다.', './community.php?page='.$page);
        } else if(!$is_member && isset($_POST['del_password'])) {
            // 비회원 비밀번호 확인
            if(password_verify($_POST['del_password'], $post['cm_password'])) {
                sql_query("DELETE FROM g5_community WHERE cm_id = '$cm_id'");
                alert('게시글이 삭제되었습니다.', './community.php?page='.$page);
            } else {
                alert('비밀번호가 일치하지 않습니다.');
            }
        }
    }
}

include_once('./_head.php');

// ===================================
// 카테고리 설정 (전문적/신뢰성 있는 카테고리)
// ===================================

$categories = array(
    '공지사항' => array('icon' => 'bi-megaphone-fill', 'color' => '#dc2626'),
    '투자전략' => array('icon' => 'bi-graph-up-arrow', 'color' => '#2563eb'),
    '시장분석' => array('icon' => 'bi-bar-chart-line-fill', 'color' => '#7c3aed'),
    '프로젝트분석' => array('icon' => 'bi-search', 'color' => '#0891b2'),
    '규제/정책' => array('icon' => 'bi-shield-check', 'color' => '#059669'),
    '기술토론' => array('icon' => 'bi-cpu', 'color' => '#ea580c'),
    '질문답변' => array('icon' => 'bi-question-circle-fill', 'color' => '#6366f1')
);

// ===================================
// 게시글 목록 조회
// ===================================

/* 검색 SQL */
$sql_search = "";
if ($stx) {
    $sql_search .= " AND ( ";
    switch ($sfl) {
        case 'subject':
            $sql_search .= " cm_subject LIKE '%{$stx}%' ";
            break;
        case 'content':
            $sql_search .= " cm_content LIKE '%{$stx}%' ";
            break;
        case 'name':
            $sql_search .= " cm_name LIKE '%{$stx}%' ";
            break;
        default:
            $sql_search .= " (cm_subject LIKE '%{$stx}%' OR cm_content LIKE '%{$stx}%') ";
    }
    $sql_search .= " ) ";
}

/* 카테고리 필터 */
if($sca) {
    $sql_search .= " AND cm_category = '".sql_real_escape_string($sca)."' ";
}

/* 전체 게시글 수 */
$sql = "SELECT COUNT(*) as cnt FROM g5_community WHERE 1=1 {$sql_search}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page = ceil($total_count / $rows);

/* 시작 위치 */
$from_record = ($page - 1) * $rows;

/* 게시글 목록 가져오기 */
$sql = "SELECT * FROM g5_community 
        WHERE 1=1 {$sql_search}
        ORDER BY cm_is_notice DESC, cm_id DESC
        LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

$list = array();
$num = $total_count - (($page - 1) * $rows);

while($row = sql_fetch_array($result)) {
    $row['num'] = $row['cm_is_notice'] ? '공지' : $num--;
    $list[] = $row;
}
?>

<style>
/* 게시판 전체 */
#bo_list_wrap {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

/* 게시판 제목 */
#bo_list_title {
    margin-bottom: 30px;
    text-align: center;
}

#bo_list_title h2 {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
}

/* 카테고리 */
#bo_cate {
    margin-bottom: 20px;
	margin-top:20px;
    background: #f9fafb;
    padding: 15px;
    border-radius: 8px;
}

#bo_cate ul {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
}

#bo_cate li {
    list-style: none;
}

#bo_cate a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: white;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

#bo_cate a i {
    font-size: 14px;
}

#bo_cate a:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#bo_cate a.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 게시판 정보 */
#bo_list_info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 0 5px;
}

.bo_info_left {
    font-size: 14px;
    color: #6b7280;
}

.bo_info_right {
    display: flex;
    gap: 10px;
}

/* 검색 */
#bo_search {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

#bo_search select {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
}

#bo_search input {
    padding: 6px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
    width: 200px;
}

#bo_search button {
    padding: 6px 12px;
    background: #374151;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

/* 버튼 */
.btn_bo_user {
    display: flex;
    gap: 5px;
}

.btn_b02 {
    padding: 6px 12px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
}

.btn_b01 {
    padding: 6px 12px;
    background: #6b7280;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
}

/* 게시판 목록 */
#bo_list {
    border-top: 2px solid #374151;
}

#bo_list table {
    width: 100%;
    border-collapse: collapse;
}

#bo_list th {
    padding: 15px 5px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 500;
    font-size: 14px;
    color: #374151;
}

#bo_list td {
    padding: 15px 5px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #1f2937;
}

#bo_list tr:hover td {
    background: #f9fafb;
}

/* 번호 */
.td_num {
    width: 60px;
    text-align: center;
}

/* 분류 */
.td_cate {
    width: 100px;
    text-align: center;
}

.bo_cate_link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #f3f4f6;
    color: #374151;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
}

.bo_cate_link i {
    font-size: 11px;
}

/* 제목 */
.td_subject {
    text-align: left;
    padding-left: 10px !important;
}

.bo_tit {
    display: inline-block;
}

.bo_tit a {
    color: #1f2937;
    text-decoration: none;
}

.bo_tit a:hover {
    color: #3b82f6;
    text-decoration: underline;
}

/* 댓글 수 */
.cnt_cmt {
    display: inline-block;
    margin-left: 5px;
    color: #ef4444;
    font-size: 12px;
    font-weight: 600;
}

/* 공지사항 */
.notice {
    background: #fef3c7 !important;
}

.notice td {
    font-weight: 500;
}

.notice .td_num {
    color: #f59e0b;
    font-weight: 600;
}

/* 이름 */
.td_name {
    width: 100px;
    text-align: center;
}

/* 날짜 */
.td_date {
    width: 80px;
    text-align: center;
    color: #6b7280;
}

/* 조회 */
.td_num2 {
    width: 60px;
    text-align: center;
    color: #6b7280;
}

/* 관리 */
.td_mng {
    width: 80px;
    text-align: center;
}

.td_mng a {
    color: #6b7280;
    font-size: 12px;
    text-decoration: none;
}

.td_mng a:hover {
    color: #ef4444;
}

/* 페이지네이션 */
.pg_wrap {
    margin-top: 30px;
    text-align: center;
}

.pg {
    display: inline-flex;
    gap: 4px;
}

.pg a,
.pg strong {
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

.pg a:hover {
    background: #f3f4f6;
    color: #374151;
}

.pg strong {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    font-weight: 500;
}

.pg_page {
    font-weight: 400;
}

/* 삭제 확인 모달 */
.delete-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.delete-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
}

/* 반응형 */
@media (max-width: 768px) {
    #bo_list_wrap {
        padding: 0 15px;
    }
    
    #bo_list_info {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    #bo_search input {
        width: 120px;
    }
    
    .td_cate,
    .td_name,
    .td_date {
        display: none;
    }
    
    #bo_list th:nth-child(2),
    #bo_list th:nth-child(4),
    #bo_list th:nth-child(5) {
        display: none;
    }
    
    .td_subject {
        padding-left: 5px !important;
    }
    
    .td_num,
    .td_num2 {
        width: 50px;
        font-size: 12px;
    }
}
</style>

<div id="bo_list_wrap">
    <!-- 게시판 제목 -->
    <div id="bo_list_title">
        <h2><i class="bi bi-people"></i> 커뮤니티</h2>
        <p>암호화폐 투자자들이 모여 정보를 공유하고 소통하는 공간입니다</p>
    </div>
    <?php include_once(G5_PATH.'/coin.php');?>
    <!-- 카테고리 -->
    <nav id="bo_cate">
        <ul>
            <li>
                <a href="?sca=" class="<?php echo !$sca ? 'active' : ''; ?>">
                    <i class="bi bi-grid"></i> 전체
                </a>
            </li>
            <?php foreach($categories as $cate_name => $cate_info) { ?>
            <li>
                <a href="?sca=<?php echo urlencode($cate_name); ?>" 
                   class="<?php echo $sca == $cate_name ? 'active' : ''; ?>"
                   style="<?php echo $sca == $cate_name ? 'background: '.$cate_info['color'].'; border-color: '.$cate_info['color'].';' : ''; ?>">
                    <i class="bi <?php echo $cate_info['icon']; ?>"></i> <?php echo $cate_name; ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </nav>
    
    <!-- 게시판 정보 -->
    <div id="bo_list_info">
        <div class="bo_info_left">
            <span>Total <?php echo number_format($total_count); ?>건</span>
            <span><?php echo $page; ?> 페이지</span>
        </div>
        <div class="bo_info_right">
            <!-- 검색 -->
            <form id="bo_search" method="get">
                <input type="hidden" name="sca" value="<?php echo $sca; ?>">
                <select name="sfl">
                    <option value="subject" <?php echo $sfl == 'subject' ? 'selected' : ''; ?>>제목</option>
                    <option value="content" <?php echo $sfl == 'content' ? 'selected' : ''; ?>>내용</option>
                    <option value="name" <?php echo $sfl == 'name' ? 'selected' : ''; ?>>글쓴이</option>
                </select>
                <input type="text" name="stx" value="<?php echo $stx; ?>" placeholder="검색어">
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>
            
            <!-- 버튼 -->
            <div class="btn_bo_user">
                <a href="./community_write.php" class="btn_b02">
                    <i class="bi bi-pencil"></i> 글쓰기
                </a>
            </div>
        </div>
    </div>
    
    <!-- 게시판 목록 -->
    <div id="bo_list">
        <table>
            <thead>
                <tr>
                    <th scope="col" class="td_num">번호</th>
                    <th scope="col" class="td_cate">분류</th>
                    <th scope="col">제목</th>
                    <th scope="col" class="td_name">글쓴이</th>
                    <th scope="col" class="td_date">날짜</th>
                    <th scope="col" class="td_num2">조회</th>
                    <?php if($is_admin) { ?>
                    <th scope="col" class="td_mng">관리</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($list as $row) { 
                    $category_info = isset($categories[$row['cm_category']]) ? $categories[$row['cm_category']] : array('icon' => 'bi-folder', 'color' => '#6b7280');
                ?>
                <tr class="<?php echo $row['cm_is_notice'] ? 'notice' : ''; ?>">
                    <td class="td_num">
                        <?php echo $row['num']; ?>
                    </td>
                    <td class="td_cate">
                        <a href="?sca=<?php echo urlencode($row['cm_category']); ?>" class="bo_cate_link">
                            <i class="bi <?php echo $category_info['icon']; ?>" style="color: <?php echo $category_info['color']; ?>;"></i>
                            <?php echo $row['cm_category']; ?>
                        </a>
                    </td>
                    <td class="td_subject">
                        <div class="bo_tit">
                            <a href="./community_view.php?cm_id=<?php echo $row['cm_id']; ?>&page=<?php echo $page; ?>">
                                <?php echo get_text($row['cm_subject']); ?>
                            </a>
                            <?php if($row['cm_comment'] > 0) { ?>
                            <span class="cnt_cmt">[<?php echo $row['cm_comment']; ?>]</span>
                            <?php } ?>
                        </div>
                    </td>
                    <td class="td_name"><?php echo get_text($row['cm_name']); ?></td>
                    <td class="td_date"><?php echo substr($row['cm_datetime'], 5, 5); ?></td>
                    <td class="td_num2"><?php echo number_format($row['cm_hit']); ?></td>
                    <?php if($is_admin) { ?>
                    <td class="td_mng">
                        <a href="./community_write.php?mode=edit&cm_id=<?php echo $row['cm_id']; ?>&page=<?php echo $page; ?>">수정</a>
                        <a href="javascript:confirmDelete(<?php echo $row['cm_id']; ?>)">삭제</a>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
                
                <?php if(count($list) == 0) { ?>
                <tr>
                    <td colspan="<?php echo $is_admin ? '7' : '6'; ?>" class="empty_table">
                        <i class="bi bi-inbox"></i>
                        <p>게시글이 없습니다.</p>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- 페이지네이션 -->
    <div class="pg_wrap">
        <span class="pg">
            <?php if($page > 1) { ?>
            <a href="?page=1<?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_start">처음</a>
            <a href="?page=<?php echo $page-1; ?><?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_prev">이전</a>
            <?php } ?>
            
            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_page, $page + 2);
            
            for($i = $start_page; $i <= $end_page; $i++) {
                if($i == $page) {
                    echo '<strong class="pg_current">'.$i.'</strong>';
                } else {
                    echo '<a href="?page='.$i.($sca ? '&sca='.$sca : '').($stx ? '&sfl='.$sfl.'&stx='.$stx : '').'" class="pg_page">'.$i.'</a>';
                }
            }
            ?>
            
            <?php if($page < $total_page) { ?>
            <a href="?page=<?php echo $page+1; ?><?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_next">다음</a>
            <a href="?page=<?php echo $total_page; ?><?php echo $sca ? '&sca='.$sca : ''; ?><?php echo $stx ? '&sfl='.$sfl.'&stx='.$stx : ''; ?>" class="pg_page pg_end">맨끝</a>
            <?php } ?>
        </span>
    </div>
</div>

<script>
function confirmDelete(cm_id) {
    if(confirm('정말 삭제하시겠습니까?')) {
        location.href = '?mode=delete&cm_id=' + cm_id + '&page=<?php echo $page; ?>';
    }
}
</script>

<?php
include_once('./_tail.php');
?>
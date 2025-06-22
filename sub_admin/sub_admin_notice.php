<?php
/*
 * 파일명: sub_admin_notice.php
 * 위치: /sub_admin/sub_admin_notice.php
 * 기능: 하부조직용 공지사항 CRUD 페이지
 * 작성일: 2025-01-22
 */

include_once('./_common.php');

// 권한 체크
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

// 하부조직 권한 체크 (2등급 이상은 읽기 가능)
if ($member['mb_grade'] < 2) {
    alert('접근 권한이 없습니다.', G5_URL);
}

// 공통 헤더 파일 포함
include_once('./header.php');

// =================================== 
// 초기 설정
// ===================================

/* 변수 초기화 */
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'list';
$idx = isset($_REQUEST['idx']) ? (int)$_REQUEST['idx'] : 0;
$page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
$rows = 15; // 페이지당 게시물 수

// =================================== 
// 데이터 처리
// ===================================

/* 저장 처리 - 최고관리자만 가능 */
if ($mode == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$is_admin) {
        alert('글쓰기 권한이 없습니다.');
    }
    
    $subject = isset($_POST['subject']) ? strip_tags($_POST['subject']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $is_notice = isset($_POST['is_notice']) ? 1 : 0;
    
    if (!$subject) {
        alert('제목을 입력해주세요.');
    }
    
    // 파일 업로드 처리
    $upload_dir = G5_DATA_PATH.'/sub_notice';
    $upload_url = G5_DATA_URL.'/sub_notice';
    
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, G5_DIR_PERMISSION);
        @chmod($upload_dir, G5_DIR_PERMISSION);
    }
    
    $file_name = '';
    $file_path = '';
    
    if (isset($_FILES['attach_file']) && $_FILES['attach_file']['name']) {
        $file = $_FILES['attach_file'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // 허용 확장자
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'hwp');
        
        if (!in_array($file_ext, $allowed)) {
            alert('허용되지 않는 파일 형식입니다.');
        }
        
        // 파일 크기 제한 (10MB)
        if ($file['size'] > 10485760) {
            alert('파일 크기는 10MB를 초과할 수 없습니다.');
        }
        
        $file_name = md5(uniqid()) . '.' . $file_ext;
        $file_path = $upload_dir . '/' . $file_name;
        
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            alert('파일 업로드 실패');
        }
        
        // 기존 파일 삭제 (수정 시)
        if ($idx) {
            $old = sql_fetch("SELECT file_name FROM g5_sub_notice WHERE idx = '{$idx}'");
            if ($old['file_name'] && file_exists($upload_dir.'/'.$old['file_name'])) {
                @unlink($upload_dir.'/'.$old['file_name']);
            }
        }
    }
    
    if ($idx) {
        // 수정
        $sql = "UPDATE g5_sub_notice SET
                    subject = '{$subject}',
                    content = '{$content}',
                    is_notice = '{$is_notice}'";
        if ($file_name) {
            $sql .= ", file_name = '{$file_name}', 
                      file_original = '{$_FILES['attach_file']['name']}'";
        }
        $sql .= ", updated_at = '".G5_TIME_YMDHIS."'
                WHERE idx = '{$idx}'";
        sql_query($sql);
        $msg = '수정되었습니다.';
    } else {
        // 등록
        $sql = "INSERT INTO g5_sub_notice SET
                    subject = '{$subject}',
                    content = '{$content}',
                    is_notice = '{$is_notice}',
                    mb_id = '{$member['mb_id']}',
                    file_name = '{$file_name}',
                    file_original = '".(isset($_FILES['attach_file']['name']) ? $_FILES['attach_file']['name'] : '')."',
                    created_at = '".G5_TIME_YMDHIS."',
                    updated_at = '".G5_TIME_YMDHIS."'";
        sql_query($sql);
        $msg = '등록되었습니다.';
    }
    
    alert($msg, './sub_admin_notice.php');
}

/* 삭제 처리 - 최고관리자만 가능 */
if ($mode == 'delete' && $idx) {
    if (!$is_admin) {
        alert('삭제 권한이 없습니다.');
    }
    
    // 첨부파일 삭제
    $row = sql_fetch("SELECT file_name FROM g5_sub_notice WHERE idx = '{$idx}'");
    if ($row['file_name']) {
        $file_path = G5_DATA_PATH.'/sub_notice/'.$row['file_name'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
    
    $sql = "DELETE FROM g5_sub_notice WHERE idx = '{$idx}'";
    sql_query($sql);
    alert('삭제되었습니다.', './sub_admin_notice.php?page='.$page);
}

/* 수정 모드 데이터 조회 */
$notice = array();
if ($mode == 'write' && $idx) {
    $sql = "SELECT * FROM g5_sub_notice WHERE idx = '{$idx}'";
    $notice = sql_fetch($sql);
    if (!$notice) {
        alert('존재하지 않는 글입니다.');
    }
}

// =================================== 
// 목록 조회
// ===================================

/* 전체 게시물 수 */
$sql = "SELECT COUNT(*) as cnt FROM g5_sub_notice";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

/* 페이징 계산 */
$total_page = ceil($total_count / $rows);
$start = ($page - 1) * $rows;

/* 목록 조회 */
$sql = "SELECT * FROM g5_sub_notice
        ORDER BY is_notice DESC, idx DESC
        LIMIT {$start}, {$rows}";
$result = sql_query($sql);

?>

<!-- =================================== 
 * 하부조직 공지사항 페이지
 * =================================== -->

<style>
/* 공지사항 전용 스타일 */
.notice-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.notice-table th {
    background: #f9fafb;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e5e7eb;
}

.notice-table th:first-child {
    text-align: center;
}

.notice-table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}

.notice-table tr:last-child td {
    border-bottom: none;
}

.notice-table tr:hover td {
    background: #f9fafb;
}

/* 공지사항 행 */
.notice-row td {
    background-color: #fef3c7;
}

.notice-row:hover td {
    background-color: #fde68a !important;
}

/* 제목 링크 */
.notice-title {
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
}

.notice-title:hover {
    color: #3b82f6;
}

/* 뱃지 */
.notice-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    background: #ef4444;
    color: white;
}

/* 작성 폼 */
.write-form {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.editor-area {
    min-height: 400px;
    font-family: inherit;
    resize: vertical;
}

/* 버튼 */
.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* 파일 첨부 */
.file-info {
    margin-top: 8px;
    font-size: 13px;
    color: #6b7280;
}

.file-link {
    color: #3b82f6;
    text-decoration: none;
}

.file-link:hover {
    text-decoration: underline;
}

/* 상세보기 */
.view-header {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.view-title {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 12px;
}

.view-meta {
    display: flex;
    gap: 16px;
    font-size: 14px;
    color: #6b7280;
}

.view-content {
    background: white;
    border-radius: 12px;
    padding: 32px;
    min-height: 300px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    line-height: 1.8;
}

/* 페이징 */
.pagination {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-top: 30px;
}

.page-link {
    display: inline-block;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    color: #6b7280;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.2s;
}

.page-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.page-link.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* 빈 데이터 */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    color: #e5e7eb;
}

/* 반응형 */
@media (max-width: 768px) {
    .notice-table {
        font-size: 13px;
    }
    
    .notice-table th,
    .notice-table td {
        padding: 12px 8px;
    }
    
    .notice-table .mobile-hide {
        display: none;
    }
    
    .write-form {
        padding: 20px;
    }
    
    .btn-area {
        flex-direction: column;
        gap: 8px;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php if ($mode == 'list'): ?>

<!-- =================================== 
 * 목록 화면
 * =================================== -->

<div class="sa-page-header">
    <h1 class="sa-page-title">
        <i class="bi bi-megaphone"></i> 공지사항
        <?php if ($is_admin): ?>
        <a href="?mode=write" class="btn btn-primary">
            <i class="bi bi-pencil-fill"></i> 글쓰기
        </a>
        <?php endif; ?>
    </h1>
</div>

<table class="notice-table">
    <thead>
        <tr>
            <th width="80">번호</th>
            <th>제목</th>
            <th width="120" class="mobile-hide">작성자</th>
            <th width="120" class="mobile-hide">작성일</th>
            <th width="80" class="mobile-hide">조회</th>
            <?php if ($is_admin): ?>
            <th width="100">관리</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($total_count > 0) {
            $num = $total_count - $start;
            while ($row = sql_fetch_array($result)) {
                $row_class = $row['is_notice'] ? 'notice-row' : '';
        ?>
        <tr class="<?php echo $row_class; ?>">
            <td class="text-center">
                <?php echo $row['is_notice'] ? '<span class="notice-badge">공지</span>' : $num; ?>
            </td>
            <td>
                <a href="?mode=view&idx=<?php echo $row['idx']; ?>&page=<?php echo $page; ?>" class="notice-title">
                    <?php echo $row['subject']; ?>
                    <?php if ($row['file_name']): ?>
                    <i class="bi bi-paperclip" style="color: #6b7280;"></i>
                    <?php endif; ?>
                </a>
            </td>
            <td class="text-center mobile-hide"><?php echo $row['mb_id']; ?></td>
            <td class="text-center mobile-hide"><?php echo substr($row['created_at'], 0, 10); ?></td>
            <td class="text-center mobile-hide"><?php echo number_format($row['hit']); ?></td>
            <?php if ($is_admin): ?>
            <td class="text-center">
                <a href="?mode=write&idx=<?php echo $row['idx']; ?>&page=<?php echo $page; ?>" class="btn btn-sm btn-primary">수정</a>
                <a href="?mode=delete&idx=<?php echo $row['idx']; ?>&page=<?php echo $page; ?>" onclick="return confirm('삭제하시겠습니까?');" class="btn btn-sm btn-danger">삭제</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php
                $num--;
            }
        } else {
        ?>
        <tr>
            <td colspan="<?php echo $is_admin ? '6' : '5'; ?>" class="empty-state">
                <i class="bi bi-inbox"></i>
                등록된 공지사항이 없습니다.
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php if ($total_page > 1): ?>
<nav class="pagination">
    <?php
    $page_block = 10;
    $start_page = (floor(($page - 1) / $page_block) * $page_block) + 1;
    $end_page = min($start_page + $page_block - 1, $total_page);
    
    if ($start_page > 1) {
        echo '<a class="page-link" href="?page=1">처음</a>';
        echo '<a class="page-link" href="?page='.($start_page - 1).'">이전</a>';
    }
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo '<a class="page-link '.$active.'" href="?page='.$i.'">'.$i.'</a>';
    }
    
    if ($end_page < $total_page) {
        echo '<a class="page-link" href="?page='.($end_page + 1).'">다음</a>';
        echo '<a class="page-link" href="?page='.$total_page.'">마지막</a>';
    }
    ?>
</nav>
<?php endif; ?>

<?php elseif ($mode == 'write'): ?>

<!-- =================================== 
 * 글쓰기/수정 화면
 * =================================== -->

<?php if (!$is_admin): ?>
<script>
alert('글쓰기 권한이 없습니다.');
location.href = './sub_admin_notice.php';
</script>
<?php exit; endif; ?>

<div class="sa-page-header">
    <h1 class="sa-page-title">
        <i class="bi bi-pencil-square"></i> 공지사항 <?php echo $idx ? '수정' : '작성'; ?>
    </h1>
    <nav class="sa-breadcrumb">
        <a href="./index.php">대시보드</a>
        <i class="bi bi-chevron-right"></i>
        <a href="./sub_admin_notice.php">공지사항</a>
        <i class="bi bi-chevron-right"></i>
        <span><?php echo $idx ? '수정' : '작성'; ?></span>
    </nav>
</div>

<form method="post" action="?mode=save" enctype="multipart/form-data" class="write-form">
    <input type="hidden" name="idx" value="<?php echo $idx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    
    <div class="form-group">
        <label class="form-label">제목</label>
        <input type="text" name="subject" class="form-control" placeholder="제목을 입력하세요" value="<?php echo isset($notice['subject']) ? $notice['subject'] : ''; ?>" required>
    </div>
    
    <div class="form-group">
        <label class="form-label">내용</label>
        <textarea name="content" class="form-control editor-area" placeholder="내용을 입력하세요" required><?php echo isset($notice['content']) ? $notice['content'] : ''; ?></textarea>
    </div>
    
    <div class="form-group">
        <label class="form-label">첨부파일</label>
        <input type="file" name="attach_file" class="form-control">
        <div class="file-info">
            허용 파일: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, zip, hwp (최대 10MB)
        </div>
        <?php if ($idx && $notice['file_name']): ?>
        <div class="file-info">
            현재 파일: <a href="<?php echo G5_DATA_URL; ?>/sub_notice/<?php echo $notice['file_name']; ?>" class="file-link" download="<?php echo $notice['file_original']; ?>">
                <i class="bi bi-download"></i> <?php echo $notice['file_original']; ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label class="form-check">
            <input type="checkbox" name="is_notice" value="1" <?php echo (isset($notice['is_notice']) && $notice['is_notice']) ? 'checked' : ''; ?>>
            <span style="margin-left: 8px;">공지사항으로 등록</span>
        </label>
    </div>
    
    <div class="btn-area" style="display: flex; gap: 12px; justify-content: center; margin-top: 32px;">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> <?php echo $idx ? '수정' : '등록'; ?>
        </button>
        <a href="?page=<?php echo $page; ?>" class="btn btn-secondary">
            <i class="bi bi-list"></i> 목록
        </a>
    </div>
</form>

<?php elseif ($mode == 'view'): ?>

<!-- =================================== 
 * 상세보기 화면
 * =================================== -->

<?php
// 조회수 증가
sql_query("UPDATE g5_sub_notice SET hit = hit + 1 WHERE idx = '{$idx}'");

// 게시물 조회
$sql = "SELECT * FROM g5_sub_notice WHERE idx = '{$idx}'";
$view = sql_fetch($sql);

if (!$view) {
    alert('존재하지 않는 글입니다.');
}
?>

<div class="sa-page-header">
    <h1 class="sa-page-title">
        <i class="bi bi-file-text"></i> 공지사항 상세
    </h1>
    <nav class="sa-breadcrumb">
        <a href="./index.php">대시보드</a>
        <i class="bi bi-chevron-right"></i>
        <a href="./sub_admin_notice.php">공지사항</a>
        <i class="bi bi-chevron-right"></i>
        <span>상세보기</span>
    </nav>
</div>

<div class="view-header">
    <h2 class="view-title">
        <?php if ($view['is_notice']): ?>
        <span class="notice-badge">공지</span>
        <?php endif; ?>
        <?php echo $view['subject']; ?>
    </h2>
    <div class="view-meta">
        <span><i class="bi bi-person"></i> <?php echo $view['mb_id']; ?></span>
        <span><i class="bi bi-calendar"></i> <?php echo $view['created_at']; ?></span>
        <span><i class="bi bi-eye"></i> 조회 <?php echo number_format($view['hit']); ?></span>
    </div>
    <?php if ($view['file_name']): ?>
    <div style="margin-top: 16px;">
        <a href="<?php echo G5_DATA_URL; ?>/sub_notice/<?php echo $view['file_name']; ?>" class="file-link" download="<?php echo $view['file_original']; ?>">
            <i class="bi bi-paperclip"></i> <?php echo $view['file_original']; ?> <i class="bi bi-download"></i>
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="view-content">
    <?php echo nl2br($view['content']); ?>
</div>

<div class="btn-area" style="display: flex; gap: 12px; justify-content: center; margin-top: 32px;">
    <?php if ($is_admin): ?>
    <a href="?mode=write&idx=<?php echo $idx; ?>&page=<?php echo $page; ?>" class="btn btn-primary">
        <i class="bi bi-pencil"></i> 수정
    </a>
    <a href="?mode=delete&idx=<?php echo $idx; ?>&page=<?php echo $page; ?>" onclick="return confirm('삭제하시겠습니까?');" class="btn btn-danger">
        <i class="bi bi-trash"></i> 삭제
    </a>
    <?php endif; ?>
    <a href="?page=<?php echo $page; ?>" class="btn btn-secondary">
        <i class="bi bi-list"></i> 목록
    </a>
</div>

<?php endif; ?>

<?php
// 공통 푸터 파일 포함
include_once('./footer.php');
?>
<?php
/*
 * 파일명: community_view.php
 * 위치: /
 * 기능: 커뮤니티 글보기
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

$cm_id = isset($_GET['cm_id']) ? (int)$_GET['cm_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if(!$cm_id) {
    alert('잘못된 접근입니다.', './community.php');
}

// ===================================
// 게시글 조회
// ===================================

$sql = "SELECT * FROM g5_community WHERE cm_id = '$cm_id'";
$view = sql_fetch($sql);

if(!$view['cm_id']) {
    alert('존재하지 않는 게시글입니다.', './community.php');
}

// 조회수 증가
sql_query("UPDATE g5_community SET cm_hit = cm_hit + 1 WHERE cm_id = '$cm_id'");
$view['cm_hit']++;

/* 페이지 제목 */
$g5['title'] = $view['cm_subject'];

// ===================================
// 이전글, 다음글
// ===================================

$sql = "SELECT cm_id, cm_subject, cm_name, cm_datetime FROM g5_community 
        WHERE cm_id < '$cm_id' 
        ORDER BY cm_id DESC 
        LIMIT 1";
$prev = sql_fetch($sql);

$sql = "SELECT cm_id, cm_subject, cm_name, cm_datetime FROM g5_community 
        WHERE cm_id > '$cm_id' 
        ORDER BY cm_id ASC 
        LIMIT 1";
$next = sql_fetch($sql);

include_once('./_head.php');

// 카테고리 정보
$categories = array(
    '공지사항' => array('icon' => 'bi-megaphone-fill', 'color' => '#dc2626'),
    '투자전략' => array('icon' => 'bi-graph-up-arrow', 'color' => '#2563eb'),
    '시장분석' => array('icon' => 'bi-bar-chart-line-fill', 'color' => '#7c3aed'),
    '프로젝트분석' => array('icon' => 'bi-search', 'color' => '#0891b2'),
    '규제/정책' => array('icon' => 'bi-shield-check', 'color' => '#059669'),
    '기술토론' => array('icon' => 'bi-cpu', 'color' => '#ea580c'),
    '질문답변' => array('icon' => 'bi-question-circle-fill', 'color' => '#6366f1')
);

$category_info = isset($categories[$view['cm_category']]) ? $categories[$view['cm_category']] : array('icon' => 'bi-folder', 'color' => '#6b7280');

// 작성자 프로필 이미지 색상 (랜덤)
$profile_colors = array('#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#6366f1');
$profile_color = $profile_colors[ord(substr($view['cm_name'], 0, 1)) % count($profile_colors)];
?>

<style>
/* 글보기 컨테이너 */
.view-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 글 헤더 */
.view-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
	margin-top:20px;
}

.view-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
    transform: rotate(45deg);
}

.view-category {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: white;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    position: relative;
    z-index: 1;
}

.view-category i {
    font-size: 16px;
}

.view-subject {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 24px;
    line-height: 1.4;
    position: relative;
    z-index: 1;
}

.view-meta {
    display: flex;
    align-items: center;
    gap: 30px;
    position: relative;
    z-index: 1;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: <?php echo $profile_color; ?>;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.author-details {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.post-date {
    font-size: 14px;
    color: #6b7280;
}

.view-stats {
    display: flex;
    gap: 20px;
    margin-left: auto;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #6b7280;
}

.stat-item i {
    font-size: 16px;
    color: #9ca3af;
}

/* 글 내용 */
.view-body {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
}

.view-content {
    padding: 40px;
    border-bottom: 1px solid #e5e7eb;
}

.view-content-text {
    font-size: 17px;
    line-height: 1.8;
    color: #374151;
    min-height: 300px;
}

.view-content-text p {
    margin-bottom: 1.5em;
}

.view-content-text img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.view-content-text h1, .view-content-text h2, .view-content-text h3,
.view-content-text h4, .view-content-text h5, .view-content-text h6 {
    margin: 2em 0 1em;
    font-weight: 600;
    color: #1f2937;
}

/* 첨부파일 */
.view-files {
    background: #f9fafb;
    padding: 24px 40px;
    border-bottom: 1px solid #e5e7eb;
}

.file-header {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}

.file-header i {
    color: #6b7280;
}

.file-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.file-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
}

.file-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.file-link {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #374151;
    text-decoration: none;
    width: 100%;
}

.file-link i {
    font-size: 20px;
    color: #3b82f6;
}

.file-name {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
}

.file-info {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #6b7280;
}

/* 버튼 영역 */
.view-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 40px;
}

.btn-group {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.btn-danger:hover {
    background: #fecaca;
    transform: translateY(-1px);
}

/* 이전글 다음글 */
.view-nav {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.nav-item {
    display: flex;
    padding: 24px 30px;
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.2s;
    position: relative;
}

.nav-item:last-child {
    border-bottom: none;
}

.nav-item:hover {
    background: #f9fafb;
}

.nav-item:hover .nav-arrow {
    transform: translateX(5px);
}

.nav-label {
    width: 100px;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.nav-link {
    color: #1f2937;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
}

.nav-link:hover {
    color: #3b82f6;
}

.nav-meta {
    font-size: 13px;
    color: #9ca3af;
}

.nav-arrow {
    display: flex;
    align-items: center;
    color: #d1d5db;
    font-size: 20px;
    transition: transform 0.2s;
}

/* 공지사항 스타일 */
.notice-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: #fef3c7;
    color: #f59e0b;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    margin-left: 12px;
}

/* 반응형 */
@media (max-width: 768px) {
    .view-header {
        padding: 30px 20px;
    }
    
    .view-subject {
        font-size: 24px;
    }
    
    .view-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .view-stats {
        margin-left: 0;
    }
    
    .view-content,
    .view-files,
    .view-actions {
        padding: 20px;
    }
    
    .view-actions {
        flex-direction: column;
        gap: 12px;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn {
        flex: 1;
        justify-content: center;
    }
    
    .nav-label {
        width: 70px;
        font-size: 13px;
    }
    
    .nav-link {
        font-size: 14px;
    }
}
</style>

<div class="view-container">
<?php include_once(G5_PATH.'/coin.php');?>
    <!-- 글 헤더 -->
    <div class="view-header">
        <div class="view-category" style="color: <?php echo $category_info['color']; ?>;">
            <i class="bi <?php echo $category_info['icon']; ?>"></i>
            <?php echo $view['cm_category']; ?>
        </div>
        
        <h1 class="view-subject">
            <?php echo get_text($view['cm_subject']); ?>
            <?php if($view['cm_is_notice']) { ?>
            <span class="notice-badge">
                <i class="bi bi-pin-angle-fill"></i> 공지
            </span>
            <?php } ?>
        </h1>
        
        <div class="view-meta">
            <div class="author-info">
                <div class="author-avatar">
                    <?php echo strtoupper(mb_substr($view['cm_name'], 0, 1)); ?>
                </div>
                <div class="author-details">
                    <div class="author-name"><?php echo get_text($view['cm_name']); ?></div>
                    <div class="post-date"><?php echo date('Y년 m월 d일 H:i', strtotime($view['cm_datetime'])); ?></div>
                </div>
            </div>
            
            <div class="view-stats">
                <div class="stat-item">
                    <i class="bi bi-eye"></i>
                    <span><?php echo number_format($view['cm_hit']); ?></span>
                </div>
                <?php if($view['cm_comment'] > 0) { ?>
                <div class="stat-item">
                    <i class="bi bi-chat-dots"></i>
                    <span><?php echo number_format($view['cm_comment']); ?></span>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <!-- 글 본문 -->
    <div class="view-body">
        <!-- 글 내용 -->
        <div class="view-content">
            <div class="view-content-text">
                <?php 
                // HTML 내용 출력 (에디터로 작성된 내용)
                echo conv_content($view['cm_content'], 1); 
                ?>
            </div>
        </div>
        
        <!-- 첨부파일 -->
        <?php
        $sql = "SELECT * FROM g5_community_file WHERE cm_id = '$cm_id' ORDER BY bf_no";
        $file_result = sql_query($sql);
        $file_count = sql_num_rows($file_result);
        
        if($file_count > 0) {
        ?>
        <div class="view-files">
            <div class="file-header">
                <i class="bi bi-paperclip"></i> 첨부파일 (<?php echo $file_count; ?>)
            </div>
            <div class="file-list">
                <?php while($file = sql_fetch_array($file_result)) { ?>
                <div class="file-item">
                    <a href="./community_download.php?cm_id=<?php echo $cm_id; ?>&bf_no=<?php echo $file['bf_no']; ?>" class="file-link">
                        <i class="bi bi-file-earmark-arrow-down"></i>
                        <span class="file-name"><?php echo $file['bf_source']; ?></span>
                        <span class="file-info">
                            <span class="file-size"><?php echo get_filesize($file['bf_filesize']); ?></span>
                            <span class="file-download">다운로드 <?php echo number_format($file['bf_download']); ?>회</span>
                        </span>
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        
        <!-- 버튼 영역 -->
        <div class="view-actions">
            <div class="btn-group">
                <a href="./community.php?page=<?php echo $page; ?>" class="btn btn-secondary">
                    <i class="bi bi-list"></i> 목록
                </a>
            </div>
            
            <?php if($is_admin || ($is_member && $view['mb_id'] == $member['mb_id'])) { ?>
            <div class="btn-group">
                <a href="./community_write.php?mode=edit&cm_id=<?php echo $cm_id; ?>&page=<?php echo $page; ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> 수정
                </a>
                <a href="javascript:confirmDelete();" class="btn btn-danger">
                    <i class="bi bi-trash"></i> 삭제
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
    
    <!-- 이전글 다음글 -->
    <div class="view-nav">
        <?php if($prev['cm_id']) { ?>
        <a href="./community_view.php?cm_id=<?php echo $prev['cm_id']; ?>&page=<?php echo $page; ?>" class="nav-item">
            <div class="nav-label">
                <i class="bi bi-chevron-up"></i> 이전글
            </div>
            <div class="nav-content">
                <div class="nav-link"><?php echo get_text($prev['cm_subject']); ?></div>
                <div class="nav-meta"><?php echo $prev['cm_name']; ?> · <?php echo date('Y.m.d', strtotime($prev['cm_datetime'])); ?></div>
            </div>
            <div class="nav-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
        <?php } ?>
        
        <?php if($next['cm_id']) { ?>
        <a href="./community_view.php?cm_id=<?php echo $next['cm_id']; ?>&page=<?php echo $page; ?>" class="nav-item">
            <div class="nav-label">
                <i class="bi bi-chevron-down"></i> 다음글
            </div>
            <div class="nav-content">
                <div class="nav-link"><?php echo get_text($next['cm_subject']); ?></div>
                <div class="nav-meta"><?php echo $next['cm_name']; ?> · <?php echo date('Y.m.d', strtotime($next['cm_datetime'])); ?></div>
            </div>
            <div class="nav-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
        <?php } ?>
    </div>
</div>

<script>
function confirmDelete() {
    if(confirm('정말 삭제하시겠습니까?\n삭제된 글은 복구할 수 없습니다.')) {
        location.href = './community.php?mode=delete&cm_id=<?php echo $cm_id; ?>&page=<?php echo $page; ?>';
    }
}
</script>

<?php
include_once('./_tail.php');
?>
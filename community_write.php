<?php
/*
 * 파일명: community_write.php
 * 위치: /
 * 기능: 커뮤니티 글쓰기/수정 (에디터, 파일첨부 포함)
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'write';
$cm_id = isset($_GET['cm_id']) ? (int)$_GET['cm_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

/* 페이지 제목 */
$g5['title'] = $mode == 'edit' ? '글 수정' : '글쓰기';

// ===================================
// 파일 업로드 테이블 생성
// ===================================

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
// 수정 모드일 경우 데이터 조회
// ===================================

$cm = array();
if($mode == 'edit' && $cm_id) {
    $sql = "SELECT * FROM g5_community WHERE cm_id = '$cm_id'";
    $cm = sql_fetch($sql);
    
    if(!$cm['cm_id']) {
        alert('존재하지 않는 게시글입니다.', './community.php');
    }
    
    // 권한 확인 (관리자이거나 본인글)
    if(!$is_admin && !($is_member && $cm['mb_id'] == $member['mb_id'])) {
        alert('수정 권한이 없습니다.', './community.php');
    }
}

// ===================================
// 폼 전송 처리
// ===================================

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cm_category = isset($_POST['cm_category']) ? trim($_POST['cm_category']) : '';
    $cm_subject = isset($_POST['cm_subject']) ? trim($_POST['cm_subject']) : '';
    $cm_content = isset($_POST['cm_content']) ? trim($_POST['cm_content']) : '';
    $cm_name = isset($_POST['cm_name']) ? trim($_POST['cm_name']) : '';
    $cm_password = isset($_POST['cm_password']) ? trim($_POST['cm_password']) : '';
    $cm_is_notice = isset($_POST['cm_is_notice']) ? 1 : 0;
    
    // 유효성 검사
    if(!$cm_category) alert('분류를 선택해주세요.');
    if(!$cm_subject) alert('제목을 입력해주세요.');
    if(!$cm_content) alert('내용을 입력해주세요.');
    
    // 회원/비회원 처리
    if($is_member) {
        $cm_name = $member['mb_nick'] ? $member['mb_nick'] : $member['mb_name'];
        $mb_id = $member['mb_id'];
    } else {
        if(!$cm_name) alert('이름을 입력해주세요.');
        if(!$cm_password && $mode == 'write') alert('비밀번호를 입력해주세요.');
        $mb_id = '';
    }
    
    if($mode == 'edit') {
        // 수정
        $sql = "UPDATE g5_community SET
                cm_category = '".sql_real_escape_string($cm_category)."',
                cm_subject = '".sql_real_escape_string($cm_subject)."',
                cm_content = '".sql_real_escape_string($cm_content)."',
                cm_name = '".sql_real_escape_string($cm_name)."'";
        
        // 관리자만 공지사항 설정 가능
        if($is_admin) {
            $sql .= ", cm_is_notice = '$cm_is_notice'";
        }
        
        $sql .= " WHERE cm_id = '$cm_id'";
        
        sql_query($sql);
        
    } else {
        // 새글 작성
        $cm_password_hash = '';
        if(!$is_member && $cm_password) {
            $cm_password_hash = password_hash($cm_password, PASSWORD_DEFAULT);
        }
        
        $sql = "INSERT INTO g5_community SET
                cm_category = '".sql_real_escape_string($cm_category)."',
                cm_subject = '".sql_real_escape_string($cm_subject)."',
                cm_content = '".sql_real_escape_string($cm_content)."',
                cm_name = '".sql_real_escape_string($cm_name)."',
                cm_password = '$cm_password_hash',
                cm_datetime = '".G5_TIME_YMDHIS."',
                cm_ip = '".$_SERVER['REMOTE_ADDR']."',
                cm_is_notice = '$cm_is_notice',
                mb_id = '$mb_id'";
        
        sql_query($sql);
        $cm_id = sql_insert_id();
    }
    
    // ===================================
    // 파일 업로드 처리
    // ===================================
    
    $file_upload_msg = '';
    $upload_max_filesize = ini_get('upload_max_filesize');
    
    if(!preg_match("/([0-9]+)M/i", $upload_max_filesize, $match))
        $upload_max_filesize = 10 * 1048576; // 10MB
    else
        $upload_max_filesize = $match[1] * 1048576;
    
    // 파일 삭제
    if(isset($_POST['bf_file_del'])) {
        foreach($_POST['bf_file_del'] as $bf_no => $val) {
            if($val == 1) {
                $sql = "SELECT * FROM g5_community_file WHERE cm_id = '$cm_id' AND bf_no = '$bf_no'";
                $file = sql_fetch($sql);
                if($file['bf_file']) {
                    @unlink(G5_DATA_PATH.'/community/'.$file['bf_file']);
                }
                sql_query("DELETE FROM g5_community_file WHERE cm_id = '$cm_id' AND bf_no = '$bf_no'");
            }
        }
    }
    
    // 파일 업로드
    if(isset($_FILES['bf_file']['name'])) {
        // 디렉토리 생성
        @mkdir(G5_DATA_PATH.'/community', G5_DIR_PERMISSION);
        @chmod(G5_DATA_PATH.'/community', G5_DIR_PERMISSION);
        
        for($i=0; $i<count($_FILES['bf_file']['name']); $i++) {
            if($_FILES['bf_file']['name'][$i] && is_uploaded_file($_FILES['bf_file']['tmp_name'][$i])) {
                
                // 파일 크기 체크
                if($_FILES['bf_file']['size'][$i] > $upload_max_filesize) {
                    $file_upload_msg .= '파일 '.$_FILES['bf_file']['name'][$i].'의 용량이 너무 큽니다.\\n';
                    continue;
                }
                
                // 파일명 생성
                $dest_file = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr(md5(uniqid(time())),0,8).'_'.str_replace('%', '', urlencode($_FILES['bf_file']['name'][$i]));
                $dest_file = preg_replace("/\s+/", "", $dest_file);
                
                // 파일 업로드
                $dest_path = G5_DATA_PATH.'/community/'.$dest_file;
                move_uploaded_file($_FILES['bf_file']['tmp_name'][$i], $dest_path);
                chmod($dest_path, G5_FILE_PERMISSION);
                
                // DB 저장
                $sql = "SELECT MAX(bf_no) as max_no FROM g5_community_file WHERE cm_id = '$cm_id'";
                $row = sql_fetch($sql);
                $bf_no = $row['max_no'] + 1;
                
                $sql = "INSERT INTO g5_community_file SET
                        cm_id = '$cm_id',
                        bf_no = '$bf_no',
                        bf_source = '".sql_real_escape_string($_FILES['bf_file']['name'][$i])."',
                        bf_file = '$dest_file',
                        bf_filesize = '".$_FILES['bf_file']['size'][$i]."',
                        bf_datetime = '".G5_TIME_YMDHIS."'";
                sql_query($sql);
            }
        }
    }
    
    if($file_upload_msg)
        alert($file_upload_msg, './community_view.php?cm_id='.$cm_id.'&page='.$page);
    else
        alert('게시글이 '.($mode == 'edit' ? '수정' : '등록').'되었습니다.', './community_view.php?cm_id='.$cm_id.'&page='.$page);
}

include_once('./_head.php');

// 스마트에디터 사용
include_once(G5_EDITOR_LIB);

// 카테고리 목록
$categories = array('투자전략', '시장분석', '프로젝트분석', '규제/정책', '기술토론', '질문답변');
if($is_admin) {
    array_unshift($categories, '공지사항');
}

// 카테고리 정보
$categories_info = array(
    '공지사항' => array('icon' => 'bi-megaphone-fill', 'color' => '#dc2626'),
    '투자전략' => array('icon' => 'bi-graph-up-arrow', 'color' => '#2563eb'),
    '시장분석' => array('icon' => 'bi-bar-chart-line-fill', 'color' => '#7c3aed'),
    '프로젝트분석' => array('icon' => 'bi-search', 'color' => '#0891b2'),
    '규제/정책' => array('icon' => 'bi-shield-check', 'color' => '#059669'),
    '기술토론' => array('icon' => 'bi-cpu', 'color' => '#ea580c'),
    '질문답변' => array('icon' => 'bi-question-circle-fill', 'color' => '#6366f1')
);
?>

<style>
/* 글쓰기 폼 */
.write-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 헤더 */
.write-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px 16px 0 0;
    padding: 40px;
	margin-top:20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.write-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: rotate(45deg);
}

.write-header h2 {
    font-size: 32px;
    font-weight: 700;
    color: white;
    margin: 0;
    position: relative;
}

.write-header p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px;
    margin-top: 8px;
    position: relative;
}

/* 진행 표시기 */
.write-progress {
    background: white;
    padding: 24px 40px;
    border-left: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
}

.progress-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #9ca3af;
    font-size: 14px;
    font-weight: 500;
}

.progress-item.active {
    color: #3b82f6;
}

.progress-number {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: all 0.3s;
}

.progress-item.active .progress-number {
    background: #3b82f6;
    color: white;
}

/* 폼 래퍼 */
.write-form {
    background: white;
    border-radius: 0 0 16px 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.form-body {
    padding: 40px;
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 32px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 12px;
}

.form-label i {
    color: #6b7280;
    font-size: 16px;
}

.required {
    color: #ef4444;
    font-weight: 400;
}

/* 도움말 */
.form-help {
    font-size: 13px;
    color: #6b7280;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-help i {
    font-size: 14px;
}

/* 입력 요소 */
.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
    background: #f9fafb;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.3s;
}

.form-select:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

/* 카테고리 선택 개선 */
.category-select-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-top: 12px;
}

.category-radio {
    display: none;
}

.category-label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
    font-weight: 500;
}

.category-label:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.category-radio:checked + .category-label {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.category-radio:checked + .category-label i {
    color: white;
}

/* 에디터 영역 */
.editor-wrapper {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

/* 파일 업로드 */
.file-upload-area {
    padding: 24px;
    background: #f8fafc;
    border: 2px dashed #e5e7eb;
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s;
}

.file-upload-area:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.file-upload-icon {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.file-upload-text {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 16px;
}

.file-input-wrapper {
    margin-top: 16px;
}

.file-input-wrapper input[type="file"] {
    display: none;
}

.file-input-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.file-input-label:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.attached-file {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 12px;
    font-size: 14px;
    text-align: left;
}

.attached-file i {
    font-size: 20px;
    color: #6b7280;
}

.attached-file-info {
    flex: 1;
}

.attached-file-name {
    font-weight: 500;
    color: #1f2937;
}

.file-size {
    color: #9ca3af;
    font-size: 12px;
}

.attached-file label {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ef4444;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.attached-file label:hover {
    color: #dc2626;
}

/* 작성자 정보 (비회원) */
.form-inline {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-inline .form-group {
    margin-bottom: 0;
}

/* 옵션 섹션 */
.form-options {
    background: #f9fafb;
    padding: 24px;
    border-radius: 10px;
    margin-top: 32px;
}

.form-checkbox {
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #3b82f6;
}

.form-checkbox label {
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
}

.notice-badge {
    padding: 4px 8px;
    background: #fef3c7;
    color: #f59e0b;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

/* 액션 버튼 */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 32px 40px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.action-left {
    font-size: 14px;
    color: #6b7280;
}

.action-buttons {
    display: flex;
    gap: 12px;
}

.btn {
    padding: 12px 28px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.sound_only, .cke_sc { display:none; }
.btn-primary {
    background: #3b82f6;
    color: white;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: white;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

/* 툴팁 */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 8px 12px;
    background: #1f2937;
    color: white;
    font-size: 12px;
    border-radius: 6px;
    white-space: nowrap;
    margin-bottom: 4px;
}

/* 반응형 */
@media (max-width: 768px) {
    .write-header {
        padding: 30px 20px;
    }
    
    .write-header h2 {
        font-size: 24px;
    }
    
    .write-progress {
        padding: 20px;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .progress-item span {
        display: none;
    }
    
    .form-body {
        padding: 24px 20px;
    }
    
    .category-select-group {
        grid-template-columns: 1fr;
    }
    
    .form-inline {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 16px;
        padding: 24px 20px;
    }
    
    .action-buttons {
        width: 100%;
        flex-direction: column-reverse;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="write-container">
<?php include_once(G5_PATH.'/coin.php');?>

    <form method="post" class="write-form" enctype="multipart/form-data" onsubmit="return fwrite_submit(this);">
        <input type="hidden" name="mode" value="<?php echo $mode; ?>">
        <input type="hidden" name="cm_id" value="<?php echo $cm_id; ?>">
        <input type="hidden" name="page" value="<?php echo $page; ?>">
        
        <!-- 헤더 -->
        <div class="write-header">
            <h2><?php echo $mode == 'edit' ? '게시글 수정' : '새 글 작성'; ?></h2>
            <p>커뮤니티 회원들과 유익한 정보를 공유해주세요</p>
        </div>
        
        <!-- 진행 표시기 -->
        <div class="write-progress">
            <div class="progress-item active">
                <div class="progress-number">1</div>
                <span>카테고리 선택</span>
            </div>
            <div class="progress-item active">
                <div class="progress-number">2</div>
                <span>내용 작성</span>
            </div>
            <div class="progress-item">
                <div class="progress-number">3</div>
                <span>작성 완료</span>
            </div>
        </div>
        
        <div class="form-body">
            <!-- 카테고리 선택 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-folder"></i> 카테고리 선택 <span class="required">*</span>
                </label>
                <div class="category-select-group">
                    <?php foreach($categories as $idx => $cate) { 
                        $cate_info = isset($categories_info[$cate]) ? $categories_info[$cate] : array('icon' => 'bi-folder', 'color' => '#6b7280');
                    ?>
                    <input type="radio" name="cm_category" value="<?php echo $cate; ?>" 
                           id="category_<?php echo $idx; ?>" class="category-radio"
                           <?php echo ($cm['cm_category'] ?? '') == $cate ? 'checked' : ''; ?>
                           required>
                    <label for="category_<?php echo $idx; ?>" class="category-label">
                        <i class="bi <?php echo $cate_info['icon']; ?>" style="color: <?php echo $cate_info['color']; ?>;"></i>
                        <?php echo $cate; ?>
                    </label>
                    <?php } ?>
                </div>
                <p class="form-help">
                    <i class="bi bi-info-circle"></i> 게시글 내용과 가장 적합한 카테고리를 선택해주세요
                </p>
            </div>
            
            <!-- 제목 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-type"></i> 제목 <span class="required">*</span>
                </label>
                <input type="text" name="cm_subject" class="form-control" required 
                       value="<?php echo get_text($cm['cm_subject'] ?? ''); ?>" 
                       placeholder="게시글 제목을 입력하세요 (최대 100자)"
                       maxlength="100">
                <p class="form-help">
                    <i class="bi bi-lightbulb"></i> 구체적이고 명확한 제목을 작성하면 더 많은 관심을 받을 수 있습니다
                </p>
            </div>
            
            <!-- 내용 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-text-paragraph"></i> 내용 <span class="required">*</span>
                </label>
                <div class="editor-wrapper">
                    <?php echo editor_html('cm_content', get_text(html_purifier($cm['cm_content'] ?? ''), 0)); ?>
                </div>
            </div>
            
            <!-- 파일 첨부 -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-paperclip"></i> 파일 첨부
                </label>
                <div class="file-upload-area">
                    <i class="bi bi-cloud-upload file-upload-icon"></i>
                    <p class="file-upload-text">파일을 드래그하거나 클릭하여 업로드하세요</p>
                    <p class="form-help">최대 2개, 개당 <?php echo ini_get('upload_max_filesize'); ?> 이하</p>
                    
                    <div class="file-input-wrapper">
                        <input type="file" name="bf_file[]" id="file1" onchange="fileSelected(this)">
                        <label for="file1" class="file-input-label">
                            <i class="bi bi-folder-plus"></i> 파일 선택
                        </label>
                    </div>
                    <div class="file-input-wrapper" style="margin-top: 8px;">
                        <input type="file" name="bf_file[]" id="file2" onchange="fileSelected(this)">
                        <label for="file2" class="file-input-label">
                            <i class="bi bi-folder-plus"></i> 파일 선택
                        </label>
                    </div>
                    
                    <?php if($mode == 'edit' && $cm_id) { 
                        // 기존 첨부파일 표시
                        $sql = "SELECT * FROM g5_community_file WHERE cm_id = '$cm_id' ORDER BY bf_no";
                        $file_result = sql_query($sql);
                        while($file = sql_fetch_array($file_result)) {
                    ?>
                    <div class="attached-file">
                        <i class="bi bi-file-earmark-text"></i>
                        <div class="attached-file-info">
                            <div class="attached-file-name"><?php echo $file['bf_source']; ?></div>
                            <span class="file-size"><?php echo get_filesize($file['bf_filesize']); ?></span>
                        </div>
                        <label>
                            <input type="checkbox" name="bf_file_del[<?php echo $file['bf_no']; ?>]" value="1">
                            <i class="bi bi-trash"></i> 삭제
                        </label>
                    </div>
                    <?php } } ?>
                </div>
            </div>
            
            <?php if(!$is_member) { ?>
            <!-- 작성자 정보 (비회원) -->
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-person"></i> 작성자 정보
                </label>
                <div class="form-inline">
                    <div class="form-group">
                        <input type="text" name="cm_name" class="form-control" required 
                               value="<?php echo get_text($cm['cm_name'] ?? ''); ?>" 
                               placeholder="이름">
                    </div>
                    <?php if($mode == 'write') { ?>
                    <div class="form-group">
                        <input type="password" name="cm_password" class="form-control" required 
                               placeholder="비밀번호 (수정/삭제 시 필요)">
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            
            <?php if($is_admin) { ?>
            <!-- 관리자 옵션 -->
            <div class="form-options">
                <div class="form-checkbox">
                    <input type="checkbox" name="cm_is_notice" id="cm_is_notice" value="1" 
                           <?php echo ($cm['cm_is_notice'] ?? 0) ? 'checked' : ''; ?>>
                    <label for="cm_is_notice">
                        <span class="notice-badge">공지</span> 이 글을 공지사항으로 등록
                    </label>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <!-- 액션 버튼 -->
        <div class="form-actions">
            <div class="action-left">
                <i class="bi bi-shield-check"></i> 커뮤니티 이용규칙을 준수해주세요
            </div>
            <div class="action-buttons">
                <a href="./community.php?page=<?php echo $page; ?>" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> 취소
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> <?php echo $mode == 'edit' ? '수정 완료' : '작성 완료'; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function fwrite_submit(f) {
    <?php echo get_editor_js('cm_content'); ?>
    
    if(!f.cm_category.value) {
        alert('분류를 선택해주세요.');
        f.cm_category.focus();
        return false;
    }
    
    if(!f.cm_subject.value) {
        alert('제목을 입력해주세요.');
        f.cm_subject.focus();
        return false;
    }
    
    return true;
}

// 파일 선택 시 파일명 표시
function fileSelected(input) {
    if(input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
        
        // 파일 정보 표시
        const fileInfo = document.createElement('div');
        fileInfo.className = 'attached-file';
        fileInfo.innerHTML = `
            <i class="bi bi-file-earmark-text"></i>
            <div class="attached-file-info">
                <div class="attached-file-name">${fileName}</div>
                <span class="file-size">${fileSize}</span>
            </div>
        `;
        
        input.parentElement.appendChild(fileInfo);
    }
}

// 드래그 앤 드롭
const fileUploadArea = document.querySelector('.file-upload-area');

fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#3b82f6';
    fileUploadArea.style.background = '#eff6ff';
});

fileUploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#e5e7eb';
    fileUploadArea.style.background = '#f8fafc';
});

fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#e5e7eb';
    fileUploadArea.style.background = '#f8fafc';
    
    const files = e.dataTransfer.files;
    // 파일 처리 로직
});
</script>

<?php
include_once('./_tail.php');
?>
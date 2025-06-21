<?php
/*
 * 파일명: member_edit.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 하위 회원 정보 수정
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 접근 권한 확인
// ===================================

/* 로그인 체크 */
if (!$member['mb_id']) {
    alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
}

/* 하부조직 권한 체크 */
if ($member['mb_grade'] < 2) {
    alert('접근 권한이 없습니다.', G5_URL);
}

/* 회원 ID 확인 */
$mb_id = isset($_GET['mb_id']) ? trim($_GET['mb_id']) : '';
if (!$mb_id) {
    alert('회원 정보가 없습니다.', './member_list.php');
}

// ===================================
// 회원 정보 조회
// ===================================

/* 하위 회원인지 확인 */
$sql = "SELECT * FROM {$g5['member_table']} 
        WHERE mb_id = '{$mb_id}' AND mb_recommend = '{$member['mb_id']}'";
$mb = sql_fetch($sql);

if (!$mb['mb_id']) {
    alert('권한이 없거나 존재하지 않는 회원입니다.', './member_list.php');
}

// ===================================
// 정보 수정 처리
// ===================================

if ($_POST['act'] == 'update') {
    $mb_name = trim($_POST['mb_name']);
    $mb_nick = trim($_POST['mb_nick']);
    $mb_email = trim($_POST['mb_email']);
    $mb_hp = trim($_POST['mb_hp']);
    $mb_zip = trim($_POST['mb_zip']);
    $mb_addr1 = trim($_POST['mb_addr1']);
    $mb_addr2 = trim($_POST['mb_addr2']);
    $mb_addr3 = trim($_POST['mb_addr3']);
    $mb_memo = trim($_POST['mb_memo']);
    
    // 비밀번호 변경 (입력된 경우만)
    $sql_password = "";
    if ($_POST['mb_password']) {
        $sql_password = ", mb_password = '".get_encrypt_string($_POST['mb_password'])."' ";
    }
    
    // 회원 정보 업데이트
    $sql = "UPDATE {$g5['member_table']} 
            SET mb_name = '{$mb_name}',
                mb_nick = '{$mb_nick}',
                mb_email = '{$mb_email}',
                mb_hp = '{$mb_hp}',
                mb_zip = '{$mb_zip}',
                mb_addr1 = '{$mb_addr1}',
                mb_addr2 = '{$mb_addr2}',
                mb_addr3 = '{$mb_addr3}',
                mb_memo = '{$mb_memo}'
                {$sql_password}
            WHERE mb_id = '{$mb_id}'";
    sql_query($sql);
    
    alert('회원 정보가 수정되었습니다.', './member_edit.php?mb_id='.$mb_id);
}

/* 페이지 제목 */
$g5['title'] = '회원 정보 수정';

include_once(G5_PATH.'/head.sub.php');
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $g5['title']; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
    /* ===================================
     * 회원 정보 수정 전용 스타일
     * =================================== */
    
    .cmk-member-edit * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .cmk-member-edit {
        font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }
    
    .cmk-me-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    /* 헤더 */
    .cmk-me-header {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .cmk-me-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .cmk-me-header h1 i {
        color: #3b82f6;
    }
    
    .cmk-me-member-info {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 16px;
        padding: 16px;
        background: #f3f4f6;
        border-radius: 12px;
    }
    
    .cmk-me-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 24px;
    }
    
    .cmk-me-member-details h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .cmk-me-member-details p {
        font-size: 14px;
        color: #6b7280;
    }
    
    /* 폼 영역 */
    .cmk-me-form {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .cmk-me-section {
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .cmk-me-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .cmk-me-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cmk-me-section-title i {
        color: #6b7280;
    }
    
    .cmk-me-form-group {
        margin-bottom: 20px;
    }
    
    .cmk-me-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .cmk-me-input-group {
        position: relative;
    }
    
    .cmk-me-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 18px;
    }
    
    .cmk-me-input,
    .cmk-me-textarea {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
        background: #f9fafb;
        font-family: inherit;
    }
    
    .cmk-me-textarea {
        padding-left: 16px;
        min-height: 100px;
        resize: vertical;
    }
    
    .cmk-me-input:focus,
    .cmk-me-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .cmk-me-help-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .cmk-me-info-box {
        background: #eff6ff;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    
    .cmk-me-info-box i {
        color: #3b82f6;
        font-size: 20px;
        flex-shrink: 0;
    }
    
    .cmk-me-info-box p {
        font-size: 14px;
        color: #1e40af;
        line-height: 1.6;
    }
    
    /* 버튼 영역 */
    .cmk-me-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }
    
    .cmk-me-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .cmk-me-btn-primary {
        background: #3b82f6;
        color: white;
    }
    
    .cmk-me-btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .cmk-me-btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }
    
    .cmk-me-btn-secondary:hover {
        background: #d1d5db;
    }
    
    /* 반응형 */
    @media (max-width: 768px) {
        .cmk-member-edit {
            padding: 10px;
        }
        
        .cmk-me-header,
        .cmk-me-form {
            padding: 24px 20px;
        }
        
        .cmk-me-actions {
            flex-direction: column;
        }
        
        .cmk-me-btn {
            width: 100%;
        }
    }
    </style>
</head>
<body>

<div class="cmk-member-edit">
    <div class="cmk-me-container">
        <!-- 헤더 -->
        <div class="cmk-me-header">
            <h1><i class="bi bi-person-gear"></i> 회원 정보 수정</h1>
            <div class="cmk-me-member-info">
                <div class="cmk-me-avatar">
                    <?php echo substr($mb['mb_name'], 0, 1); ?>
                </div>
                <div class="cmk-me-member-details">
                    <h2><?php echo $mb['mb_name']; ?> (<?php echo $mb['mb_id']; ?>)</h2>
                    <p>가입일: <?php echo date('Y년 m월 d일', strtotime($mb['mb_datetime'])); ?></p>
                </div>
            </div>
        </div>
        
        <!-- 폼 영역 -->
        <div class="cmk-me-form">
            <form method="post" action="" onsubmit="return fmember_submit(this);">
            <input type="hidden" name="act" value="update">
            
            <!-- 기본 정보 -->
            <div class="cmk-me-section">
                <h3 class="cmk-me-section-title">
                    <i class="bi bi-person"></i> 기본 정보
                </h3>
                
                <div class="cmk-me-info-box">
                    <i class="bi bi-info-circle"></i>
                    <p>회원의 기본 정보를 수정할 수 있습니다. 아이디는 변경할 수 없으며, 비밀번호는 입력 시에만 변경됩니다.</p>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">아이디</label>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-person cmk-me-input-icon"></i>
                        <input type="text" class="cmk-me-input" value="<?php echo $mb['mb_id']; ?>" readonly style="background: #e5e7eb;">
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label for="mb_password" class="cmk-me-label">새 비밀번호</label>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-lock cmk-me-input-icon"></i>
                        <input type="password" name="mb_password" id="mb_password" class="cmk-me-input" placeholder="변경할 경우에만 입력하세요">
                    </div>
                    <div class="cmk-me-help-text">
                        <i class="bi bi-info-circle"></i>
                        비밀번호를 변경하지 않으려면 비워두세요
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label for="mb_name" class="cmk-me-label">이름</label>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-person-badge cmk-me-input-icon"></i>
                        <input type="text" name="mb_name" id="mb_name" class="cmk-me-input" value="<?php echo $mb['mb_name']; ?>" required>
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label for="mb_nick" class="cmk-me-label">닉네임</label>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-chat-dots cmk-me-input-icon"></i>
                        <input type="text" name="mb_nick" id="mb_nick" class="cmk-me-input" value="<?php echo $mb['mb_nick']; ?>">
                    </div>
                </div>
            </div>
            
            <!-- 연락처 정보 -->
            <div class="cmk-me-section">
                <h3 class="cmk-me-section-title">
                    <i class="bi bi-telephone"></i> 연락처 정보
                </h3>
                
                <div class="cmk-me-form-group">
                    <label for="mb_email" class="cmk-me-label">이메일</label>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-envelope cmk-me-input-icon"></i>
                        <input type="email" name="mb_email" id="mb_email" class="cmk-me-input" value="<?php echo $mb['mb_email']; ?>" required>
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label for="mb_hp" class="cmk-me-label">휴대폰</label>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-phone cmk-me-input-icon"></i>
                        <input type="text" name="mb_hp" id="mb_hp" class="cmk-me-input" value="<?php echo $mb['mb_hp']; ?>">
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label for="mb_zip" class="cmk-me-label">주소</label>
                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                        <div class="cmk-me-input-group" style="flex: 1;">
                            <i class="bi bi-geo-alt cmk-me-input-icon"></i>
                            <input type="text" name="mb_zip" id="mb_zip" class="cmk-me-input" value="<?php echo $mb['mb_zip']; ?>" placeholder="우편번호">
                        </div>
                        <button type="button" class="cmk-me-btn cmk-me-btn-secondary" onclick="win_zip('fmember', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">
                            <i class="bi bi-search"></i> 주소 검색
                        </button>
                    </div>
                    <div class="cmk-me-input-group" style="margin-bottom: 8px;">
                        <i class="bi bi-house cmk-me-input-icon"></i>
                        <input type="text" name="mb_addr1" id="mb_addr1" class="cmk-me-input" value="<?php echo $mb['mb_addr1']; ?>" placeholder="기본주소">
                    </div>
                    <div class="cmk-me-input-group" style="margin-bottom: 8px;">
                        <i class="bi bi-house-door cmk-me-input-icon"></i>
                        <input type="text" name="mb_addr2" id="mb_addr2" class="cmk-me-input" value="<?php echo $mb['mb_addr2']; ?>" placeholder="상세주소">
                    </div>
                    <div class="cmk-me-input-group">
                        <i class="bi bi-signpost cmk-me-input-icon"></i>
                        <input type="text" name="mb_addr3" id="mb_addr3" class="cmk-me-input" value="<?php echo $mb['mb_addr3']; ?>" placeholder="참고항목" readonly>
                    </div>
                </div>
            </div>
            
            <!-- 메모 -->
            <div class="cmk-me-section">
                <h3 class="cmk-me-section-title">
                    <i class="bi bi-pencil-square"></i> 관리자 메모
                </h3>
                
                <div class="cmk-me-form-group">
                    <textarea name="mb_memo" id="mb_memo" class="cmk-me-textarea" placeholder="회원에 대한 메모를 입력하세요"><?php echo $mb['mb_memo']; ?></textarea>
                </div>
            </div>
            
            <!-- 버튼 영역 -->
            <div class="cmk-me-actions">
                <button type="submit" class="cmk-me-btn cmk-me-btn-primary">
                    <i class="bi bi-check-lg"></i> 정보 수정
                </button>
                <a href="./member_list.php" class="cmk-me-btn cmk-me-btn-secondary">
                    <i class="bi bi-list"></i> 목록으로
                </a>
            </div>
            
            </form>
        </div>
    </div>
</div>

<script>
function fmember_submit(f) {
    if (f.mb_password.value) {
        if (f.mb_password.value.length < 3) {
            alert("비밀번호는 3자 이상 입력하세요.");
            f.mb_password.focus();
            return false;
        }
    }
    
    if (!f.mb_name.value) {
        alert("이름을 입력하세요.");
        f.mb_name.focus();
        return false;
    }
    
    if (!f.mb_email.value) {
        alert("이메일을 입력하세요.");
        f.mb_email.focus();
        return false;
    }
    
    return confirm("회원 정보를 수정하시겠습니까?");
}
</script>

</body>
</html>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
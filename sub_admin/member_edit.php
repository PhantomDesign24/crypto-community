<?php
/*
 * 파일명: member_edit.php
 * 위치: /sub_admin/
 * 기능: 하부조직 관리자 - 하위 회원 정보 수정
 * 작성일: 2025-01-23
 * 수정일: 2025-01-23 (수정 시)
 */

include_once('./_common.php');
include_once('./header.php');

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
/* 최고관리자는 모든 회원 수정 가능, 일반 관리자는 하위 회원만 */
if ($is_admin) {
    $sql = "SELECT * FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}'";
} else {
    $sql = "SELECT * FROM {$g5['member_table']} 
            WHERE mb_id = '{$mb_id}' AND mb_recommend = '{$member['mb_id']}'";
}
$mb = sql_fetch($sql);

if (!$mb['mb_id']) {
    alert('권한이 없거나 존재하지 않는 회원입니다.', './member_list.php');
}


// ===================================
// 정보 수정 처리
// ===================================

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['act'] == 'update') {
    // 입력값 정리 (그누보드5 방식)
    $mb_name = isset($_POST['mb_name']) ? strip_tags(clean_xss_attributes($_POST['mb_name'])) : '';
    $mb_nick = isset($_POST['mb_nick']) ? strip_tags(clean_xss_attributes($_POST['mb_nick'])) : '';
    $mb_email = isset($_POST['mb_email']) ? strip_tags(clean_xss_attributes($_POST['mb_email'])) : '';
    $mb_hp = isset($_POST['mb_hp']) ? strip_tags(clean_xss_attributes($_POST['mb_hp'])) : '';
    $mb_zip = isset($_POST['mb_zip']) ? strip_tags(clean_xss_attributes($_POST['mb_zip'])) : '';
    $mb_addr1 = isset($_POST['mb_addr1']) ? strip_tags(clean_xss_attributes($_POST['mb_addr1'])) : '';
    $mb_addr2 = isset($_POST['mb_addr2']) ? strip_tags(clean_xss_attributes($_POST['mb_addr2'])) : '';
    $mb_addr3 = isset($_POST['mb_addr3']) ? strip_tags(clean_xss_attributes($_POST['mb_addr3'])) : '';
    $mb_memo = isset($_POST['mb_memo']) ? strip_tags(clean_xss_attributes($_POST['mb_memo'])) : '';
    
    // 필수값 체크
    if (!$mb_name) {
        alert('이름을 입력해주세요.');
    }
    
    if (!$mb_email) {
        alert('이메일을 입력해주세요.');
    }
    
    // 이메일 형식 체크
    if (!filter_var($mb_email, FILTER_VALIDATE_EMAIL)) {
        alert('올바른 이메일 형식이 아닙니다.');
    }
    
    // 닉네임 중복 체크 (본인 제외)
    if ($mb_nick) {
        $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
                WHERE mb_nick = '$mb_nick' AND mb_id != '$mb_id'";
        $row = sql_fetch($sql);
        if ($row['cnt'] > 0) {
            alert('이미 사용 중인 닉네임입니다.');
        }
    }
    
    // 이메일 중복 체크 (본인 제외)
    $sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} 
            WHERE mb_email = '$mb_email' AND mb_id != '$mb_id'";
    $row = sql_fetch($sql);
    if ($row['cnt'] > 0) {
        alert('이미 사용 중인 이메일입니다.');
    }
    
    // 비밀번호 변경 (입력된 경우만)
    $sql_password = "";
    if ($_POST['mb_password'] && strlen($_POST['mb_password']) >= 4) {
        $sql_password = ", mb_password = '".get_encrypt_string($_POST['mb_password'])."' ";
    } elseif ($_POST['mb_password'] && strlen($_POST['mb_password']) < 4) {
        alert('비밀번호는 4자 이상 입력해주세요.');
    }
    // 추천인 변경 (최고관리자만 가능)
	$sql_recommend = "";
	if ($is_admin && isset($_POST['mb_recommend'])) {
		$new_recommend = trim($_POST['mb_recommend']);
		
		// 추천인 유효성 검사
		if ($new_recommend) {
			// 자기 자신 체크
			if ($mb_id == $new_recommend) {
				alert('자기 자신을 추천인으로 설정할 수 없습니다.');
			}
			
			// 추천인 존재 및 권한 확인
			$rec_sql = "SELECT mb_id, mb_grade FROM {$g5['member_table']} WHERE mb_id = '{$new_recommend}'";
			$rec_info = sql_fetch($rec_sql);
			
			if (!$rec_info) {
				alert('존재하지 않는 추천인입니다.');
			}
			
			if ($rec_info['mb_grade'] < 2) {
				alert('해당 회원은 하부조직 관리 권한이 없습니다. (2등급 이상만 가능)');
			}
			
			// 순환 참조 체크
			if ($rec_info['mb_recommend'] == $mb_id) {
				alert('순환 참조가 발생합니다. (상호 추천 불가)');
			}
		}
		
		$sql_recommend = ", mb_recommend = '{$new_recommend}' ";
	}
	// 회원 정보 업데이트 SQL 수정
	$sql = "UPDATE {$g5['member_table']} 
			SET mb_name = '{$mb_name}',
				mb_nick = '{$mb_nick}',
				mb_email = '{$mb_email}',
				mb_hp = '{$mb_hp}',
				mb_zip1 = '',
				mb_zip2 = '',
				mb_addr1 = '{$mb_addr1}',
				mb_addr2 = '{$mb_addr2}',
				mb_addr3 = '{$mb_addr3}',
				mb_memo = '{$mb_memo}'
				{$sql_password}
				{$sql_recommend}
			WHERE mb_id = '{$mb_id}'";
    
    // 쿼리 실행
    sql_query($sql);
    
    // 성공 메시지
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
    
    <!-- Daum 우편번호 서비스 -->
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    
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
        max-width: 100%;
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
        width: 48px;
        height: 48px;
        background: #3b82f6;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 600;
    }
    
    .cmk-me-member-details h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .cmk-me-member-details p {
        color: #6b7280;
        font-size: 14px;
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
    }
    
    .cmk-me-section:last-child {
        margin-bottom: 0;
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
        color: #3b82f6;
    }
    
    /* 입력 필드 */
    .cmk-me-form-group {
        margin-bottom: 16px;
    }
    
    .cmk-me-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .cmk-me-label .required {
        color: #ef4444;
    }
    
    .cmk-me-input-wrap {
        position: relative;
    }
    
    .cmk-me-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #3b82f6;
        font-size: 18px;
    }
    
    .cmk-me-input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .cmk-me-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .cmk-me-input[readonly] {
        background: #f9fafb;
        cursor: not-allowed;
    }
    
    /* 주소 검색 */
    .cmk-me-btn-address {
        margin-top: 8px;
        padding: 8px 16px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .cmk-me-btn-address:hover {
        background: #e5e7eb;
    }
    
    /* 텍스트에어리어 */
    .cmk-me-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        min-height: 100px;
        resize: vertical;
        transition: all 0.2s;
    }
    
    .cmk-me-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* 정보 박스 */
    .cmk-me-info-box {
        padding: 12px 16px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        font-size: 14px;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
    
    /* 버튼 영역 */
    .cmk-me-actions {
        margin-top: 32px;
        display: flex;
        gap: 12px;
    }
    
    .cmk-me-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .cmk-me-btn-primary {
        background: #3b82f6;
        color: white;
    }
    
    .cmk-me-btn-primary:hover {
        background: #2563eb;
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
            justify-content: center;
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
                    <?php echo mb_substr($mb['mb_name'], 0, 1); ?>
                </div>
                <div class="cmk-me-member-details">
                    <h2><?php echo get_text($mb['mb_name']); ?> (<?php echo $mb['mb_id']; ?>)</h2>
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
                    <p>회원의 기본 정보를 수정할 수 있습니다. 아이디는 변경할 수 없습니다.</p>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">아이디</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-person cmk-me-input-icon"></i>
                        <input type="text" class="cmk-me-input" value="<?php echo $mb['mb_id']; ?>" readonly>
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">이름 <span class="required">*</span></label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-person-badge cmk-me-input-icon"></i>
                        <input type="text" name="mb_name" id="mb_name" class="cmk-me-input" value="<?php echo get_text($mb['mb_name']); ?>" required>
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">닉네임</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-chat-square-text cmk-me-input-icon"></i>
                        <input type="text" name="mb_nick" id="mb_nick" class="cmk-me-input" value="<?php echo get_text($mb['mb_nick']); ?>">
                    </div>
                </div>
				<!-- 기본 정보 섹션에 추천인 필드 추가 (닉네임 필드 다음에 삽입) -->

				<?php if ($is_admin) { ?>
				<div class="cmk-me-form-group">
					<label class="cmk-me-label">추천인 <span style="color: #6b7280; font-weight: normal;">(최고관리자 전용)</span></label>
					<div class="cmk-me-input-wrap">
						<i class="bi bi-person-check cmk-me-input-icon"></i>
						<input type="text" name="mb_recommend" id="mb_recommend" class="cmk-me-input" 
							   value="<?php echo get_text($mb['mb_recommend']); ?>" 
							   placeholder="추천인 아이디 (비워두면 추천인 없음)">
					</div>
					<div style="margin-top: 8px; font-size: 13px; color: #6b7280;">
						<i class="bi bi-info-circle"></i> 
						2등급 이상의 회원만 추천인이 될 수 있습니다.
					</div>
				</div>
				<?php } else { ?>
				<div class="cmk-me-form-group">
					<label class="cmk-me-label">추천인</label>
					<div class="cmk-me-input-wrap">
						<i class="bi bi-person-check cmk-me-input-icon"></i>
						<input type="text" class="cmk-me-input" value="<?php echo get_text($mb['mb_recommend']); ?>" readonly>
					</div>
				</div>
				<?php } ?>
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">새 비밀번호</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-lock cmk-me-input-icon"></i>
                        <input type="password" name="mb_password" id="mb_password" class="cmk-me-input" placeholder="변경 시에만 입력 (4자 이상)">
                    </div>
                </div>
            </div>
            
            <!-- 연락처 정보 -->
            <div class="cmk-me-section">
                <h3 class="cmk-me-section-title">
                    <i class="bi bi-telephone"></i> 연락처 정보
                </h3>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">이메일 <span class="required">*</span></label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-envelope cmk-me-input-icon"></i>
                        <input type="email" name="mb_email" id="mb_email" class="cmk-me-input" value="<?php echo get_text($mb['mb_email']); ?>" required>
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">휴대폰</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-phone cmk-me-input-icon"></i>
                        <input type="tel" name="mb_hp" id="mb_hp" class="cmk-me-input" value="<?php echo get_text($mb['mb_hp']); ?>" placeholder="010-0000-0000">
                    </div>
                </div>
            </div>
            
            <!-- 주소 정보 -->
            <div class="cmk-me-section">
                <h3 class="cmk-me-section-title">
                    <i class="bi bi-geo-alt"></i> 주소 정보
                </h3>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">우편번호</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-mailbox cmk-me-input-icon"></i>
                        <input type="text" name="mb_zip" id="mb_zip" class="cmk-me-input" value="<?php echo $mb['mb_zip']; ?>" readonly>
                    </div>
                    <button type="button" onclick="execDaumPostcode()" class="cmk-me-btn-address">
                        <i class="bi bi-search"></i> 우편번호 찾기
                    </button>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">기본주소</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-house cmk-me-input-icon"></i>
                        <input type="text" name="mb_addr1" id="mb_addr1" class="cmk-me-input" value="<?php echo get_text($mb['mb_addr1']); ?>" readonly>
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">상세주소</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-geo cmk-me-input-icon"></i>
                        <input type="text" name="mb_addr2" id="mb_addr2" class="cmk-me-input" value="<?php echo get_text($mb['mb_addr2']); ?>" placeholder="상세주소를 입력하세요">
                    </div>
                </div>
                
                <div class="cmk-me-form-group">
                    <label class="cmk-me-label">참고항목</label>
                    <div class="cmk-me-input-wrap">
                        <i class="bi bi-signpost cmk-me-input-icon"></i>
                        <input type="text" name="mb_addr3" id="mb_addr3" class="cmk-me-input" value="<?php echo get_text($mb['mb_addr3']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <!-- 메모 -->
            <div class="cmk-me-section">
                <h3 class="cmk-me-section-title">
                    <i class="bi bi-pencil-square"></i> 관리자 메모
                </h3>
                
                <div class="cmk-me-form-group">
                    <textarea name="mb_memo" id="mb_memo" class="cmk-me-textarea" placeholder="회원에 대한 메모를 입력하세요"><?php echo get_text($mb['mb_memo']); ?></textarea>
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
// 폼 제출 전 유효성 검사
function fmember_submit(f) {
    // 비밀번호 체크
    if (f.mb_password.value) {
        if (f.mb_password.value.length < 4) {
            alert("비밀번호는 4자 이상 입력하세요.");
            f.mb_password.focus();
            return false;
        }
    }
    
    // 이름 체크
    if (!f.mb_name.value) {
        alert("이름을 입력하세요.");
        f.mb_name.focus();
        return false;
    }
    
    // 이메일 체크
    if (!f.mb_email.value) {
        alert("이메일을 입력하세요.");
        f.mb_email.focus();
        return false;
    }
    
    return confirm("회원 정보를 수정하시겠습니까?");
}

// 다음 우편번호 서비스
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            var addr = '';
            var extraAddr = '';

            if (data.userSelectedType === 'R') {
                addr = data.roadAddress;
            } else {
                addr = data.jibunAddress;
            }

            if(data.userSelectedType === 'R'){
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraAddr += data.bname;
                }
                if(data.buildingName !== '' && data.apartment === 'Y'){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                if(extraAddr !== ''){
                    extraAddr = ' (' + extraAddr + ')';
                }
                document.getElementById("mb_addr3").value = extraAddr;
            } else {
                document.getElementById("mb_addr3").value = '';
            }

            // 구버전 우편번호 처리 (3-3 형식)
            var zip = data.zonecode;
            if(zip.length == 5) {
                document.getElementById('mb_zip1').value = zip.substr(0, 3);
                document.getElementById('mb_zip2').value = zip.substr(3, 2);
            }
            
            document.getElementById("mb_addr1").value = addr;
            document.getElementById("mb_addr2").focus();
        }
    }).open();
}
</script>

</body>
</html>

<?php
include_once('/footer.php');
?>
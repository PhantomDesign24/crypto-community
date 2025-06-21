<?php
/*
 * 파일명: login.skin.php
 * 위치: /theme/사용테마/skin/member/basic/
 * 기능: 로그인 페이지 스킨 (회원가입 모달 포함)
 * 작성일: 2025-01-23
 */

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - 코인 마케팅 대행사</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
    /* ===================================
     * 로그인 페이지 전용 스타일
     * =================================== */
    
    /* 전역 리셋 */
    .cmk-login-wrap * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .cmk-login-wrap {
        font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    /* 로그인 컨테이너 */
    .cmk-login-container {
        width: 100%;
        max-width: 440px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    /* 헤더 */
    .cmk-login-header {
        background: #f8f9fa;
        padding: 40px;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .cmk-login-logo {
        font-size: 48px;
        color: #667eea;
        margin-bottom: 16px;
    }
    
    .cmk-login-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .cmk-login-header p {
        font-size: 14px;
        color: #6b7280;
    }
    
    /* 폼 래퍼 */
    .cmk-login-form-wrapper {
        padding: 40px;
    }
    
    /* 폼 그룹 */
    .cmk-login-form-group {
        margin-bottom: 20px;
    }
    
    /* 라벨 */
    .cmk-login-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    /* 인풋 그룹 */
    .cmk-login-input-group {
        position: relative;
    }
    
    .cmk-login-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 18px;
    }
    
    /* 인풋 필드 */
    .cmk-login-input {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
        background: #f9fafb;
    }
    
    .cmk-login-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .cmk-login-input::placeholder {
        color: #9ca3af;
    }
    
    /* 옵션 영역 */
    .cmk-login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    /* 체크박스 */
    .cmk-login-checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cmk-login-checkbox {
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .cmk-login-checkbox:checked {
        background: #667eea;
        border-color: #667eea;
    }
    
    .cmk-login-checkbox-label {
        font-size: 14px;
        color: #4b5563;
        cursor: pointer;
    }
    
    /* 링크 */
    .cmk-login-link {
        font-size: 14px;
        color: #667eea;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .cmk-login-link:hover {
        color: #5a67d8;
        text-decoration: underline;
    }
    
    /* 버튼 */
    .cmk-login-btn {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .cmk-login-btn-primary {
        background: #667eea;
        color: white;
        margin-bottom: 12px;
    }
    
    .cmk-login-btn-primary:hover {
        background: #5a67d8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .cmk-login-btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }
    
    .cmk-login-btn-secondary:hover {
        background: #d1d5db;
    }
    
    /* 구분선 */
    .cmk-login-divider {
        text-align: center;
        margin: 24px 0;
        position: relative;
    }
    
    .cmk-login-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e5e7eb;
    }
    
    .cmk-login-divider span {
        position: relative;
        background: white;
        padding: 0 16px;
        font-size: 13px;
        color: #9ca3af;
    }
    
    /* 하단 링크 */
    .cmk-login-footer {
        text-align: center;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }
    
    .cmk-login-footer p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
    }
    
    /* 에러 메시지 */
    .cmk-login-error {
        background: #fee2e2;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* ===================================
     * 회원가입 모달
     * =================================== */
    
    /* 모달 오버레이 */
    .cmk-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        padding: 20px;
        overflow-y: auto;
    }
    
    .cmk-modal-overlay.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* 모달 컨테이너 */
    .cmk-modal-container {
        width: 100%;
        max-width: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* 모달 헤더 */
    .cmk-modal-header {
        background: #f8f9fa;
        padding: 30px;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
    }
    
    .cmk-modal-header h2 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .cmk-modal-header p {
        font-size: 14px;
        color: #6b7280;
    }
    
    /* 모달 닫기 버튼 */
    .cmk-modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 36px;
        height: 36px;
        border: none;
        background: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }
    
    .cmk-modal-close:hover {
        background: #f3f4f6;
        transform: rotate(90deg);
    }
    
    .cmk-modal-close i {
        font-size: 20px;
        color: #6b7280;
    }
    
    /* 모달 컨텐츠 */
    .cmk-modal-content {
        padding: 30px;
    }
    
    /* 성공 메시지 */
    .cmk-success-message {
        display: none;
        text-align: center;
        padding: 40px;
    }
    
    .cmk-success-icon {
        width: 80px;
        height: 80px;
        background: #d1fae5;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .cmk-success-icon i {
        font-size: 40px;
        color: #10b981;
    }
    
    .cmk-success-message h3 {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .cmk-success-message p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 20px;
    }
    
    /* 반응형 */
    @media (max-width: 480px) {
        .cmk-login-header {
            padding: 30px 20px;
        }
        
        .cmk-login-form-wrapper {
            padding: 30px 20px;
        }
        
        .cmk-login-options {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }
        
        .cmk-modal-header {
            padding: 20px;
        }
        
        .cmk-modal-content {
            padding: 20px;
        }
    }
    </style>
</head>
<body>

<!-- ===================================
     로그인 폼
     =================================== -->
<div class="cmk-login-wrap">
    <div class="cmk-login-container">
        <!-- 헤더 -->
        <div class="cmk-login-header">
            <div class="cmk-login-logo">
                <i class="bi bi-currency-bitcoin"></i>
            </div>
            <h1>로그인</h1>
            <p>코인 마케팅 대행사에 오신 것을 환영합니다</p>
        </div>
        
        <!-- 폼 래퍼 -->
        <div class="cmk-login-form-wrapper">
            <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
            <input type="hidden" name="url" value="<?php echo $login_url ?>">
            
            <?php if ($msg) { ?>
            <div class="cmk-login-error">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo $msg; ?>
            </div>
            <?php } ?>
            
            <!-- 아이디 -->
            <div class="cmk-login-form-group">
                <label for="login_id" class="cmk-login-label">아이디</label>
                <div class="cmk-login-input-group">
                    <i class="bi bi-person cmk-login-input-icon"></i>
                    <input type="text" name="mb_id" id="login_id" required class="cmk-login-input" placeholder="아이디를 입력하세요" maxlength="20">
                </div>
            </div>
            
            <!-- 비밀번호 -->
            <div class="cmk-login-form-group">
                <label for="login_pw" class="cmk-login-label">비밀번호</label>
                <div class="cmk-login-input-group">
                    <i class="bi bi-lock cmk-login-input-icon"></i>
                    <input type="password" name="mb_password" id="login_pw" required class="cmk-login-input" placeholder="비밀번호를 입력하세요" maxlength="20">
                </div>
            </div>
            
            <!-- 옵션 -->
            <div class="cmk-login-options">
                <div class="cmk-login-checkbox-group">
                    <input type="checkbox" name="auto_login" id="login_auto_login" class="cmk-login-checkbox">
                    <label for="login_auto_login" class="cmk-login-checkbox-label">자동로그인</label>
                </div>
                <a href="<?php echo G5_BBS_URL ?>/password_lost.php" class="cmk-login-link">비밀번호 찾기</a>
            </div>
            
            <!-- 로그인 버튼 -->
            <button type="submit" class="cmk-login-btn cmk-login-btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> 로그인
            </button>
            
            <!-- 회원가입 버튼 -->
            <button type="button" class="cmk-login-btn cmk-login-btn-secondary" onclick="showRegisterModal()">
                <i class="bi bi-person-plus"></i> 회원가입
            </button>
            
            </form>
            
            <!-- 하단 링크 -->
            <div class="cmk-login-footer">
                <p>아직 회원이 아니신가요?</p>
                <a href="#" onclick="showRegisterModal(); return false;" class="cmk-login-link">
                    지금 바로 회원가입하세요
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ===================================
     회원가입 모달
     =================================== -->
<div class="cmk-modal-overlay" id="registerModal">
    <div class="cmk-modal-container">
        <!-- 모달 헤더 -->
        <div class="cmk-modal-header">
            <button type="button" class="cmk-modal-close" onclick="hideRegisterModal()">
                <i class="bi bi-x-lg"></i>
            </button>
            <h2>간편 회원가입</h2>
            <p>필수 정보만 입력하여 빠르게 가입하세요</p>
        </div>
        
        <!-- 모달 컨텐츠 -->
        <div class="cmk-modal-content">
            <!-- 회원가입 폼 -->
            <form id="registerForm" onsubmit="return submitRegister(event);">
                <!-- 추천 코드 -->
                <div class="cmk-login-form-group">
                    <label for="reg_referral_code" class="cmk-login-label">추천 코드 <span style="color: #ef4444;">*</span></label>
                    <div class="cmk-login-input-group">
                        <i class="bi bi-gift cmk-login-input-icon"></i>
                        <input type="text" name="mb_referral_code" id="reg_referral_code" required class="cmk-login-input" placeholder="8자리 추천 코드" maxlength="8" style="text-transform: uppercase;">
                    </div>
                    <div id="referral_msg" style="margin-top: 6px; font-size: 13px; display: none;"></div>
                </div>
                
                <!-- 아이디 -->
                <div class="cmk-login-form-group">
                    <label for="reg_mb_id" class="cmk-login-label">아이디 <span style="color: #ef4444;">*</span></label>
                    <div class="cmk-login-input-group">
                        <i class="bi bi-person cmk-login-input-icon"></i>
                        <input type="text" name="mb_id" id="reg_mb_id" required class="cmk-login-input" placeholder="영문 소문자, 숫자, _ (3~20자)" maxlength="20">
                    </div>
                </div>
                
                <!-- 비밀번호 -->
                <div class="cmk-login-form-group">
                    <label for="reg_mb_password" class="cmk-login-label">비밀번호 <span style="color: #ef4444;">*</span></label>
                    <div class="cmk-login-input-group">
                        <i class="bi bi-lock cmk-login-input-icon"></i>
                        <input type="password" name="mb_password" id="reg_mb_password" required class="cmk-login-input" placeholder="4자 이상" maxlength="20">
                    </div>
                </div>
                
                <!-- 이름 -->
                <div class="cmk-login-form-group">
                    <label for="reg_mb_name" class="cmk-login-label">이름 <span style="color: #ef4444;">*</span></label>
                    <div class="cmk-login-input-group">
                        <i class="bi bi-person-badge cmk-login-input-icon"></i>
                        <input type="text" name="mb_name" id="reg_mb_name" required class="cmk-login-input" placeholder="실명을 입력하세요">
                    </div>
                </div>
                
                <!-- 이메일 -->
                <div class="cmk-login-form-group">
                    <label for="reg_mb_email" class="cmk-login-label">이메일 <span style="color: #ef4444;">*</span></label>
                    <div class="cmk-login-input-group">
                        <i class="bi bi-envelope cmk-login-input-icon"></i>
                        <input type="email" name="mb_email" id="reg_mb_email" required class="cmk-login-input" placeholder="이메일 주소">
                    </div>
                </div>
                
                <!-- 휴대폰 -->
                <div class="cmk-login-form-group">
                    <label for="reg_mb_hp" class="cmk-login-label">휴대폰 번호 <span style="color: #ef4444;">*</span></label>
                    <div class="cmk-login-input-group">
                        <i class="bi bi-phone cmk-login-input-icon"></i>
                        <input type="tel" name="mb_hp" id="reg_mb_hp" required class="cmk-login-input" placeholder="- 없이 숫자만 입력">
                    </div>
                </div>
                
                <!-- 가입 버튼 -->
                <button type="submit" class="cmk-login-btn cmk-login-btn-primary" id="registerSubmitBtn">
                    <i class="bi bi-check-lg"></i> 회원가입
                </button>
            </form>
            
            <!-- 성공 메시지 -->
            <div class="cmk-success-message" id="successMessage">
                <div class="cmk-success-icon">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3>회원가입 완료!</h3>
                <p id="successText">회원가입이 성공적으로 완료되었습니다.</p>
                <button type="button" class="cmk-login-btn cmk-login-btn-primary" onclick="location.reload();">
                    <i class="bi bi-box-arrow-in-right"></i> 로그인하기
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// 자동로그인 확인
$("#login_auto_login").click(function(){
    if (this.checked) {
        this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
    }
});

// 로그인 폼 제출
function flogin_submit(f)
{
    if( $( document.body ).triggerHandler( 'login_sumit', [f, 'flogin'] ) !== false ){
        return true;
    }
    return false;
}

// 회원가입 모달 표시
function showRegisterModal() {
    document.getElementById('registerModal').classList.add('active');
    document.getElementById('registerForm').reset();
    document.getElementById('registerForm').style.display = 'block';
    document.getElementById('successMessage').style.display = 'none';
}

// 회원가입 모달 숨기기
function hideRegisterModal() {
    document.getElementById('registerModal').classList.remove('active');
}

// 추천 코드 자동 대문자 변환
document.getElementById('reg_referral_code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// 추천 코드 확인
document.getElementById('reg_referral_code').addEventListener('blur', function() {
    const code = this.value;
    const msgDiv = document.getElementById('referral_msg');
    
    if (code.length === 0) {
        msgDiv.style.display = 'none';
        return;
    }
    
    if (code.length !== 8) {
        msgDiv.style.color = '#ef4444';
        msgDiv.textContent = '추천 코드는 8자리여야 합니다.';
        msgDiv.style.display = 'block';
        return;
    }
    
    // AJAX로 추천 코드 확인
    $.post('<?php echo G5_BBS_URL; ?>/ajax.mb_referral_code.php', {
        referral_code: code
    }, function(response) {
        msgDiv.style.display = 'block';
        if (response.indexOf('유효한') > -1) {
            msgDiv.style.color = '#10b981';
            msgDiv.innerHTML = response;
        } else {
            msgDiv.style.color = '#ef4444';
            msgDiv.innerHTML = response;
        }
    });
});

// 회원가입 폼 제출
function submitRegister(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('registerSubmitBtn');
    
    // 버튼 비활성화
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 처리중...';
    
    // AJAX 요청
    $.ajax({
        url: '<?php echo G5_BBS_URL; ?>/simple_register.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // 성공 메시지 표시
                document.getElementById('registerForm').style.display = 'none';
                document.getElementById('successMessage').style.display = 'block';
                document.getElementById('successText').innerHTML = 
                    response.data.mb_name + '님, 회원가입이 완료되었습니다.<br>' +
                    '추천인: ' + response.data.recommender_name;
            } else {
                alert(response.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> 회원가입';
            }
        },
        error: function() {
            alert('회원가입 처리 중 오류가 발생했습니다.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> 회원가입';
        }
    });
}

// 모달 외부 클릭 시 닫기
document.getElementById('registerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRegisterModal();
    }
});
</script>

</body>
</html>
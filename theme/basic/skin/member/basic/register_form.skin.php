<?php
/*
 * 파일명: register_form.skin.php
 * 위치: /theme/사용테마/skin/member/basic/
 * 기능: 회원가입 폼 - 추천 코드 필드 추가
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
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
    /* ===================================
     * 회원가입 폼 전용 스타일
     * =================================== */
    
    /* 전역 리셋 */
    .cmk-register-wrap * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .cmk-register-wrap {
        font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        background: #f8f9fa;
        min-height: 100vh;
        padding: 40px 20px;
    }
    
    /* 메인 컨테이너 */
    .cmk-reg-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    /* 헤더 */
    .cmk-reg-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        text-align: center;
    }
    
    .cmk-reg-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .cmk-reg-header p {
        font-size: 16px;
        opacity: 0.9;
    }
    
    /* 폼 래퍼 */
    .cmk-reg-form-wrapper {
        padding: 40px;
    }
    
    /* 섹션 */
    .cmk-reg-section {
        margin-bottom: 40px;
    }
    
    .cmk-reg-section:last-child {
        margin-bottom: 0;
    }
    
    .cmk-reg-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cmk-reg-section-title i {
        color: #6b7280;
    }
    
    /* 폼 그룹 */
    .cmk-reg-form-group {
        margin-bottom: 24px;
    }
    
    .cmk-reg-form-group:last-child {
        margin-bottom: 0;
    }
    
    /* 라벨 */
    .cmk-reg-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .cmk-reg-required {
        color: #ef4444;
        font-weight: normal;
    }
    
    /* 인풋 그룹 */
    .cmk-reg-input-group {
        position: relative;
        display: flex;
        align-items: stretch;
    }
    
    .cmk-reg-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 18px;
        z-index: 1;
    }
    
    /* 인풋 필드 */
    .cmk-reg-input {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
        background: #f9fafb;
    }
    
    .cmk-reg-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .cmk-reg-input::placeholder {
        color: #9ca3af;
    }
    
    /* 버튼이 있는 인풋 그룹 */
    .cmk-reg-input-with-btn {
        display: flex;
        gap: 8px;
    }
    
    .cmk-reg-input-with-btn .cmk-reg-input {
        flex: 1;
    }
    
    /* 도움말 텍스트 */
    .cmk-reg-help-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .cmk-reg-help-text i {
        font-size: 12px;
    }
    
    /* 메시지 */
    .cmk-reg-message {
        margin-top: 8px;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        display: none;
    }
    
    .cmk-reg-message.cmk-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .cmk-reg-message.cmk-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    /* 버튼 */
    .cmk-reg-btn {
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
    }
    
    .cmk-reg-btn-primary {
        background: #667eea;
        color: white;
    }
    
    .cmk-reg-btn-primary:hover {
        background: #5a67d8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .cmk-reg-btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }
    
    .cmk-reg-btn-secondary:hover {
        background: #d1d5db;
    }
    
    .cmk-reg-btn-small {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    /* 체크박스 */
    .cmk-reg-checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .cmk-reg-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 2px;
    }
    
    .cmk-reg-checkbox:checked {
        background: #667eea;
        border-color: #667eea;
    }
    
    .cmk-reg-checkbox-label {
        flex: 1;
        font-size: 14px;
        color: #4b5563;
        cursor: pointer;
        line-height: 1.6;
    }
    
    /* 폼 액션 */
    .cmk-reg-form-actions {
        display: flex;
        gap: 12px;
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid #e5e7eb;
    }
    
    .cmk-reg-form-actions .cmk-reg-btn {
        flex: 1;
    }
    
    /* 주소 검색 */
    .cmk-reg-address-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    /* 파일 업로드 */
    .cmk-reg-file-input {
        display: none;
    }
    
    .cmk-reg-file-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .cmk-reg-file-label:hover {
        background: #e5e7eb;
    }
    
    /* 추천 코드 특별 스타일 */
    .cmk-reg-referral-input {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }
    
    /* 캡차 */
    .cmk-reg-captcha {
        background: #f9fafb;
        padding: 20px;
        border-radius: 10px;
        margin-top: 24px;
    }
    
    /* 반응형 */
    @media (max-width: 640px) {
        .cmk-register-wrap {
            padding: 20px 10px;
        }
        
        .cmk-reg-header {
            padding: 30px 20px;
        }
        
        .cmk-reg-header h1 {
            font-size: 24px;
        }
        
        .cmk-reg-form-wrapper {
            padding: 30px 20px;
        }
        
        .cmk-reg-form-actions {
            flex-direction: column;
        }
        
        .cmk-reg-input-with-btn {
            flex-direction: column;
        }
    }
    </style>
</head>
<body>

<!-- ===================================
     회원가입 폼 시작
     =================================== -->
<div class="cmk-register-wrap">
    <div class="cmk-reg-container">
        <!-- 헤더 -->
        <div class="cmk-reg-header">
            <h1>회원가입</h1>
            <p>코인 마케팅 대행사에 오신 것을 환영합니다</p>
        </div>
        
        <!-- 폼 래퍼 -->
        <div class="cmk-reg-form-wrapper">
            <form id="fregisterform" name="fregisterform" action="<?php echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="w" value="<?php echo $w ?>">
            <input type="hidden" name="url" value="<?php echo $urlencode ?>">
            <input type="hidden" name="agree" value="<?php echo $agree ?>">
            <input type="hidden" name="agree2" value="<?php echo $agree2 ?>">
            <input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
            <input type="hidden" name="cert_no" value="">
            <?php if (isset($member['mb_sex'])) {  ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php }  ?>
            <?php if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))) { // 닉네임수정일이 지나지 않았다면  ?>
            <input type="hidden" name="mb_nick_default" value="<?php echo get_text($member['mb_nick']) ?>">
            <input type="hidden" name="mb_nick" value="<?php echo get_text($member['mb_nick']) ?>">
            <?php }  ?>
            
            <!-- 사이트 이용정보 -->
            <div class="cmk-reg-section">
                <h2 class="cmk-reg-section-title">
                    <i class="bi bi-shield-lock"></i>
                    사이트 이용정보
                </h2>
                
                <!-- 아이디 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_id" class="cmk-reg-label">
                        아이디 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-person cmk-reg-input-icon"></i>
                        <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" <?php echo $required ?> <?php echo $readonly ?> class="cmk-reg-input <?php echo $required ?> <?php echo $readonly ?>" minlength="3" maxlength="20" placeholder="아이디를 입력하세요">
                    </div>
                    <div id="msg_mb_id" class="cmk-reg-message"></div>
                    <div class="cmk-reg-help-text">
                        <i class="bi bi-info-circle"></i>
                        영문자, 숫자, _ 만 입력 가능. 최소 3자이상
                    </div>
                </div>
                
                <!-- 추천 코드 (필수) -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_referral_code" class="cmk-reg-label">
                        추천 코드 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-gift cmk-reg-input-icon"></i>
                        <input type="text" name="mb_referral_code" id="reg_mb_referral_code" required class="cmk-reg-input cmk-reg-referral-input required" maxlength="8" placeholder="추천 코드를 입력하세요 (필수)">
                    </div>
                    <div id="msg_mb_referral" class="cmk-reg-message"></div>
                    <div class="cmk-reg-help-text">
                        <i class="bi bi-info-circle"></i>
                        추천인에게 받은 8자리 추천 코드를 입력하세요
                    </div>
                </div>
                
                <!-- 비밀번호 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_password" class="cmk-reg-label">
                        비밀번호 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-lock cmk-reg-input-icon"></i>
                        <input type="password" name="mb_password" id="reg_mb_password" <?php echo $required ?> class="cmk-reg-input <?php echo $required ?>" minlength="3" maxlength="20" placeholder="비밀번호를 입력하세요">
                    </div>
                    <div class="cmk-reg-help-text">
                        <i class="bi bi-info-circle"></i>
                        영문자, 숫자, 특수문자 조합 최소 3자이상
                    </div>
                </div>
                
                <!-- 비밀번호 확인 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_password_re" class="cmk-reg-label">
                        비밀번호 확인 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-lock-fill cmk-reg-input-icon"></i>
                        <input type="password" name="mb_password_re" id="reg_mb_password_re" <?php echo $required ?> class="cmk-reg-input <?php echo $required ?>" minlength="3" maxlength="20" placeholder="비밀번호를 다시 입력하세요">
                    </div>
                </div>
            </div>
            
            <!-- 개인정보 -->
            <div class="cmk-reg-section">
                <h2 class="cmk-reg-section-title">
                    <i class="bi bi-person-badge"></i>
                    개인정보
                </h2>
                
                <!-- 이름 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_name" class="cmk-reg-label">
                        이름 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-person-badge cmk-reg-input-icon"></i>
                        <input type="text" id="reg_mb_name" name="mb_name" value="<?php echo get_text($member['mb_name']) ?>" <?php echo $required ?> <?php echo $readonly; ?> class="cmk-reg-input <?php echo $required ?> <?php echo $readonly ?>" placeholder="실명을 입력하세요">
                    </div>
                    <?php if($config['cf_cert_use']) { ?>
                    <div class="cmk-reg-help-text">
                        <i class="bi bi-info-circle"></i>
                        본인확인 후에는 이름이 자동 입력되며 수정할 수 없습니다
                    </div>
                    <?php } ?>
                </div>
                
                <?php if ($req_nick) { ?>
                <!-- 닉네임 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_nick" class="cmk-reg-label">
                        닉네임 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-chat-dots cmk-reg-input-icon"></i>
                        <input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>">
                        <input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>" id="reg_mb_nick" <?php echo $required ?> class="cmk-reg-input <?php echo $required ?>" maxlength="20" placeholder="닉네임을 입력하세요">
                    </div>
                    <div id="msg_mb_nick" class="cmk-reg-message"></div>
                    <div class="cmk-reg-help-text">
                        <i class="bi bi-info-circle"></i>
                        공백없이 한글,영문,숫자만 입력 가능 (한글2자, 영문4자 이상)
                    </div>
                </div>
                <?php } ?>
                
                <!-- 이메일 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_email" class="cmk-reg-label">
                        이메일 <span class="cmk-reg-required">*</span>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-envelope cmk-reg-input-icon"></i>
                        <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
                        <input type="email" name="mb_email" value="<?php echo isset($member['mb_email'])?$member['mb_email']:''; ?>" id="reg_mb_email" <?php echo $required ?> class="cmk-reg-input <?php echo $required ?>" size="70" maxlength="100" placeholder="이메일 주소를 입력하세요">
                    </div>
                    <div id="msg_mb_email" class="cmk-reg-message"></div>
                    <?php if ($config['cf_use_email_certify']) { ?>
                    <div class="cmk-reg-help-text">
                        <i class="bi bi-info-circle"></i>
                        <?php if ($w=='') { echo "이메일로 발송된 내용을 확인한 후 인증하셔야 회원가입이 완료됩니다."; } ?>
                        <?php if ($w=='u') { echo "이메일 주소를 변경하시면 다시 인증하셔야 합니다."; } ?>
                    </div>
                    <?php } ?>
                </div>
                
                <?php if ($config['cf_use_hp']) { ?>
                <!-- 휴대폰 -->
                <div class="cmk-reg-form-group">
                    <label for="reg_mb_hp" class="cmk-reg-label">
                        휴대폰번호 <?php if ($req_hp) echo '<span class="cmk-reg-required">*</span>'; ?>
                    </label>
                    <div class="cmk-reg-input-group">
                        <i class="bi bi-phone cmk-reg-input-icon"></i>
                        <input type="text" name="mb_hp" value="<?php echo get_text($member['mb_hp']) ?>" id="reg_mb_hp" <?php echo ($req_hp)?"required":""; ?> class="cmk-reg-input <?php echo ($req_hp)?"required":""; ?>" maxlength="20" placeholder="휴대폰 번호를 입력하세요">
                    </div>
                    <div id="msg_mb_hp" class="cmk-reg-message"></div>
                </div>
                <?php } ?>
                
                <?php if ($config['cf_use_addr']) { ?>
                <!-- 주소 -->
                <div class="cmk-reg-form-group">
                    <label class="cmk-reg-label">
                        주소 <?php if ($req_addr) echo '<span class="cmk-reg-required">*</span>'; ?>
                    </label>
                    <div class="cmk-reg-address-group">
                        <div class="cmk-reg-input-with-btn">
                            <div class="cmk-reg-input-group" style="flex: 1;">
                                <i class="bi bi-geo-alt cmk-reg-input-icon"></i>
                                <input type="text" name="mb_zip" value="<?php echo $member['mb_zip1'].$member['mb_zip2'] ?>" id="reg_mb_zip" <?php echo $req_addr?"required":""; ?> class="cmk-reg-input <?php echo $req_addr?"required":""; ?>" size="5" maxlength="6" placeholder="우편번호">
                            </div>
                            <button type="button" class="cmk-reg-btn cmk-reg-btn-secondary cmk-reg-btn-small" onclick="win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">
                                <i class="bi bi-search"></i> 주소 검색
                            </button>
                        </div>
                        
                        <div class="cmk-reg-input-group">
                            <i class="bi bi-house cmk-reg-input-icon"></i>
                            <input type="text" name="mb_addr1" value="<?php echo get_text($member['mb_addr1']) ?>" id="reg_mb_addr1" <?php echo $req_addr?"required":""; ?> class="cmk-reg-input <?php echo $req_addr?"required":""; ?>" placeholder="기본주소">
                        </div>
                        
                        <div class="cmk-reg-input-group">
                            <i class="bi bi-house-door cmk-reg-input-icon"></i>
                            <input type="text" name="mb_addr2" value="<?php echo get_text($member['mb_addr2']) ?>" id="reg_mb_addr2" class="cmk-reg-input" placeholder="상세주소">
                        </div>
                        
                        <div class="cmk-reg-input-group">
                            <i class="bi bi-signpost cmk-reg-input-icon"></i>
                            <input type="text" name="mb_addr3" value="<?php echo get_text($member['mb_addr3']) ?>" id="reg_mb_addr3" class="cmk-reg-input" readonly="readonly" placeholder="참고항목">
                        </div>
                        <input type="hidden" name="mb_addr_jibeon" value="<?php echo get_text($member['mb_addr_jibeon']); ?>">
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <!-- 기타 설정 -->
            <div class="cmk-reg-section">
                <h2 class="cmk-reg-section-title">
                    <i class="bi bi-gear"></i>
                    기타 설정
                </h2>
                
                <!-- 정보공개 -->
                <div class="cmk-reg-form-group">
                    <div class="cmk-reg-checkbox-group">
                        <input type="checkbox" name="mb_open" value="1" id="reg_mb_open" class="cmk-reg-checkbox" <?php echo ($w=='' || $member['mb_open'])?'checked':''; ?>>
                        <label for="reg_mb_open" class="cmk-reg-checkbox-label">
                            다른분들이 나의 정보를 볼 수 있도록 합니다.
                        </label>
                    </div>
                </div>
                
                <!-- 메일링 서비스 -->
                <div class="cmk-reg-form-group">
                    <div class="cmk-reg-checkbox-group">
                        <input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" class="cmk-reg-checkbox" <?php echo ($w=='' || $member['mb_mailling'])?'checked':''; ?>>
                        <label for="reg_mb_mailling" class="cmk-reg-checkbox-label">
                            정보 메일을 받겠습니다.
                        </label>
                    </div>
                </div>
                
                <?php if ($config['cf_use_hp']) { ?>
                <!-- SMS 수신 -->
                <div class="cmk-reg-form-group">
                    <div class="cmk-reg-checkbox-group">
                        <input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" class="cmk-reg-checkbox" <?php echo ($w=='' || $member['mb_sms'])?'checked':''; ?>>
                        <label for="reg_mb_sms" class="cmk-reg-checkbox-label">
                            휴대폰 문자메세지를 받겠습니다.
                        </label>
                    </div>
                </div>
                <?php } ?>
            </div>
            
    
            
            <!-- 버튼 -->
            <div class="cmk-reg-form-actions">
                <a href="<?php echo G5_URL ?>" class="cmk-reg-btn cmk-reg-btn-secondary">
                    <i class="bi bi-x-lg"></i> 취소
                </a>
                <button type="submit" id="btn_submit" class="cmk-reg-btn cmk-reg-btn-primary" accesskey="s">
                    <i class="bi bi-check-lg"></i> <?php echo $w==''?'회원가입':'정보수정'; ?>
                </button>
            </div>
            
            </form>
        </div>
    </div>
</div>

<script>
$(function() {
    // 아이디 중복검사
    $("#reg_mb_id").blur(function() {
        var mb_id = $(this).val();
        if (mb_id.length < 3) {
            $("#msg_mb_id").removeClass('cmk-success').addClass('cmk-error').html("아이디는 최소 3자 이상 입력하세요.").show();
            return;
        }
        
        $.post(
            <?php echo G5_BBS_URL;?>+"/ajax.mb_id.php",
            { mb_id: mb_id },
            function(data) {
                $("#msg_mb_id").html(data).show();
                if (data.indexOf("사용하셔도") > -1) {
                    $("#msg_mb_id").removeClass('cmk-error').addClass('cmk-success');
                } else {
                    $("#msg_mb_id").removeClass('cmk-success').addClass('cmk-error');
                }
            }
        );
    });
    
    // 추천 코드 확인
    $("#reg_mb_referral_code").blur(function() {
        var referral_code = $(this).val().toUpperCase();
        $(this).val(referral_code);
        
        if (referral_code.length === 0) {
            $("#msg_mb_referral").hide();
            return;
        }
        
        if (referral_code.length !== 8) {
            $("#msg_mb_referral").removeClass('cmk-success').addClass('cmk-error').html("추천 코드는 8자리입니다.").show();
            return;
        }
        
        $.post(
            <?php echo G5_BBS_URL;?>+"/ajax.mb_referral_code.php",
            { referral_code: referral_code },
            function(data) {
                $("#msg_mb_referral").html(data).show();
                if (data.indexOf("유효한") > -1) {
                    $("#msg_mb_referral").removeClass('cmk-error').addClass('cmk-success');
                } else {
                    $("#msg_mb_referral").removeClass('cmk-success').addClass('cmk-error');
                }
            }
        );
    });
    
    // 닉네임 중복검사
    $("#reg_mb_nick").blur(function() {
        var mb_nick = $(this).val();
        if (mb_nick.length < 2) {
            $("#msg_mb_nick").removeClass('cmk-success').addClass('cmk-error').html("닉네임은 최소 2자 이상 입력하세요.").show();
            return;
        }
        
        $.post(
            <?php echo G5_BBS_URL;?>+"/ajax.mb_nick.php",
            { mb_nick: mb_nick, mb_id: $("#reg_mb_id").val() },
            function(data) {
                $("#msg_mb_nick").html(data).show();
                if (data.indexOf("사용하셔도") > -1) {
                    $("#msg_mb_nick").removeClass('cmk-error').addClass('cmk-success');
                } else {
                    $("#msg_mb_nick").removeClass('cmk-success').addClass('cmk-error');
                }
            }
        );
    });
    
    // E-mail 중복검사
    $("#reg_mb_email").blur(function() {
        var mb_email = $(this).val();
        if (!mb_email || !check_email(mb_email)) {
            $("#msg_mb_email").removeClass('cmk-success').addClass('cmk-error').html("올바른 이메일 주소를 입력하세요.").show();
            return;
        }
        
        $.post(
            <?php echo G5_BBS_URL;?>+"/ajax.mb_email.php",
            { mb_email: mb_email, mb_id: $("#reg_mb_id").val() },
            function(data) {
                $("#msg_mb_email").html(data).show();
                if (data.indexOf("사용하셔도") > -1) {
                    $("#msg_mb_email").removeClass('cmk-error').addClass('cmk-success');
                } else {
                    $("#msg_mb_email").removeClass('cmk-success').addClass('cmk-error');
                }
            }
        );
    });
    
    // 추천 코드 자동 대문자 변환
    $("#reg_mb_referral_code").on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});

// submit 최종 폼체크
function fregisterform_submit(f)
{
    // 회원아이디 검사
    if (f.w.value == "") {
        var msg = reg_mb_id_check();
        if (msg) {
            alert(msg);
            f.mb_id.select();
            return false;
        }
    }

    if (f.w.value == "") {
        if (f.mb_password.value.length < 3) {
            alert("비밀번호를 3글자 이상 입력하십시오.");
            f.mb_password.focus();
            return false;
        }
    }

    if (f.mb_password.value != f.mb_password_re.value) {
        alert("비밀번호가 같지 않습니다.");
        f.mb_password_re.focus();
        return false;
    }

    if (f.mb_password.value.length > 0) {
        if (f.mb_password_re.value.length < 3) {
            alert("비밀번호를 3글자 이상 입력하십시오.");
            f.mb_password_re.focus();
            return false;
        }
    }

    // 이름 검사
    if (f.w.value=="") {
        if (f.mb_name.value.length < 1) {
            alert("이름을 입력하십시오.");
            f.mb_name.focus();
            return false;
        }
    }

    <?php if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
    // 본인확인 체크
    if(f.cert_no.value=="") {
        alert("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
        return false;
    }
    <?php } ?>

    // 닉네임 검사
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
        var msg = reg_mb_nick_check();
        if (msg) {
            alert(msg);
            f.mb_nick.select();
            return false;
        }
    }

    // E-mail 검사
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
        var msg = reg_mb_email_check();
        if (msg) {
            alert(msg);
            f.mb_email.select();
            return false;
        }
    }

    <?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {  ?>
    // 휴대폰번호 체크
    var msg = reg_mb_hp_check();
    if (msg) {
        alert(msg);
        f.mb_hp.select();
        return false;
    }
    <?php } ?>

    // 추천 코드 체크 (필수)
    if (!f.mb_referral_code.value) {
        alert("추천 코드를 입력하세요.");
        f.mb_referral_code.focus();
        return false;
    }
    
    if (f.mb_referral_code.value.length !== 8) {
        alert("추천 코드는 8자리여야 합니다.");
        f.mb_referral_code.focus();
        return false;
    }
    
    var msg = check_mb_referral_code();
    if (msg) {
        alert(msg);
        f.mb_referral_code.select();
        return false;
    }

    <?php echo chk_captcha_js();  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

function check_mb_referral_code() {
    var result = "";
    $.ajax({
        type: "POST",
        url: <?php echo G5_BBS_URL;?>+"/ajax.mb_referral_code.php",
        data: {
            referral_code: $("#reg_mb_referral_code").val()
        },
        async: false,
        cache: false,
        success: function(data, textStatus) {
            if (data.indexOf("존재하지 않는") > -1 || data.indexOf("유효하지 않은") > -1) {
                result = data.replace(/<[^>]*>/g, '');
            }
        }
    });
    return result;
}

function check_email(email) {
    var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regex.test(email);
}
</script>

</body>
</html>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
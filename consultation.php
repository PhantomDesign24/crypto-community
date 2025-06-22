<?php
/*
 * 파일명: consultation.php
 * 위치: /
 * 기능: 상담신청 페이지
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 페이지 제목 */
$g5['title'] = '상담신청';

/* 로그인 체크 (옵션) */
// if (!$member['mb_id']) {
//     alert('로그인 후 이용하세요.', G5_BBS_URL.'/login.php');
// }

include_once('./_head.php');
?>

<!-- ===================================
     상담신청 페이지 스타일
     =================================== -->
<style>
/* 컨테이너 */
.consultation-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 페이지 헤더 */
.consultation-header {
    text-align: center;
    margin-bottom: 50px;
}

.consultation-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
}

.consultation-header p {
    font-size: 18px;
    color: #6b7280;
    line-height: 1.6;
}

/* 폼 래퍼 */
.consultation-form-wrapper {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 40px;
}

/* 섹션 */
.form-section {
    margin-bottom: 40px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: #3b82f6;
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-label .required {
    color: #ef4444;
}

/* 입력 필드 */
.input-group {
    display: flex;
    align-items: center;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s;
}

.input-group:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-group-text {
    padding: 12px 14px;
    background: #fff;
    border-right: 1px solid #e5e7eb;
}

.input-group-text i {
    color: #3b82f6;
    font-size: 18px;
}

.form-control {
    flex: 1;
    padding: 12px 14px;
    border: none;
    font-size: 14px;
    outline: none;
}

.form-control::placeholder {
    color: #9ca3af;
}

/* 텍스트에어리어 */
.form-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    min-height: 120px;
    resize: vertical;
    outline: none;
    transition: all 0.2s;
}

.form-textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 라디오/체크박스 */
.form-check-group {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-check input[type="radio"],
.form-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-check label {
    font-size: 14px;
    color: #374151;
    cursor: pointer;
}

/* 셀렉트 박스 */
.form-select {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    cursor: pointer;
    transition: all 0.2s;
}

.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 정보 박스 */
.info-box {
    padding: 16px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box i {
    color: #3b82f6;
    margin-right: 8px;
}

.info-box p {
    color: #1e40af;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

/* 개인정보 동의 */
.privacy-agreement {
    padding: 20px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 20px;
}

.privacy-content {
    max-height: 200px;
    overflow-y: auto;
    padding: 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 16px;
    font-size: 13px;
    line-height: 1.6;
    color: #4b5563;
}

/* 버튼 */
.form-actions {
    margin-top: 40px;
    text-align: center;
}

.btn-submit {
    padding: 14px 32px;
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-submit:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-submit:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

/* 반응형 */
@media (max-width: 768px) {
    .consultation-form-wrapper {
        padding: 24px 16px;
    }
    
    .form-check-group {
        flex-direction: column;
        gap: 12px;
    }
}
</style>

<!-- ===================================
     상담신청 폼
     =================================== -->
<div class="consultation-container">
    <!-- 페이지 헤더 -->
    <div class="consultation-header">
        <h1>상담신청</h1>
        <p>전문 상담사가 신속하고 정확하게 답변해드립니다.<br>
        궁금하신 사항을 자세히 작성해주세요.</p>
    </div>
    
    <!-- 폼 영역 -->
    <div class="consultation-form-wrapper">
        <form id="consultationForm" method="post" action="./consultation_update.php" onsubmit="return validateForm(this);">
            
            <!-- 기본 정보 섹션 -->
            <div class="form-section">
                <h2 class="section-title">
                    <i class="bi bi-person-lines-fill"></i> 기본 정보
                </h2>
                
                <div class="info-box">
                    <p><i class="bi bi-info-circle"></i> 정확한 상담을 위해 연락처를 정확히 입력해주세요.</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">이름 <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="cs_name" class="form-control border-start-0" 
                               placeholder="성함을 입력하세요" required
                               value="<?php echo $member['mb_name'] ? get_text($member['mb_name']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">연락처 <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="tel" name="cs_hp" class="form-control border-start-0" 
                               placeholder="010-0000-0000" required
                               pattern="[0-9]{3}-[0-9]{4}-[0-9]{4}"
                               value="<?php echo $member['mb_hp'] ? get_text($member['mb_hp']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">이메일</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="cs_email" class="form-control border-start-0" 
                               placeholder="example@email.com"
                               value="<?php echo $member['mb_email'] ? get_text($member['mb_email']) : ''; ?>">
                    </div>
                </div>
            </div>
            
            <!-- 상담 내용 섹션 -->
            <div class="form-section">
                <h2 class="section-title">
                    <i class="bi bi-chat-left-dots"></i> 상담 내용
                </h2>
                
                <div class="form-group">
                    <label class="form-label">상담 분야 <span class="required">*</span></label>
                    <select name="cs_category" class="form-select" required>
                        <option value="">선택하세요</option>
                        <option value="투자상담">투자 상담</option>
                        <option value="계좌개설">계좌 개설</option>
                        <option value="기술지원">기술 지원</option>
                        <option value="입출금">입출금 문의</option>
                        <option value="기타">기타</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">상담 희망 시간</label>
                    <div class="form-check-group">
                        <div class="form-check">
                            <input type="radio" name="cs_time" id="time1" value="오전" checked>
                            <label for="time1">오전 (09:00 ~ 12:00)</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="cs_time" id="time2" value="오후">
                            <label for="time2">오후 (13:00 ~ 18:00)</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="cs_time" id="time3" value="상관없음">
                            <label for="time3">상관없음</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">제목 <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-pencil"></i>
                        </span>
                        <input type="text" name="cs_subject" class="form-control border-start-0" 
                               placeholder="상담 제목을 입력하세요" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">상담 내용 <span class="required">*</span></label>
                    <textarea name="cs_content" class="form-textarea" 
                              placeholder="상담하실 내용을 자세히 작성해주세요." required></textarea>
                </div>
            </div>
            
            <!-- 개인정보 동의 섹션 -->
            <div class="form-section">
                <h2 class="section-title">
                    <i class="bi bi-shield-check"></i> 개인정보 수집 및 이용 동의
                </h2>
                
                <div class="privacy-agreement">
                    <div class="privacy-content">
                        <strong>1. 개인정보 수집목적</strong><br>
                        - 상담 신청 접수 및 상담 진행<br>
                        - 상담 결과 안내 및 사후 관리<br><br>
                        
                        <strong>2. 수집하는 개인정보 항목</strong><br>
                        - 필수항목: 이름, 연락처<br>
                        - 선택항목: 이메일<br><br>
                        
                        <strong>3. 개인정보 보유 및 이용기간</strong><br>
                        - 상담 완료 후 1년간 보관<br>
                        - 관련 법령에 따라 보존 필요시 해당 기간 동안 보관<br><br>
                        
                        <strong>4. 개인정보 수집 동의 거부 권리</strong><br>
                        - 개인정보 수집 동의를 거부할 수 있으며, 거부 시 상담 신청이 제한됩니다.
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" name="privacy_agree" id="privacy_agree" value="1" required>
                        <label for="privacy_agree">개인정보 수집 및 이용에 동의합니다. <span class="required">*</span></label>
                    </div>
                </div>
            </div>
            
            <!-- 버튼 영역 -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-send"></i> 상담 신청하기
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
// 폼 유효성 검사
function validateForm(f) {
    // 이름 체크
    if (!f.cs_name.value.trim()) {
        alert("이름을 입력해주세요.");
        f.cs_name.focus();
        return false;
    }
    
    // 연락처 체크
    if (!f.cs_hp.value.trim()) {
        alert("연락처를 입력해주세요.");
        f.cs_hp.focus();
        return false;
    }
    
    // 연락처 형식 체크
    var hp_pattern = /^[0-9]{3}-[0-9]{4}-[0-9]{4}$/;
    if (!hp_pattern.test(f.cs_hp.value)) {
        alert("연락처를 올바른 형식으로 입력해주세요. (예: 010-0000-0000)");
        f.cs_hp.focus();
        return false;
    }
    
    // 상담 분야 체크
    if (!f.cs_category.value) {
        alert("상담 분야를 선택해주세요.");
        f.cs_category.focus();
        return false;
    }
    
    // 제목 체크
    if (!f.cs_subject.value.trim()) {
        alert("제목을 입력해주세요.");
        f.cs_subject.focus();
        return false;
    }
    
    // 내용 체크
    if (!f.cs_content.value.trim()) {
        alert("상담 내용을 입력해주세요.");
        f.cs_content.focus();
        return false;
    }
    
    // 개인정보 동의 체크
    if (!f.privacy_agree.checked) {
        alert("개인정보 수집 및 이용에 동의해주세요.");
        return false;
    }
    
    return confirm("상담을 신청하시겠습니까?");
}

// 연락처 자동 하이픈
document.querySelector('input[name="cs_hp"]').addEventListener('input', function(e) {
    var value = e.target.value.replace(/[^0-9]/g, '');
    var formatted = '';
    
    if (value.length <= 3) {
        formatted = value;
    } else if (value.length <= 7) {
        formatted = value.slice(0, 3) + '-' + value.slice(3);
    } else {
        formatted = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
    }
    
    e.target.value = formatted;
});
</script>

<?php
include_once('./_tail.php');
?>
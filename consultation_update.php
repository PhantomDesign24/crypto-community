<?php
/*
 * 파일명: consultation_update.php
 * 위치: /
 * 기능: 상담신청 처리
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 접근 체크
// ===================================

/* POST 요청만 허용 */
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    alert('잘못된 접근입니다.');
}

// ===================================
// 입력값 받기
// ===================================

$cs_name = isset($_POST['cs_name']) ? trim($_POST['cs_name']) : '';
$cs_hp = isset($_POST['cs_hp']) ? trim($_POST['cs_hp']) : '';
$cs_email = isset($_POST['cs_email']) ? trim($_POST['cs_email']) : '';
$cs_category = isset($_POST['cs_category']) ? trim($_POST['cs_category']) : '';
$cs_time = isset($_POST['cs_time']) ? trim($_POST['cs_time']) : '';
$cs_subject = isset($_POST['cs_subject']) ? trim($_POST['cs_subject']) : '';
$cs_content = isset($_POST['cs_content']) ? trim($_POST['cs_content']) : '';
$privacy_agree = isset($_POST['privacy_agree']) ? $_POST['privacy_agree'] : '';

// ===================================
// 유효성 검사
// ===================================

if (!$cs_name) {
    alert('이름을 입력해주세요.');
}

if (!$cs_hp) {
    alert('연락처를 입력해주세요.');
}

if (!$cs_category) {
    alert('상담 분야를 선택해주세요.');
}

if (!$cs_subject) {
    alert('제목을 입력해주세요.');
}

if (!$cs_content) {
    alert('상담 내용을 입력해주세요.');
}

if (!$privacy_agree) {
    alert('개인정보 수집 및 이용에 동의해주세요.');
}

// ===================================
// 데이터베이스 처리
// ===================================

/* 테이블이 없는 경우 생성 */
$sql = "CREATE TABLE IF NOT EXISTS g5_consultation (
    cs_id INT NOT NULL AUTO_INCREMENT,
    cs_name VARCHAR(50) NOT NULL,
    cs_hp VARCHAR(20) NOT NULL,
    cs_email VARCHAR(100),
    cs_category VARCHAR(50) NOT NULL,
    cs_time VARCHAR(20),
    cs_subject VARCHAR(255) NOT NULL,
    cs_content TEXT NOT NULL,
    cs_status VARCHAR(20) DEFAULT '접수',
    cs_datetime DATETIME NOT NULL,
    cs_ip VARCHAR(50),
    mb_id VARCHAR(50),
    PRIMARY KEY (cs_id),
    KEY idx_datetime (cs_datetime),
    KEY idx_status (cs_status)
)";
sql_query($sql);

/* 상담 내용 저장 */
$sql = "INSERT INTO g5_consultation SET
        cs_name = '".sql_real_escape_string($cs_name)."',
        cs_hp = '".sql_real_escape_string($cs_hp)."',
        cs_email = '".sql_real_escape_string($cs_email)."',
        cs_category = '".sql_real_escape_string($cs_category)."',
        cs_time = '".sql_real_escape_string($cs_time)."',
        cs_subject = '".sql_real_escape_string($cs_subject)."',
        cs_content = '".sql_real_escape_string($cs_content)."',
        cs_status = '접수',
        cs_datetime = '".G5_TIME_YMDHIS."',
        cs_ip = '".sql_real_escape_string($_SERVER['REMOTE_ADDR'])."',
        mb_id = '".$member['mb_id']."'";

if (sql_query($sql)) {
    // ===================================
    // 메일 발송 (관리자)
    // ===================================
    
    if ($config['cf_email_use'] && $config['cf_admin_email']) {
        include_once(G5_LIB_PATH.'/mailer.lib.php');
        
        $subject = '[상담신청] '.$cs_subject;
        
        $content = '<div style="padding:20px;">';
        $content .= '<h2>상담 신청이 접수되었습니다.</h2>';
        $content .= '<table style="width:100%; border-collapse:collapse; margin-top:20px;">';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">이름</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.$cs_name.'</td></tr>';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">연락처</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.$cs_hp.'</td></tr>';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">이메일</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.$cs_email.'</td></tr>';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">상담분야</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.$cs_category.'</td></tr>';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">희망시간</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.$cs_time.'</td></tr>';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">제목</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.$cs_subject.'</td></tr>';
        $content .= '<tr><td style="padding:10px; border:1px solid #ddd; background:#f5f5f5;">내용</td>';
        $content .= '<td style="padding:10px; border:1px solid #ddd;">'.nl2br($cs_content).'</td></tr>';
        $content .= '</table>';
        $content .= '<p style="margin-top:20px;">접수일시: '.G5_TIME_YMDHIS.'</p>';
        $content .= '</div>';
        
        mailer($cs_name, $cs_email, $config['cf_admin_email'], $subject, $content, 1);
    }
    
    // ===================================
    // 고객 확인 메일 발송
    // ===================================
    
    if ($config['cf_email_use'] && $cs_email) {
        $subject = '[상담접수완료] '.$cs_subject;
        
        $content = '<div style="padding:20px;">';
        $content .= '<h2>'.$cs_name.'님, 상담 신청이 정상적으로 접수되었습니다.</h2>';
        $content .= '<p>빠른 시일 내에 담당자가 연락드리겠습니다.</p>';
        $content .= '<div style="margin-top:30px; padding:20px; background:#f5f5f5; border-radius:8px;">';
        $content .= '<h3>신청 내용</h3>';
        $content .= '<p><strong>상담분야:</strong> '.$cs_category.'</p>';
        $content .= '<p><strong>희망시간:</strong> '.$cs_time.'</p>';
        $content .= '<p><strong>제목:</strong> '.$cs_subject.'</p>';
        $content .= '<p><strong>접수일시:</strong> '.G5_TIME_YMDHIS.'</p>';
        $content .= '</div>';
        $content .= '<p style="margin-top:30px; color:#666;">본 메일은 발신 전용입니다. 문의사항은 고객센터로 연락주세요.</p>';
        $content .= '</div>';
        
        mailer($config['cf_title'], $config['cf_admin_email'], $cs_email, $subject, $content, 1);
    }
    
    alert('상담 신청이 완료되었습니다.\\n\\n빠른 시일 내에 연락드리겠습니다.', G5_URL);
    
} else {
    alert('상담 신청 중 오류가 발생했습니다.');
}
?>
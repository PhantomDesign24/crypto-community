<?php
/*
 * 파일명: ajax.mb_referral_code.php
 * 위치: /bbs/
 * 기능: 추천 코드 유효성 확인 (AJAX)
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 입력값 받기
// ===================================

$referral_code = isset($_POST['referral_code']) ? strtoupper(trim($_POST['referral_code'])) : '';

// ===================================
// 유효성 검사
// ===================================

if (!$referral_code) {
    die('추천 코드를 입력해주세요.');
}

if (strlen($referral_code) !== 8) {
    die('추천 코드는 8자리여야 합니다.');
}

if (!preg_match("/^[A-Z0-9]+$/", $referral_code)) {
    die('추천 코드는 영문 대문자와 숫자만 입력 가능합니다.');
}

// ===================================
// 추천 코드 확인
// ===================================

$sql = "SELECT mb_id, mb_name, mb_grade FROM {$g5['member_table']} 
        WHERE mb_referral_code = '{$referral_code}'";
$row = sql_fetch($sql);

if (!$row['mb_id']) {
    die('존재하지 않는 추천 코드입니다.');
}

// 추천인 등급 확인 (일반회원은 추천인이 될 수 없음)
if ($row['mb_grade'] < 2) {
    die('유효하지 않은 추천 코드입니다.');
}

// 성공 메시지 (HTML 태그 없이 텍스트만)
echo '유효한 추천 코드입니다. (추천인: ' . get_text($row['mb_name']) . ')';
?>
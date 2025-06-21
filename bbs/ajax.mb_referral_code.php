<?php
/*
 * 파일명: ajax.mb_referral_code.php
 * 위치: /bbs/
 * 기능: 추천 코드 확인 AJAX 처리
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

/* 변수 초기화 */
$referral_code = isset($_POST['referral_code']) ? strtoupper(trim($_POST['referral_code'])) : '';

// ===================================
// 유효성 검사
// ===================================

/* 추천 코드 길이 체크 */
if (strlen($referral_code) !== 8) {
    die('추천 코드는 8자리여야 합니다.');
}

/* 추천 코드 형식 체크 */
if (!preg_match("/^[A-Z0-9]+$/", $referral_code)) {
    die('추천 코드는 영문 대문자와 숫자만 가능합니다.');
}

// ===================================
// 데이터베이스 확인
// ===================================

/* 추천 코드로 회원 조회 */
$sql = "SELECT mb_id, mb_name, mb_grade FROM {$g5['member_table']} 
        WHERE mb_referral_code = '{$referral_code}'";
$row = sql_fetch($sql);

if (!$row['mb_id']) {
    die('<span class="cmk-error">존재하지 않는 추천 코드입니다.</span>');
}

/* 추천인 등급 확인 (일반회원은 추천인이 될 수 없음) */
if ($row['mb_grade'] < 2) {
    die('<span class="cmk-error">유효하지 않은 추천 코드입니다.</span>');
}

// ===================================
// 결과 출력
// ===================================

/* 유효한 추천 코드 */
$mb_name = get_text($row['mb_name']);
$grade_text = '';

switch($row['mb_grade']) {
    case 9:
        $grade_text = '총관리자';
        break;
    case 2:
        $grade_text = '하부조직';
        break;
}

echo '<span class="cmk-success">유효한 추천 코드입니다. (추천인: ' . $mb_name . ')</span>';
?>
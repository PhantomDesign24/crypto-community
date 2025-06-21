<?php
/*
 * 파일명: register_form_update.skin.php
 * 위치: /bbs/
 * 기능: 회원가입 시 추천 코드 처리
 * 작성일: 2025-01-23
 */

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// ===================================
// 추천 코드 처리
// ===================================

/* 추천 코드 확인 */
$referral_code = isset($_POST['mb_referral_code']) ? strtoupper(trim($_POST['mb_referral_code'])) : '';

if ($referral_code) {
    // 추천 코드 유효성 검사
    if (strlen($referral_code) !== 8) {
        alert('추천 코드는 8자리여야 합니다.');
    }
    
    if (!preg_match("/^[A-Z0-9]+$/", $referral_code)) {
        alert('추천 코드는 영문 대문자와 숫자만 가능합니다.');
    }
    
    // 추천 코드로 추천인 조회
    $sql = "SELECT mb_id, mb_grade FROM {$g5['member_table']} 
            WHERE mb_referral_code = '{$referral_code}'";
    $recommend_member = sql_fetch($sql);
    
    if (!$recommend_member['mb_id']) {
        alert('존재하지 않는 추천 코드입니다.');
    }
    
    // 추천인 등급 확인 (일반회원은 추천인이 될 수 없음)
    if ($recommend_member['mb_grade'] < 2) {
        alert('유효하지 않은 추천 코드입니다.');
    }
    
    // 자기 자신을 추천인으로 설정할 수 없음
    if ($w == 'u' && $recommend_member['mb_id'] == $mb_id) {
        alert('자기 자신을 추천인으로 설정할 수 없습니다.');
    }
    
    // 추천인 ID 설정
    $mb_recommend = $recommend_member['mb_id'];
}

// ===================================
// 회원 등급 설정
// ===================================

/* 신규 회원가입 시 기본 등급 설정 */
if ($w == '') {
    // 기본적으로 일반회원으로 가입
    $mb_grade = 1;
    
    // 추천인이 있는 경우 추천인 정보 저장
    if ($mb_recommend) {
        $sql_common .= " , mb_recommend = '{$mb_recommend}' ";
    }
    
    // 회원 등급 저장
    $sql_common .= " , mb_grade = '{$mb_grade}' ";
}

// ===================================
// 회원정보 수정 시 처리
// ===================================

/* 회원정보 수정 시 추천인 변경 불가 */
if ($w == 'u') {
    // 기존 추천인 정보는 변경하지 않음
    // 관리자만 회원 등급 변경 가능
    if (!$is_admin) {
        unset($_POST['mb_grade']);
        unset($_POST['mb_recommend']);
        unset($_POST['mb_referral_code']);
    }
}
?>
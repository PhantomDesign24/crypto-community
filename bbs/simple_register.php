<?php
/*
 * 파일명: simple_register.php
 * 위치: /bbs/
 * 기능: 간단한 회원가입 처리 (AJAX)
 * 작성일: 2025-01-23
 */

include_once('./_common.php');

// ===================================
// 초기 설정
// ===================================

header('Content-Type: application/json');

// 이미 로그인한 경우
if ($member['mb_id']) {
    die(json_encode(['success' => false, 'message' => '이미 로그인되어 있습니다.']));
}

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => '잘못된 접근입니다.']));
}

// ===================================
// 입력값 받기
// ===================================

$mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
$mb_password = isset($_POST['mb_password']) ? trim($_POST['mb_password']) : '';
$mb_name = isset($_POST['mb_name']) ? trim($_POST['mb_name']) : '';
$mb_email = isset($_POST['mb_email']) ? trim($_POST['mb_email']) : '';
$mb_hp = isset($_POST['mb_hp']) ? trim($_POST['mb_hp']) : '';
$mb_referral_code = isset($_POST['mb_referral_code']) ? strtoupper(trim($_POST['mb_referral_code'])) : '';

// ===================================
// 유효성 검사
// ===================================

// 필수값 체크
if (!$mb_id) {
    die(json_encode(['success' => false, 'message' => '아이디를 입력해주세요.']));
}

if (!$mb_password) {
    die(json_encode(['success' => false, 'message' => '비밀번호를 입력해주세요.']));
}

if (!$mb_name) {
    die(json_encode(['success' => false, 'message' => '이름을 입력해주세요.']));
}

if (!$mb_email) {
    die(json_encode(['success' => false, 'message' => '이메일을 입력해주세요.']));
}

if (!$mb_hp) {
    die(json_encode(['success' => false, 'message' => '휴대폰 번호를 입력해주세요.']));
}

if (!$mb_referral_code) {
    die(json_encode(['success' => false, 'message' => '추천 코드를 입력해주세요.']));
}

// 아이디 형식 체크
if (!preg_match("/^[a-z0-9_]{3,20}$/", $mb_id)) {
    die(json_encode(['success' => false, 'message' => '아이디는 영문 소문자, 숫자, _ 만 사용하여 3~20자로 입력해주세요.']));
}

// 비밀번호 길이 체크
if (strlen($mb_password) < 4) {
    die(json_encode(['success' => false, 'message' => '비밀번호는 4자 이상 입력해주세요.']));
}

// 이메일 형식 체크
if (!filter_var($mb_email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'message' => '올바른 이메일 주소를 입력해주세요.']));
}

// 추천 코드 형식 체크
if (strlen($mb_referral_code) !== 8 || !preg_match("/^[A-Z0-9]+$/", $mb_referral_code)) {
    die(json_encode(['success' => false, 'message' => '추천 코드는 8자리 영문 대문자와 숫자로 입력해주세요.']));
}

// ===================================
// 중복 체크
// ===================================

// 아이디 중복 체크
$sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}'";
$row = sql_fetch($sql);
if ($row['cnt'] > 0) {
    die(json_encode(['success' => false, 'message' => '이미 사용 중인 아이디입니다.']));
}

// 이메일 중복 체크
$sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_email = '{$mb_email}'";
$row = sql_fetch($sql);
if ($row['cnt'] > 0) {
    die(json_encode(['success' => false, 'message' => '이미 사용 중인 이메일입니다.']));
}

// ===================================
// 추천인 확인
// ===================================

// 추천 코드로 추천인 조회
$sql = "SELECT mb_id, mb_name, mb_grade FROM {$g5['member_table']} 
        WHERE mb_referral_code = '{$mb_referral_code}'";
$recommender = sql_fetch($sql);

if (!$recommender['mb_id']) {
    die(json_encode(['success' => false, 'message' => '존재하지 않는 추천 코드입니다.']));
}

// 추천인 등급 확인 (일반회원은 추천인이 될 수 없음)
if ($recommender['mb_grade'] < 2) {
    die(json_encode(['success' => false, 'message' => '유효하지 않은 추천 코드입니다.']));
}

// ===================================
// 회원 등록
// ===================================

// 비밀번호 암호화
$mb_password_encrypted = get_encrypt_string($mb_password);

// 닉네임 기본값 (이름과 동일)
$mb_nick = $mb_name;

// 현재 시간
$mb_datetime = G5_TIME_YMDHIS;
$mb_today_login = G5_TIME_YMDHIS;

// 회원 정보 삽입
$sql = "INSERT INTO {$g5['member_table']} SET
        mb_id = '{$mb_id}',
        mb_password = '{$mb_password_encrypted}',
        mb_name = '{$mb_name}',
        mb_nick = '{$mb_nick}',
        mb_email = '{$mb_email}',
        mb_hp = '{$mb_hp}',
        mb_datetime = '{$mb_datetime}',
        mb_today_login = '{$mb_today_login}',
        mb_ip = '".sql_real_escape_string($_SERVER['REMOTE_ADDR'])."',
        mb_level = '{$config['cf_register_level']}',
        mb_recommend = '{$recommender['mb_id']}',
        mb_grade = '1',
        mb_open = '1',
        mb_mailling = '1',
        mb_sms = '1',
        mb_email_certify = '".G5_TIME_YMDHIS."',
        mb_nick_date = '".sql_real_escape_string(date('Y-m-d', G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400)))."',
        mb_open_date = '".sql_real_escape_string(date('Y-m-d', G5_SERVER_TIME - ($config['cf_open_modify'] * 86400)))."'";

if (sql_query($sql)) {
    // 회원가입 포인트 지급
    if ($config['cf_register_point']) {
        insert_point($mb_id, $config['cf_register_point'], '회원가입 축하', '@member', $mb_id, '회원가입');
    }
    
    // 추천인에게 포인트 지급
    if ($config['cf_use_recommend'] && $recommender['mb_id'] && $config['cf_recommend_point']) {
        insert_point($recommender['mb_id'], $config['cf_recommend_point'], $mb_id.'님의 회원가입 추천', '@member', $recommender['mb_id'], $mb_id.' 추천');
    }
    
    // 자동 로그인
    set_session('ss_mb_id', $mb_id);
    set_session('ss_mb_key', md5($mb_datetime . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
    
    // 성공 응답
    echo json_encode([
        'success' => true, 
        'message' => '회원가입이 완료되었습니다.',
        'data' => [
            'mb_id' => $mb_id,
            'mb_name' => $mb_name,
            'recommender_name' => $recommender['mb_name']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => '회원가입 중 오류가 발생했습니다.']);
}
?>
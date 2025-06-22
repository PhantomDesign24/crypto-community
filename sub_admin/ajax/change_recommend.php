<?php
/*
 * 파일명: change_recommend.php
 * 위치: /sub_admin/ajax/change_recommend.php
 * 기능: 추천인 변경 처리 (최고관리자 전용)
 * 작성일: 2025-01-23
 */

include_once('../../common.php');

header('Content-Type: application/json');

// 최고관리자 체크
if (!$is_admin) {
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

$mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
$mb_recommend = isset($_POST['mb_recommend']) ? trim($_POST['mb_recommend']) : '';

if (!$mb_id) {
    echo json_encode(['success' => false, 'message' => '회원 아이디가 없습니다.']);
    exit;
}

// 대상 회원 확인
$sql = "SELECT mb_id FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}'";
$member_check = sql_fetch($sql);

if (!$member_check) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 회원입니다.']);
    exit;
}

// 추천인 확인 (입력된 경우)
if ($mb_recommend) {
    // 자기 자신 체크
    if ($mb_id == $mb_recommend) {
        echo json_encode(['success' => false, 'message' => '자기 자신을 추천인으로 설정할 수 없습니다.']);
        exit;
    }
    
    // 추천인 존재 확인
    $sql = "SELECT mb_id, mb_grade FROM {$g5['member_table']} WHERE mb_id = '{$mb_recommend}'";
    $recommend_check = sql_fetch($sql);
    
    if (!$recommend_check) {
        echo json_encode(['success' => false, 'message' => '존재하지 않는 추천인입니다.']);
        exit;
    }
    
    // 추천인이 하부조직 관리 권한이 있는지 확인 (2등급 이상)
    if ($recommend_check['mb_grade'] < 2) {
        echo json_encode(['success' => false, 'message' => '해당 회원은 하부조직 관리 권한이 없습니다.']);
        exit;
    }
    
    // 순환 참조 체크 (A가 B를 추천하고, B가 A를 추천하는 경우 방지)
    $sql = "SELECT mb_recommend FROM {$g5['member_table']} WHERE mb_id = '{$mb_recommend}'";
    $recommend_info = sql_fetch($sql);
    
    if ($recommend_info['mb_recommend'] == $mb_id) {
        echo json_encode(['success' => false, 'message' => '순환 참조가 발생합니다. (상호 추천 불가)']);
        exit;
    }
}

// 추천인 업데이트
$sql = "UPDATE {$g5['member_table']} 
        SET mb_recommend = '{$mb_recommend}' 
        WHERE mb_id = '{$mb_id}'";

if (sql_query($sql)) {
    // 로그 기록 (선택사항)
    $log_content = "추천인 변경: {$mb_id} → " . ($mb_recommend ? $mb_recommend : '없음');
    sql_query("INSERT INTO {$g5['board_new_table']} 
               SET bo_table = 'admin_log',
                   wr_id = 0,
                   mb_id = '{$member['mb_id']}',
                   bn_datetime = '".G5_TIME_YMDHIS."',
                   wr_subject = '{$log_content}'");
    
    echo json_encode(['success' => true, 'message' => '추천인이 변경되었습니다.']);
} else {
    echo json_encode(['success' => false, 'message' => '처리 중 오류가 발생했습니다.']);
}
?>
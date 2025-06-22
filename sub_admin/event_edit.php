<?php
/*
 * 파일명: event_edit.php
 * 위치: /sub_admin/ajax/event_edit.php
 * 기능: 이벤트 신청 정보 수정
 * 작성일: 2025-01-11
 */

include_once('../../common.php');

header('Content-Type: application/json');

// 로그인 및 권한 체크
if(!$member['mb_id'] || $member['mb_grade'] < 2) {
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

$ea_id = isset($_POST['ea_id']) ? (int)$_POST['ea_id'] : 0;
$wallet_address = isset($_POST['wallet_address']) ? trim($_POST['wallet_address']) : '';

if(!$ea_id || !$wallet_address) {
    echo json_encode(['success' => false, 'message' => '필수 정보가 누락되었습니다.']);
    exit;
}

// 신청 정보 확인
$apply = sql_fetch("SELECT * FROM g5_event_apply WHERE ea_id = '{$ea_id}'");
if(!$apply) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 신청입니다.']);
    exit;
}

// 이미 지급완료된 경우 수정 불가
if($apply['ea_status'] == 'paid') {
    echo json_encode(['success' => false, 'message' => '이미 지급 완료된 신청은 수정할 수 없습니다.']);
    exit;
}

// 관리 권한 확인
$managed_members = get_managed_members($member['mb_id']);
if(!in_array($apply['mb_id'], $managed_members) && $member['mb_id'] != $apply['mb_id'] && $member['mb_grade'] < 10) {
    echo json_encode(['success' => false, 'message' => '관리 권한이 없습니다.']);
    exit;
}

// 지갑 주소 업데이트
sql_query("UPDATE g5_event_apply SET 
          ea_wallet_address = '{$wallet_address}'
          WHERE ea_id = '{$ea_id}'");

// 수정 로그 남기기 (옵션)
$memo = "지갑주소 변경: " . date('Y-m-d H:i:s') . " by " . $member['mb_id'];
sql_query("UPDATE g5_event_apply SET 
          ea_memo = CONCAT(IFNULL(ea_memo, ''), '\n', '{$memo}')
          WHERE ea_id = '{$ea_id}'");

echo json_encode(['success' => true, 'message' => '수정되었습니다.']);

// 담당자가 관리하는 회원 목록
function get_managed_members($manager_id) {
    $members = array();
    
    // 1단계 추천인
    $sql = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
            WHERE mb_recommend = '{$manager_id}'";
    $result = sql_query($sql);
    while($row = sql_fetch_array($result)) {
        $members[] = $row['mb_id'];
        
        // 2단계 추천인
        $sql2 = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
                 WHERE mb_recommend = '{$row['mb_id']}'";
        $result2 = sql_query($sql2);
        while($row2 = sql_fetch_array($result2)) {
            $members[] = $row2['mb_id'];
        }
    }
    
    return array_unique($members);
}
?>
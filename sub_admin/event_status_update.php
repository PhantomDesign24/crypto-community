<?php
/*
 * 파일명: event_status_update.php
 * 위치: /sub_admin/event_status_update.php
 * 기능: 이벤트 신청 상태 변경 (AJAX)
 * 작성일: 2025-01-24
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

header('Content-Type: application/json');

// 권한 체크 - 최고관리자만 상태 변경 가능
if (!$is_admin) {
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

$ea_id = isset($_POST['ea_id']) ? (int)$_POST['ea_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$ea_id) {
    echo json_encode(['success' => false, 'message' => '잘못된 접근입니다.']);
    exit;
}

if (!in_array($status, ['applied', 'paid'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 상태값입니다.']);
    exit;
}

// 신청 정보 확인
$sql = "SELECT ea.*, e.ev_coin_symbol, e.ev_coin_amount 
        FROM g5_event_apply ea 
        LEFT JOIN g5_event e ON ea.ev_id = e.ev_id
        WHERE ea_id = '{$ea_id}'";
$apply = sql_fetch($sql);

if (!$apply['ea_id']) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 신청입니다.']);
    exit;
}

// 이미 같은 상태인 경우
if ($apply['ea_status'] == $status) {
    echo json_encode(['success' => true, 'message' => '이미 같은 상태입니다.']);
    exit;
}

// 상태를 지급완료로 변경하는 경우
if ($status == 'paid' && $apply['ea_status'] == 'applied') {
    // 코인 지급 처리
    if ($apply['ev_coin_amount'] > 0) {
        // 회원 정보
        $mb = get_member($apply['mb_id']);
        
        // 코인 지급
        $coin_field = 'mb_' . strtolower($apply['ev_coin_symbol']);
        $sql = "UPDATE {$g5['member_table']} 
                SET {$coin_field} = {$coin_field} + {$apply['ev_coin_amount']} 
                WHERE mb_id = '{$apply['mb_id']}'";
        sql_query($sql);
        
        // 코인 내역 기록
        $sql = "INSERT INTO g5_coin_history SET
                mb_id = '{$apply['mb_id']}',
                coin_type = '{$apply['ev_coin_symbol']}',
                amount = {$apply['ev_coin_amount']},
                balance = (SELECT {$coin_field} FROM {$g5['member_table']} WHERE mb_id = '{$apply['mb_id']}'),
                type = 'event',
                description = '이벤트 참여 보상',
                created_at = NOW()";
        sql_query($sql);
    }
}

// 상태를 대기중으로 변경하는 경우 (지급 취소)
if ($status == 'applied' && $apply['ea_status'] == 'paid') {
    // 코인 회수 처리
    if ($apply['ev_coin_amount'] > 0) {
        // 회원 정보
        $mb = get_member($apply['mb_id']);
        
        // 코인 차감
        $coin_field = 'mb_' . strtolower($apply['ev_coin_symbol']);
        $sql = "UPDATE {$g5['member_table']} 
                SET {$coin_field} = {$coin_field} - {$apply['ev_coin_amount']} 
                WHERE mb_id = '{$apply['mb_id']}' AND {$coin_field} >= {$apply['ev_coin_amount']}";
        $result = sql_query($sql);
        
        if(sql_affected_rows() == 0) {
            echo json_encode(['success' => false, 'message' => '코인 잔액이 부족하여 취소할 수 없습니다.']);
            exit;
        }
        
        // 코인 내역 기록
        $sql = "INSERT INTO g5_coin_history SET
                mb_id = '{$apply['mb_id']}',
                coin_type = '{$apply['ev_coin_symbol']}',
                amount = -{$apply['ev_coin_amount']},
                balance = (SELECT {$coin_field} FROM {$g5['member_table']} WHERE mb_id = '{$apply['mb_id']}'),
                type = 'event_cancel',
                description = '이벤트 보상 취소',
                created_at = NOW()";
        sql_query($sql);
    }
}

// 상태 업데이트
$sql = "UPDATE g5_event_apply 
        SET ea_status = '{$status}',
            ea_pay_datetime = " . ($status == 'paid' ? "NOW()" : "NULL") . "
        WHERE ea_id = '{$ea_id}'";
sql_query($sql);

// 이벤트 참여 수 업데이트
if ($status == 'paid' && $apply['ea_status'] == 'applied') {
    sql_query("UPDATE g5_event SET ev_apply_count = ev_apply_count + 1 WHERE ev_id = '{$apply['ev_id']}'");
} else if ($status == 'applied' && $apply['ea_status'] == 'paid') {
    sql_query("UPDATE g5_event SET ev_apply_count = ev_apply_count - 1 WHERE ev_id = '{$apply['ev_id']}'");
}

echo json_encode(['success' => true, 'message' => '상태가 변경되었습니다.']);
?>
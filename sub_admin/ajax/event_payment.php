<?php
/*
 * 파일명: event_payment.php
 * 위치: /sub_admin/ajax/event_payment.php
 * 기능: 이벤트 지급 처리 (지급/취소)
 * 작성일: 2025-01-11
 */

define('_GNUBOARD_', true);
include_once('../../common.php');

header('Content-Type: application/json');

// 로그인 및 권한 체크
if(!$member['mb_id'] || $member['mb_grade'] < 2) {
    die(json_encode(['success' => false, 'message' => '권한이 없습니다.']));
}

$ea_id = isset($_POST['ea_id']) ? (int)$_POST['ea_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'pay'; // pay 또는 cancel

if(!$ea_id) {
    die(json_encode(['success' => false, 'message' => '잘못된 요청입니다.']));
}

// 신청 정보 확인
$apply = sql_fetch("SELECT * FROM g5_event_apply WHERE ea_id = '{$ea_id}'");
if(!$apply) {
    die(json_encode(['success' => false, 'message' => '존재하지 않는 신청입니다.']));
}

// 관리 권한 확인
$managed_members = get_managed_members($member['mb_id']);
if(!in_array($apply['mb_id'], $managed_members) && $member['mb_grade'] < 10) {
    die(json_encode(['success' => false, 'message' => '관리 권한이 없습니다.']));
}

if($action == 'pay') {
    // 지급 완료 처리
    sql_query("UPDATE g5_event_apply SET 
              ea_status = 'paid', 
              ea_pay_datetime = NOW(),
              ea_pay_mb_id = '{$member['mb_id']}'
              WHERE ea_id = '{$ea_id}'");

    // 게시글 제목 변경
    if($apply['wr_id']) {
        $board_table = 'event_apply';
        sql_query("UPDATE {$g5['write_prefix']}{$board_table} SET 
                  wr_subject = REPLACE(wr_subject, '[신청완료]', '[지급완료]'),
                  wr_3 = 'paid'
                  WHERE wr_id = '{$apply['wr_id']}'");
    }

    // 전광판 업데이트
    $ticker_sql = "SELECT m.mb_nick, e.ev_coin_symbol, e.ev_coin_amount, e.ev_subject 
                   FROM g5_event_apply ea 
                   LEFT JOIN g5_member m ON ea.mb_id = m.mb_id 
                   LEFT JOIN g5_event e ON ea.ev_id = e.ev_id 
                   WHERE ea.ea_id = '{$ea_id}'";
    $ticker_data = sql_fetch($ticker_sql);
    
    if($ticker_data) {
        update_ticker_file($ticker_data);
    }
    
    die(json_encode(['success' => true, 'message' => '지급 완료 처리되었습니다.']));
    
} else if($action == 'cancel') {
    // 지급 취소 (대기 상태로 변경)
    sql_query("UPDATE g5_event_apply SET 
              ea_status = 'applied', 
              ea_pay_datetime = NULL,
              ea_pay_mb_id = NULL
              WHERE ea_id = '{$ea_id}'");

    // 게시글 제목 변경
    if($apply['wr_id']) {
        $board_table = 'event_apply';
        sql_query("UPDATE {$g5['write_prefix']}{$board_table}} SET 
                  wr_subject = REPLACE(wr_subject, '[지급완료]', '[신청완료]'),
                  wr_3 = 'applied'
                  WHERE wr_id = '{$apply['wr_id']}'");
    }
    
    die(json_encode(['success' => true, 'message' => '대기 상태로 변경되었습니다.']));
}

// 담당자가 관리하는 회원 목록
function get_managed_members($manager_id) {
    $members = array();
    
    $sql = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
            WHERE mb_recommend = '{$manager_id}'";
    $result = sql_query($sql);
    while($row = sql_fetch_array($result)) {
        $members[] = $row['mb_id'];
        
        $sql2 = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
                 WHERE mb_recommend = '{$row['mb_id']}'";
        $result2 = sql_query($sql2);
        while($row2 = sql_fetch_array($result2)) {
            $members[] = $row2['mb_id'];
        }
    }
    
    return array_unique($members);
}

// 전광판 업데이트 함수
function update_ticker_file($data) {
    $cache_dir = G5_DATA_PATH.'/cache';
    if(!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755);
        @chmod($cache_dir, 0755);
    }
    
    $ticker_file = $cache_dir.'/ticker_data.json';
    
    $ticker_items = array();
    if(file_exists($ticker_file)) {
        $json_data = file_get_contents($ticker_file);
        $ticker_items = json_decode($json_data, true);
        if(!is_array($ticker_items)) {
            $ticker_items = array();
        }
    }
    
    $nick = $data['mb_nick'];
    if(mb_strlen($nick) > 2) {
        $nick = mb_substr($nick, 0, 1) . '*' . mb_substr($nick, -1);
    }
    
    $new_item = array(
        'name' => $nick,
        'amount' => $data['ev_coin_amount'] . ' ' . $data['ev_coin_symbol'],
        'event' => $data['ev_subject'],
        'datetime' => G5_TIME_YMDHIS
    );
    
    array_unshift($ticker_items, $new_item);
    
    if(count($ticker_items) > 50) {
        $ticker_items = array_slice($ticker_items, 0, 50);
    }
    
    $json_data = json_encode($ticker_items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents($ticker_file, $json_data);
    @chmod($ticker_file, 0644);
}
?>
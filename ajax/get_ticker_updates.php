<?php
/*
 * 파일명: get_ticker_updates.php
 * 위치: /ajax/get_ticker_updates.php
 * 기능: 전광판 실시간 업데이트 데이터 제공
 * 작성일: 2025-01-22
 */

include_once('../common.php');

header('Content-Type: application/json');

// 마지막 업데이트 시간
$last_time = isset($_GET['last_time']) ? $_GET['last_time'] : date('Y-m-d H:i:s', strtotime('-1 hour'));

// 새로운 지급 내역 조회
$sql = "SELECT a.*, e.ev_subject, e.ev_coin_symbol, e.ev_coin_amount, m.mb_nick 
        FROM g5_event_apply a 
        JOIN g5_event e ON a.ev_id = e.ev_id 
        JOIN {$g5['member_table']} m ON a.mb_id = m.mb_id 
        WHERE a.ea_status = 'paid' 
        AND a.ea_pay_datetime > '{$last_time}'
        ORDER BY a.ea_pay_datetime DESC 
        LIMIT 10";

$result = sql_query($sql);
$items = array();
$new_last_time = $last_time;

while($row = sql_fetch_array($result)) {
    // 닉네임 마스킹
    $masked_nick = mb_substr($row['mb_nick'], 0, 1) . str_repeat('*', mb_strlen($row['mb_nick']) - 2) . mb_substr($row['mb_nick'], -1);
    
    $items[] = array(
        'name' => $masked_nick,
        'amount' => $row['ev_coin_amount'] . ' ' . $row['ev_coin_symbol'],
        'event' => $row['ev_subject'],
        'time' => $row['ea_pay_datetime']
    );
    
    // 가장 최근 시간 업데이트
    if($row['ea_pay_datetime'] > $new_last_time) {
        $new_last_time = $row['ea_pay_datetime'];
    }
}

// JSON 응답
echo json_encode(array(
    'success' => true,
    'items' => $items,
    'last_time' => $new_last_time
));
?>
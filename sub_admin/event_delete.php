<?php
/*
 * 파일명: event_delete.php
 * 위치: /sub_admin/event_delete.php
 * 기능: 이벤트 신청 개별 삭제 처리
 * 작성일: 2025-01-24
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// 권한 체크 - 최고관리자만 삭제 가능
if (!$is_admin) {
    alert('최고관리자만 삭제할 수 있습니다.');
}

$ea_id = isset($_GET['ea_id']) ? (int)$_GET['ea_id'] : 0;

if (!$ea_id) {
    alert('잘못된 접근입니다.');
}

// 신청 정보 확인
$sql = "SELECT * FROM g5_event_apply WHERE ea_id = '{$ea_id}'";
$ea = sql_fetch($sql);

if (!$ea['ea_id']) {
    alert('존재하지 않는 신청입니다.');
}

// 이미 승인된 신청인 경우 추가 확인
if ($ea['ea_status'] == 'paid') {
    alert('이미 승인 완료된 신청은 삭제할 수 없습니다.\n\n삭제가 필요한 경우 지급된 코인을 먼저 회수해야 합니다.');
}

// 신청 삭제
$sql = "DELETE FROM g5_event_apply WHERE ea_id = '{$ea_id}'";
sql_query($sql);

// 쿼리스트링 처리
$qstr = '';
if (isset($_GET['sfl'])) $qstr .= '&sfl='.$_GET['sfl'];
if (isset($_GET['stx'])) $qstr .= '&stx='.$_GET['stx'];
if (isset($_GET['status'])) $qstr .= '&status='.$_GET['status'];
if (isset($_GET['page'])) $qstr .= '&page='.$_GET['page'];

alert('삭제되었습니다.', './event_list.php?'.$qstr);
?>
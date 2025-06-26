<?php
/*
 * 파일명: event_list_update.php
 * 위치: /sub_admin/event_list_update.php
 * 기능: 이벤트 신청 일괄 처리 (선택 삭제)
 * 작성일: 2025-01-24
 */

define('_GNUBOARD_', true);
include_once('./_common.php');

// 권한 체크 - 최고관리자만 처리 가능
if (!$is_admin) {
    alert('최고관리자만 접근할 수 있습니다.');
}

$act_button = isset($_POST['act_button']) ? $_POST['act_button'] : '';
$post_chk = isset($_POST['chk']) ? (array)$_POST['chk'] : array();
$post_ea_id = isset($_POST['ea_id']) ? (array)$_POST['ea_id'] : array();

$chk_count = count($post_chk);

if (!$chk_count) {
    alert($act_button.'할 항목을 하나 이상 선택하세요.');
}

// 쿼리스트링 처리
$qstr = '';
if (isset($_POST['sfl'])) $qstr .= '&sfl='.$_POST['sfl'];
if (isset($_POST['stx'])) $qstr .= '&stx='.$_POST['stx'];
if (isset($_POST['status'])) $qstr .= '&status='.$_POST['status'];
if (isset($_POST['page'])) $qstr .= '&page='.$_POST['page'];

// =================================== 
// 선택 삭제 처리
// ===================================
if ($act_button == "선택삭제") {
    
    $delete_count = 0;
    $msg = '';
    
    for ($i=0; $i<$chk_count; $i++) {
        // 실제 번호를 넘김
        $k = isset($post_chk[$i]) ? (int)$post_chk[$i] : 0;
        $ea_id = isset($post_ea_id[$k]) ? (int)$post_ea_id[$k] : 0;
        
        if (!$ea_id) continue;
        
        // 신청 정보 확인
        $sql = "SELECT * FROM g5_event_apply WHERE ea_id = '{$ea_id}'";
        $ea = sql_fetch($sql);
        
        if (!$ea['ea_id']) {
            continue;
        }
        
        // 이미 승인된 신청인 경우 삭제 불가
        if ($ea['ea_status'] == 'paid') {
            $msg .= "신청번호 {$ea_id}번은 이미 승인 완료되어 삭제할 수 없습니다.\\n";
            continue;
        }
        
        // 신청 삭제
        $sql = "DELETE FROM g5_event_apply WHERE ea_id = '{$ea_id}'";
        sql_query($sql);
        
        $delete_count++;
    }
    
    if ($msg) {
        $msg .= "\\n나머지 {$delete_count}개 항목이 삭제되었습니다.";
        alert($msg, './event_list.php?'.$qstr);
    } else {
        alert("{$delete_count}개 항목이 삭제되었습니다.", './event_list.php?'.$qstr);
    }
}

// 그 외의 경우
alert('잘못된 접근입니다.', './event_list.php?'.$qstr);
?>
<?php
// ===================================
// ajax/event_payment.php
// 하부조직 관리자용 지급 처리 Ajax
// ===================================
?>
<?php
include_once('../../common.php');

header('Content-Type: application/json');

// 로그인 및 권한 체크
if(!$member['mb_id'] || $member['mb_grade'] < 2) {
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

$ea_id = isset($_POST['ea_id']) ? (int)$_POST['ea_id'] : 0;

if(!$ea_id) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

// 신청 정보 확인
$apply = sql_fetch("SELECT * FROM g5_event_apply WHERE ea_id = '{$ea_id}'");
if(!$apply) {
    echo json_encode(['success' => false, 'message' => '존재하지 않는 신청입니다.']);
    exit;
}

// 관리 권한 확인
$managed_members = get_managed_members($member['mb_id']);
if(!in_array($apply['mb_id'], $managed_members)) {
    echo json_encode(['success' => false, 'message' => '관리 권한이 없습니다.']);
    exit;
}

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

echo json_encode(['success' => true]);

function get_managed_members($manager_id) {
    $members = array();
    $sql = "SELECT mb_id FROM {$GLOBALS['g5']['member_table']} 
            WHERE mb_recommend = '{$manager_id}'";
    $result = sql_query($sql);
    while($row = sql_fetch_array($result)) {
        $members[] = $row['mb_id'];
    }
    return $members;
}
?>